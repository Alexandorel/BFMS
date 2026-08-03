<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Fișă client - {{ $client->full_name }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 10px; }
        h1 { margin: 0 0 4px; color: #0f172a; font-size: 21px; }
        h2 { margin: 22px 0 8px; color: #0f172a; font-size: 13px; }
        p { margin: 2px 0; }
        .header { width: 100%; border-bottom: 2px solid #4f46e5; padding-bottom: 12px; }
        .header td { vertical-align: top; }
        .right { text-align: right; }
        .muted { color: #64748b; }
        .info { width: 100%; margin-top: 14px; border-collapse: collapse; }
        .info td { width: 50%; padding: 8px 10px; border: 1px solid #e2e8f0; vertical-align: top; }
        .label { color: #64748b; font-size: 8px; text-transform: uppercase; }
        .value { margin-top: 3px; color: #0f172a; font-size: 11px; font-weight: bold; }
        .summary { width: 100%; margin-top: 14px; border-collapse: separate; border-spacing: 6px 0; }
        .summary td { padding: 10px; border: 1px solid #c7d2fe; background: #eef2ff; }
        .summary .amount { margin-top: 4px; color: #312e81; font-size: 14px; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { padding: 7px 6px; background: #4f46e5; color: #fff; text-align: left; font-size: 8px; }
        table.data td { padding: 7px 6px; border-bottom: 1px solid #e2e8f0; }
        table.data tr:nth-child(even) td { background: #f8fafc; }
        .number { text-align: right; white-space: nowrap; }
        .empty { padding: 22px !important; text-align: center; color: #64748b; }
        .footer { position: fixed; bottom: -16px; left: 0; right: 0; color: #94a3b8; font-size: 8px; text-align: center; }
    </style>
</head>
<body>
<table class="header">
    <tr>
        <td>
            <h1>Fișă client</h1>
            <p class="muted">Situația relației comerciale și istoricul facturilor</p>
        </td>
        <td class="right">
            <strong>{{ $company->name }} {{ $company->juridical_form }}</strong>
            <p>CUI: {{ $company->cui }}</p>
            <p>{{ $company->city }}, {{ $company->address }}</p>
        </td>
    </tr>
</table>

<table class="info">
    <tr>
        <td>
            <div class="label">Client</div>
            <div class="value">{{ $client->full_name }}</div>
            <p>{{ $client->client_type === 'company' ? 'CUI' : 'CNP' }}: {{ $client->tax_id ?? '-' }}</p>
            <p>{{ $client->city }}, {{ $client->address }}</p>
        </td>
        <td>
            <div class="label">Raport generat</div>
            <div class="value">{{ $generated_at->format('d.m.Y H:i') }}</div>
            <p>Valorile agregate sunt exprimate în echivalent RON.</p>
        </td>
    </tr>
</table>

<table class="summary">
    <tr>
        <td><div class="label">Facturi</div><div class="amount">{{ $summary['invoice_count'] }}</div></td>
        <td><div class="label">Total facturat</div><div class="amount">{{ number_format($summary['invoiced_ron'], 2, ',', '.') }} RON</div></td>
        <td><div class="label">Total plătit</div><div class="amount">{{ number_format($summary['paid_ron'], 2, ',', '.') }} RON</div></td>
        <td><div class="label">Sold de încasat</div><div class="amount">{{ number_format($summary['outstanding_ron'], 2, ',', '.') }} RON</div></td>
    </tr>
</table>

<h2>Istoric facturi</h2>
<table class="data">
    <thead>
    <tr>
        <th>Data</th>
        <th>Număr</th>
        <th>Status</th>
        <th>Monedă</th>
        <th class="number">Total nativ</th>
        <th class="number">Total RON</th>
        <th class="number">Plătit RON</th>
        <th class="number">Sold RON</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($invoices as $invoice)
        <tr>
            <td>{{ \Carbon\Carbon::parse($invoice['date'])->format('d.m.Y') }}</td>
            <td>{{ $invoice['number'] }}</td>
            <td>{{ $invoice['status'] }}</td>
            <td>{{ $invoice['currency'] }}</td>
            <td class="number">{{ number_format($invoice['total'], 2, ',', '.') }}</td>
            <td class="number">{{ number_format($invoice['total_ron'], 2, ',', '.') }}</td>
            <td class="number">{{ number_format($invoice['paid_ron'], 2, ',', '.') }}</td>
            <td class="number">{{ number_format($invoice['balance_ron'], 2, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="empty">Clientul nu are facturi emise.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">BFMS - document generat automat la {{ $generated_at->format('d.m.Y H:i') }}</div>
</body>
</html>
