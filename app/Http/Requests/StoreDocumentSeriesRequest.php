<?php

namespace App\Http\Requests;

use App\Enums\DocumentType;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentSeriesRequest extends FormRequest
{
    //doar administratorul configureaza serii (NFR-1)
    public function authorize(): bool
    {
        return $this->user()?->role === 'administrator';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'prefix' => strtoupper(
                preg_replace('/\s+/', '', (string) $this->input('prefix'))
            ),
            'is_default' => $this->boolean('is_default'),
        ]);
    }

    public function rules(): array
    {
        return [
            'company_id' => [
                'required',
                'integer',
                Rule::exists('companies', 'id'),
                //firma trebuie sa fie una dintre firmele userului
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $this->user()?->companies()->whereKey($value)->exists()) {
                        $fail('Nu ai acces la firma selectată.');
                    }
                },
            ],

            'document_type' => ['required', Rule::enum(DocumentType::class)],

            'prefix' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9][A-Z0-9\-]*$/',
                //acelasi prefix nu se poate repeta pe acelasi tip de document
                Rule::unique('document_series', 'prefix')->where(
                    fn ($query) => $query
                        ->where('company_id', $this->input('company_id'))
                        ->where('document_type', $this->input('document_type'))
                ),
            ],

            //coloana din baza de date este unsignedInteger
            'start_number' => ['required', 'integer', 'min:1', 'max:4294967295'],

            'is_default' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Firma este obligatorie.',
            'company_id.exists' => 'Firma selectată nu există.',

            'document_type.required' => 'Tipul documentului este obligatoriu.',
            'document_type.enum' => 'Tipul de document selectat nu este valid.',

            'prefix.required' => 'Prefixul seriei este obligatoriu.',
            'prefix.max' => 'Prefixul nu poate avea mai mult de 10 caractere.',
            'prefix.regex' =>
                'Prefixul poate conține doar litere, cifre și liniuțe și trebuie să înceapă cu o literă sau o cifră.',
            'prefix.unique' =>
                'Există deja o serie cu acest prefix pentru acest tip de document.',

            'start_number.required' => 'Numărul de pornire este obligatoriu.',
            'start_number.integer' => 'Numărul de pornire trebuie să fie un număr întreg.',
            'start_number.min' => 'Numărul de pornire trebuie să fie cel puțin 1.',
            'start_number.max' => 'Numărul de pornire depășește valoarea maximă permisă.',

            'is_default.boolean' => 'Valoarea pentru seria implicită nu este validă.',
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => 'firma',
            'document_type' => 'tipul documentului',
            'prefix' => 'prefixul seriei',
            'start_number' => 'numărul de pornire',
            'is_default' => 'seria implicită',
        ];
    }
}
