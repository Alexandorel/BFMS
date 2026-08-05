{{-- Conținut fiscal comun tuturor temelor. Doar CSS-ul diferă între teme (F-601). --}}
@php
    $number = $invoice->number ? $invoice->series.'-'.$invoice->number : 'Ciornă #'.$invoice->id;
    $issuerAddress = collect([$invoice->company->address, $invoice->company->city, $invoice->company->county])->filter()->implode(', ');
    $clientAddress = collect([$invoice->client?->address, $invoice->client?->city, $invoice->client?->county])->filter()->implode(', ');
@endphp

<table class="header">
    <tr>
        <td class="summary-totals">
            <div class="brand">{{ $invoice->company->name }}</div>
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
