<?php

namespace App\Observers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Services\InvoiceNotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class InvoiceObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(
        private readonly InvoiceNotificationService $notifications,
    ) {}

    public function created(Invoice $invoice): void
    {
        if ($invoice->status === InvoiceStatus::Issued) {
            $this->notify($invoice);
        }
    }

    public function updated(Invoice $invoice): void
    {
        if ($invoice->wasChanged('status') && $invoice->status === InvoiceStatus::Issued) {
            $this->notify($invoice);
        }
    }

    private function notify(Invoice $invoice): void
    {
        $freshInvoice = $invoice->fresh(['client', 'company']);

        if ($freshInvoice !== null) {
            $this->notifications->sendInvoice($freshInvoice);
        }
    }
}
