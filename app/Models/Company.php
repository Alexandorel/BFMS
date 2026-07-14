<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'denumire',
        'forma_juridica',
        'cui',
        'nr_reg_com',
        'judet',
        'localitate',
        'adresa',
        'capital_social',
        'platitor_tva',
    ];

    protected function casts(): array
    {
        return [
            'platitor_tva' => 'boolean',
            'capital_social' => 'decimal:2',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
