<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\InvoiceNotificationService;

class PaymentObserver
{
    public function __construct(protected InvoiceNotificationService $notifier) {}

    public function created(Payment $payment): void
    {
        $invoice = $payment->invoice;

        $totalPaid = $invoice->payments()
            ->get()
            ->sum(fn ($p) => $p->amount * $p->exchange_rate / $invoice->exchange_rate);

        $invoice->update([
            'status' => $totalPaid >= $invoice->total ? 'fully_paid' : 'partially_paid',
        ]);

        $this->notifier->sendPaymentConfirmation($payment);
    }
}
