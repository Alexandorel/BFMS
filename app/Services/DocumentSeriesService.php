<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\DocumentSeries;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DocumentSeriesService
{
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

        // unused serie starts from current_number
        $next = $locked->current_number < $locked->start_number
            ? $locked->start_number
            : $locked->current_number + 1;

        $locked->update(['current_number' => $next]);

        $series->refresh();

        return $next;
    }
}
