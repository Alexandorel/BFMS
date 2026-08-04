<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceNotification extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_id',
        'type',
        'sent_at',
        'sent_to',
        'status',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
