<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct( 
        public Invoice $invoice,
        public string $subjectLine,
        public string $htmlBody
        ){}
    
    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('email.generic-template')
            ->with([
                'htmlBody' => $this->htmlBody,
            ]);
    }

}
