<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Company;
use App\Models\DocumentSeries;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DocumentSeriesService
{
    /**
     * Default series for a new company, one per document type.
     * Skips a type that already has a series, so it is safe to re-run.
     */
    public function ensureDefaultsFor(Company $company): void
    {
        foreach (DocumentType::cases() as $documentType) {
            $alreadyHasSeries = DocumentSeries::where('company_id', $company->id)
                ->where('document_type', $documentType)
                ->exists();

            if ($alreadyHasSeries) {
                continue;
            }

            DocumentSeries::create([
                'company_id' => $company->id,
                'document_type' => $documentType,
                'prefix' => $documentType->defaultPrefix(),
                'start_number' => 1,
                'current_number' => 0,
                'is_default' => true,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Marks a series as default and clears the previous one of the same type,
     * so defaultFor() always has a single unambiguous answer.
     */
    public function makeDefault(DocumentSeries $series): void
    {
        DB::transaction(function () use ($series) {
            //starea reala vine din baza de date, nu din instanta primita
            $locked = DocumentSeries::whereKey($series->getKey())->lockForUpdate()->firstOrFail();

            if (! $locked->is_active) {
                throw new RuntimeException(
                    "Seria {$locked->prefix} este inactivă și nu poate deveni serie implicită."
                );
            }

            DocumentSeries::where('company_id', $locked->company_id)
                ->where('document_type', $locked->document_type)
                ->whereKeyNot($locked->getKey())
                ->update(['is_default' => false]);

            $locked->update(['is_default' => true]);

            $series->refresh();
        });
    }

    /**
     * default serie for company
     */
    public function defaultFor(int $companyId, DocumentType $documentType): ?DocumentSeries
    {
        return DocumentSeries::where('company_id', $companyId)
            ->where('document_type', $documentType)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->first();
    }

    /**
     * transactionLevel() = 0 means you are not into a transaction.
     * lockforUpdate() blocks the row untill the transaction ends.
     */
    public function allocateNumber(DocumentSeries $series): int
    {
        if (DB::transactionLevel() === 0) {
            throw new RuntimeException(
                'allocateNumber() trebuie apelată în interiorul unei tranzacții.'
            );
        }

        $locked = DocumentSeries::whereKey($series->getKey())->lockForUpdate()->firstOrFail();

        if (! $locked->is_active) {
            throw new RuntimeException(
                "Seria {$locked->prefix} este inactivă și nu mai poate emite documente."
            );
        }

        $next = $locked->next_number;

        $locked->update(['current_number' => $next]);

        $series->refresh();

        return $next;
    }
}
