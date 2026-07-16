<?php

namespace Tests\Feature;

use App\Rules\RomanianCuiRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

final class RomanianCuiRuleTest extends TestCase
{
    public function test_it_accepts_valid_romanian_cuis(): void
    {
        $validCuis = [
            'RO19',         // limita minima: 2 cifre
            'RO795',        // CUI scurt
            'RO850',        // checksum calculat 10, transformat in 0
            'RO1973096',    // 7 cifre
            'RO14837428',   // 8 cifre
            'RO1234567897', // limita maxima: 10 cifre
        ];

        foreach ($validCuis as $cui) {
            $validator = $this->makeValidator($cui);

            $this->assertTrue(
                $validator->passes(),
                sprintf(
                    'CUI-ul %s trebuia sa fie valid. Erori: %s',
                    $cui,
                    $validator->errors()->first('cui')
                )
            );
        }
    }

    public function test_it_accepts_lowercase_prefix_and_outer_spaces(): void
    {
        $validator = $this->makeValidator('  ro14837428  ');

        $this->assertTrue(
            $validator->passes(),
            $validator->errors()->first('cui')
        );
    }

    public function test_it_rejects_invalid_control_digits(): void
    {
        $invalidCuis = [
            'RO18',
            'RO794',
            'RO851',
            'RO1973097',
            'RO14837429',
            'RO1234567898',
        ];

        foreach ($invalidCuis as $cui) {
            $validator = $this->makeValidator($cui);

            $this->assertFalse(
                $validator->passes(),
                sprintf(
                    'CUI-ul %s trebuia sa fie invalid.',
                    $cui
                )
            );
        }
    }

    public function test_it_rejects_invalid_formats(): void
    {
        $invalidValues = [
            '14837428',       // lipseste prefixul RO
            'FR14837428',     // prefix incorect
            'RO',             // lipsesc cifrele
            'RO1',            // prea putine cifre
            'RO01234567',     // incepe cu zero
            'RO12345678901',  // prea multe cifre
            'RO1234ABC',      // contine litere
            'RO14 837 428',   // contine spatii interioare
            'RO14-837-428',   // contine separator
            '',
            null,
            14837428,
        ];

        foreach ($invalidValues as $value) {
            $validator = $this->makeValidator($value);

            $this->assertFalse(
                $validator->passes(),
                sprintf(
                    'Valoarea %s trebuia sa fie invalida.',
                    var_export($value, true)
                )
            );
        }
    }

    private function makeValidator(mixed $value)
    {
        return Validator::make(
            [
                'cui' => $value,
            ],
            [
                'cui' => [
                    'required',
                    new RomanianCuiRule(),
                ],
            ]
        );
    }
}
