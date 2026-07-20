<?php

namespace App\Http\Requests;

use App\Rules\RomanianCuiRule;
use App\Rules\RomanianIbanRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    //ce user are dreptul sa adauge companie
    public function authorize(): bool
    {
        return in_array(
            $this->user()?->role,
            ['administrator', 'superadmin'],
            true
        );
    }

    //pregatire inputuri
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'juridical_form' => strtoupper(
                trim((string) $this->input('juridical_form'))
            ),
            'cui' => strtoupper(
                preg_replace('/\s+/', '', (string) $this->input('cui'))
            ),
            'trade_registry_number' => strtoupper(
                trim((string) $this->input('trade_registry_number'))
            ),
            'county' => trim((string) $this->input('county')),
            'city' => trim((string) $this->input('city')),
            'address' => trim((string) $this->input('address')),
            'vat_payer' => $this->boolean('vat_payer'),

            'iban' => collect($this->input('iban', []))
                ->map(fn ($iban) => strtoupper(
                    preg_replace('/\s+/', '', (string) $iban)
                ))
                ->all(),

            'bank_name' => collect($this->input('bank_name', []))
                ->map(fn ($name) => trim((string) $name))
                ->all(),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'juridical_form' => [
                'required',
                Rule::in(['SRL', 'SA', 'PFA', 'II', 'IF', 'SRL-D']),
            ],

            'cui' => [
                'required',
                'string',
                'max:12',
                new RomanianCuiRule(),
                Rule::unique('companies', 'cui'),
            ],

            'trade_registry_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('companies', 'trade_registry_number'),
            ],

            'county' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],

            'social_capital' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:9999999999999.99',
            ],

            'vat_payer' => ['required', 'boolean'],

            'iban' => ['nullable', 'array', 'max:10'],
            'iban.*' => [
                'nullable',
                'required_with:bank_name.*',
                'string',
                'distinct',
                new RomanianIbanRule(),
                Rule::unique('bank_accounts', 'iban'),
            ],

            'bank_name' => ['nullable', 'array', 'max:10'],
            'bank_name.*' => [
                'nullable',
                'required_with:iban.*',
                'string',
                'max:255',
            ],
        ];
    }
}
