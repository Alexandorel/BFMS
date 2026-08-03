<?php

namespace App\Http\Requests;

use App\Models\BankAccount;
use App\Rules\RomanianIbanRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        $bankAccount = $this->route('bankAccount');

        return $this->user()?->role === 'administrator'
            && $bankAccount instanceof BankAccount
            && $this->user()
                ->companies()
                ->whereKey($bankAccount->company_id)
                ->exists();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'bank_name' => trim((string) $this->input('bank_name')),
            'iban' => strtoupper(
                preg_replace('/\s+/', '', (string) $this->input('iban'))
            ),
            'currency' => strtoupper(
                trim((string) $this->input('currency'))
            ),
        ]);
    }

    public function rules(): array
    {
        $bankAccount = $this->route('bankAccount');

        return [
            'bank_name' => ['required', 'string', 'max:255'],
            'iban' => [
                'required',
                'string',
                new RomanianIbanRule(),
                Rule::unique('bank_accounts', 'iban')
                    ->ignore($bankAccount->id),
            ],
            'currency' => [
                'required',
                Rule::in(['RON']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'bank_name.required' => 'Numele băncii este obligatoriu.',
            'iban.required' => 'IBAN-ul este obligatoriu.',
            'iban.unique' => 'Acest IBAN este deja înregistrat.',
            'currency.required' => 'Moneda este obligatorie.',
            'currency.in' => 'Moneda selectată nu este validă.',
        ];
    }
}
