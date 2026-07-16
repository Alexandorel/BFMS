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
        'invoice_id',
        'company_id',
        'payment_date',
        'amount',
        'currency',
        'exchange_rate',
        'payment_method',
        'reference',
        'created_by',
    ];

    /**
     * Cast-uri pentru tipuri de date specifice.
     */
    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
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
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
