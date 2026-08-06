<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Company;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\User;
use App\Services\DocumentSeriesService;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Mașina de stări a facturii (DoD #2): anularea și stornarea nu pot fi ocolite
 * prin operațiuni ilegale. Teste la nivel de serviciu, ca PaymentServiceTest.
 */
class InvoiceStateMachineTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    private InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(InvoiceService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Anulare (cancel)
    |--------------------------------------------------------------------------
    */

    public function test_cancel_marks_the_last_issued_invoice_as_cancelled_and_keeps_the_number(): void
    {
        [$invoice] = $this->makeInvoice();

        $this->service->cancel($invoice);

        $fresh = $invoice->fresh();
        $this->assertSame(InvoiceStatus::Cancelled, $fresh->status);
        // numarul ramane atribuit — nu apare gol in serie (F-103)
        $this->assertSame('FCT', $fresh->series);
        $this->assertSame(1, $fresh->number);
    }

    public function test_cancel_is_rejected_when_the_invoice_is_not_last_in_series(): void
    {
        [$invoice] = $this->makeInvoice();

        // o factura ulterioara a fost emisa intre timp
        $this->seriesFor($invoice)->update(['current_number' => 5]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ultima factură emisă din serie');

        $this->service->cancel($invoice);
    }

    public function test_cancel_is_rejected_when_the_invoice_has_payments(): void
    {
        [$invoice] = $this->makeInvoice(InvoiceStatus::PartiallyPaid);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('fără plăți');

        $this->service->cancel($invoice);
    }

    public function test_cancel_is_rejected_on_a_draft(): void
    {
        [$invoice] = $this->makeInvoice(InvoiceStatus::Draft);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('factură emisă');

        $this->service->cancel($invoice);
    }

    public function test_cancel_is_rejected_on_a_credit_note(): void
    {
        [$invoice, $user] = $this->makeInvoice();
        $creditNote = $this->service->storno($invoice, $user);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('storno nu poate fi anulată');

        $this->service->cancel($creditNote);
    }

    public function test_cancel_is_rejected_on_a_proforma(): void
    {
        [$invoice] = $this->makeInvoice(InvoiceStatus::Issued, DocumentType::Proforma);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Doar facturile pot fi anulate');

        $this->service->cancel($invoice);
    }

    /*
    |--------------------------------------------------------------------------
    | Stornare (storno)
    |--------------------------------------------------------------------------
    */

    public function test_storno_emits_a_negative_invoice_and_credits_the_original(): void
    {
        [$invoice, $user] = $this->makeInvoice();

        $storno = $this->service->storno($invoice, $user);

        // originalul devine Stornată
        $this->assertSame(InvoiceStatus::Credited, $invoice->fresh()->status);

        // nota de storno: valori negative, legata de original, emisa
        $this->assertSame(InvoiceStatus::Issued, $storno->status);
        $this->assertSame($invoice->id, $storno->credited_invoice_id);
        $this->assertSame(DocumentType::Invoice, $storno->document_type);
        $this->assertEquals(-1190.00, (float) $storno->total);
        $this->assertEquals(-1000.00, (float) $storno->subtotal);
        $this->assertEquals(-190.00, (float) $storno->vat_total);
    }

    public function test_storno_mirrors_lines_with_negated_values(): void
    {
        [$invoice, $user] = $this->makeInvoice();

        $storno = $this->service->storno($invoice, $user);
        $line = $storno->lines()->first();

        // cantitatile si totalurile sunt negate
        $this->assertEquals(-2.0, (float) $line->quantity);
        $this->assertEquals(-1000.0, (float) $line->line_subtotal);
        $this->assertEquals(-190.0, (float) $line->line_vat);
        $this->assertEquals(-1190.0, (float) $line->line_total);

        // snapshot-urile raman identice cu originalul (imutabilitate)
        $this->assertSame('Servicii consultanță', $line->product_name_snapshot);
        $this->assertSame('SRV-01', $line->sku_snapshot);
        $this->assertEquals(500.0, (float) $line->unit_price_snapshot);
        $this->assertEquals(19.0, (float) $line->vat_rate_snapshot);
    }

    public function test_storno_allocates_the_next_fiscal_number(): void
    {
        [$invoice, $user] = $this->makeInvoice();

        $storno = $this->service->storno($invoice, $user);

        // originalul are numarul 1, seria era la 1 -> storno primeste 2, fara gol
        $this->assertSame('FCT', $storno->series);
        $this->assertSame(2, $storno->number);
        $this->assertSame(2, (int) $this->seriesFor($invoice)->fresh()->current_number);
    }

    public function test_a_second_storno_on_the_same_invoice_is_rejected(): void
    {
        [$invoice, $user] = $this->makeInvoice();
        $this->service->storno($invoice, $user);

        $this->expectException(RuntimeException::class);
        // originalul e deja Stornată -> nu mai poate fi stornat
        $this->expectExceptionMessage('nu poate fi stornată în starea curentă');

        $this->service->storno($invoice->fresh(), $user);
    }

    public function test_the_anti_double_storno_guard_blocks_a_duplicate_credit_note(): void
    {
        [$invoice, $user] = $this->makeInvoice();
        $this->service->storno($invoice, $user);

        // fortam o stare inconsistenta: originalul redevine Emisă desi are deja
        // storno (update direct in DB, ca sa ocolim starea in-memory a modelului)
        Invoice::whereKey($invoice->id)->update(['status' => InvoiceStatus::Issued->value]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('a fost deja stornată');

        $this->service->storno($invoice->fresh(), $user);
    }

    public function test_storno_is_rejected_on_a_draft(): void
    {
        [$invoice, $user] = $this->makeInvoice(InvoiceStatus::Draft);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('nu poate fi stornată în starea curentă');

        $this->service->storno($invoice, $user);
    }

    public function test_storno_is_rejected_on_a_credit_note(): void
    {
        [$invoice, $user] = $this->makeInvoice();
        $creditNote = $this->service->storno($invoice, $user);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('nu se stornează la rândul ei');

        $this->service->storno($creditNote, $user);
    }

    public function test_storno_is_allowed_on_a_fully_paid_invoice(): void
    {
        [$invoice, $user] = $this->makeInvoice(InvoiceStatus::FullyPaid);

        $storno = $this->service->storno($invoice, $user);

        $this->assertSame(InvoiceStatus::Credited, $invoice->fresh()->status);
        $this->assertEquals(-1190.00, (float) $storno->total);
    }

    public function test_a_rejected_storno_leaves_no_partial_record(): void
    {
        [$invoice, $user] = $this->makeInvoice();
        $this->service->storno($invoice, $user);

        $invoicesBefore = Invoice::count();
        $numberBefore = (int) $this->seriesFor($invoice)->fresh()->current_number;

        try {
            // originalul e deja Stornată -> respins
            $this->service->storno($invoice->fresh(), $user);
        } catch (RuntimeException) {
            // asteptat
        }

        // nicio factura noua, niciun numar ars in serie
        $this->assertSame($invoicesBefore, Invoice::count());
        $this->assertSame($numberBefore, (int) $this->seriesFor($invoice)->fresh()->current_number);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function seriesFor(Invoice $invoice): DocumentSeries
    {
        return DocumentSeries::whereKey($invoice->document_series_id)->firstOrFail();
    }

    /**
     * @return array{0: Invoice, 1: User}
     */
    private function makeInvoice(
        InvoiceStatus $status = InvoiceStatus::Issued,
        DocumentType $type = DocumentType::Invoice
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

        // cate o serie pentru fiecare tip de document (FCT/PRF/CHT)
        app(DocumentSeriesService::class)->ensureDefaultsFor($company);

        $series = DocumentSeries::where('company_id', $company->id)
            ->where('document_type', $type)
            ->firstOrFail();

        $isDraft = $status->isDraft();

        $invoice = Invoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'document_series_id' => $series->id,
            'document_type' => $type,
            'series' => $isDraft ? null : $series->prefix,
            'number' => $isDraft ? null : 1,
            'status' => $status,
            'issue_date' => '2026-08-01',
            'due_date' => '2026-08-31',
            'currency' => 'RON',
            'exchange_rate' => 1,
            'subtotal' => 1000.00,
            'vat_total' => 190.00,
            'total' => 1190.00,
            'created_by' => $user->id,
        ]);

        $invoice->lines()->create([
            'product_id' => null,
            'product_name_snapshot' => 'Servicii consultanță',
            'sku_snapshot' => 'SRV-01',
            'unit_measure_snapshot' => 'oră',
            'unit_price_snapshot' => 500.00,
            'vat_rate_snapshot' => 19.00,
            'quantity' => 2.00,
            'line_subtotal' => 1000.00,
            'line_vat' => 190.00,
            'line_total' => 1190.00,
            'position' => 1,
        ]);

        // factura emisa e "ultima din serie": numarul == varful seriei
        if (! $isDraft) {
            $series->update(['current_number' => 1]);
        }

        return [$invoice->fresh(), $user];
    }
}
