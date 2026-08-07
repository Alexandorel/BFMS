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
use App\Services\DocumentSeriesService;
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

    public function test_the_payment_shortcut_lists_only_invoices_that_accept_payments(): void
    {
        [$issued, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $issued->update(['number' => 101]);
        $partiallyPaid = $issued->replicate()->fill([
            'number' => 102,
            'status' => InvoiceStatus::PartiallyPaid,
        ]);
        $partiallyPaid->save();
        $fullyPaid = $issued->replicate()->fill([
            'number' => 103,
            'status' => InvoiceStatus::FullyPaid,
        ]);
        $fullyPaid->save();
        $creditNote = $issued->replicate()->fill([
            'number' => 104,
            'credited_invoice_id' => $issued->id,
            'subtotal' => -1000.00,
            'total' => -1000.00,
        ]);
        $creditNote->save();
        $draft = $issued->replicate()->fill([
            'series' => null,
            'number' => null,
            'status' => InvoiceStatus::Draft,
        ]);
        $draft->save();

        $this->withoutVite()
            ->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('invoices.index', ['payment' => 1]))
            ->assertOk()
            ->assertSee('Facturi eligibile pentru plată')
            ->assertSee('FCT-101')
            ->assertSee('FCT-102')
            ->assertDontSee('FCT-103')
            ->assertDontSee('FCT-104')
            ->assertDontSee('<option value="fully_paid">', false)
            ->assertSee('appearance-none');
    }

    public function test_the_operator_payment_shortcut_opens_the_filtered_invoice_list(): void
    {
        [, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->withoutVite()
            ->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('operator.dashboard'))
            ->assertOk()
            ->assertSee(route('invoices.index', ['payment' => 1]), false);
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

    public function test_a_credit_note_is_not_payable_from_the_ui_or_a_direct_request(): void
    {
        [$original, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $creditNote = $original->replicate()->fill([
            'number' => 999,
            'credited_invoice_id' => $original->id,
            'subtotal' => -1000.00,
            'total' => -1000.00,
        ]);
        $creditNote->save();

        $this->withoutVite()
            ->actingAs($operator)
            ->get(route('invoices.show', $creditNote))
            ->assertOk()
            ->assertSee('Valoare storno')
            ->assertSee('Facturile de storno nu primesc încasări')
            ->assertDontSee('Înregistrează o încasare');

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $creditNote), $this->payload(100.00))
            ->assertSessionHasErrors('amount');

        $this->assertSame(0, Payment::where('invoice_id', $creditNote->id)->count());
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

    public function test_an_operator_sees_the_payment_form_on_an_issued_invoice(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertSee('Înregistrează o încasare')
            ->assertSee(route('invoices.payments.store', $invoice));
    }

    /**
     * NFR-1: contabilul are acces exclusiv de vizualizare.
     */
    public function test_a_contabil_does_not_see_the_payment_form(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $contabil = $this->createUser('contabil', $company);

        $this->actingAs($contabil)
            ->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertDontSee('Înregistrează o încasare');
    }

    public function test_the_form_disappears_once_the_invoice_is_fully_paid(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(1000.00));

        $this->actingAs($operator)
            ->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertDontSee('Înregistrează o încasare');
    }

    public function test_the_payment_delete_control_is_hidden_for_every_role(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);
        $administrator = $this->createUser('administrator', $company);

        $payment = Payment::withoutEvents(fn () => Payment::create([
            'invoice_id' => $invoice->id,
            'company_id' => $company->id,
            'payment_date' => '2026-08-01',
            'amount' => 400.00,
            'currency' => 'RON',
            'exchange_rate' => 1,
            'payment_method' => 'bank_transfer',
            'reference' => 'OP-4471',
            'created_by' => $operator->id,
        ]));
        $deleteRoute = route('invoices.payments.destroy', $payment);

        $this->actingAs($administrator)
            ->get(route('invoices.show', $invoice))
            ->assertDontSee($deleteRoute);

        $this->actingAs($operator)
            ->get(route('invoices.show', $invoice))
            ->assertDontSee($deleteRoute);
    }

    /**
     * NFR-3: fara functii native blocante din browser.
     */
    public function test_the_page_does_not_use_a_native_confirm_dialog(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $administrator = $this->createUser('administrator', $company);

        $this->actingAs($administrator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(400.00));

        $this->actingAs($administrator)
            ->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertDontSee('onsubmit="return confirm', false)
            ->assertDontSee('onclick="return confirm', false);
    }

    public function test_validation_errors_are_displayed_on_the_page(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->from(route('invoices.show', $invoice))
            ->post(route('invoices.payments.store', $invoice), $this->payload(1500.00));

        $this->actingAs($operator)
            ->get(route('invoices.show', $invoice))
            ->assertSee('Suma depășește restul de plată');
    }

    public function test_a_cash_payment_can_issue_a_receipt_from_the_form(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), [
                'payment_date' => '2026-08-01',
                'amount' => 500.00,
                'payment_method' => 'cash',
                'issue_receipt' => 'on', // asa trimite un checkbox HTML
            ])
            ->assertSessionHasNoErrors();

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();

        $this->assertSame('CHT', $payment->receipt_series);
        $this->assertSame(1, $payment->receipt_number);
    }

    public function test_the_receipt_option_is_rejected_for_a_bank_transfer(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), $this->payload(100.00) + [
                'issue_receipt' => 'on',
            ])
            ->assertSessionHasErrors('issue_receipt');

        $this->assertSame(0, Payment::where('invoice_id', $invoice->id)->count());
    }

    public function test_the_receipt_number_is_shown_in_the_payments_table(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->post(route('invoices.payments.store', $invoice), [
                'payment_date' => '2026-08-01',
                'amount' => 500.00,
                'payment_method' => 'cash',
                'issue_receipt' => 'on',
            ]);

        $this->actingAs($operator)
            ->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertSee('CHT-1');
    }

    public function test_a_payment_with_a_receipt_cannot_be_deleted_from_the_ui(): void
    {
        [$invoice, $company] = $this->createInvoice(1000.00);
        $administrator = $this->createUser('administrator', $company);

        $this->actingAs($administrator)
            ->post(route('invoices.payments.store', $invoice), [
                'payment_date' => '2026-08-01',
                'amount' => 500.00,
                'payment_method' => 'cash',
                'issue_receipt' => 'on',
            ]);

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();

        $this->actingAs($administrator)
            ->delete(route('invoices.payments.destroy', $payment))
            ->assertSessionHas('error');

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

        // ca in productie: cate o serie pentru fiecare tip de document (inclusiv CHT)
        app(DocumentSeriesService::class)->ensureDefaultsFor($company);

        $series = DocumentSeries::where('company_id', $company->id)
            ->where('document_type', DocumentType::Invoice)
            ->firstOrFail();

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
