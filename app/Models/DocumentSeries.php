<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class DocumentSeries extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'document_series';

    protected $fillable = [
        'company_id',
        'document_type',
        'prefix',
        'start_number',
        'current_number',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Numarul pe care seria il va emite la urmatoarea alocare.
     * O serie nefolosita porneste de la start_number, nu de la start_number + 1.
     */
    public function getNextNumberAttribute(): int
    {
        return $this->current_number < $this->start_number
            ? $this->start_number
            : $this->current_number + 1;
    }

    /**
     * O serie cu documente emise nu-si mai poate schimba prefixul
     * sau numarul de pornire.
     */
    public function getIsUsedAttribute(): bool
    {
        return $this->current_number > 0;
    }
}
