<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Issued = 'issued';
    case PartiallyPaid = 'partially_paid';
    case FullyPaid = 'fully_paid';
    case Cancelled = 'cancelled';
    case Credited = 'credited';

    /**
     * Tag showing in interface
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Ciornă',
            self::Issued => 'Emisă',
            self::PartiallyPaid => 'Încasată parțial',
            self::FullyPaid => 'Încasată total',
            self::Cancelled => 'Anulată',
            self::Credited => 'Stornată',
        };
    }

    /**
     * Tailwind classes for the status badge.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft, self::Credited => 'bg-slate-100 text-slate-600',
            self::Issued => 'bg-sky-50 text-sky-700',
            self::PartiallyPaid => 'bg-amber-50 text-amber-700',
            self::FullyPaid => 'bg-emerald-50 text-emerald-700',
            self::Cancelled => 'bg-rose-50 text-rose-700',
        };
    }

    /**
     * A draft has no fiscal number and can still be edited or deleted.
     */
    public function isDraft(): bool
    {
        return $this === self::Draft;
    }

    /**
     * Unel an issued or partially paid invoice cand get payments
     */
    public function acceptsPayments(): bool
    {
        return in_array($this, [self::Issued, self::PartiallyPaid], true);
    }
}
