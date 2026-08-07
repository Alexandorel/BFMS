<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function __construct(
        private DocumentSeriesService $seriesService
    ) {}

    /**
     * Records a payment and updates the invoice status.
     *
     * Every check runs after the invoice row is locked: a balance read before
     * the lock may already be stale. (NFR-2)
     */
    public function record(Invoice $invoice, array $data, User $user): Payment
    {
        return DB::transaction(function () use ($invoice, $data, $user) {
            $locked = $this->lockInvoice($invoice->getKey());

            if (! $locked->canAcceptPayments()) {
                if ($locked->isCreditNote()) {
                    throw new RuntimeException(
                        'O factura de storno nu poate primi incasari. '
                        .'Foloseste un flux de restituire sau compensare.'
                    );
                }

                throw new RuntimeException(
                    'Documentul este '.mb_strtolower($locked->status->label())
                    .' si nu mai poate primi plati.'
                );
            }

            $currency = $data['currency'] ?? $locked->currency;

            // A payment in another currency would be subtracted from the total
            // as if it matched, silently corrupting the balance.
            if ($currency !== $locked->currency) {
                throw new RuntimeException(
                    "Plata trebuie inregistrata in moneda facturii ({$locked->currency})."
                );
            }

            $amount = round((float) $data['amount'], 2);

            if ($amount <= 0) {
                throw new RuntimeException(
                    'Suma incasata trebuie sa fie mai mare decat zero.'
                );
            }

            // Re-check the balance HERE, under the lock: the FormRequest ran
            // before the lock, so it is no longer a guarantee. (NFR-2, F-402)
            $balance = $locked->balance();

            if ($amount > $balance) {
                throw new RuntimeException(sprintf(
                    'Suma depaseste restul de plata (%s %s).',
                    number_format($balance, 2, ',', '.'),
                    $locked->currency
                ));
            }

            // the form sends a string, internal callers may pass the enum
            $method = $data['payment_method'] instanceof PaymentMethod
                ? $data['payment_method']
                : PaymentMethod::from($data['payment_method']);

            // Allocated inside the payment transaction: if the insert fails,
            // the number is not burned and the series keeps no gaps. (F-103)
            $receipt = ! empty($data['issue_receipt'])
                ? $this->allocateReceiptNumber($locked->company_id, $method)
                : ['series' => null, 'number' => null];

            $payment = Payment::create([
                'invoice_id' => $locked->id,
                'company_id' => $locked->company_id,
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'currency' => $currency,
                'exchange_rate' => $data['exchange_rate'] ?? $locked->exchange_rate,
                'payment_method' => $method,
                'reference' => $data['reference'] ?? null,
                'receipt_series' => $receipt['series'],
                'receipt_number' => $receipt['number'],
                'created_by' => $user->id,
            ]);

            $this->syncStatus($locked);

            // hand the caller the refreshed status, not the pre-payment one
            $invoice->refresh();

            return $payment;
        });
    }

    /**
     * Deletes a wrongly recorded payment and recalculates the invoice status.
     *
     * The status may move back down (fully_paid -> partially_paid -> issued):
     * payment state is derived from sums, not a business transition.
     *
     * A payment with an issued receipt is never deleted: the number is already
     * allocated and the document went to the client, so removing it would
     * leave a gap in the receipt series. (F-103)
     */
    public function remove(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $locked = $this->lockInvoice($payment->invoice_id);

            if ($payment->hasReceipt()) {
                throw new RuntimeException(sprintf(
                    'Plata are chitanta %s emisa si nu mai poate fi stearsa. '
                    .'Corectia se face prin stornarea facturii.',
                    $payment->receipt_label
                ));
            }

            $payment->delete();

            $this->syncStatus($locked);
        });
    }

    /**
     * Locks the invoice row until the transaction ends, so two operators
     * recording a payment at the same time are serialized. (NFR-2)
     */
    private function lockInvoice(int $invoiceId): Invoice
    {
        if (DB::transactionLevel() === 0) {
            throw new RuntimeException(
                'lockInvoice() trebuie apelata in interiorul unei tranzactii.'
            );
        }

        return Invoice::whereKey($invoiceId)->lockForUpdate()->firstOrFail();
    }

    /**
     * Allocates a number from the company receipt series. (F-401)
     *
     * Called only inside record()'s transaction: allocateNumber() bumps
     * current_number, so a payment failing afterwards would burn the number
     * and leave a gap in the series. (F-103)
     *
     * @return array{series: ?string, number: ?int}
     */
    private function allocateReceiptNumber(int $companyId, PaymentMethod $method): array
    {
        if (! $method->canIssueReceipt()) {
            throw new RuntimeException(
                'Chitanta se poate emite doar pentru incasarile in numerar.'
            );
        }

        $series = $this->seriesService->defaultFor($companyId, DocumentType::Receipt);

        if (! $series) {
            throw new RuntimeException(
                'Firma nu are o serie de chitante activa. '
                .'Configureaz-o din Setari > Serii documente.'
            );
        }

        return [
            'series' => $series->prefix,
            'number' => $this->seriesService->allocateNumber($series),
        ];
    }

    /**
     * Payment state is DERIVED, never set by hand: recalculated from the sum
     * of payments whenever one is added or removed.
     *
     * Private on purpose - the only place allowed to write the payment status,
     * so nothing outside can mark an invoice paid without payments behind it.
     */
    private function syncStatus(Invoice $invoice): void
    {
        $derivable = [
            InvoiceStatus::Issued,
            InvoiceStatus::PartiallyPaid,
            InvoiceStatus::FullyPaid,
        ];

        // a draft, cancelled or credited invoice never changes state from payments
        if (! in_array($invoice->status, $derivable, true)) {
            return;
        }

        // force a re-read: the in-memory collection predates the insert
        $invoice->unsetRelation('payments');

        $status = match (true) {
            $invoice->paidAmount() <= 0 => InvoiceStatus::Issued,
            $invoice->isFullyPaid() => InvoiceStatus::FullyPaid,
            default => InvoiceStatus::PartiallyPaid,
        };

        if ($invoice->status !== $status) {
            $invoice->update(['status' => $status]);
        }
    }
}
