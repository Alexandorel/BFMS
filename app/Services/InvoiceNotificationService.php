<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Mail\InvoiceReminderMail;
use App\Mail\PaymentConfirmationMail;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;

class InvoiceNotificationService
{
    public function sendInvoice(Invoice $invoice): void
    {
        $this->send($invoice, 'issued', new InvoiceMail($invoice));
    }

    public function sendReminder(Invoice $invoice, string $type): void
    {
        if ($this->alreadySent($invoice, $type)) {
            return;
        }

        $this->send($invoice, $type, new InvoiceReminderMail($invoice, $type));
    }

    public function sendPaymentConfirmation(Payment $payment): void
    {
        $email = $payment->invoice->client->email;

        InvoiceNotification::create([
            'invoice_id' => $payment->invoice_id,
            'payment_id' => $payment->id,
            'type' => 'payment_confirmation',
            'sent_to' => $email,
            'status' => 'pending',
        ]);

        Mail::to($email)->send(new PaymentConfirmationMail($payment));
    }

    protected function alreadySent(Invoice $invoice, string $type): bool
    {
        return InvoiceNotification::where('invoice_id', $invoice->id)
            ->where('type', $type)
            ->where('status', 'sent')
            ->exists();
    }

    protected function send(Invoice $invoice, string $type, $mailable): void
    {
        $email = $invoice->client->email;

        $notification = InvoiceNotification::create([
            'invoice_id' => $invoice->id,
            'type' => $type,
            'sent_to' => $email,
            'status' => 'pending',
        ]);

        try {
            Mail::to($email)->send($mailable); // sincron aici ca să prindem eroarea; vezi nota de mai jos
            $notification->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            $notification->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
