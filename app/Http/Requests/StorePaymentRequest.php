<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    /**
     * Contabilul are acces exclusiv de vizualizare (NFR-1), deci nu inregistreaza
     * incasari. Middleware-ul de pe grupul de rute il opreste deja - verificarea
     * de aici e al doilea strat, pentru cazul in care rutele se rearanjeaza.
     */
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');

        return in_array($this->user()?->role, ['administrator', 'operator'], true)
            && $invoice instanceof Invoice
            && $this->user()
                ->companies()
                ->whereKey($invoice->company_id)
                ->exists();
    }

    protected function prepareForValidation(): void
    {
        $invoice = $this->route('invoice');

        $this->merge([
            // moneda nu se alege din formular: e intotdeauna cea a facturii
            'currency' => $invoice instanceof Invoice ? $invoice->currency : null,
            'payment_date' => $this->input('payment_date') ?: now()->toDateString(),
            'reference' => trim((string) $this->input('reference')) ?: null,
        ]);
    }

    public function rules(): array
    {
        /** @var Invoice $invoice */
        $invoice = $this->route('invoice');

        return [
            'payment_date' => [
                'required',
                'date',
                'before_or_equal:'.now()->toDateString(),
                ...($invoice->issue_date
                    ? ['after_or_equal:'.$invoice->issue_date->toDateString()]
                    : []),
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                $this->amountFitsTheBalance($invoice),
            ],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'reference' => [
                Rule::requiredIf(
                    fn () => $this->input('payment_method') === PaymentMethod::BankTransfer->value
                ),
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * Oglindeste portile din PaymentService, cu aceleasi mesaje: utilizatorul
     * vede acelasi text indiferent daca eroarea vine din validare sau din
     * tranzactie. Garantia reala ramane in service, sub lock - aici e doar UX.
     */
    private function amountFitsTheBalance(Invoice $invoice): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($invoice): void {
            if (! $invoice->status->acceptsPayments()) {
                $fail(
                    'Documentul este '.mb_strtolower($invoice->status->label())
                    .' și nu mai poate primi plăți.'
                );

                return;
            }

            if (round((float) $value, 2) > $invoice->balance()) {
                $fail(sprintf(
                    'Suma depășește restul de plată (%s %s).',
                    number_format($invoice->balance(), 2, ',', '.'),
                    $invoice->currency
                ));
            }
        };
    }

    public function messages(): array
    {
        return [
            'payment_date.required' => 'Data plății este obligatorie.',
            'payment_date.date' => 'Data plății nu este validă.',
            'payment_date.before_or_equal' => 'Data plății nu poate fi în viitor.',
            'payment_date.after_or_equal' => 'Data plății nu poate fi anterioară emiterii facturii.',
            'amount.required' => 'Suma încasată este obligatorie.',
            'amount.numeric' => 'Suma încasată trebuie să fie un număr.',
            'amount.min' => 'Suma încasată trebuie să fie mai mare decât zero.',
            'payment_method.required' => 'Metoda de plată este obligatorie.',
            'payment_method.enum' => 'Metoda de plată selectată nu este validă.',
            'reference.required' => 'Pentru un ordin de plată, referința este obligatorie.',
            'reference.max' => 'Referința nu poate depăși 100 de caractere.',
        ];
    }
}
