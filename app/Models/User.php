<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\AuditsCompany;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<UserFactory> */
    // our transformAudit() replaces the package no-op, stamping company_id
    use HasFactory, HasApiTokens, Notifiable, \OwenIt\Auditing\Auditable, AuditsCompany {
        AuditsCompany::transformAudit insteadof \OwenIt\Auditing\Auditable;
    }

    /**
     * A user belongs to several companies through company_user, so the row has
     * no company of its own. The audit is filed under the company the actor
     * was working in, which is the one change that is visible to them.
     *
     * Known limit: role is a single global column, so a role change made while
     * company A is active stays out of company B's log even though it applies
     * there too.
     */
    protected function auditCompanyId(): ?int
    {
        $companyId = session('active_company_id');

        return $companyId !== null ? (int) $companyId : null;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * audit.strict is false, so $hidden alone does not keep these out of the
     * audits table. Without this list every password change copies the hash
     * into a second, longer lived table.
     *
     * @var list<string>
     */
    protected $auditExclude = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withTimestamps();
    }

     /**
     * Verificări rapide de rol, utile în controllere/policies (RBAC — NFR-1).
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isContabil(): bool
    {
        return $this->role === 'contabil';
    }

    /**
     * Numele rutei către dashboard-ul corespunzător rolului.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'administrator' => 'dashboard.administrator',
            'contabil'      => 'dashboard.contabil',
            'operator'      => 'dashboard.operator',
            default         => 'login',
        };
    }

}
