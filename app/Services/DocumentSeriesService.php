<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\DocumentSeries;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DocumentSeriesService
{
    /**
     * Seria pe care o folosește firma pentru un tip de document.
     * Preferă seria marcată ca implicită; altfel prima serie activă.
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
     * Alocă următorul număr din serie și avansează contorul.
     *
     * Trebuie apelată din interiorul unei tranzacții, altfel lock-ul pe rândul
     * seriei se eliberează imediat și doi utilizatori pot primi același număr.
     */
    public function allocateNumber(DocumentSeries $series): int
    {
        if (DB::transactionLevel() === 0) {
            throw new RuntimeException(
                'allocateNumber() trebuie apelată în interiorul unei tranzacții.'
            );
        }

        // Recitim seria sub lock: instanța primită poate fi învechită.
        $locked = DocumentSeries::whereKey($series->getKey())->lockForUpdate()->firstOrFail();

        if (! $locked->is_active) {
            throw new RuntimeException(
                "Seria {$locked->prefix} este inactivă și nu mai poate emite documente."
            );
        }

        // O serie nefolosită pornește de la start_number, nu de la 1.
        $next = $locked->current_number < $locked->start_number
            ? $locked->start_number
            : $locked->current_number + 1;

        $locked->update(['current_number' => $next]);

        $series->refresh();

        return $next;
    }
}
