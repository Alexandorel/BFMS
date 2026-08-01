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

class InvoiceBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_balance_is_the_total_when_there_is_no_payment(): void
    {
        $invoice = $this->createInvoice(600.60);

        $this->assertSame(0.0, $invoice->paidAmount());
        $this->assertSame(600.60, $invoice->balance());
        $this->assertFalse($invoice->isFullyPaid());
    }

    public function test_balance_drops_after_a_partial_payment(): void
    {
        $invoice = $this->createInvoice(600.60);
        $this->addPayment($invoice, 100.10);

        $this->assertSame(100.10, $invoice->paidAmount());
        $this->assertSame(500.50, $invoice->balance());
        $this->assertFalse($invoice->isFullyPaid());
    }

    /**
     * Cazul care justifica strategia de rotunjire (F-302).
     *
     * 100.10 + 200.20 + 300.30 nu se pot reprezenta exact in baza 2, iar suma
     * lor lasa un rest de ~1.1e-13. Fara rotunjire, `balance() > 0` ar fi true
     * si factura ar ramane vesnic "incasata partial", desi interfata afiseaza
     * "rest de plata 0,00".
     */
    public function test_balance_is_exactly_zero_despite_floating_point_drift(): void
    {
        $invoice = $this->createInvoice(600.60);

        $this->addPayment($invoice, 100.10);
        $this->addPayment($invoice, 200.20);
        $this->addPayment($invoice, 300.30);

        // deriva bruta pe care o elimina round()
        $this->assertGreaterThan(0, 600.60 - (100.10 + 200.20 + 300.30));

        $this->assertSame(600.60, $invoice->paidAmount());
        $this->assertSame(0.0, $invoice->balance());
        $this->assertTrue($invoice->isFullyPaid());
    }

    public function test_paid_amount_is_the_same_whether_payments_are_eager_loaded_or_not(): void
    {
        $invoice = $this->createInvoice(600.60);
        $this->addPayment($invoice, 100.10);
        $this->addPayment($invoice, 200.20);

        $fresh = Invoice::findOrFail($invoice->id);
        $eager = Invoice::with('payments')->findOrFail($invoice->id);

        $this->assertFalse($fresh->relationLoaded('payments'));
        $this->assertTrue($eager->relationLoaded('payments'));
        $this->assertSame($fresh->paidAmount(), $eager->paidAmount());
        $this->assertSame(300.30, $eager->paidAmount());
    }

    public function test_only_issued_and_partially_paid_invoices_accept_payments(): void
    {
        $this->assertTrue(InvoiceStatus::Issued->acceptsPayments());
        $this->assertTrue(InvoiceStatus::PartiallyPaid->acceptsPayments());

        $this->assertFalse(InvoiceStatus::Draft->acceptsPayments());
        $this->assertFalse(InvoiceStatus::FullyPaid->acceptsPayments());
        $this->assertFalse(InvoiceStatus::Cancelled->acceptsPayments());
        $this->assertFalse(InvoiceStatus::Credited->acceptsPayments());
    }

    private function createInvoice(float $total): Invoice
    {
        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => 'Administrator',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'administrator',
        ]);

        $company = Company::create([
            'name' => 'Firma Test',
            'juridical_form' => 'SRL',
            'cui' => 'RO12345670',
            'trade_registry_number' => 'J40/0001/2026',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Test nr. 1',
            'social_capital' => 200.00,
            'vat_payer' => true,
        ]);
        $user->companies()->attach($company);

        $client = Client::create([
            'company_id' => $company->id,
            'client_type' => 'company',
            'name' => 'Client Test SRL',
            'cui' => 'RO87654321',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Client nr. 2',
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

        return Invoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'document_series_id' => $series->id,
            'document_type' => DocumentType::Invoice,
            'series' => 'FCT',
            'number' => 1,
            'status' => InvoiceStatus::Issued,
            'issue_date' => '2026-08-01',
            'due_date' => '2026-08-31',
            'currency' => 'RON',
            'exchange_rate' => 1,
            'subtotal' => $total,
            'vat_total' => 0,
            'total' => $total,
            'created_by' => $user->id,
        ]);
    }

    private function addPayment(Invoice $invoice, float $amount): void
    {
        Payment::create([
            'invoice_id' => $invoice->id,
            'company_id' => $invoice->company_id,
            'payment_date' => '2026-08-01',
            'amount' => $amount,
            'currency' => 'RON',
            'exchange_rate' => 1,
            'payment_method' => 'bank_transfer',
            'created_by' => $invoice->created_by,
        ]);

        $invoice->unsetRelation('payments');
    }
}
