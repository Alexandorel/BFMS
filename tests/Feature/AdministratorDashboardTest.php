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
use Tests\TestCase;

class AdministratorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_only_the_five_most_recent_invoices_and_links_to_the_full_list(): void
    {
        $company = Company::create([
            'name' => 'Firma Dashboard SRL',
            'juridical_form' => 'SRL',
            'cui' => 'RO12345678',
            'trade_registry_number' => 'J12/1234/2026',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Test nr. 1',
            'social_capital' => 200,
            'vat_payer' => true,
        ]);

        $administrator = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Dashboard',
            'email' => 'admin-dashboard@example.com',
            'password' => 'password',
            'role' => 'administrator',
        ]);
        $administrator->companies()->attach($company);

        $client = Client::create([
            'company_id' => $company->id,
            'client_type' => 'company',
            'name' => 'Client Dashboard SRL',
            'cui' => 'RO87654321',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Client nr. 1',
        ]);

        app(DocumentSeriesService::class)->ensureDefaultsFor($company);
        $series = DocumentSeries::query()
            ->where('company_id', $company->id)
            ->where('document_type', DocumentType::Invoice)
            ->firstOrFail();

        foreach (range(1, 6) as $number) {
            $invoice = Invoice::create([
                'company_id' => $company->id,
                'client_id' => $client->id,
                'document_series_id' => $series->id,
                'document_type' => DocumentType::Invoice,
                'series' => 'FCT',
                'number' => $number,
                'status' => InvoiceStatus::Issued,
                'issue_date' => now()->subDays(7 - $number),
                'due_date' => now()->addDays($number),
                'currency' => 'RON',
                'subtotal' => 100 * $number,
                'vat_total' => 0,
                'total' => 100 * $number,
                'created_by' => $administrator->id,
            ]);
            $invoice->forceFill(['created_at' => now()->subDays(7 - $number)])->saveQuietly();
        }

        $this->withoutVite()
            ->actingAs($administrator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('dashboard.administrator'))
            ->assertOk()
            ->assertDontSee('FCT-1')
            ->assertSee('FCT-2')
            ->assertSee('FCT-6')
            ->assertSee('Vezi mai multe')
            ->assertSee(route('invoices.index'), false)
            ->assertSee('data-theme-toggle', false)
            ->assertSee('fixed top-2.5 right-4 z-40 hidden md:inline-flex', false);
    }
}
