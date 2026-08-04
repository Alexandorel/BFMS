<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        $invoice = $this->payment->invoice;

        return new Envelope(
            subject: "Am primit plata pentru factura {$invoice->series}{$invoice->number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-confirmation',
            with: [
                'payment' => $this->payment,
                'invoice' => $this->payment->invoice,
                'client' => $this->payment->invoice->client,
            ],
        );
    }
}
