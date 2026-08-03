<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    private int $companySequence = 0;

    public function test_administrator_can_see_the_bank_accounts_page(): void
    {
        $administrator = $this->createUser('administrator');
        $company = $this->createCompany();
        $administrator->companies()->attach($company);

        BankAccount::create([
            'company_id' => $company->id,
            'bank_name' => 'Banca Transilvania',
            'iban' => 'RO37BTRL0000000000000001',
            'currency' => 'RON',
        ]);

        $response = $this
            ->actingAs($administrator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('administrator.bank-accounts.index'));

        $response
            ->assertOk()
            ->assertSee('Banca Transilvania')
            ->assertSee('RO37BTRL0000000000000001');
    }

    public function test_administrator_sees_only_the_accounts_of_the_selected_company(): void
    {
        $administrator = $this->createUser('administrator');
        $ownedCompany = $this->createCompany();
        $otherCompany = $this->createCompany();
        $administrator->companies()->attach($ownedCompany);

        BankAccount::create([
            'company_id' => $ownedCompany->id,
            'bank_name' => 'Banca firmei proprii',
            'iban' => 'RO37BTRL0000000000000001',
            'currency' => 'RON',
        ]);

        BankAccount::create([
            'company_id' => $otherCompany->id,
            'bank_name' => 'Banca altei firme',
            'iban' => 'RO83RNCB0000000000000002',
            'currency' => 'RON',
        ]);

        $response = $this
            ->actingAs($administrator)
            ->get(route('administrator.bank-accounts.index', [
                'firma' => $ownedCompany->id,
            ]));

        $response
            ->assertOk()
            ->assertSee('Banca firmei proprii')
            ->assertDontSee('Banca altei firme');
    }

    public function test_administrator_can_create_a_bank_account(): void
    {
        $administrator = $this->createUser('administrator');
        $company = $this->createCompany();
        $administrator->companies()->attach($company);

        $response = $this
            ->actingAs($administrator)
            ->post(route('administrator.bank-accounts.store'), [
                'company_id' => $company->id,
                'bank_name' => 'BCR',
                'iban' => 'ro37 btrl 0000 0000 0000 0001',
                'currency' => 'RON',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('administrator.bank-accounts.index', [
                'firma' => $company->id,
            ]));

        $this->assertDatabaseHas('bank_accounts', [
            'company_id' => $company->id,
            'bank_name' => 'BCR',
            'iban' => 'RO37BTRL0000000000000001',
            'currency' => 'RON',
        ]);
    }

    public function test_invalid_iban_is_rejected(): void
    {
        $administrator = $this->createUser('administrator');
        $company = $this->createCompany();
        $administrator->companies()->attach($company);

        $response = $this
            ->actingAs($administrator)
            ->from(route('administrator.bank-accounts.index'))
            ->post(route('administrator.bank-accounts.store'), [
                'company_id' => $company->id,
                'bank_name' => 'Banca Test',
                'iban' => 'RO123',
                'currency' => 'RON',
            ]);

        $response
            ->assertRedirect(route('administrator.bank-accounts.index'))
            ->assertSessionHasErrors('iban');

        $this->assertDatabaseCount('bank_accounts', 0);
    }

    public function test_administrator_can_update_a_bank_account(): void
    {
        $administrator = $this->createUser('administrator');
        $company = $this->createCompany();
        $administrator->companies()->attach($company);

        $bankAccount = BankAccount::create([
            'company_id' => $company->id,
            'bank_name' => 'Banca Veche',
            'iban' => 'RO37BTRL0000000000000001',
            'currency' => 'RON',
        ]);

        $response = $this
            ->actingAs($administrator)
            ->put(route('administrator.bank-accounts.update', $bankAccount), [
                'bank_name' => 'Banca Nouă',
                'iban' => 'RO83RNCB0000000000000002',
                'currency' => 'RON',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('administrator.bank-accounts.index', [
                'firma' => $company->id,
            ]));

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bankAccount->id,
            'company_id' => $company->id,
            'bank_name' => 'Banca Nouă',
            'iban' => 'RO83RNCB0000000000000002',
            'currency' => 'RON',
        ]);
    }

    public function test_administrator_can_delete_a_bank_account(): void
    {
        $administrator = $this->createUser('administrator');
        $company = $this->createCompany();
        $administrator->companies()->attach($company);

        $bankAccount = BankAccount::create([
            'company_id' => $company->id,
            'bank_name' => 'Banca de șters',
            'iban' => 'RO37BTRL0000000000000001',
            'currency' => 'RON',
        ]);

        $response = $this
            ->actingAs($administrator)
            ->delete(route('administrator.bank-accounts.destroy', $bankAccount));

        $response->assertRedirect(route('administrator.bank-accounts.index', [
            'firma' => $company->id,
        ]));

        $this->assertDatabaseMissing('bank_accounts', [
            'id' => $bankAccount->id,
        ]);
    }

    public function test_administrator_cannot_update_or_delete_another_companys_account(): void
    {
        $administrator = $this->createUser('administrator');
        $ownedCompany = $this->createCompany();
        $otherCompany = $this->createCompany();
        $administrator->companies()->attach($ownedCompany);

        $otherAccount = BankAccount::create([
            'company_id' => $otherCompany->id,
            'bank_name' => 'Banca protejată',
            'iban' => 'RO37BTRL0000000000000001',
            'currency' => 'RON',
        ]);

        $this
            ->actingAs($administrator)
            ->put(route('administrator.bank-accounts.update', $otherAccount), [
                'bank_name' => 'Modificare nepermisă',
                'iban' => 'RO83RNCB0000000000000002',
                'currency' => 'RON',
            ])
            ->assertForbidden();

        $this
            ->actingAs($administrator)
            ->delete(route('administrator.bank-accounts.destroy', $otherAccount))
            ->assertForbidden();

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $otherAccount->id,
            'bank_name' => 'Banca protejată',
            'iban' => 'RO37BTRL0000000000000001',
        ]);
    }

    public function test_non_administrator_cannot_access_bank_account_settings(): void
    {
        $operator = $this->createUser('operator');

        $this
            ->actingAs($operator)
            ->get(route('administrator.bank-accounts.index'))
            ->assertForbidden();
    }

    private function createUser(string $role): User
    {
        return User::create([
            'first_name' => 'Utilizator',
            'last_name' => ucfirst($role),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function createCompany(): Company
    {
        $this->companySequence++;

        return Company::create([
            'name' => 'Firma Test '.$this->companySequence,
            'juridical_form' => 'SRL',
            'cui' => 'RO'.(12345670 + $this->companySequence),
            'trade_registry_number' => sprintf(
                'J40/%04d/2026',
                $this->companySequence
            ),
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Test nr. '.$this->companySequence,
            'social_capital' => 200.00,
            'vat_payer' => true,
        ]);
    }
}
