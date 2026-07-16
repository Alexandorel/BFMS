<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class RomanianCuiRule implements ValidationRule
{
    //cheie oficiala pt calcularea cifrei de control
    private const CONTROL_KEY = '753217532';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Campul :attribute trebuie sa contina un CUI valid.');
            return;
        }

        $cui = strtoupper(trim($value));

        /*
         * Format:
         * - prefixul RO obligatoriu
         * - intre 2 si 10 cifre
         * - prima cifra nu poate fi zero.
         */
        if (!preg_match('/^RO[1-9][0-9]{1,9}$/D', $cui)) {
            $fail('Campul :attribute trebuie sa inceapa cu RO si '
                . 'sa contina intre 2 si 10 cifre.');
            return;
        }

        /*
         * Prefixul RO nu participa la calcularea cifrei de control.
         */
        $numericCui = substr($cui, 2);

        if (!$this->hasValidChecksum($numericCui)) {
            $fail('Campul :attribute nu contine un CUI valid.');
        }
    }

    private function hasValidChecksum(string $numericCui): bool
    {

         //Ultima cifra din CUI este cifra de control primita.
        $receivedControlDigit = (int) substr($numericCui, -1);

        $digits = substr($numericCui, 0, -1);

        /*
         * Aliniem cifrele la dreapta fata de cheia de control.
         *
         * Exemplu:
         * CUI:              14837428
         * Fara control:     1483742
         * Dupa completare:  001483742
         * Cheie:            753217532
         */
        $digits = str_pad(
            $digits,
            strlen(self::CONTROL_KEY),
            '0',
            STR_PAD_LEFT
        );

        $sum = 0;

        for ($index = 0; $index < strlen(self::CONTROL_KEY); $index++) {
            $sum += (int) $digits[$index]
                * (int) self::CONTROL_KEY[$index];
        }

        /*
         * Formula cifrei de control:
         * (suma × 10) modulo 11
         */
        $calculatedControlDigit = ($sum * 10) % 11;


        // Daca rezultatul este 10, cifra de control devine 0.
        if ($calculatedControlDigit === 10) {
            $calculatedControlDigit = 0;
        }
        return $receivedControlDigit === $calculatedControlDigit;
    }
}
