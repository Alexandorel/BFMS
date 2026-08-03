<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Models\Company;
use App\Models\DocumentSeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DocumentSeriesSettingsTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Alfa Test SRL',
            'juridical_form' => 'SRL',
            'cui' => 'RO14837428',
            'trade_registry_number' => 'J12/100/2024',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Str. Test nr. 1',
            'social_capital' => '200.00',
            'vat_payer' => false,
        ]);

        $this->admin = $this->user('administrator');
        $this->company->users()->attach($this->admin->id);
    }

    private function user(string $role): User
    {
        return User::create([
            'first_name' => 'Ion',
            'last_name' => 'Popescu',
            'email' => 'ion'.uniqid().'@example.com',
            'password' => Hash::make('parolaveche1'),
            'role' => $role,
        ]);
    }

    private function series(array $overrides = []): DocumentSeries
    {
        return DocumentSeries::create(array_merge([
            'company_id' => $this->company->id,
            'document_type' => DocumentType::Invoice,
            'prefix' => 'ACME-F',
            'start_number' => 1,
        ], $overrides));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'company_id' => $this->company->id,
            'document_type' => 'invoice',
            'prefix' => 'ACME-F',
            'start_number' => 1,
            'is_default' => 0,
        ], $overrides);
    }

    public function test_doar_administratorul_are_acces(): void
    {
        foreach (['operator', 'contabil'] as $rol) {
            $this->actingAs($this->user($rol))
                ->get(route('administrator.series.index'))
                ->assertForbidden();

            $this->actingAs($this->user($rol))
                ->post(route('administrator.series.store'), $this->payload())
                ->assertForbidden();
        }
    }

    public function test_pagina_afiseaza_seriile_si_urmatorul_numar(): void
    {
        $this->series(['prefix' => 'ACME-F', 'start_number' => 1001, 'current_number' => 1003]);

        $this->withoutVite()
            ->actingAs($this->admin)
            ->get(route('administrator.series.index'))
            ->assertOk()
            ->assertSee('Serii documente')
            ->assertSee('ACME-F')
            //urmatorul document continua numerotarea, nu o ia de la start_number
            ->assertSee('ACME-F-1004');
    }

    public function test_seria_se_creeaza(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administrator.series.store'), $this->payload(['prefix' => 'ACME-F', 'start_number' => 1001]))
            ->assertRedirect();

        $this->assertDatabaseHas('document_series', [
            'company_id' => $this->company->id,
            'prefix' => 'ACME-F',
            'start_number' => 1001,
            'current_number' => 0,
        ]);
    }

    public function test_prima_serie_a_unui_tip_devine_implicita_automat(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administrator.series.store'), $this->payload(['is_default' => 0]))
            ->assertRedirect();

        $this->assertTrue(DocumentSeries::firstWhere('prefix', 'ACME-F')->is_default);
    }

    public function test_prefixul_este_normalizat(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administrator.series.store'), $this->payload(['prefix' => ' acme-f ']))
            ->assertRedirect();

        $this->assertDatabaseHas('document_series', ['prefix' => 'ACME-F']);
    }

    public function test_prefixul_duplicat_pe_acelasi_tip_este_respins(): void
    {
        $this->series(['prefix' => 'ACME-F']);

        $this->actingAs($this->admin)
            ->post(route('administrator.series.store'), $this->payload(['prefix' => 'ACME-F']))
            ->assertSessionHasErrors('prefix');
    }

    public function test_acelasi_prefix_este_permis_pe_alt_tip_de_document(): void
    {
        $this->series(['prefix' => 'ACME']);

        $this->actingAs($this->admin)
            ->post(route('administrator.series.store'), $this->payload([
                'prefix' => 'ACME',
                'document_type' => 'proforma',
            ]))
            ->assertSessionHasNoErrors();
    }

    public function test_prefixul_cu_caractere_invalide_este_respins(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administrator.series.store'), $this->payload(['prefix' => 'ACME/F']))
            ->assertSessionHasErrors('prefix');
    }

    public function test_nu_poti_crea_serii_pe_firma_altcuiva(): void
    {
        $straina = Company::create([
            'name' => 'Beta SRL',
            'juridical_form' => 'SRL',
            'cui' => 'RO34567890',
            'trade_registry_number' => 'J12/200/2024',
            'county' => 'Bihor',
            'city' => 'Oradea',
            'address' => 'Str. Alta nr. 2',
            'social_capital' => '200.00',
            'vat_payer' => false,
        ]);

        $this->actingAs($this->admin)
            ->post(route('administrator.series.store'), $this->payload(['company_id' => $straina->id]))
            ->assertSessionHasErrors('company_id');

        $this->assertDatabaseMissing('document_series', ['company_id' => $straina->id]);
    }

    public function test_seria_nefolosita_se_poate_edita(): void
    {
        $series = $this->series(['prefix' => 'ACME-F', 'start_number' => 1]);

        $this->actingAs($this->admin)
            ->put(route('administrator.series.update', $series), [
                'prefix' => 'ACME-2027',
                'start_number' => 500,
            ])
            ->assertSessionHasNoErrors();

        $proaspata = $series->fresh();
        $this->assertSame('ACME-2027', $proaspata->prefix);
        $this->assertSame(500, $proaspata->start_number);
    }

    public function test_seria_folosita_nu_isi_mai_poate_schimba_prefixul(): void
    {
        $series = $this->series(['prefix' => 'ACME-F', 'start_number' => 1, 'current_number' => 7]);

        $this->actingAs($this->admin)
            ->put(route('administrator.series.update', $series), [
                'prefix' => 'ALTCEVA',
                'start_number' => 1,
            ])
            ->assertSessionHasErrors('prefix');

        $this->assertSame('ACME-F', $series->fresh()->prefix);
    }

    public function test_seria_folosita_nu_isi_mai_poate_schimba_numarul_de_pornire(): void
    {
        $series = $this->series(['prefix' => 'ACME-F', 'start_number' => 1, 'current_number' => 7]);

        $this->actingAs($this->admin)
            ->put(route('administrator.series.update', $series), [
                'prefix' => 'ACME-F',
                'start_number' => 5000,
            ])
            ->assertSessionHasErrors('start_number');

        $this->assertSame(1, $series->fresh()->start_number);
    }

    public function test_salvarea_fara_modificari_nu_da_eroare_de_unicitate(): void
    {
        $series = $this->series(['prefix' => 'ACME-F', 'start_number' => 1]);

        $this->actingAs($this->admin)
            ->put(route('administrator.series.update', $series), [
                'prefix' => 'ACME-F',
                'start_number' => 1,
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_seria_implicita_se_poate_schimba(): void
    {
        $veche = $this->series(['prefix' => 'ACME-2026', 'is_default' => true]);
        $noua = $this->series(['prefix' => 'ACME-2027']);

        $this->actingAs($this->admin)
            ->patch(route('administrator.series.default', $noua))
            ->assertRedirect();

        $this->assertFalse($veche->fresh()->is_default);
        $this->assertTrue($noua->fresh()->is_default);
    }

    public function test_seria_implicita_nu_poate_fi_dezactivata(): void
    {
        $series = $this->series(['is_default' => true]);

        $this->actingAs($this->admin)
            ->patch(route('administrator.series.status', $series))
            ->assertSessionHas('error');

        $this->assertTrue($series->fresh()->is_active);
    }

    public function test_seria_neimplicita_se_dezactiveaza_si_se_reactiveaza(): void
    {
        $this->series(['prefix' => 'ACME-2026', 'is_default' => true]);
        $series = $this->series(['prefix' => 'ACME-2027']);

        $this->actingAs($this->admin)
            ->patch(route('administrator.series.status', $series))
            ->assertRedirect();
        $this->assertFalse($series->fresh()->is_active);

        $this->actingAs($this->admin)
            ->patch(route('administrator.series.status', $series))
            ->assertRedirect();
        $this->assertTrue($series->fresh()->is_active);
    }

    /**
     * O serie cu documente emise nu se sterge niciodata, se dezactiveaza.
     * URI-ul exista pentru PUT, deci DELETE intoarce 405, nu 404.
     */
    public function test_seriile_nu_se_pot_sterge(): void
    {
        $series = $this->series();

        $this->actingAs($this->admin)
            ->delete(route('administrator.series.index').'/'.$series->id)
            ->assertMethodNotAllowed();

        $this->assertDatabaseHas('document_series', ['id' => $series->id]);
    }
}
