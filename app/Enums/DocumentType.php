<?php

namespace App\Enums;

enum DocumentType: string
{
    case Invoice = 'invoice';
    case Proforma = 'proforma';
    case Receipt = 'receipt';

    /**
     * Eticheta afișată în interfață.
     */
    public function label(): string
    {
        return match ($this) {
            self::Invoice => 'Factură',
            self::Proforma => 'Proformă',
            self::Receipt => 'Chitanță',
        };
    }
}
