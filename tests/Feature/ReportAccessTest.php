<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    public function test_accountant_can_open_reports_for_the_active_company(): void
    {
        $company = $this->createCompany();
        $accountant = $this->createUser('contabil', $company);
        $client = $this->createClient($company);

        $this->withoutVite()
            ->actingAs($accountant)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('dashboard.contabil.reports.index'))
            ->assertOk()
            ->assertSee('Rapoarte financiare')
            ->assertSee($client->full_name);
    }

    public function test_administrator_can_open_reports_from_the_administrator_area(): void
    {
        $company = $this->createCompany();
        $administrator = $this->createUser('administrator', $company);
        $client = $this->createClient($company);

        $this->withoutVite()
            ->actingAs($administrator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('administrator.reports.index'))
            ->assertOk()
            ->assertSee('Rapoarte financiare')
            ->assertSee('Administrator')
            ->assertSee($client->full_name)
            ->assertSee(route('administrator.reports.client-sheet'), false)
            ->assertSee(route('administrator.reports.month-close'), false);
    }

    public function test_administrator_sidebar_links_to_the_reports_page(): void
    {
        $company = $this->createCompany();
        $administrator = $this->createUser('administrator', $company);

        $this->withoutVite()
            ->actingAs($administrator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('dashboard.administrator'))
            ->assertOk()
            ->assertSee(route('administrator.reports.index'), false);
    }

    public function test_operator_can_open_reports_from_the_operator_area(): void
    {
        $company = $this->createCompany();
        $operator = $this->createUser('operator', $company);
        $client = $this->createClient($company);

        $this->withoutVite()
            ->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('operator.reports.index'))
            ->assertOk()
            ->assertSee('Rapoarte financiare')
            ->assertSee('Operator')
            ->assertSee($client->full_name)
            ->assertSee(route('operator.reports.client-sheet'), false)
            ->assertSee(route('operator.reports.month-close'), false);
    }

    public function test_operator_sidebar_links_to_the_reports_page(): void
    {
        $company = $this->createCompany();
        $operator = $this->createUser('operator', $company);

        $this->withoutVite()
            ->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('operator.dashboard'))
            ->assertOk()
            ->assertSee(route('operator.reports.index'), false);
    }

    public function test_report_forms_are_not_duplicated_on_the_accountant_dashboard(): void
    {
        $company = $this->createCompany();
        $accountant = $this->createUser('contabil', $company);
        $this->createClient($company);

        $this->withoutVite()
            ->actingAs($accountant)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('dashboard.contabil'))
            ->assertOk()
            ->assertDontSee('Fișă client')
            ->assertDontSee('Închidere lună');
    }

    public function test_operator_cannot_access_accountant_reports(): void
    {
        $company = $this->createCompany();
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->get(route('dashboard.contabil.reports.index'))
            ->assertForbidden();
    }

    public function test_operator_cannot_access_administrator_reports(): void
    {
        $company = $this->createCompany();
        $operator = $this->createUser('operator', $company);

        $this->actingAs($operator)
            ->get(route('administrator.reports.index'))
            ->assertForbidden();
    }

    public function test_client_statement_validates_the_requested_format(): void
    {
        $company = $this->createCompany();
        $accountant = $this->createUser('contabil', $company);
        $client = $this->createClient($company);

        $this->actingAs($accountant)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('dashboard.contabil.reports.client-sheet', [
                'client_id' => $client->id,
                'format' => 'csv',
            ]))
            ->assertSessionHasErrors('format');
    }

    public function test_month_close_requires_a_valid_month(): void
    {
        $company = $this->createCompany();
        $accountant = $this->createUser('contabil', $company);

        $this->actingAs($accountant)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('dashboard.contabil.reports.month-close', [
                'month' => 'iulie-2026',
                'format' => 'pdf',
            ]))
            ->assertSessionHasErrors('month');
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
            'email' => 'report-access'.$this->sequence.'@example.com',
            'password' => 'password',
            'role' => $role,
        ]);
        $user->companies()->attach($company);

        return $user;
    }

    private function createClient(Company $company): Client
    {
        $this->sequence++;

        return Client::create([
            'company_id' => $company->id,
            'client_type' => 'company',
            'name' => 'Client Test '.$this->sequence.' SRL',
            'cui' => 'RO'.str_pad((string) (20000000 + $this->sequence), 8, '0', STR_PAD_LEFT),
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Client nr. '.$this->sequence,
        ]);
    }
}
