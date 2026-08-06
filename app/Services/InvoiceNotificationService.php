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
        $this->send(
            $payment->invoice,
            'payment_confirmation',
            new PaymentConfirmationMail($payment),
            $payment->id,
        );
    }

    protected function alreadySent(Invoice $invoice, string $type): bool
    {
        return InvoiceNotification::where('invoice_id', $invoice->id)
            ->where('type', $type)
            ->where('status', 'sent')
            ->exists();
    }

    protected function send(Invoice $invoice, string $type, $mailable, ?int $paymentId = null): void
    {
        $email = $invoice->client?->email;

        // Emailul clientului este opțional. Lipsa lui nu trebuie să anuleze
        // operațiunea financiară ce a declanșat notificarea (de exemplu plata).
        if (blank($email)) {
            InvoiceNotification::create([
                'invoice_id' => $invoice->id,
                'payment_id' => $paymentId,
                'type' => $type,
                'sent_to' => 'fara-email@client.com',
                'status' => 'failed',
                'error_message' => 'Clientul nu are adresă de email definită.',
            ]);

            return;
        }

        $notification = InvoiceNotification::create([
            'invoice_id' => $invoice->id,
            'payment_id' => $paymentId,
            'type' => $type,
            'sent_to' => $email,
            'status' => 'pending',
        ]);

        try {
            Mail::to($email)->queue($mailable);
            $notification->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            $notification->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
