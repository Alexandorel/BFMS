<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Izolare multi-tenant pe clienti (DoD#3): nimeni nu poate edita/sterge un
 * client al altei firme, chiar daca ii ghiceste id-ul (IDOR).
 */
class ClientAccessTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    public function test_an_operator_cannot_open_the_edit_form_of_another_companys_client(): void
    {
        [$operator, $companyA] = $this->userWithCompany('operator');
        $foreignClient = $this->makeClient($this->userWithCompany('operator')[1]);

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $companyA->id])
            ->get(route('clients.edit', $foreignClient))
            ->assertForbidden();
    }

    public function test_an_operator_cannot_update_another_companys_client(): void
    {
        [$operator, $companyA] = $this->userWithCompany('operator');
        $foreignClient = $this->makeClient($this->userWithCompany('operator')[1]);

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $companyA->id])
            ->put(route('clients.update', $foreignClient), $this->validPayload('Nume Furat SRL'))
            ->assertForbidden();

        // datele clientului strain raman neatinse
        $this->assertDatabaseMissing('clients', [
            'id' => $foreignClient->id,
            'name' => 'Nume Furat SRL',
        ]);
    }

    public function test_an_administrator_cannot_delete_another_companys_client(): void
    {
        [$admin, $companyA] = $this->userWithCompany('administrator');
        $foreignClient = $this->makeClient($this->userWithCompany('administrator')[1]);

        $this->actingAs($admin)
            ->withSession(['active_company_id' => $companyA->id])
            ->delete(route('clients.destroy', $foreignClient))
            ->assertForbidden();

        $this->assertDatabaseHas('clients', ['id' => $foreignClient->id]);
    }

    public function test_an_operator_can_edit_a_client_of_the_active_company(): void
    {
        [$operator, $company] = $this->userWithCompany('operator');
        $ownClient = $this->makeClient($company);

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('clients.edit', $ownClient))
            ->assertOk();
    }

    public function test_an_operator_can_update_a_client_of_the_active_company(): void
    {
        [$operator, $company] = $this->userWithCompany('operator');
        $ownClient = $this->makeClient($company);

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->put(route('clients.update', $ownClient), $this->validPayload('Client Actualizat SRL'))
            ->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', [
            'id' => $ownClient->id,
            'name' => 'Client Actualizat SRL',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(string $name): array
    {
        return [
            'client_type' => 'company',
            'name' => $name,
            'cui' => 'RO14837428',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Noua nr. 1',
        ];
    }

    /**
     * @return array{0: User, 1: Company}
     */
    private function userWithCompany(string $role): array
    {
        $this->sequence++;

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

        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => ucfirst($role),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => $role,
        ]);

        $user->companies()->attach($company);

        return [$user, $company];
    }

    private function makeClient(Company $company): Client
    {
        $this->sequence++;

        return Client::create([
            'company_id' => $company->id,
            'client_type' => 'company',
            'name' => 'Client '.$this->sequence,
            'cui' => 'RO'.(87654320 + $this->sequence),
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Client nr. '.$this->sequence,
        ]);
    }
}
