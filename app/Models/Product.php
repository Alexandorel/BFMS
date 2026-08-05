<?php

namespace App\Models;

use App\Concerns\AuditsCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    // our transformAudit() replaces the package no-op, stamping company_id
    use HasFactory, \OwenIt\Auditing\Auditable, AuditsCompany {
        AuditsCompany::transformAudit insteadof \OwenIt\Auditing\Auditable;
    }

    protected $fillable = [
        'company_id',
        'name',
        'sku',
        'unit_measure',
        'unit_price',
        'quantity',
        'vat_rate',
        'is_vat_exempt',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'is_vat_exempt' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoiceLines(): HasMany
    {
        return $this->hasMany(InvoiceLines::class);
    }
}
