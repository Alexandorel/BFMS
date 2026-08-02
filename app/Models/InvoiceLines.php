<?php

namespace App\Models;

use App\Concerns\AuditsCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class InvoiceLines extends Model implements Auditable
{
    // our transformAudit() replaces the package no-op, stamping company_id
    use HasFactory, \OwenIt\Auditing\Auditable, AuditsCompany {
        AuditsCompany::transformAudit insteadof \OwenIt\Auditing\Auditable;
    }

    /**
     * A line has no company of its own, it inherits the invoice scope.
     */
    protected function auditCompanyId(): ?int
    {
        return $this->invoice?->company_id;
    }

    protected $table = 'invoice_lines';

    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name_snapshot',
        'sku_snapshot',
        'unit_measure_snapshot',
        'unit_price_snapshot',
        'vat_rate_snapshot',
        'quantity',
        'line_subtotal',
        'line_vat',
        'line_total',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_snapshot' => 'decimal:2',
            'vat_rate_snapshot' => 'decimal:2',
            'line_subtotal' => 'decimal:2',
            'line_vat' => 'decimal:2',
            'line_total' => 'decimal:2',
            'position' => 'integer',
            'quantity' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
