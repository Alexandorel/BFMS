<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveCompanyAccessTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    public function test_a_user_can_switch_to_an_assigned_company(): void
    {
        $firstCompany = $this->createCompany();
        $secondCompany = $this->createCompany();
        $accountant = $this->createUser('contabil', [$firstCompany, $secondCompany]);

        $this->actingAs($accountant)
            ->withSession(['active_company_id' => $firstCompany->id])
            ->get(route('company.switch', $secondCompany->id))
            ->assertRedirect(route('dashboard.contabil'))
            ->assertSessionHas('active_company_id', $secondCompany->id)
            ->assertSessionHas('success');
    }

    public function test_a_user_cannot_switch_to_an_unassigned_company(): void
    {
        $ownCompany = $this->createCompany();
        $foreignCompany = $this->createCompany();
        $accountant = $this->createUser('contabil', [$ownCompany]);

        $this->actingAs($accountant)
            ->withSession(['active_company_id' => $ownCompany->id])
            ->get(route('company.switch', $foreignCompany->id))
            ->assertForbidden()
            ->assertSessionHas('active_company_id', $ownCompany->id);
    }

    public function test_a_forged_company_id_in_session_cannot_list_foreign_invoices(): void
    {
        $ownCompany = $this->createCompany();
        $foreignCompany = $this->createCompany();
        $accountant = $this->createUser('contabil', [$ownCompany]);

        $this->actingAs($accountant)
            ->withSession(['active_company_id' => $foreignCompany->id])
            ->get(route('dashboard.contabil.invoices'))
            ->assertForbidden();
    }

    public function test_the_first_assigned_company_becomes_active_when_session_is_empty(): void
    {
        $firstCompany = $this->createCompany();
        $secondCompany = $this->createCompany();
        $accountant = $this->createUser('contabil', [$firstCompany, $secondCompany]);

        $this->withoutVite()
            ->actingAs($accountant)
            ->get(route('dashboard.contabil'))
            ->assertOk()
            ->assertSessionHas('active_company_id', $firstCompany->id)
            ->assertViewHas('company', fn (Company $company) => $company->is($firstCompany));
    }

    public function test_a_user_without_companies_cannot_open_a_company_scoped_page(): void
    {
        $accountant = $this->createUser('contabil');

        $this->actingAs($accountant)
            ->get(route('dashboard.contabil.invoices'))
            ->assertForbidden();
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

    /**
     * @param array<int, Company> $companies
     */
    private function createUser(string $role, array $companies = []): User
    {
        $this->sequence++;

        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => ucfirst($role),
            'email' => 'user'.$this->sequence.'@example.com',
            'password' => 'password',
            'role' => $role,
        ]);

        foreach ($companies as $company) {
            $user->companies()->attach($company->id);
        }

        return $user;
    }
}
