<?php

namespace App\Models;

use App\Concerns\AuditsCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Company extends Model implements Auditable
{
    // our transformAudit() replaces the package no-op, stamping company_id
    use HasFactory, \OwenIt\Auditing\Auditable, AuditsCompany {
        AuditsCompany::transformAudit insteadof \OwenIt\Auditing\Auditable;
    }

    /**
     * The company is the scope itself, so its own key is the audit scope.
     */
    protected function auditCompanyId(): ?int
    {
        return $this->getKey();
    }

    protected $fillable = [
        'name',
        'juridical_form',
        'cui',
        'trade_registry_number',
        'county',
        'city',
        'address',
        'social_capital',
        'vat_payer',
    ];

    protected function casts(): array
    {
        return [
            'vat_payer' => 'boolean',
            'social_capital' => 'decimal:2',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withTimestamps();
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function documentSeries(): HasMany
    {
        return $this->hasMany(DocumentSeries::class);
    }
}
