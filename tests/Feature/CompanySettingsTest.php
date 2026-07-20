<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompanySettingsTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role = 'administrator'): User
    {
        return User::create([
            'first_name' => 'Ion',
            'last_name' => 'Popescu',
            'email' => 'ion'.uniqid().'@example.com',
            'password' => Hash::make('secret123'),
            'role' => $role,
        ]);
    }

    private function company(array $overrides = []): Company
    {
        return Company::create(array_merge([
            'name' => 'Alfa Test SRL',
            'juridical_form' => 'SRL',
            'cui' => 'RO14837428',
            'trade_registry_number' => 'J12/100/2024',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Str. Test nr. 1',
            'social_capital' => '200.00',
            'vat_payer' => false,
        ], $overrides));
    }

    public function test_pagina_afiseaza_firma_reala_din_baza_de_date(): void
    {
        $user = $this->user();
        $company = $this->company();
        $user->companies()->attach($company->id);

        $response = $this->actingAs($user)->get(route('administrator.settings.company'));

        $response->assertOk();
        $response->assertSee('Alfa Test SRL');
        $response->assertSee('RO14837428');
        // placeholderele vechi nu mai trebuie sa apara nicaieri
        $response->assertDontSee('SC Exemplu SRL');
        $response->assertDontSee('Demo Consulting SRL');
    }

    public function test_formularul_de_sediu_salveaza_in_baza_de_date(): void
    {
        $user = $this->user();
        $company = $this->company();
        $user->companies()->attach($company->id);

        $response = $this->actingAs($user)->put(
            route('administrator.companies.update', $company),
            ['county' => 'Bihor', 'city' => 'Oradea', 'address' => 'Str. Republicii nr. 5']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'county' => 'Bihor',
            'city' => 'Oradea',
            'address' => 'Str. Republicii nr. 5',
            // campurile netrimise raman neatinse
            'name' => 'Alfa Test SRL',
        ]);
    }

    public function test_datele_de_identificare_se_salveaza(): void
    {
        $user = $this->user();
        $company = $this->company();
        $user->companies()->attach($company->id);

        $this->actingAs($user)->put(route('administrator.companies.update', $company), [
            'name' => 'Alfa Modificat SRL',
            'juridical_form' => 'SA',
            'cui' => 'RO10000008',
            'trade_registry_number' => 'J12/999/2025',
            'social_capital' => '5000.50',
        ])->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Alfa Modificat SRL',
            'juridical_form' => 'SA',
            'cui' => 'RO10000008',
            'social_capital' => '5000.50',
        ]);
    }

    public function test_tva_poate_fi_debifat(): void
    {
        $user = $this->user();
        $company = $this->company(['vat_payer' => true]);
        $user->companies()->attach($company->id);

        // checkbox nebifat => browserul trimite doar hidden-ul cu 0
        $this->actingAs($user)
            ->put(route('administrator.companies.update', $company), ['vat_payer' => '0'])
            ->assertRedirect();

        $this->assertFalse($company->fresh()->vat_payer);
    }

    public function test_nu_pot_edita_o_firma_care_nu_imi_apartine(): void
    {
        $user = $this->user();
        $altaFirma = $this->company(['cui' => 'RO10000016', 'trade_registry_number' => 'J12/777/2024']);
        // deliberat: nu o atasam userului

        $this->actingAs($user)
            ->put(route('administrator.companies.update', $altaFirma), ['city' => 'Hacked'])
            ->assertForbidden();

        $this->assertDatabaseHas('companies', [
            'id' => $altaFirma->id,
            'city' => 'Cluj-Napoca',
        ]);
    }

    public function test_cui_duplicat_este_respins(): void
    {
        $user = $this->user();
        $a = $this->company();
        $b = $this->company(['cui' => 'RO10000016', 'trade_registry_number' => 'J12/555/2024']);
        $user->companies()->attach([$a->id, $b->id]);

        $this->actingAs($user)
            ->put(route('administrator.companies.update', $b), [
                'name' => $b->name,
                'juridical_form' => $b->juridical_form,
                'cui' => 'RO14837428', // deja folosit de $a
                'trade_registry_number' => $b->trade_registry_number,
                'social_capital' => $b->social_capital,
            ])
            ->assertSessionHasErrors('cui');

        $this->assertSame('RO10000016', $b->fresh()->cui);
    }
}
