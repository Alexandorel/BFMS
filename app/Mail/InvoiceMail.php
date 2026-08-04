<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Factura {$this->invoice->series}{$this->invoice->number} - {$this->invoice->company->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'client' => $this->invoice->client,
            ],
        );
    }

    public function attachments(): array
    {
        $pdfPath = storage_path("app/invoices/{$this->invoice->id}.pdf");

        if (! file_exists($pdfPath)) {
            return [];
        }

        return [
            Attachment::fromPath($pdfPath)
                ->as("Factura_{$this->invoice->series}{$this->invoice->number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
