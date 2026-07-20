<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // "current_password" compara valoarea cu hash-ul contului autentificat
            'current_password' => ['required', 'current_password'],

            // "confirmed" cere automat si campul password_confirmation
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:current_password',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => 'Parola actuală nu este corectă.',
            'password.different' => 'Parola nouă trebuie să difere de cea actuală.',
            'password.confirmed' => 'Confirmarea nu coincide cu parola nouă.',
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'parola actuală',
            'password' => 'parola nouă',
        ];
    }
}
