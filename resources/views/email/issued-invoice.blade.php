<!DOCTYPE html>
<html>
    <body style="font-family: sans-serif; color: #1e293b;">
        <div style="max-width: 500px; margin: 0 auto; padding: 24px;">
            <h2 style="color: #4f46e5;">Factură nouă: {{ $invoice->series }}-{{ $invoice->number }}</h2>
            <p>Bună,</p>
            <p>A fost emisă o factură nouă pentru dumneavoastră.</p>
            <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Client</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $invoice->client?->full_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Data emiterii</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $invoice->issue_date?->format('d.m.Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Scadență</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $invoice->due_date?->format('d.m.Y') }}</td>
                </tr>
                <tr style="border-top: 1px solid #e2e8f0; font-weight: bold;">
                    <td style="padding: 8px 0;">Total</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($invoice->total, 2,',','.') }} {{ $invoice->currency }}</td>
                </tr>
            </table>
            <p style="font-size: 13px; color: #94a3b8;">Acest email a fost generat automat de {{ config('app.name') }}.</p>
        </div>
    </body>
</html>