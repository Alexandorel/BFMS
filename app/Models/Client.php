<?php

namespace App\Models;

use App\Concerns\AuditsCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Client extends Model implements Auditable

{
    // our transformAudit() replaces the package no-op, stamping company_id
    use HasFactory, \OwenIt\Auditing\Auditable, AuditsCompany {
        AuditsCompany::transformAudit insteadof \OwenIt\Auditing\Auditable;
    }

    protected $fillable = [
        'company_id',
        'client_type',
        'name',
        'cui',
        'trade_registry_number',
        'vat_number',
        'first_name',
        'last_name',
        'cnp',
        'county',
        'city',
        'address',
        'delivery_address',
        'email',
        'phone',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function getFullNameAttribute(): string
    {
        if ($this->client_type === 'individual') {
            return trim($this->first_name . ' ' . $this->last_name);
        }

        return $this->name;
    }

    public function getTaxIdAttribute(): ?string
    {
        return $this->client_type === 'individual' ? $this->cnp : $this->cui;
    }
}
