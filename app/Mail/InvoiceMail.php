<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public string $type = 'issued',
        public string $pdfTheme = 'modern' // Redenumit din $theme în $pdfTheme
    ) {
        $this->invoice->loadMissing(['client', 'company']);
    }

    public function envelope(): Envelope
    {
        $documentNumber = $this->invoice->number
            ? $this->invoice->series . '-' . $this->invoice->number
            : 'ciorna-' . $this->invoice->id;

        $subject = match ($this->type) {
            'reminder_before_due' => "Reamintire: Factura {$documentNumber} scadență în curând",
            'reminder_due'        => "Factura {$documentNumber} este scadentă astăzi",
            'overdue_1',
            'overdue_2'           => "NOTIFICARE: Factura {$documentNumber} este restantă",
            default               => "Factura {$documentNumber}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $view = $this->type === 'issued'
            ? 'emails.invoice'
            : 'emails.invoice-reminder';

        return new Content(
            markdown: $view,
            with: [
                'invoice' => $this->invoice,
                'client'  => $this->invoice->client,
                'type'    => $this->type,
            ],
        );
    }

    public function attachments(): array
    {
        $this->invoice->loadMissing([
            'client',
            'company.bankAccounts',
            'lines' => fn ($query) => $query->orderBy('position'),
        ]);

        // Folosim $this->pdfTheme aici:
        $pdfContent = Pdf::loadView('invoices.pdf.' . $this->pdfTheme, [
            'invoice' => $this->invoice,
        ])
        ->setPaper('a4')
        ->setOption('isPhpEnabled', true)
        ->output();

        $documentNumber = $this->invoice->number
            ? $this->invoice->series . '-' . $this->invoice->number
            : 'ciorna-' . $this->invoice->id;

        return [
            Attachment::fromData(fn () => $pdfContent, "factura-{$documentNumber}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
