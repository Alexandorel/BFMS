<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Models\Client;
use App\Models\Company;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InvoiceSeriesSelectionTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    private User $user;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = $this->company('Alfa Test SRL', 'RO14837428', 'J12/100/2024');

        $this->user = User::create([
            'first_name' => 'Ion',
            'last_name' => 'Popescu',
            'email' => 'ion'.uniqid().'@example.com',
            'password' => Hash::make('parolaveche1'),
            'role' => 'administrator',
        ]);
        $this->company->users()->attach($this->user->id);

        $this->client = Client::create([
            'company_id' => $this->company->id,
            'client_type' => 'company',
            'name' => 'Beta SRL',
            'cui' => 'RO34567890',
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Str. Test nr. 1',
        ]);
    }

    private function company(string $name, string $cui, string $reg): Company
    {
        return Company::create([
            'name' => $name,
            'juridical_form' => 'SRL',
            'cui' => $cui,
            'trade_registry_number' => $reg,
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Str. Test nr. 1',
            'social_capital' => '200.00',
            'vat_payer' => true,
        ]);
    }

    private function series(array $overrides = []): DocumentSeries
    {
        return DocumentSeries::create(array_merge([
            'company_id' => $this->company->id,
            'document_type' => DocumentType::Invoice,
            'prefix' => 'ACME-F',
            'start_number' => 1,
            'current_number' => 0,
            'is_default' => false,
            'is_active' => true,
        ], $overrides));
    }

    private function payload(DocumentSeries $series, array $overrides = []): array
    {
        return array_merge([
            'client_id' => $this->client->id,
            'document_type' => 'invoice',
            'document_series_id' => $series->id,
            'issue_date' => '2026-07-29',
            'due_date' => '2026-08-28',
            'currency' => 'RON',
            'product_name' => ['Consultanta'],
            'quantity' => [2],
            'unit_price' => [100],
            'vat_rate' => [19],
            'action' => 'issue',
        ], $overrides);
    }

    private function emite(array $payload)
    {
        return $this->actingAs($this->user)
            ->withSession(['active_company_id' => $this->company->id])
            ->post(route('invoices.store'), $payload);
    }

    public function test_factura_foloseste_seria_aleasa_nu_pe_cea_implicita(): void
    {
        $this->series(['prefix' => 'IMPLICITA', 'is_default' => true]);
        $aleasa = $this->series(['prefix' => 'ALEASA', 'start_number' => 500]);

        $this->emite($this->payload($aleasa))->assertRedirect();

        $invoice = Invoice::first();
        $this->assertSame('ALEASA', $invoice->series);
        $this->assertSame(500, $invoice->number);
        $this->assertSame($aleasa->id, $invoice->document_series_id);
    }

    public function test_contorul_avanseaza_doar_pe_seria_aleasa(): void
    {
        $implicita = $this->series(['prefix' => 'IMPLICITA', 'is_default' => true]);
        $aleasa = $this->series(['prefix' => 'ALEASA']);

        $this->emite($this->payload($aleasa))->assertRedirect();

        $this->assertSame(1, $aleasa->fresh()->current_number);
        $this->assertSame(0, $implicita->fresh()->current_number);
    }

    public function test_seria_altei_firme_este_respinsa(): void
    {
        $straina = $this->company('Beta SRL', 'RO45678901', 'J12/200/2024');
        $seriaStraina = DocumentSeries::create([
            'company_id' => $straina->id,
            'document_type' => DocumentType::Invoice,
            'prefix' => 'STRAINA',
            'start_number' => 1,
            'is_active' => true,
        ]);

        $this->emite($this->payload($seriaStraina))
            ->assertSessionHasErrors('document_series_id');

        $this->assertSame(0, Invoice::count());
    }

    public function test_seria_inactiva_este_respinsa(): void
    {
        $inactiva = $this->series(['prefix' => 'ARHIVATA', 'is_active' => false]);

        $this->emite($this->payload($inactiva))
            ->assertSessionHasErrors('document_series_id');

        $this->assertSame(0, Invoice::count());
    }

    public function test_seria_de_alt_tip_de_document_este_respinsa(): void
    {
        $proforma = $this->series([
            'prefix' => 'ACME-P',
            'document_type' => DocumentType::Proforma,
        ]);

        // seria e de proforma, dar documentul cerut e factura
        $this->emite($this->payload($proforma, ['document_type' => 'invoice']))
            ->assertSessionHasErrors('document_series_id');

        $this->assertSame(0, Invoice::count());
    }

    public function test_seria_este_obligatorie(): void
    {
        $series = $this->series();

        $payload = $this->payload($series);
        unset($payload['document_series_id']);

        $this->emite($payload)->assertSessionHasErrors('document_series_id');

        $this->assertSame(0, Invoice::count());
    }

    public function test_formularul_afiseaza_doar_seriile_active_ale_firmei(): void
    {
        $this->series(['prefix' => 'ACTIVA', 'is_default' => true]);
        $this->series(['prefix' => 'ARHIVATA', 'is_active' => false]);

        $this->withoutVite()
            ->actingAs($this->user)
            ->withSession(['active_company_id' => $this->company->id])
            ->get(route('invoices.create'))
            ->assertOk()
            ->assertSee('ACTIVA')
            ->assertDontSee('ARHIVATA');
    }
}
