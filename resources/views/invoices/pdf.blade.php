<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $invoice->number ? $invoice->series.'-'.$invoice->number : 'ciorna' }}</title>
    <style>
        @page { margin: 34px 38px 52px; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #1e293b;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            line-height: 1.45;
        }

        .header, .parties, .summary { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .brand { color: #0369a1; font-size: 22px; font-weight: 700; letter-spacing: .5px; }
        .document-title { font-size: 21px; font-weight: 700; text-align: right; text-transform: uppercase; }
        .document-number { margin-top: 3px; color: #475569; font-size: 12px; text-align: right; }

        .status {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            background: #f0f9ff;
            color: #0369a1;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .divider { margin: 16px 0; border-top: 2px solid #0ea5e9; }

        .parties td {
            width: 50%;
            padding: 0 14px 0 0;
            vertical-align: top;
        }

        .parties td + td { padding: 0 0 0 14px; border-left: 1px solid #e2e8f0; }
        .section-label { margin-bottom: 5px; color: #64748b; font-size: 8px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
        .party-name { margin-bottom: 4px; color: #0f172a; font-size: 12px; font-weight: 700; }
        .muted { color: #64748b; }

        .meta {
            width: 100%;
            margin: 18px 0;
            border-collapse: collapse;
            background: #f8fafc;
        }

        .meta td { width: 25%; padding: 9px 10px; border: 1px solid #e2e8f0; }
        .meta-label { display: block; margin-bottom: 2px; color: #64748b; font-size: 8px; text-transform: uppercase; }
        .meta-value { color: #0f172a; font-weight: 700; }

        .lines { width: 100%; border-collapse: collapse; }
        .lines thead { display: table-header-group; }
        .lines tr { page-break-inside: avoid; }
        .lines th {
            padding: 8px 6px;
            border-bottom: 2px solid #cbd5e1;
            background: #f1f5f9;
            color: #475569;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
        }

        .lines td { padding: 8px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .number { text-align: right !important; white-space: nowrap; }
        .description { width: 34%; }
        .sku { margin-top: 2px; color: #94a3b8; font-size: 8px; }

        .summary { margin-top: 14px; }
        .summary-spacer { width: 58%; }
        .summary-totals { width: 42%; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 5px 7px; }
        .summary-table .total td {
            padding-top: 8px;
            border-top: 2px solid #0ea5e9;
            color: #0f172a;
            font-size: 12px;
            font-weight: 700;
        }

        .payment-details {
            margin-top: 18px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: #f8fafc;
            page-break-inside: avoid;
        }

        .payment-details p { margin: 2px 0; }
        .footer { position: fixed; right: 0; bottom: -30px; left: 0; color: #94a3b8; font-size: 8px; }
        .footer-right { float: right; }
    </style>
</head>
<body>
@php
    $number = $invoice->number ? $invoice->series.'-'.$invoice->number : 'Ciornă #'.$invoice->id;
    $issuerAddress = collect([$invoice->company->address, $invoice->company->city, $invoice->company->county])->filter()->implode(', ');
    $clientAddress = collect([$invoice->client?->address, $invoice->client?->city, $invoice->client?->county])->filter()->implode(', ');
@endphp

<table class="header">
    <tr>
        <td class="summary-totals">
            <div class="brand">BFMS</div>
            <div class="muted">Document financiar</div>
        </td>
        <td>
            <div class="document-title">{{ $invoice->document_type->label() }}</div>
            <div class="document-number">{{ $number }}</div>
            <div style="text-align: right;"><span class="status">{{ $invoice->status->label() }}</span></div>
        </td>
    </tr>
</table>

<div class="divider"></div>

<table class="parties">
    <tr>
        <td>
            <div class="section-label">Furnizor</div>
            <div class="party-name">{{ $invoice->company->name }}</div>
            <div>CUI: {{ $invoice->company->cui }}</div>
            @if ($invoice->company->trade_registry_number)
                <div>Reg. com.: {{ $invoice->company->trade_registry_number }}</div>
            @endif
            <div>{{ $issuerAddress ?: '-' }}</div>
            @if ($invoice->company->social_capital)
                <div>Capital social: {{ number_format((float) $invoice->company->social_capital, 2, ',', '.') }} RON</div>
            @endif
        </td>
        <td>
            <div class="section-label">Client</div>
            <div class="party-name">{{ $invoice->client?->full_name ?? '-' }}</div>
            <div>{{ $invoice->client?->client_type === 'individual' ? 'CNP' : 'CUI' }}: {{ $invoice->client?->tax_id ?? '-' }}</div>
            @if ($invoice->client?->trade_registry_number)
                <div>Reg. com.: {{ $invoice->client->trade_registry_number }}</div>
            @endif
            <div>{{ $clientAddress ?: '-' }}</div>
            @if ($invoice->client?->email)
                <div>{{ $invoice->client->email }}</div>
            @endif
        </td>
    </tr>
</table>

<table class="meta">
    <tr>
        <td><span class="meta-label">Data emiterii</span><span class="meta-value">{{ $invoice->issue_date?->format('d.m.Y') ?? '-' }}</span></td>
        <td><span class="meta-label">Data scadenței</span><span class="meta-value">{{ $invoice->due_date?->format('d.m.Y') ?? '-' }}</span></td>
        <td><span class="meta-label">Monedă</span><span class="meta-value">{{ $invoice->currency }}</span></td>
        <td><span class="meta-label">TVA furnizor</span><span class="meta-value">{{ $invoice->company->vat_payer ? 'Plătitor TVA' : 'Neplătitor TVA' }}</span></td>
    </tr>
</table>

<table class="lines">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th class="description">Produs / serviciu</th>
            <th class="number" style="width: 13%;">Cantitate</th>
            <th class="number" style="width: 15%;">Preț unitar</th>
            <th class="number" style="width: 10%;">TVA</th>
            <th class="number" style="width: 18%;">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($invoice->lines as $line)
            <tr>
                <td>{{ $line->position }}</td>
                <td class="description">
                    {{ $line->product_name_snapshot }}
                    @if ($line->sku_snapshot)<div class="sku">Cod: {{ $line->sku_snapshot }}</div>@endif
                </td>
                <td class="number">{{ number_format((float) $line->quantity, 2, ',', '.') }} {{ $line->unit_measure_snapshot }}</td>
                <td class="number">{{ number_format((float) $line->unit_price_snapshot, 2, ',', '.') }}</td>
                <td class="number">{{ number_format((float) $line->vat_rate_snapshot, 2, ',', '.') }}%</td>
                <td class="number">{{ number_format((float) $line->line_total, 2, ',', '.') }} {{ $invoice->currency }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="padding: 18px; text-align: center; color: #94a3b8;">Documentul nu conține linii.</td></tr>
        @endforelse
    </tbody>
</table>

<table class="summary">
    <tr>
        <td class="summary-spacer"></td>
        <td>
            <table class="summary-table">
                <tr><td class="muted">Subtotal</td><td class="number">{{ number_format((float) $invoice->subtotal, 2, ',', '.') }} {{ $invoice->currency }}</td></tr>
                <tr><td class="muted">TVA</td><td class="number">{{ number_format((float) $invoice->vat_total, 2, ',', '.') }} {{ $invoice->currency }}</td></tr>
                <tr class="total"><td>Total de plată</td><td class="number">{{ number_format((float) $invoice->total, 2, ',', '.') }} {{ $invoice->currency }}</td></tr>
                @if ($invoice->currency !== 'RON')
                    <tr><td class="muted">Curs valutar</td><td class="number">{{ number_format((float) $invoice->exchange_rate, 4, ',', '.') }} RON</td></tr>
                @endif
            </table>
        </td>
    </tr>
</table>

@if ($invoice->company->bankAccounts->isNotEmpty())
    <div class="payment-details">
        <div class="section-label">Conturi pentru plată</div>
        @foreach ($invoice->company->bankAccounts as $account)
            <p><strong>{{ $account->currency }}:</strong> {{ $account->iban }}{{ $account->bank_name ? ' - '.$account->bank_name : '' }}</p>
        @endforeach
    </div>
@endif

<div class="footer">
    Document generat electronic la {{ now()->format('d.m.Y H:i') }}.
    <span class="footer-right">{{ $invoice->company->name }} | {{ $number }}</span>
</div>

<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
        $pdf->page_text(495, 806, 'Pagina {PAGE_NUM} / {PAGE_COUNT}', $font, 8, [0.58, 0.64, 0.72]);
    }
</script>
</body>
</html>
