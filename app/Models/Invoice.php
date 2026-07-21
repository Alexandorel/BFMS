<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Câmpurile care pot fi completate în masă (Mass Assignment).
     * Punem aici tot ce vine din formular sau din logica de salvare.
     */
    protected $fillable = [
        'company_id',
        'client_id',
        'document_series_id',
        'document_type',
        'series',
        'number',
        'status',
        'issue_date',
        'due_date',
        'currency',
        'exchange_rate',
        'subtotal',
        'vat_total',
        'total',
        'credited_invoice_id',
        'created_by',
    ];

    /**
     * Cast-uri pentru tipuri de date specifice.
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'exchange_rate' => 'decimal:4',
            'subtotal' => 'decimal:2',
            'vat_total' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Relația cu Compania emitentă.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relația cu Clientul.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relația cu Seria de document folosită.
     */
    public function documentSeries(): BelongsTo
    {
        return $this->belongsTo(DocumentSeries::class, 'document_series_id');
    }

    /**
     * Relația cu Utilizatorul care a creat factura.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relația cu Factura originală (dacă aceasta este o stornare).
     */
    public function creditedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'credited_invoice_id');
    }

    /**
     * Relația cu Liniile facturii (produsele de pe ea).
     */
    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLines::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
