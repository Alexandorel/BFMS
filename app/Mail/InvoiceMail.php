<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public string $type = 'issued' // 'issued', 'reminder_before_due', 'reminder_due', 'reminder_overdue'
    ) {
        // Încarcă articolele, clientul și compania
        $this->invoice->loadMissing(['lines', 'client', 'company']);
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->type) {
            'reminder_before_due' => "Reamintire: Factura {$this->invoice->series}{$this->invoice->number} scadență în curând",
            'reminder_due'        => "Factura {$this->invoice->series}{$this->invoice->number} este scadentă astăzi",
            'reminder_overdue'    => "NOTIFICARE: Factura {$this->invoice->series}{$this->invoice->number} este restantă",
            default               => "Factura {$this->invoice->series}{$this->invoice->number}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        // Selectează șablonul Blade potrivit
        $view = $this->type === 'issued'
            ? 'emails.invoice'
            : 'emails.invoice_reminder';

        return new Content(
            markdown: $view,
            with: [
                'client' => $this->invoice->client,
                'type'   => $this->type,
            ],
        );
    }

    public function attachments(): array
    {
        // Niciun PDF atașat
        return [];
    }
}
