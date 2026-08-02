<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit as BaseAudit;

class Audit extends BaseAudit
{
    /**
     * Audits store the model FQCN, the screen shows Romanian. Kept here and
     * not in the blade so the log screen, the tests and any future export
     * read the same labels.
     */
    public const ENTITY_LABELS = [
        Invoice::class        => 'Factură',
        InvoiceLines::class   => 'Linie factură',
        Client::class         => 'Client',
        Product::class        => 'Produs',
        Payment::class        => 'Încasare',
        Company::class        => 'Firmă',
        DocumentSeries::class => 'Serie documente',
        User::class           => 'Utilizator',
    ];

    public const EVENT_LABELS = [
        'created'  => 'Creare',
        'updated'  => 'Modificare',
        'deleted'  => 'Ștergere',
        'restored' => 'Restaurare',
    ];

    /**
     * Only the columns whose humanized name would be wrong or unclear in
     * Romanian. Anything missing falls back to Str::headline().
     */
    public const FIELD_LABELS = [
        'name'                   => 'Denumire',
        'status'                 => 'Stare',
        'currency'               => 'Monedă',
        'exchange_rate'          => 'Curs valutar',
        'issue_date'             => 'Dată emitere',
        'due_date'               => 'Dată scadență',
        'series'                 => 'Serie',
        'number'                 => 'Număr',
        'document_type'          => 'Tip document',
        'subtotal'               => 'Subtotal',
        'vat_total'              => 'Total TVA',
        'total'                  => 'Total',
        'quantity'               => 'Cantitate',
        'unit_price'             => 'Preț unitar',
        'unit_measure'           => 'Unitate de măsură',
        'vat_rate'               => 'Cotă TVA',
        'is_vat_exempt'          => 'Scutit de TVA',
        'line_subtotal'          => 'Subtotal linie',
        'line_vat'               => 'TVA linie',
        'line_total'             => 'Total linie',
        'payment_date'           => 'Dată încasare',
        'amount'                 => 'Sumă',
        'payment_method'         => 'Metodă de plată',
        'reference'              => 'Referință',
        'client_type'            => 'Tip client',
        'address'                => 'Adresă',
        'delivery_address'       => 'Adresă livrare',
        'phone'                  => 'Telefon',
        'city'                   => 'Localitate',
        'county'                 => 'Județ',
        'trade_registry_number'  => 'Nr. registrul comerțului',
        'vat_number'             => 'Cod TVA',
        'prefix'                 => 'Prefix',
        'start_number'           => 'Număr start',
        'current_number'         => 'Număr curent',
        'is_default'             => 'Implicită',
        'is_active'              => 'Activă',
        'role'                   => 'Rol',
        'first_name'             => 'Prenume',
        'last_name'              => 'Nume',
        'juridical_form'         => 'Formă juridică',
        'social_capital'         => 'Capital social',
        'vat_payer'              => 'Plătitor de TVA',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Every read of the audit log goes through here, so no query can
     * accidentally leak rows from another company. (NFR-1, F-101)
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Filters coming from the audit log screen. Empty values are ignored.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when($filters['event'] ?? null, fn ($q, $v) => $q->where('event', $v))
            ->when($filters['auditable_type'] ?? null, fn ($q, $v) => $q->where('auditable_type', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
    }
}
