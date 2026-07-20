<?php

namespace App\Http\Requests;

use App\Rules\RomanianCuiRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Doar administratorii, si doar pe firmele la care userul are acces.
     */
    public function authorize(): bool
    {
        $company = $this->route('company');

        if (! $company) {
            return false;
        }

        return in_array($this->user()?->role, ['administrator', 'superadmin'], true)
            && $this->user()->companies()->whereKey($company->getKey())->exists();
    }

    /**
     * Pagina are trei formulare separate (identificare, sediu, TVA), fiecare
     * trimite doar campurile lui. Normalizam strict ce a venit in request,
     * altfel campurile absente ar deveni sir gol si regula "sometimes" nu ar
     * mai avea efect.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['name', 'county', 'city', 'address'] as $field) {
            if ($this->has($field)) {
                $merge[$field] = trim((string) $this->input($field));
            }
        }

        foreach (['juridical_form', 'trade_registry_number'] as $field) {
            if ($this->has($field)) {
                $merge[$field] = strtoupper(trim((string) $this->input($field)));
            }
        }

        if ($this->has('cui')) {
            $merge['cui'] = strtoupper(
                preg_replace('/\s+/', '', (string) $this->input('cui'))
            );
        }

        if ($this->has('vat_payer')) {
            $merge['vat_payer'] = $this->boolean('vat_payer');
        }

        $this->merge($merge);
    }

    public function rules(): array
    {
        $companyId = $this->route('company')->getKey();

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],

            'juridical_form' => [
                'sometimes',
                'required',
                Rule::in(['SRL', 'SA', 'PFA', 'II', 'IF', 'SRL-D']),
            ],

            'cui' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                new RomanianCuiRule(),
                Rule::unique('companies', 'cui')->ignore($companyId),
            ],

            'trade_registry_number' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('companies', 'trade_registry_number')->ignore($companyId),
            ],

            'county' => ['sometimes', 'required', 'string', 'max:255'],
            'city' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],

            'social_capital' => [
                'sometimes',
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:9999999999999.99',
            ],

            'vat_payer' => ['sometimes', 'boolean'],
        ];
    }
}
