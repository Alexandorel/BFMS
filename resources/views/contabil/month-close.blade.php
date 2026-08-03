<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Închidere lună - {{ $month_label }}</title>
    <style>
        @page { margin: 26px 30px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 9px; }
        h1 { margin: 0 0 4px; color: #0f172a; font-size: 20px; }
        h2 { margin: 18px 0 7px; color: #0f172a; font-size: 12px; }
        p { margin: 2px 0; }
        .header { width: 100%; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header td { vertical-align: top; }
        .right { text-align: right; }
        .muted { color: #64748b; }
        .summary { width: 100%; margin-top: 12px; border-collapse: separate; border-spacing: 5px 0; }
        .summary td { padding: 9px; border: 1px solid #c7d2fe; background: #eef2ff; }
        .label { color: #64748b; font-size: 7px; text-transform: uppercase; }
        .amount { margin-top: 4px; color: #312e81; font-size: 12px; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { padding: 6px 5px; background: #4f46e5; color: #fff; text-align: left; font-size: 7px; }
        table.data td { padding: 6px 5px; border-bottom: 1px solid #e2e8f0; }
        table.data tr:nth-child(even) td { background: #f8fafc; }
        .number { text-align: right; white-space: nowrap; }
        .empty { padding: 18px !important; text-align: center; color: #64748b; }
        .page-break { page-break-before: always; }
        .footer { position: fixed; bottom: -15px; left: 0; right: 0; color: #94a3b8; font-size: 7px; text-align: center; }
    </style>
</head>
<body>
<table class="header">
    <tr>
        <td>
            <h1>Raport închidere lună</h1>
            <p class="muted">{{ $month_label }} ({{ $period_start->format('d.m.Y') }} - {{ $period_end->format('d.m.Y') }})</p>
        </td>
        <td class="right">
            <strong>{{ $company->name }} {{ $company->juridical_form }}</strong>
            <p>CUI: {{ $company->cui }}</p>
            <p>Generat la {{ $generated_at->format('d.m.Y H:i') }}</p>
        </td>
    </tr>
</table>

<table class="summary">
    <tr>
        <td><div class="label">Facturi emise</div><div class="amount">{{ $summary['invoice_count'] }}</div></td>
        <td><div class="label">Total facturat</div><div class="amount">{{ number_format($summary['invoiced_ron'], 2, ',', '.') }} RON</div></td>
        <td><div class="label">Încasări lunare</div><div class="amount">{{ number_format($summary['collections_ron'], 2, ',', '.') }} RON</div></td>
        <td><div class="label">Sold la {{ $period_end->format('d.m.Y') }}</div><div class="amount">{{ number_format($summary['outstanding_ron'], 2, ',', '.') }} RON</div></td>
        <td><div class="label">TVA facturi lunare</div><div class="amount">{{ number_format($summary['vat_ron'], 2, ',', '.') }} RON</div></td>
    </tr>
</table>

<h2>Defalcare pe cote de TVA</h2>
<table class="data">
    <thead><tr><th>Cotă TVA</th><th class="number">Bază impozabilă RON</th><th class="number">TVA RON</th><th class="number">Total RON</th></tr></thead>
    <tbody>
    @forelse ($vat_breakdown as $vat)
        <tr>
            <td>{{ number_format($vat['rate'], 2, ',', '.') }}%</td>
            <td class="number">{{ number_format($vat['net_ron'], 2, ',', '.') }}</td>
            <td class="number">{{ number_format($vat['vat_ron'], 2, ',', '.') }}</td>
            <td class="number">{{ number_format($vat['gross_ron'], 2, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="empty">Nu există facturi emise în această lună.</td></tr>
    @endforelse
    </tbody>
</table>

<h2>Facturi emise în lună</h2>
<table class="data">
    <thead>
    <tr><th>Data</th><th>Număr</th><th>Client</th><th>Status</th><th>Monedă</th><th class="number">Total nativ</th><th class="number">Total RON</th></tr>
    </thead>
    <tbody>
    @forelse ($invoices as $invoice)
        <tr>
            <td>{{ \Carbon\Carbon::parse($invoice['date'])->format('d.m.Y') }}</td>
            <td>{{ $invoice['number'] }}</td>
            <td>{{ $invoice['client'] }}</td>
            <td>{{ $invoice['status'] }}</td>
            <td>{{ $invoice['currency'] }}</td>
            <td class="number">{{ number_format($invoice['total'], 2, ',', '.') }}</td>
            <td class="number">{{ number_format($invoice['total_ron'], 2, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="7" class="empty">Nu există facturi emise în această lună.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="page-break"></div>
<h2>Încasări înregistrate în lună</h2>
<table class="data">
    <thead>
    <tr><th>Data</th><th>Factură</th><th>Client</th><th>Metodă</th><th>Referință</th><th>Monedă</th><th class="number">Sumă nativă</th><th class="number">Sumă RON</th></tr>
    </thead>
    <tbody>
    @forelse ($payments as $payment)
        <tr>
            <td>{{ \Carbon\Carbon::parse($payment['date'])->format('d.m.Y') }}</td>
            <td>{{ $payment['invoice_number'] }}</td>
            <td>{{ $payment['client'] }}</td>
            <td>{{ $payment['method'] }}</td>
            <td>{{ $payment['reference'] ?? '-' }}</td>
            <td>{{ $payment['currency'] }}</td>
            <td class="number">{{ number_format($payment['amount'], 2, ',', '.') }}</td>
            <td class="number">{{ number_format($payment['amount_ron'], 2, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="empty">Nu există încasări înregistrate în această lună.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">BFMS - valorile agregate sunt exprimate în echivalent RON folosind cursurile salvate în documente</div>
</body>
</html>
