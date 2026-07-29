<?php

namespace App\Enums;

enum DocumentType: string
{
    case Invoice = 'invoice';
    case Proforma = 'proforma';
    case Receipt = 'receipt';

    /**
     * Tag showing in interface
     */
    public function label(): string
    {
        return match ($this) {
            self::Invoice => 'Factură',
            self::Proforma => 'Proformă',
            self::Receipt => 'Chitanță',
        };
    }

    /**
     * Prefixes for company 
     */
    public function defaultPrefix(): string
    {
        return match ($this) {
            self::Invoice => 'FCT',
            self::Proforma => 'PRF',
            self::Receipt => 'CHT',
        };
    }
}
