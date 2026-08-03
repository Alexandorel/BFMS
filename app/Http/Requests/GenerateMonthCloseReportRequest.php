<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateMonthCloseReportRequest extends FormRequest
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
            'month' => ['required', 'date_format:Y-m'],
            'format' => ['required', Rule::in(['pdf', 'xlsx'])],
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => 'Selectați luna pentru care doriți raportul.',
            'month.date_format' => 'Luna trebuie să aibă formatul AAAA-LL.',
            'format.required' => 'Selectați formatul raportului.',
            'format.in' => 'Formatul raportului trebuie să fie PDF sau Excel.',
        ];
    }
}
