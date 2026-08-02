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
    ) {
    }

    /**
     * register a encashment
     * all reviews after block the invocie row
     */
    public function record(Invoice $invoice, array $data, User $user): Payment
    {
        return DB::transaction(function () use ($invoice, $data, $user) {
            $locked = $this->lockInvoice($invoice->getKey());

            if (! $locked->status->acceptsPayments()) {
                throw new RuntimeException(
                    'Documentul este '.mb_strtolower($locked->status->label())
                    .' si nu mai poate primi plati.'
                );
            }

            $currency = $data['currency'] ?? $locked->currency;
            
            // Make sure is the same currency
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

            // Recontrolam soldul AICI, sub lock: validarea din FormRequest a
            // rulat inainte de blocare si nu mai e o garantie. (NFR-2, F-402)
            $balance = $locked->balance();

            if ($amount > $balance) {
                throw new RuntimeException(sprintf(
                    'Suma depaseste restul de plata (%s %s).',
                    number_format($balance, 2, ',', '.'),
                    $locked->currency
                ));
            }

            // din formular metoda vine ca string, dintr-un apel intern ca enum
            $method = $data['payment_method'] instanceof PaymentMethod
                ? $data['payment_method']
                : PaymentMethod::from($data['payment_method']);

            // Numarul se aloca in aceeasi tranzactie cu plata: daca inserarea
            // esueaza, numarul nu ramane ars si seria nu capata goluri. (F-103)
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

            // apelantul primeste factura cu noua stare, nu cu cea de dinainte
            $invoice->refresh();

            return $payment;
        });
    }

    /**
     * Sterge o incasare inregistrata gresit si recalculeaza starea facturii.
     *
     * Statusul poate cobori inapoi (fully_paid -> partially_paid -> issued):
     * starea de incasare e derivata din sume, nu o tranzitie de business.
     *
     * O plata cu chitanta emisa NU se sterge: numarul e deja alocat si
     * documentul a fost predat clientului, deci stergerea ar lasa un gol in
     * seria de chitante. (F-103)
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
     * Blocheaza randul facturii pana la finalul tranzactiei, ca doi operatori
     * care incaseaza simultan aceeasi factura sa se serializeze. (NFR-2)
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
     * Aloca un numar din seria de chitante a firmei (F-401).
     *
     * Se apeleaza doar din interiorul tranzactiei lui record(), pentru ca
     * allocateNumber() incrementeaza current_number: daca plata ar esua dupa
     * alocare, numarul ar ramane ars si seria ar capata un gol. (F-103)
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
     * Starea de incasare e DERIVATA, niciodata setata manual: o recalculam din
     * suma platilor ori de cate ori se adauga sau se sterge una.
     *
     * Metoda e privata intentionat - e singurul loc care are voie sa scrie
     * status-ul de plata, deci nimeni nu poate marca din exterior o factura
     * drept incasata fara sa existe platile in spate.
     */
    private function syncStatus(Invoice $invoice): void
    {
        $derivable = [
            InvoiceStatus::Issued,
            InvoiceStatus::PartiallyPaid,
            InvoiceStatus::FullyPaid,
        ];

        // o ciorna, una anulata sau una stornata nu-si schimba starea din plati
        if (! in_array($invoice->status, $derivable, true)) {
            return;
        }

        // fortam recitirea din baza: colectia din memorie e de dinainte de insert
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
