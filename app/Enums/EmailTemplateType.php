<?php

namespace App\Enums;

enum EmailTemplateType: string
{
    case InvoiceIssued = 'invoice_issued';
    case DueReminder = 'due_reminder';
    case OverdueAlert = 'overdue_alert';
    public function label():string
    {
        return match($this)
        {
            self::InvoiceIssued => 'Emitere factură',
            self::DueReminder => 'Reamintire scadență',
            self::OverdueAlert => 'Alertă întârziere',
        };
    }
}