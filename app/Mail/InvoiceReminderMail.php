<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public function __construct(
        public Invoice $invoice,
        public string $type,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'reminder_before_due' => "Factura {$this->invoice->series}{$this->invoice->number} - scadentă în curând",
            'reminder_due' => "Factura {$this->invoice->series}{$this->invoice->number} - scadentă astăzi",
            'overdue_1' => "Factura {$this->invoice->series}{$this->invoice->number} - restantă",
            'overdue_2' => "Factura {$this->invoice->series}{$this->invoice->number} - restantă, urgent",
        ];

        return new Envelope(subject: $subjects[$this->type] ?? 'Notificare factură');
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-reminder',
            with: [
                'invoice' => $this->invoice,
                'client' => $this->invoice->client,
                'type' => $this->type,
            ],
        );
    }
}
