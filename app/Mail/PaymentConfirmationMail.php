<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Address;

class PaymentConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        $company = $this->payment->invoice->company;
        $invoice = $this->payment->invoice;

        return new Envelope(
            from: new Address($company->email, $company->name),
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

