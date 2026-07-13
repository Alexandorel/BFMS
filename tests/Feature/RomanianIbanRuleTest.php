<?php

namespace Tests\Feature;

use App\Rules\RomanianIbanRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RomanianIbanRuleTest extends TestCase
{
    public function test_valid_iban_passes(): void
    {
        $validator = Validator::make(
            ['iban' => 'RO49AAAA1B31007593840000'],
            ['iban' => [new RomanianIbanRule()]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_valid_iban_with_spaces_passes(): void
    {
        $validator = Validator::make(
            ['iban' => 'RO49 AAAA 1B31 0075 9384 0000'],
            ['iban' => [new RomanianIbanRule()]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_valid_lowercase_iban_passes(): void
    {
        $validator = Validator::make(
            ['iban' => 'ro49aaaa1b31007593840000'],
            ['iban' => [new RomanianIbanRule()]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_invalid_length_fails(): void
    {
        $validator = Validator::make(
            ['iban' => 'RO49AAAA1B3100759384000'],
            ['iban' => [new RomanianIbanRule()]]
        );

        $this->assertFalse($validator->passes());
    }

    public function test_invalid_country_code_fails(): void
    {
        $validator = Validator::make(
            ['iban' => 'DE49AAAA1B31007593840000'],
            ['iban' => [new RomanianIbanRule()]]
        );

        $this->assertFalse($validator->passes());
    }

    public function test_invalid_checksum_fails(): void
    {
        $validator = Validator::make(
            ['iban' => 'RO49AAAA1B31007593840001'],
            ['iban' => [new RomanianIbanRule()]]
        );

        $this->assertFalse($validator->passes());
    }

    public function test_invalid_characters_fail(): void
    {
        $validator = Validator::make(
            ['iban' => 'RO49AAAA1B31-007593840000'],
            ['iban' => [new RomanianIbanRule()]]
        );

        $this->assertFalse($validator->passes());
    }
}
