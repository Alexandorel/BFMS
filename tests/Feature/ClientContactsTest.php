<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * F-202: un client B2B poate avea mai multe persoane de contact (relație 1:N).
 */
class ClientContactsTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    public function test_a_company_client_is_created_with_several_contacts(): void
    {
        [$operator, $company] = $this->userWithCompany();

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->post(route('clients.store'), $this->payload([
                ['name' => 'Ion Popescu', 'role' => 'Director', 'email' => 'ion@acme.ro', 'phone' => '0711111111'],
                ['name' => 'Maria Ionescu', 'role' => 'Contabil', 'email' => 'maria@acme.ro', 'phone' => '0722222222'],
            ]))
            ->assertRedirect(route('clients.index'));

        $client = Client::where('company_id', $company->id)->firstOrFail();
        $this->assertSame(2, $client->contacts()->count());
        $this->assertDatabaseHas('client_contacts', ['client_id' => $client->id, 'email' => 'ion@acme.ro']);
        $this->assertDatabaseHas('client_contacts', ['client_id' => $client->id, 'email' => 'maria@acme.ro']);
    }

    public function test_updating_a_client_replaces_its_contacts(): void
    {
        [$operator, $company] = $this->userWithCompany();

        $client = Client::create($this->baseClient($company) + ['name' => 'Acme SRL']);
        $client->contacts()->createMany([
            ['name' => 'Vechi Unu', 'role' => 'A', 'email' => 'vechi1@acme.ro', 'phone' => '0700000001'],
            ['name' => 'Vechi Doi', 'role' => 'B', 'email' => 'vechi2@acme.ro', 'phone' => '0700000002'],
        ]);

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->put(route('clients.update', $client), $this->payload([
                ['name' => 'Nou Unu', 'role' => 'Nou', 'email' => 'nou@acme.ro', 'phone' => '0733333333'],
            ]))
            ->assertRedirect(route('clients.index'));

        // vechile contacte au disparut, ramane doar cel nou
        $this->assertSame(1, $client->contacts()->count());
        $this->assertDatabaseHas('client_contacts', ['client_id' => $client->id, 'email' => 'nou@acme.ro']);
        $this->assertDatabaseMissing('client_contacts', ['email' => 'vechi1@acme.ro']);
    }

    public function test_a_client_can_be_saved_without_any_contact(): void
    {
        [$operator, $company] = $this->userWithCompany();

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->post(route('clients.store'), $this->payload([]))
            ->assertRedirect(route('clients.index'));

        $client = Client::where('company_id', $company->id)->firstOrFail();
        $this->assertSame(0, $client->contacts()->count());
    }

    public function test_duplicate_contact_emails_are_rejected(): void
    {
        [$operator, $company] = $this->userWithCompany();

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->post(route('clients.store'), $this->payload([
                ['name' => 'Unu', 'role' => 'A', 'email' => 'acelasi@acme.ro', 'phone' => '0711111111'],
                ['name' => 'Doi', 'role' => 'B', 'email' => 'acelasi@acme.ro', 'phone' => '0722222222'],
            ]))
            ->assertSessionHasErrors('contacts.1.email');

        $this->assertSame(0, Client::where('company_id', $company->id)->count());
    }

    public function test_an_incomplete_contact_is_rejected(): void
    {
        [$operator, $company] = $this->userWithCompany();

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->post(route('clients.store'), $this->payload([
                ['name' => 'Fara email', 'role' => 'A', 'email' => '', 'phone' => '0711111111'],
            ]))
            ->assertSessionHasErrors('contacts.0.email');

        $this->assertSame(0, Client::where('company_id', $company->id)->count());
    }

    public function test_a_duplicate_cui_is_rejected_with_a_validation_error(): void
    {
        [$operator, $company] = $this->userWithCompany();

        // primul client cu CUI-ul din payload (RO14837428)
        Client::create($this->baseClient($company) + ['name' => 'Primul SRL']);

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->post(route('clients.store'), $this->payload([]))
            ->assertSessionHasErrors('cui'); // mesaj de validare, nu excepție 500

        $this->assertSame(1, Client::where('company_id', $company->id)->count());
    }

    public function test_a_client_can_keep_its_own_cui_on_edit(): void
    {
        [$operator, $company] = $this->userWithCompany();
        $client = Client::create($this->baseClient($company) + ['name' => 'Acme SRL']);

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->put(route('clients.update', $client), array_merge($this->payload([]), ['name' => 'Acme Redenumit SRL']))
            ->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Acme Redenumit SRL']);
    }

    /**
     * @param array<int, array<string, string>> $contacts
     * @return array<string, mixed>
     */
    private function payload(array $contacts): array
    {
        return [
            'client_type' => 'company',
            'name' => 'Acme SRL',
            'cui' => 'RO14837428',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Test nr. 1',
            'contacts' => $contacts,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function baseClient(Company $company): array
    {
        return [
            'company_id' => $company->id,
            'client_type' => 'company',
            'cui' => 'RO14837428',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Test nr. 1',
        ];
    }

    /**
     * @return array{0: User, 1: Company}
     */
    private function userWithCompany(): array
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
            'last_name' => 'Operator',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'operator',
        ]);

        $user->companies()->attach($company);

        return [$user, $company];
    }
}
