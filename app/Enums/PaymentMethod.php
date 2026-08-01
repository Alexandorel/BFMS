<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Card = 'card';
    case Ramburs = 'ramburs';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Numerar',
            self::BankTransfer => 'Ordin de plată (OP)',
            self::Card => 'Card',
            self::Ramburs => 'Ramburs',
        };
    }

    /**
     * Doar incasarile care intra efectiv intr-un cont bancar cer selectarea
     * contului. La ramburs banii vin prin curier si sunt virati ulterior,
     * deci contul nu se cunoaste in momentul inregistrarii platii.
     */
    public function requiresBankAccount(): bool
    {
        return in_array($this, [self::BankTransfer, self::Card], true);
    }

    /**
     * F-401: pentru incasarile in numerar se poate emite optional si o
     * chitanta, cu numar alocat din seria de chitante a firmei.
     */
    public function canIssueReceipt(): bool
    {
        return $this === self::Cash;
    }
}
