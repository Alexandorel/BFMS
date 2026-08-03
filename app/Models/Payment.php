<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Concerns\AuditsCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends Model implements Auditable
{
    // our transformAudit() replaces the package no-op, stamping company_id
    use HasFactory, \OwenIt\Auditing\Auditable, AuditsCompany {
        AuditsCompany::transformAudit insteadof \OwenIt\Auditing\Auditable;
    }

    protected $fillable = [
        'invoice_id',
        'company_id',
        'payment_date',
        'amount',
        'currency',
        'exchange_rate',
        'payment_method',
        'reference',
        'receipt_series',
        'receipt_number',
        'created_by',
    ];

    /**
     * Number of receipt displayable form (ex: CHT-1001)
     */
    public function getReceiptLabelAttribute(): ?string
    {
        return $this->receipt_number
            ? $this->receipt_series.'-'.$this->receipt_number
            : null;
    }

    public function hasReceipt(): bool
    {
        return $this->receipt_number !== null;
    }

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'payment_method' => PaymentMethod::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
