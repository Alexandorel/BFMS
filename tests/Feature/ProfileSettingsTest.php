<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function user(array $overrides = []): User
    {
        return User::create(array_merge([
            'first_name' => 'Ion',
            'last_name' => 'Popescu',
            'email' => 'ion'.uniqid().'@example.com',
            'password' => Hash::make('parolaveche1'),
            'role' => 'administrator',
        ], $overrides));
    }

    public function test_pagina_afiseaza_datele_reale_ale_userului(): void
    {
        $user = $this->user(['first_name' => 'Maria', 'last_name' => 'Ionescu']);

        $response = $this->actingAs($user)->get(route('administrator.settings.profile'));

        $response->assertOk();
        $response->assertSee('Maria');
        $response->assertSee('Ionescu');
        $response->assertSee($user->email);
        // vechile valori hardcodate nu mai trebuie sa existe
        $response->assertDontSee('vintalexandru03@gmail.com');
    }

    public function test_datele_personale_se_salveaza(): void
    {
        $user = $this->user();

        $this->actingAs($user)->put(route('administrator.profile.update'), [
            'first_name' => 'Andrei',
            'last_name' => 'Georgescu',
        ])->assertRedirect();

        $proaspat = $user->fresh();
        $this->assertSame('Andrei', $proaspat->first_name);
        $this->assertSame('Georgescu', $proaspat->last_name);
    }

    public function test_un_singur_camp_lasa_restul_neatins(): void
    {
        $user = $this->user(['first_name' => 'Ion', 'last_name' => 'Popescu']);
        $emailInitial = $user->email;

        $this->actingAs($user)
            ->put(route('administrator.profile.update'), ['first_name' => 'Vasile'])
            ->assertRedirect();

        $proaspat = $user->fresh();
        $this->assertSame('Vasile', $proaspat->first_name);
        $this->assertSame('Popescu', $proaspat->last_name);
        $this->assertSame($emailInitial, $proaspat->email);
    }

    public function test_emailul_este_normalizat_si_curatat(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->put(route('administrator.profile.update'), ['email' => '  ION@Example.COM  '])
            ->assertRedirect();

        $this->assertSame('ion@example.com', $user->fresh()->email);
    }

    public function test_emailul_altui_user_este_respins(): void
    {
        $altul = $this->user(['email' => 'ocupat@example.com']);
        $user = $this->user();

        $this->actingAs($user)
            ->put(route('administrator.profile.update'), ['email' => 'ocupat@example.com'])
            ->assertSessionHasErrors('email');

        $this->assertNotSame('ocupat@example.com', $user->fresh()->email);
    }

    public function test_propriul_email_nesch1mbat_nu_declanseaza_eroare_de_unicitate(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->put(route('administrator.profile.update'), ['email' => $user->email])
            ->assertSessionHasNoErrors();
    }

    public function test_rolul_nu_poate_fi_modificat_prin_formular(): void
    {
        $user = $this->user(['role' => 'operator']);

        $this->actingAs($user)->put(route('administrator.profile.update'), [
            'first_name' => 'Andrei',
            'role' => 'superadmin',
        ])->assertRedirect();

        // validated() nu contine 'role', deci escaladarea de privilegii nu are efect
        $this->assertSame('operator', $user->fresh()->role);
    }

    public function test_parola_se_schimba_cu_parola_actuala_corecta(): void
    {
        $user = $this->user();

        $this->actingAs($user)->put(route('administrator.profile.password'), [
            'current_password' => 'parolaveche1',
            'password' => 'parolanoua123',
            'password_confirmation' => 'parolanoua123',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('parolanoua123', $user->fresh()->password));
    }

    public function test_parola_actuala_gresita_este_respinsa(): void
    {
        $user = $this->user();

        $this->actingAs($user)->put(route('administrator.profile.password'), [
            'current_password' => 'gresita',
            'password' => 'parolanoua123',
            'password_confirmation' => 'parolanoua123',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('parolaveche1', $user->fresh()->password));
    }

    public function test_confirmarea_care_nu_coincide_este_respinsa(): void
    {
        $user = $this->user();

        $this->actingAs($user)->put(route('administrator.profile.password'), [
            'current_password' => 'parolaveche1',
            'password' => 'parolanoua123',
            'password_confirmation' => 'altceva12345',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('parolaveche1', $user->fresh()->password));
    }

    public function test_un_vizitator_neautentificat_nu_ajunge_la_profil(): void
    {
        $this->get(route('administrator.settings.profile'))->assertRedirect(route('login'));
    }
}
