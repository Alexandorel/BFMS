<?php

namespace App\Http\Requests;

use App\Models\DocumentSeries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateDocumentSeriesRequest extends FormRequest
{
    //administrator + seria trebuie sa fie a uneia dintre firmele lui
    public function authorize(): bool
    {
        $series = $this->route('series');

        return $this->user()?->role === 'administrator'
            && $series instanceof DocumentSeries
            && $this->user()->companies()->whereKey($series->company_id)->exists();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'prefix' => strtoupper(
                preg_replace('/\s+/', '', (string) $this->input('prefix'))
            ),
        ]);
    }

    public function rules(): array
    {
        $series = $this->series();

        return [
            'prefix' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9][A-Z0-9\-]*$/',
                //unicitatea se verifica ignorand seria editata
                Rule::unique('document_series', 'prefix')
                    ->ignore($series->getKey())
                    ->where(
                        fn ($query) => $query
                            ->where('company_id', $series->company_id)
                            ->where('document_type', $series->document_type)
                    ),
            ],

            'start_number' => ['required', 'integer', 'min:1', 'max:4294967295'],
        ];
    }

    /**
     * Once the series has issued documents, prefix and start number are frozen:
     * changing them would rewrite the identity of documents already issued.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $series = $this->series();

                if (! $series->is_used) {
                    return;
                }

                if ($this->input('prefix') !== $series->prefix) {
                    $validator->errors()->add(
                        'prefix',
                        'Seria are deja documente emise, prefixul nu mai poate fi modificat.'
                    );
                }

                if ((int) $this->input('start_number') !== (int) $series->start_number) {
                    $validator->errors()->add(
                        'start_number',
                        'Seria are deja documente emise, numărul de pornire nu mai poate fi modificat.'
                    );
                }
            },
        ];
    }

    private function series(): DocumentSeries
    {
        return $this->route('series');
    }

    public function messages(): array
    {
        return [
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
        ];
    }

    public function attributes(): array
    {
        return [
            'prefix' => 'prefixul seriei',
            'start_number' => 'numărul de pornire',
        ];
    }
}
