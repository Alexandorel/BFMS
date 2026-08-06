<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Mail\InvoiceMail;
use App\Mail\PaymentConfirmationMail;
use App\Models\Client;
use App\Models\Company;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\User;
use App\Observers\InvoiceObserver;
use App\Services\DocumentSeriesService;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoiceNotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_and_payment_notifications_are_queued_without_blocking_the_flow(): void
    {
        Mail::fake();
        [$invoice, $user] = $this->invoiceContext(InvoiceStatus::Draft);

        Invoice::withoutEvents(fn (): Invoice => app(InvoiceService::class)->issue($invoice));

        $observer = app(InvoiceObserver::class);
        $this->assertInstanceOf(ShouldHandleEventsAfterCommit::class, $observer);
        $observer->updated($invoice);

        $this->assertSame(InvoiceStatus::Issued, $invoice->fresh()->status);
        Mail::assertQueued(InvoiceMail::class, 1);
        Mail::assertQueued(
            InvoiceMail::class,
            fn (InvoiceMail $mail): bool => $mail->hasTo('facturare@example.com')
                && $mail->invoice->is($invoice),
        );
        $this->assertDatabaseHas('invoice_notifications', [
            'invoice_id' => $invoice->id,
            'type' => 'issued',
            'sent_to' => 'facturare@example.com',
            'status' => 'sent',
        ]);

        $payment = app(PaymentService::class)->record($invoice, [
            'payment_date' => '2026-08-06',
            'amount' => 400.00,
            'payment_method' => 'bank_transfer',
            'reference' => 'OP-EMAIL-1',
        ], $user);

        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
        Mail::assertQueued(PaymentConfirmationMail::class, 1);
        Mail::assertQueued(
            PaymentConfirmationMail::class,
            fn (PaymentConfirmationMail $mail): bool => $mail->hasTo('facturare@example.com')
                && $mail->payment->is($payment),
        );
        $this->assertDatabaseHas('invoice_notifications', [
            'invoice_id' => $invoice->id,
            'payment_id' => $payment->id,
            'type' => 'payment_confirmation',
            'sent_to' => 'facturare@example.com',
            'status' => 'sent',
        ]);
    }

    /**
     * @return array{Invoice, User}
     */
    private function invoiceContext(
        InvoiceStatus $status,
    ): array {
        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => 'Administrator',
            'email' => 'administrator@example.com',
            'password' => 'password',
            'role' => 'administrator',
        ]);
        $company = Company::create([
            'name' => 'Firma Test SRL',
            'juridical_form' => 'SRL',
            'cui' => 'RO12345678',
            'trade_registry_number' => 'J12/1234/2026',
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
            'name' => 'Client Email SRL',
            'cui' => 'RO87654321',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Client nr. 1',
            'email' => 'facturare@example.com',
        ]);

        app(DocumentSeriesService::class)->ensureDefaultsFor($company);
        $series = DocumentSeries::where('company_id', $company->id)
            ->where('document_type', DocumentType::Invoice)
            ->firstOrFail();

        $attributes = [
            'company_id' => $company->id,
            'client_id' => $client->id,
            'document_series_id' => $series->id,
            'document_type' => DocumentType::Invoice,
            'series' => $status->isDraft() ? null : $series->prefix,
            'number' => $status->isDraft() ? null : 1,
            'status' => $status,
            'issue_date' => '2026-08-01',
            'due_date' => '2026-08-31',
            'currency' => 'RON',
            'exchange_rate' => 1,
            'subtotal' => 1000.00,
            'vat_total' => 190.00,
            'total' => 1190.00,
            'created_by' => $user->id,
        ];

        $invoice = Invoice::create($attributes);

        $invoice->lines()->create([
            'product_name_snapshot' => 'Servicii consultanță',
            'sku_snapshot' => 'SRV-01',
            'unit_measure_snapshot' => 'oră',
            'unit_price_snapshot' => 1000.00,
            'vat_rate_snapshot' => 19.00,
            'quantity' => 1.00,
            'line_subtotal' => 1000.00,
            'line_vat' => 190.00,
            'line_total' => 1190.00,
            'position' => 1,
        ]);

        return [$invoice->fresh(), $user];
    }
}
