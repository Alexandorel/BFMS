<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\InvoiceNotification;
use App\Enums\InvoiceStatus;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class InvoiceObserver
{
    /**
     * Când creezi o factură NOUĂ direct cu statusul 'emisa'
     */
    public function created(Invoice $invoice): void
    {
        $statusValue = $invoice->status instanceof InvoiceStatus ? $invoice->status->value : $invoice->status;

        if ($statusValue === 'issued' || $statusValue === 'emisa') {
            // Așteptăm salvarea liniilor facturii (afterCommit)
            DB::afterCommit(function () use ($invoice) {
                $this->processNotification($invoice);
            });
        }
    }

    /**
     * Când editezi o factură EXISTENTĂ și statusul devine 'emisa'
     */
    public function updating(Invoice $invoice): void
    {
        $statusValue = $invoice->status instanceof InvoiceStatus ? $invoice->status->value : $invoice->status;

        if ($invoice->isDirty('status') && ($statusValue === 'issued' || $statusValue === 'emisa')) {
            $this->processNotification($invoice);
        }
    }

    private function processNotification(Invoice $invoice): void
    {
        // Reîncărcăm complet factura din DB împreună cu liniile salvate
        $invoice = $invoice->fresh(['lines', 'client', 'company']);

        if (!$invoice->client || !$invoice->client->email) {
            Log::warning("Observer: Factura ID {$invoice->id} nu are client cu email valid.");

            InvoiceNotification::create([
                'invoice_id'    => $invoice->id,
                'type'          => 'issued',
                'sent_to'       => $invoice->client->email ?? 'fara-email@client.com',
                'status'        => 'failed',
                'error_message' => 'Clientul nu are adresă de email definită.',
            ]);
            return;
        }

        $email = $invoice->client->email;

        try {
            Mail::to($email)->send(new InvoiceMail($invoice, 'issued'));

            InvoiceNotification::create([
                'invoice_id' => $invoice->id,
                'type'       => 'issued',
                'sent_at'    => now(),
                'sent_to'    => $email,
                'status'     => 'sent',
            ]);

            Log::info("Observer: Mail trimis cu succes pentru factura ID {$invoice->id}");

        } catch (Throwable $e) {
            Log::error("Observer: Eroare la trimiterea mail-ului: " . $e->getMessage());

            InvoiceNotification::create([
                'invoice_id'    => $invoice->id,
                'type'          => 'issued',
                'sent_to'       => $email,
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
