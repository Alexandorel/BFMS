<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentSeries extends Model
{
    use HasFactory;

    protected $table = 'document_series';

    protected $fillable = [
        'company_id',
        'document_type',
        'prefix',
        'start_number',
        'current_number',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
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
}
