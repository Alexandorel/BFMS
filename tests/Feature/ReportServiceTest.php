<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Exports\ClientStatementExport;
use App\Models\Client;
use App\Models\Company;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\InvoiceLines;
use App\Models\Payment;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    public function test_client_statement_calculates_totals_and_excludes_non_fiscal_documents(): void
    {
        [$company, $user, $client, $series] = $this->reportContext();
        $invoice = $this->createInvoice($company, $user, $client, $series, InvoiceStatus::Issued, '2026-07-10');
        $this->addPayment($invoice, $user, 60.00, '2026-07-15');

        $this->createInvoice($company, $user, $client, $series, InvoiceStatus::Draft, '2026-07-11');
        $this->createInvoice($company, $user, $client, $series, InvoiceStatus::Cancelled, '2026-07-12');

        $report = app(ReportService::class)->clientStatement($company, $client->id);

        $this->assertSame(1, $report['summary']['invoice_count']);
        $this->assertSame(119.00, $report['summary']['invoiced_ron']);
        $this->assertSame(60.00, $report['summary']['paid_ron']);
        $this->assertSame(59.00, $report['summary']['outstanding_ron']);
        $this->assertIsFloat($report['invoices']->first()['total_ron']);
    }

    public function test_month_close_uses_the_historical_balance_at_month_end(): void
    {
        [$company, $user, $client, $series] = $this->reportContext();
        $invoice = $this->createInvoice($company, $user, $client, $series, InvoiceStatus::FullyPaid, '2026-07-10');

        $this->addPayment($invoice, $user, 60.00, '2026-07-15');
        $this->addPayment($invoice, $user, 59.00, '2026-08-02');

        $report = app(ReportService::class)->monthClose($company, '2026-07');

        $this->assertSame(1, $report['summary']['invoice_count']);
        $this->assertSame(119.00, $report['summary']['invoiced_ron']);
        $this->assertSame(60.00, $report['summary']['collections_ron']);
        $this->assertSame(59.00, $report['summary']['outstanding_ron']);
        $this->assertSame(19.00, $report['summary']['vat_ron']);
        $this->assertSame(19.00, $report['vat_breakdown']->first()['rate']);
    }

    public function test_reports_never_include_data_from_another_company(): void
    {
        [$company, $user, $client, $series] = $this->reportContext();
        $this->createInvoice($company, $user, $client, $series, InvoiceStatus::Issued, '2026-07-10');

        [$foreignCompany, $foreignUser, $foreignClient, $foreignSeries] = $this->reportContext();
        $this->createInvoice($foreignCompany, $foreignUser, $foreignClient, $foreignSeries, InvoiceStatus::Issued, '2026-07-10');

        $report = app(ReportService::class)->monthClose($company, '2026-07');

        $this->assertSame(1, $report['summary']['invoice_count']);
        $this->assertSame($client->full_name, $report['invoices']->first()['client']);
    }

    public function test_a_client_from_another_company_cannot_be_requested(): void
    {
        [$company] = $this->reportContext();
        [, , $foreignClient] = $this->reportContext();

        $this->expectException(ModelNotFoundException::class);

        app(ReportService::class)->clientStatement($company, $foreignClient->id);
    }

    public function test_excel_export_receives_amounts_as_numbers_not_text(): void
    {
        [$company, $user, $client, $series] = $this->reportContext();
        $this->createInvoice($company, $user, $client, $series, InvoiceStatus::Issued, '2026-07-10');

        $report = app(ReportService::class)->clientStatement($company, $client->id);
        $sheets = (new ClientStatementExport($report))->sheets();
        $summaryRows = $sheets[0]->array();
        $historyRows = $sheets[1]->array();

        $this->assertIsFloat($summaryRows[9][1]);
        $this->assertIsFloat($historyRows[3][5]);
    }

    /**
     * @return array{Company, User, Client, DocumentSeries}
     */
    private function reportContext(): array
    {
        $company = $this->createCompany();
        $user = $this->createUser('contabil', $company);
        $client = Client::create([
            'company_id' => $company->id,
            'client_type' => 'company',
            'name' => 'Client '.$this->sequence.' SRL',
            'cui' => 'RO'.str_pad((string) (20000000 + $this->sequence), 8, '0', STR_PAD_LEFT),
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Client nr. '.$this->sequence,
        ]);
        $series = DocumentSeries::create([
            'company_id' => $company->id,
            'document_type' => DocumentType::Invoice,
            'prefix' => 'F'.$this->sequence,
            'start_number' => 1,
            'current_number' => 1,
            'is_default' => true,
            'is_active' => true,
        ]);

        return [$company, $user, $client, $series];
    }

    private function createInvoice(
        Company $company,
        User $user,
        Client $client,
        DocumentSeries $series,
        InvoiceStatus $status,
        string $issueDate,
    ): Invoice {
        $this->sequence++;

        $invoice = Invoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'document_series_id' => $series->id,
            'document_type' => DocumentType::Invoice,
            'series' => $series->prefix,
            'number' => $this->sequence,
            'status' => $status,
            'issue_date' => $issueDate,
            'due_date' => '2026-08-10',
            'currency' => 'RON',
            'exchange_rate' => 1,
            'subtotal' => 100.00,
            'vat_total' => 19.00,
            'total' => 119.00,
            'created_by' => $user->id,
        ]);

        InvoiceLines::create([
            'invoice_id' => $invoice->id,
            'product_name_snapshot' => 'Serviciu test',
            'sku_snapshot' => 'TEST-'.$this->sequence,
            'unit_measure_snapshot' => 'buc',
            'unit_price_snapshot' => 100.00,
            'vat_rate_snapshot' => 19.00,
            'quantity' => 1,
            'line_subtotal' => 100.00,
            'line_vat' => 19.00,
            'line_total' => 119.00,
            'position' => 1,
        ]);

        return $invoice;
    }

    private function addPayment(Invoice $invoice, User $user, float $amount, string $date): void
    {
        Payment::create([
            'invoice_id' => $invoice->id,
            'company_id' => $invoice->company_id,
            'payment_date' => $date,
            'amount' => $amount,
            'currency' => 'RON',
            'exchange_rate' => 1,
            'payment_method' => 'bank_transfer',
            'reference' => 'OP-'.$this->sequence,
            'created_by' => $user->id,
        ]);
    }

    private function createCompany(): Company
    {
        $this->sequence++;

        return Company::create([
            'name' => 'Firma Test '.$this->sequence,
            'juridical_form' => 'SRL',
            'cui' => 'RO'.str_pad((string) (10000000 + $this->sequence), 8, '0', STR_PAD_LEFT),
            'trade_registry_number' => sprintf('J12/%04d/2026', $this->sequence),
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Test nr. '.$this->sequence,
            'social_capital' => 200.00,
            'vat_payer' => true,
        ]);
    }

    private function createUser(string $role, Company $company): User
    {
        $this->sequence++;

        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => ucfirst($role),
            'email' => 'report-user'.$this->sequence.'@example.com',
            'password' => 'password',
            'role' => $role,
        ]);
        $user->companies()->attach($company);

        return $user;
    }
}
