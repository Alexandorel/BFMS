<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class RomanianCnpRule implements ValidationRule
{
    // cheia oficiala pentru cifra de control a CNP-ului
    private const CONTROL_KEY = '279146358279';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Campul :attribute trebuie sa contina un CNP valid.');
            return;
        }

        $cnp = trim($value);

        // Format: exact 13 cifre, prima cifra 1-9 (sex/secol; 0 e invalid).
        if (!preg_match('/^[1-9][0-9]{12}$/D', $cnp)) {
            $fail('Campul :attribute trebuie sa contina exact 13 cifre.');
            return;
        }

        // Luna nasterii (pozitiile 4-5) trebuie sa fie 01-12.
        $month = (int) substr($cnp, 3, 2);
        if ($month < 1 || $month > 12) {
            $fail('Campul :attribute contine o luna a nasterii invalida.');
            return;
        }

        // Ziua nasterii (pozitiile 6-7) trebuie sa fie 01-31.
        $day = (int) substr($cnp, 5, 2);
        if ($day < 1 || $day > 31) {
            $fail('Campul :attribute contine o zi a nasterii invalida.');
            return;
        }

        if (!$this->hasValidChecksum($cnp)) {
            $fail('Campul :attribute nu contine un CNP valid.');
        }
    }

    private function hasValidChecksum(string $cnp): bool
    {
        // Cifra de control primita este a 13-a cifra.
        $receivedControlDigit = (int) $cnp[12];

        $sum = 0;
        for ($index = 0; $index < strlen(self::CONTROL_KEY); $index++) {
            $sum += (int) $cnp[$index] * (int) self::CONTROL_KEY[$index];
        }

        $calculatedControlDigit = $sum % 11;

        // Daca restul este 10, cifra de control devine 1.
        if ($calculatedControlDigit === 10) {
            $calculatedControlDigit = 1;
        }

        return $receivedControlDigit === $calculatedControlDigit;
    }
}
