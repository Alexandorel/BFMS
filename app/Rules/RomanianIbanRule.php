<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class RomanianIbanRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $iban = strtoupper(preg_replace('/\s+/', '', (string)$value));

        if($iban===''){
            $fail('IBAN-ul este necesar!');
            return;
        }

        if(strlen($iban) !== 24){
            $fail('IBAN-ul trebuie să aibă 24 de caractere');
            return;
        }

        if(!str_starts_with($iban, 'RO')){
            $fail('IBAN-ul trebuie să înceapă cu RO');
            return;
        }

        if(!preg_match('/^[A-Z0-9]+$/', $iban)){
            $fail('IBAN-ul trebuie să conțină doar litere și cifre');
            return;
        }

        if(!$this->passesMod97($iban)){
            $fail('IBAN-ul este invalid');
            return;
        }
    }

    //transform all letters to digits and calculate mod97
    private function passesMod97(string $iban): bool{

        //move first 4 letters to final
        $rearrangedIban = substr($iban, 4).substr($iban, 0, 4);

        $numeric = '';

        //convert letters to numbers
        foreach(str_split($rearrangedIban) as $char){
            if(ctype_alpha($char)){
                $numeric .= ord($char) - 55;
            }else{
                $numeric .= $char;
            }
        }

        $remainder = 0;

        foreach(str_split($numeric) as $digit){
            $remainder = ($remainder * 10 + (int)$digit) % 97;
        }

        return $remainder === 1;
    }
}
