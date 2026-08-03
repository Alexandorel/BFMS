<?php

namespace App\Models;

use App\Enums\EmailTemplateType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable =[
        'company_id',
        'type',
        'subject',
        'body',
    ];
    protected $casts = [
        'type' => EmailTemplateType::class,
    ];
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
