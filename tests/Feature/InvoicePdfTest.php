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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_an_authorized_user_can_download_the_invoice_pdf(): void
    {
        [$invoice, $administrator] = $this->createInvoice();

        $response = $this
            ->actingAs($administrator)
            ->get(route('invoices.pdf', $invoice));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString(
            'factura-FCT-101.pdf',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_the_invoice_page_contains_the_pdf_download_action(): void
    {
        [$invoice, $administrator] = $this->createInvoice();

        $this->withoutVite()
            ->actingAs($administrator)
            ->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertSee('Descarcă PDF')
            ->assertSee(route('invoices.pdf', $invoice), false);
    }

    public function test_a_user_from_another_company_cannot_download_the_invoice_pdf(): void
    {
        [$invoice] = $this->createInvoice();
        $otherCompany = $this->createCompany('Altă Firmă SRL', 'RO22222222', 'J12/2222/2026');
        $outsider = $this->createUser('outsider@example.com', $otherCompany, 'operator');

        $this->actingAs($outsider)
            ->get(route('invoices.pdf', $invoice))
            ->assertForbidden();
    }

    /**
     * @return array{0: Invoice, 1: User}
     */
    private function createInvoice(): array
    {
        $company = $this->createCompany('Firma PDF SRL', 'RO11111111', 'J12/1111/2026');
        $administrator = $this->createUser('admin-pdf@example.com', $company, 'administrator');

        $client = Client::create([
            'company_id' => $company->id,
            'client_type' => 'company',
            'name' => 'Client PDF SRL',
            'cui' => 'RO33333333',
            'trade_registry_number' => 'J12/3333/2026',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Clientului nr. 10',
            'email' => 'client-pdf@example.com',
        ]);

        app(DocumentSeriesService::class)->ensureDefaultsFor($company);
        $series = DocumentSeries::query()
            ->where('company_id', $company->id)
            ->where('document_type', DocumentType::Invoice)
            ->firstOrFail();

        $invoice = Invoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'document_series_id' => $series->id,
            'document_type' => DocumentType::Invoice,
            'series' => 'FCT',
            'number' => 101,
            'status' => InvoiceStatus::Issued,
            'issue_date' => '2026-08-05',
            'due_date' => '2026-08-20',
            'currency' => 'RON',
            'exchange_rate' => 1,
            'subtotal' => 200,
            'vat_total' => 38,
            'total' => 238,
            'created_by' => $administrator->id,
        ]);

        $invoice->lines()->create([
            'product_name_snapshot' => 'Servicii de consultanță',
            'sku_snapshot' => 'CONS-01',
            'unit_measure_snapshot' => 'ore',
            'unit_price_snapshot' => 100,
            'vat_rate_snapshot' => 19,
            'quantity' => 2,
            'line_subtotal' => 200,
            'line_vat' => 38,
            'line_total' => 238,
            'position' => 1,
        ]);

        return [$invoice, $administrator];
    }

    private function createCompany(string $name, string $cui, string $registryNumber): Company
    {
        return Company::create([
            'name' => $name,
            'juridical_form' => 'SRL',
            'cui' => $cui,
            'trade_registry_number' => $registryNumber,
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Furnizorului nr. 1',
            'social_capital' => 200,
            'vat_payer' => true,
        ]);
    }

    private function createUser(string $email, Company $company, string $role): User
    {
        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => ucfirst($role),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
        $user->companies()->attach($company);

        return $user;
    }
}
