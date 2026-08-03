<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateClientStatementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'format' => strtolower((string) $this->input('format', 'pdf')),
        ]);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer'],
            'format' => ['required', Rule::in(['pdf', 'xlsx'])],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Selectați clientul pentru care doriți raportul.',
            'client_id.integer' => 'Clientul selectat nu este valid.',
            'format.required' => 'Selectați formatul raportului.',
            'format.in' => 'Formatul raportului trebuie să fie PDF sau Excel.',
        ];
    }
}
