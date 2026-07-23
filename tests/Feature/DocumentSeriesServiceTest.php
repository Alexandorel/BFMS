<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Models\Company;
use App\Models\DocumentSeries;
use App\Services\DocumentSeriesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class DocumentSeriesServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentSeriesService $service;

    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DocumentSeriesService();
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

    /**
     * Serviciul cere o tranzacție; o deschidem aici, ca la emiterea reală.
     */
    private function aloca(DocumentSeries $series): int
    {
        return DB::transaction(fn () => $this->service->allocateNumber($series));
    }

    public function test_prima_alocare_foloseste_numarul_de_pornire(): void
    {
        $series = $this->series(['start_number' => 1001]);

        $this->assertSame(1001, $this->aloca($series));
        $this->assertSame(1001, $series->fresh()->current_number);
    }

    public function test_alocarile_succesive_nu_lasa_gauri(): void
    {
        $series = $this->series(['start_number' => 1001]);

        $numere = [$this->aloca($series), $this->aloca($series), $this->aloca($series)];

        $this->assertSame([1001, 1002, 1003], $numere);
        $this->assertSame(1003, $series->fresh()->current_number);
    }

    public function test_ridicarea_numarului_de_pornire_avanseaza_contorul(): void
    {
        // firma trece pe o numerotare nouă la început de an
        $series = $this->series(['start_number' => 1]);
        $this->assertSame(1, $this->aloca($series));

        $series->update(['start_number' => 5000]);

        $this->assertSame(5000, $this->aloca($series));
    }

    public function test_seria_inactiva_nu_poate_emite(): void
    {
        $series = $this->series(['is_active' => false]);

        $this->expectException(RuntimeException::class);

        $this->aloca($series);
    }

    // Nota: garda "apel în afara unei tranzacții" nu poate fi testată aici —
    // RefreshDatabase rulează fiecare test într-o tranzacție, deci
    // transactionLevel() e mereu >= 1. Comportamentul e verificat manual pe
    // MySQL; codul e în DocumentSeriesService::allocateNumber().

    public function test_contorul_nu_avanseaza_daca_seria_e_inactiva(): void
    {
        $series = $this->series(['is_active' => false]);

        try {
            $this->aloca($series);
        } catch (RuntimeException) {
            // așteptat
        }

        $this->assertSame(0, $series->fresh()->current_number);
    }

    public function test_default_for_prefera_seria_implicita(): void
    {
        $this->series(['prefix' => 'ACME-VECHI']);
        $this->series(['prefix' => 'ACME-F', 'is_default' => true]);

        $gasita = $this->service->defaultFor($this->company->id, DocumentType::Invoice);

        $this->assertSame('ACME-F', $gasita->prefix);
    }

    public function test_default_for_ignora_seriile_inactive(): void
    {
        $this->series(['prefix' => 'ACME-2026', 'is_default' => true, 'is_active' => false]);
        $this->series(['prefix' => 'ACME-2027']);

        $gasita = $this->service->defaultFor($this->company->id, DocumentType::Invoice);

        $this->assertSame('ACME-2027', $gasita->prefix);
    }

    public function test_default_for_nu_amesteca_tipurile_de_document(): void
    {
        $this->series(['prefix' => 'ACME-P', 'document_type' => DocumentType::Proforma]);

        $this->assertNull($this->service->defaultFor($this->company->id, DocumentType::Invoice));
    }

    public function test_default_for_intoarce_null_cand_nu_exista_serie(): void
    {
        $this->assertNull($this->service->defaultFor($this->company->id, DocumentType::Receipt));
    }
}
