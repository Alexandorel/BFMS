<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Company;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    public function test_an_operator_can_record_a_payment(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $response = $this
            ->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(400.00));

        $response
            ->assertRedirect(route('invoices.show', $invoice))
            ->assertSessionHas('success');

        $this->assertSame(1, Payment::where('invoice_id', $invoice->id)->count());
        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->fresh()->status);
    }

    public function test_a_full_payment_marks_the_invoice_as_fully_paid(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $administrator = $this->createUser('administrator', $company);

        $this->actingAs($administrator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(1000.00))
            ->assertSessionHasNoErrors();

        $this->assertSame(InvoiceStatus::FullyPaid, $invoice->fresh()->status);
        $this->assertSame(0.0, $invoice->fresh()->balance());
    }

    public function test_a_contabil_cannot_record_a_payment(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $contabil = $this->createUser('contabil', $company);

        $this->actingAs($contabil)
            ->post(route('invoices.payments.store', $invoice), $this->payload(100.00))
            ->assertForbidden();

        $this->assertSame(0, Payment::where('invoice_id', $invoice->id)->count());
    }

    public function test_a_user_from_another_company_cannot_record_a_payment(): void
    {
        [$invoice] = $this->createInvoice(1000.00);
        $outsider = $this->createUser('operator', $this->createCompany());

        $this->actingAs($outsider)
            ->post(route('invoices.payments.store', $invoice), $this->payload(100.00))
            ->assertForbidden();
    }

    public function test_an_overpayment_is_rejected_by_validation(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(1500.00))
            ->assertSessionHasErrors('amount');

        $this->assertSame(0, Payment::where('invoice_id', $invoice->id)->count());
    }

    public function test_a_payment_on_a_draft_is_rejected_by_validation(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00, InvoiceStatus::Draft);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(100.00))
            ->assertSessionHasErrors('amount');
    }

    public function test_a_bank_transfer_requires_a_reference(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), [
                'payment_date' => '2026-08-01',
                'amount' => 100.00,
                'payment_method' => 'bank_transfer',
            ])
            ->assertSessionHasErrors('reference');
    }

    public function test_a_cash_payment_does_not_require_a_reference(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), [
                'payment_date' => '2026-08-01',
                'amount' => 100.00,
                'payment_method' => 'cash',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Payment::where('invoice_id', $invoice->id)->count());
    }

    public function test_a_payment_dated_in_the_future_is_rejected(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), [
                'payment_date' => now()->addDay()->toDateString(),
                'amount' => 100.00,
                'payment_method' => 'cash',
            ])
            ->assertSessionHasErrors('payment_date');
    }

    public function test_a_payment_dated_before_the_invoice_is_rejected(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), [
                'payment_date' => '2026-07-01', // factura e emisa la 2026-08-01
                'amount' => 100.00,
                'payment_method' => 'cash',
            ])
            ->assertSessionHasErrors('payment_date');
    }

    public function test_the_payment_currency_always_follows_the_invoice(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00, InvoiceStatus::Issued, 'EUR');
        $operator = $this->createUser('operator', $company);

        // chiar daca formularul trimite RON, moneda facturii castiga
        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(100.00) + ['currency' => 'RON'])
            ->assertSessionHasNoErrors();

        $this->assertSame('EUR', Payment::where('invoice_id', $invoice->id)->value('currency'));
    }

    public function test_an_administrator_can_delete_a_payment_and_the_status_goes_back(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $administrator = $this->createUser('administrator', $company);

        $this->actingAs($administrator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(1000.00));

        $this->assertSame(InvoiceStatus::FullyPaid, $invoice->fresh()->status);

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();

        $this->actingAs($administrator)
            ->delete(route('invoices.payments.destroy', $payment))
            ->assertRedirect(route('invoices.show', $invoice->id))
            ->assertSessionHas('success');

        $this->assertSame(0, Payment::where('invoice_id', $invoice->id)->count());
        $this->assertSame(InvoiceStatus::Issued, $invoice->fresh()->status);
    }

    /**
     * NFR-1: operatorul gestioneaza plati, dar nu sterge date.
     */
    public function test_an_operator_cannot_delete_a_payment(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(400.00));

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();

        $this->actingAs($operator)
            ->delete(route('invoices.payments.destroy', $payment))
            ->assertForbidden();

        $this->assertSame(1, Payment::where('invoice_id', $invoice->id)->count());
    }

    private function payload(float $amount): array
    {
        return [
            'payment_date' => '2026-08-01',
            'amount' => $amount,
            'payment_method' => 'bank_transfer',
            'reference' => 'OP-4471',
        ];
    }

    private function createUser(string $role, ?Company $company = null): User
    {
        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => ucfirst($role),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => $role,
        ]);

        if ($company) {
            $user->companies()->attach($company);
        }

        return $user;
    }

    private function createCompany(): Company
    {
        $this->sequence++;

        return Company::create([
            'name' => 'Firma Test '.$this->sequence,
            'juridical_form' => 'SRL',
            'cui' => 'RO'.(12345670 + $this->sequence),
            'trade_registry_number' => sprintf('J40/%04d/2026', $this->sequence),
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Test nr. '.$this->sequence,
            'social_capital' => 200.00,
            'vat_payer' => true,
        ]);
    }

    /**
     * @return array{0: Invoice, 1: Company}
     */
    private function createInvoice(
        float $total,
        InvoiceStatus $status = InvoiceStatus::Issued,
        string $currency = 'RON'
    ): array {
        $company = $this->createCompany();
        $author = $this->createUser('administrator', $company);

        $client = Client::create([
            'company_id' => $company->id,
            'client_type' => 'company',
            'name' => 'Client Test SRL',
            'cui' => 'RO'.(87654320 + $this->sequence),
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Client nr. '.$this->sequence,
        ]);

        $series = DocumentSeries::create([
            'company_id' => $company->id,
            'document_type' => DocumentType::Invoice,
            'prefix' => 'FCT',
            'start_number' => 1,
            'current_number' => 1,
            'is_default' => true,
            'is_active' => true,
        ]);

        $invoice = Invoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'document_series_id' => $series->id,
            'document_type' => DocumentType::Invoice,
            'series' => $status->isDraft() ? null : 'FCT',
            'number' => $status->isDraft() ? null : $this->sequence,
            'status' => $status,
            'issue_date' => '2026-08-01',
            'due_date' => '2026-08-31',
            'currency' => $currency,
            'exchange_rate' => $currency === 'RON' ? 1 : 4.9755,
            'subtotal' => $total,
            'vat_total' => 0,
            'total' => $total,
            'created_by' => $author->id,
        ]);

        return [$invoice, $company];
    }
}
