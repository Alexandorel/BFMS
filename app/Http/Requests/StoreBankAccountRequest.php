<?php

namespace App\Http\Requests;

use App\Rules\RomanianIbanRule;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'administrator';
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
        return [
            'company_id' => [
                'required',
                'integer',
                Rule::exists('companies', 'id'),
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $this->user()?->companies()->whereKey($value)->exists()) {
                        $fail('Nu ai acces la firma selectată.');
                    }
                },
            ],
            'bank_name' => ['required', 'string', 'max:255'],
            'iban' => [
                'required',
                'string',
                new RomanianIbanRule(),
                Rule::unique('bank_accounts', 'iban'),
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
            'bank_name.max' => 'Numele băncii nu poate depăși 255 de caractere.',
            'iban.required' => 'IBAN-ul este obligatoriu.',
            'iban.unique' => 'Acest IBAN este deja înregistrat.',
            'currency.required' => 'Moneda este obligatorie.',
            'currency.in' => 'Moneda selectată nu este validă.',
        ];
    }
}
