<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Fiecare utilizator autentificat isi poate edita propriul profil.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Normalizam doar cheile primite, ca formularul sa poata trimite
     * strict campurile modificate (vezi regulile "sometimes" de mai jos).
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['first_name', 'last_name'] as $field) {
            if ($this->has($field)) {
                $merge[$field] = trim((string) $this->input($field));
            }
        }

        if ($this->has('email')) {
            $merge['email'] = strtolower(trim((string) $this->input('email')));
        }

        $this->merge($merge);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],

            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'prenume',
            'last_name' => 'nume',
            'email' => 'email',
        ];
    }
}
