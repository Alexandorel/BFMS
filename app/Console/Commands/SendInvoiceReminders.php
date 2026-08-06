<?php

namespace App\Console\Commands;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoices:send-reminders';
    protected $description = 'Trimite reminder-uri pentru facturi la scadență și restante';

    public function handle(): void
    {
        $activeStatuses = ['issued', 'partially_paid'];

        // 1. Reminder cu 3 zile înainte de scadență
        $this->processReminders(
            Invoice::whereIn('status', $activeStatuses)
                ->whereDate('due_date', now()->addDays(3)->toDateString())
                ->get(),
            'reminder_before_due'
        );

        // 2. Reminder în ziua scadenței
        $this->processReminders(
            Invoice::whereIn('status', $activeStatuses)
                ->whereDate('due_date', now()->toDateString())
                ->get(),
            'reminder_due'
        );

        // 3. Restant (folosim 'overdue_1' din ENUM)
        $this->processReminders(
            Invoice::whereIn('status', $activeStatuses)
                ->whereDate('due_date', '<', now()->toDateString())
                ->get(),
            'overdue_1'
        );

        $this->info('Reminder-uri procesate cu succes.');
    }

    protected function processReminders($invoices, string $type): void
    {
        foreach ($invoices as $invoice) {
            $invoice->loadMissing(['lines', 'client', 'company']);

            if (!$invoice->client || !$invoice->client->email) {
                continue;
            }

            $alreadySent = InvoiceNotification::where('invoice_id', $invoice->id)
                ->where('type', $type)
                ->where('status', 'sent')
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $email = $invoice->client->email;

            try {
                Mail::to($email)->send(new InvoiceMail($invoice, $type));

                InvoiceNotification::create([
                    'invoice_id' => $invoice->id,
                    'type'       => $type,
                    'sent_to'    => $email,
                    'status'     => 'sent',
                    'sent_at'    => now(),
                ]);

            } catch (Throwable $e) {
                Log::error("Eroare trimitere reminder (ID Factura: {$invoice->id}): " . $e->getMessage());

                InvoiceNotification::create([
                    'invoice_id'    => $invoice->id,
                    'type'          => $type,
                    'sent_to'       => $email,
                    'status'        => 'failed',
                    'error_message' => substr($e->getMessage(), 0, 255),
                ]);
            }
        }
    }
}
