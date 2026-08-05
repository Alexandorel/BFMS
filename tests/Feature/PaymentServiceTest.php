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

    public function test_a_missing_client_email_does_not_block_the_payment(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $payment = $this->service->record($invoice, $this->paymentData(400.00), $user);

        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
        $this->assertDatabaseHas('invoice_notifications', [
            'invoice_id' => $invoice->id,
            'payment_id' => $payment->id,
            'type' => 'payment_confirmation',
            'status' => 'failed',
            'error_message' => 'Clientul nu are adresă de email definită.',
        ]);
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

    public function test_a_cash_payment_can_issue_a_receipt(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $payment = $this->service->record($invoice, [
            'payment_date' => '2026-08-01',
            'amount' => 500.00,
            'payment_method' => 'cash',
            'issue_receipt' => true,
        ], $user);

        $this->assertTrue($payment->hasReceipt());
        $this->assertSame('CHT', $payment->receipt_series);
        $this->assertSame(1, $payment->receipt_number);
        $this->assertSame('CHT-1', $payment->receipt_label);
    }

    public function test_a_payment_without_the_option_gets_no_receipt(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $payment = $this->service->record($invoice, [
            'payment_date' => '2026-08-01',
            'amount' => 500.00,
            'payment_method' => 'cash',
        ], $user);

        $this->assertFalse($payment->hasReceipt());
        $this->assertNull($payment->receipt_label);
    }

    public function test_receipt_numbers_are_consecutive(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $data = [
            'payment_date' => '2026-08-01',
            'amount' => 100.00,
            'payment_method' => 'cash',
            'issue_receipt' => true,
        ];

        $first = $this->service->record($invoice, $data, $user);
        $second = $this->service->record($invoice, $data, $user);

        $this->assertSame(1, $first->receipt_number);
        $this->assertSame(2, $second->receipt_number);
    }

    public function test_a_receipt_cannot_be_issued_for_a_bank_transfer(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('doar pentru incasarile in numerar');

        $this->service->record($invoice, [
            'payment_date' => '2026-08-01',
            'amount' => 100.00,
            'payment_method' => 'bank_transfer',
            'reference' => 'OP-1',
            'issue_receipt' => true,
        ], $user);
    }

    /**
     * F-103: daca plata esueaza dupa alocare, numarul ar ramane ars si seria
     * ar capata un gol. Tranzactia trebuie sa dea totul inapoi.
     */
    public function test_a_failed_payment_does_not_burn_a_receipt_number(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $series = DocumentSeries::where('company_id', $invoice->company_id)
            ->where('document_type', DocumentType::Receipt)
            ->firstOrFail();

        $numberBefore = $series->current_number;

        try {
            // suma depaseste soldul: pica DUPA alocarea numarului
            $this->service->record($invoice, [
                'payment_date' => '2026-08-01',
                'amount' => 5000.00,
                'payment_method' => 'cash',
                'issue_receipt' => true,
            ], $user);
        } catch (RuntimeException) {
            // asteptat
        }

        $this->assertSame($numberBefore, $series->fresh()->current_number);
        $this->assertSame(0, Payment::where('invoice_id', $invoice->id)->count());
    }

    /**
     * F-103: chitanta e predata clientului, deci numarul nu se poate elibera.
     */
    public function test_a_payment_with_a_receipt_cannot_be_deleted(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $payment = $this->service->record($invoice, [
            'payment_date' => '2026-08-01',
            'amount' => 500.00,
            'payment_method' => 'cash',
            'issue_receipt' => true,
        ], $user);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('nu mai poate fi stearsa');

        $this->service->remove($payment);
    }

    public function test_a_payment_without_a_receipt_can_still_be_deleted(): void
    {
        [$invoice, $user] = $this->createInvoice(1000.00);

        $payment = $this->service->record($invoice, $this->paymentData(400.00), $user);

        $this->service->remove($payment);

        $this->assertSame(0, Payment::where('invoice_id', $invoice->id)->count());
        $this->assertSame(InvoiceStatus::Issued, $invoice->fresh()->status);
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
            'created_by' => $user->id,
        ]);

        return [$invoice, $user];
    }
}
