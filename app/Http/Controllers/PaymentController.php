<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PaymentController extends Controller
{
    /**
     * Inregistrarea unei incasari. Validarea din StorePaymentRequest a rulat
     * deja, dar verdictul final il da service-ul, sub lock pe factura.
     */
    public function store(
        StorePaymentRequest $request,
        Invoice $invoice,
        PaymentService $paymentService
    ): RedirectResponse {
        try {
            $paymentService->record($invoice, $request->validated(), $request->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Plata a fost înregistrată.');
    }

    /**
     * Stergerea unei incasari inregistrate gresit.
     *
     * NFR-1: operatorul gestioneaza plati, dar nu sterge date - actiunea e
     * rezervata administratorului.
     */
    public function destroy(
        Request $request,
        Payment $payment,
        PaymentService $paymentService
    ): RedirectResponse {
        abort_unless($request->user()?->role === 'administrator', 403);

        abort_unless(
            $request->user()
                ->companies()
                ->whereKey($payment->company_id)
                ->exists(),
            403
        );

        $invoiceId = $payment->invoice_id;

        try {
            $paymentService->remove($payment);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('invoices.show', $invoiceId)
            ->with('success', 'Plata a fost ștearsă.');
    }
}
