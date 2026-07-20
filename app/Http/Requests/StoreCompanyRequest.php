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

    public function messages(): array
    {
        return [
            'name.required' => 'Numele firmei este obligatoriu.',
            'name.string' => 'Numele firmei trebuie să fie un text.',
            'name.max' => 'Numele firmei nu poate avea mai mult de 255 de caractere.',

            'juridical_form.required' => 'Forma juridică este obligatorie.',
            'juridical_form.in' => 'Forma juridică selectată nu este validă.',

            'cui.required' => 'CUI-ul este obligatoriu.',
            'cui.string' => 'CUI-ul trebuie să fie un text.',
            'cui.max' => 'CUI-ul nu poate avea mai mult de 12 caractere.',
            'cui.unique' => 'CUI-ul este deja folosit de o altă firmă.',

            'trade_registry_number.required' =>
                'Numărul de înregistrare la Registrul Comerțului este obligatoriu.',

            'trade_registry_number.string' =>
                'Numărul de înregistrare trebuie să fie un text.',

            'trade_registry_number.max' =>
                'Numărul de înregistrare nu poate avea mai mult de 20 de caractere.',

            'trade_registry_number.unique' =>
                'Numărul de înregistrare este deja folosit de o altă firmă.',

            'county.required' => 'Județul este obligatoriu.',
            'county.string' => 'Județul trebuie să fie un text.',
            'county.max' => 'Județul nu poate avea mai mult de 255 de caractere.',

            'city.required' => 'Orașul este obligatoriu.',
            'city.string' => 'Orașul trebuie să fie un text.',
            'city.max' => 'Orașul nu poate avea mai mult de 255 de caractere.',

            'address.required' => 'Adresa este obligatorie.',
            'address.string' => 'Adresa trebuie să fie un text.',
            'address.max' => 'Adresa nu poate avea mai mult de 255 de caractere.',

            'social_capital.required' => 'Capitalul social este obligatoriu.',
            'social_capital.numeric' => 'Capitalul social trebuie să fie un număr.',
            'social_capital.decimal' =>
                'Capitalul social poate avea maximum două zecimale.',
            'social_capital.min' =>
                'Capitalul social nu poate fi mai mic decât zero.',
            'social_capital.max' =>
                'Capitalul social depășește valoarea maximă permisă.',

            'vat_payer.required' => 'Statutul TVA este obligatoriu.',
            'vat_payer.boolean' => 'Valoarea selectată pentru TVA nu este validă.',

            'iban.array' => 'Lista conturilor bancare nu este validă.',
            'iban.max' => 'Nu poți adăuga mai mult de 10 conturi bancare.',

            'iban.*.required_with' =>
                'IBAN-ul este obligatoriu atunci când numele băncii este completat.',

            'iban.*.string' => 'IBAN-ul trebuie să fie un text.',
            'iban.*.distinct' => 'Același IBAN a fost introdus de mai multe ori.',
            'iban.*.unique' => 'Acest IBAN este deja folosit.',

            'bank_name.array' => 'Lista băncilor nu este validă.',
            'bank_name.max' => 'Nu poți adăuga mai mult de 10 bănci.',

            'bank_name.*.required_with' =>
                'Numele băncii este obligatoriu atunci când IBAN-ul este completat.',

            'bank_name.*.string' => 'Numele băncii trebuie să fie un text.',

            'bank_name.*.max' =>
                'Numele băncii nu poate avea mai mult de 255 de caractere.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'numele firmei',
            'juridical_form' => 'forma juridică',
            'cui' => 'CUI',
            'trade_registry_number' => 'numărul de înregistrare',
            'county' => 'județul',
            'city' => 'orașul',
            'address' => 'adresa',
            'social_capital' => 'capitalul social',
            'vat_payer' => 'statutul TVA',
            'iban.*' => 'IBAN',
            'bank_name.*' => 'numele băncii',
        ];
    }
}
