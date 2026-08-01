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
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PaymentService::class);
    }

    public function test_a_partial_payment_moves_the_invoice_to_partially_paid(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $this->service->record($invoice, $this->paymentData(400.00), $user);

        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->fresh()->status);
        $this->assertSame(600.00, $invoice->fresh()->balance());
    }

    public function test_a_full_payment_moves_the_invoice_to_fully_paid(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $this->service->record($invoice, $this->paymentData(1000.00), $user);

        $this->assertSame(InvoiceStatus::FullyPaid, $invoice->fresh()->status);
        $this->assertSame(0.0, $invoice->fresh()->balance());
    }

    public function test_several_partial_payments_add_up_to_fully_paid(): void
    {
        [$invoice, $user] = $this->createInvoice(600.60);

        $this->service->record($invoice, $this->paymentData(100.10), $user);
        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->fresh()->status);

        $this->service->record($invoice, $this->paymentData(200.20), $user);
        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->fresh()->status);

        $this->service->record($invoice, $this->paymentData(300.30), $user);
        $this->assertSame(InvoiceStatus::FullyPaid, $invoice->fresh()->status);
        $this->assertSame(0.0, $invoice->fresh()->balance());
    }

    public function test_an_overpayment_is_rejected(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $this->service->record($invoice, $this->paymentData(900.00), $user);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Suma depaseste restul de plata');

        $this->service->record($invoice, $this->paymentData(200.00), $user);
    }

    public function test_the_rejected_payment_is_not_persisted(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        try {
            $this->service->record($invoice, $this->paymentData(1500.00), $user);
        } catch (RuntimeException) {
            // asteptat
        }

        $this->assertSame(0, Payment::where('invoice_id', $invoice->id)->count());
        $this->assertSame(InvoiceStatus::Issued, $invoice->fresh()->status);
    }

    public function test_a_payment_on_a_draft_is_rejected(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00, InvoiceStatus::Draft);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('nu mai poate primi plati');

        $this->service->record($invoice, $this->paymentData(100.00), $user);
    }

    public function test_a_payment_on_a_credited_invoice_is_rejected(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00, InvoiceStatus::Credited);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('nu mai poate primi plati');

        $this->service->record($invoice, $this->paymentData(100.00), $user);
    }

    public function test_a_payment_in_another_currency_is_rejected(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00, InvoiceStatus::Issued, 'EUR');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('moneda facturii (EUR)');

        $this->service->record(
            $invoice,
            $this->paymentData(100.00) + ['currency' => 'RON'],
            $user
        );
    }

    public function test_a_zero_payment_is_rejected(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('mai mare decat zero');

        $this->service->record($invoice, $this->paymentData(0.00), $user);
    }

    public function test_removing_a_payment_moves_the_status_back(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $first = $this->service->record($invoice, $this->paymentData(400.00), $user);
        $second = $this->service->record($invoice, $this->paymentData(600.00), $user);

        $this->assertSame(InvoiceStatus::FullyPaid, $invoice->fresh()->status);

        $this->service->remove($second);
        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->fresh()->status);

        $this->service->remove($first);
        $this->assertSame(InvoiceStatus::Issued, $invoice->fresh()->status);
        $this->assertSame(1000.00, $invoice->fresh()->balance());
    }

    /**
     * O factura stornata nu trebuie sa redevina incasata cand se umbla la
     * platile ei istorice - syncStatus() nu atinge starile din afara fluxului.
     */
    public function test_removing_a_payment_never_revives_a_credited_invoice(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $payment = $this->service->record($invoice, $this->paymentData(400.00), $user);
        $invoice->update(['status' => InvoiceStatus::Credited]);

        $this->service->remove($payment);

        $this->assertSame(InvoiceStatus::Credited, $invoice->fresh()->status);
    }

    private function paymentData(float $amount): array
    {
        return [
            'payment_date' => '2026-08-01',
            'amount' => $amount,
            'payment_method' => 'bank_transfer',
            'reference' => 'OP-123',
        ];
    }

    /**
     * @return array{0: Invoice, 1: User}
     */
    private function createInvoice(
        float $total,
        InvoiceStatus $status = InvoiceStatus::Issued,
        string $currency = 'RON'
    ): array {
        $this->sequence++;

        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => 'Administrator',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'administrator',
        ]);

        $company = Company::create([
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
        $user->companies()->attach($company);

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
            'created_by' => $user->id,
        ]);

        return [$invoice, $user];
    }
}
