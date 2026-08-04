@component('mail::message')
# Factura {{ $invoice->series }}{{ $invoice->number }}

Bună ziua, {{ $client->name ?? 'Client' }},

Vă transmitem mai jos detaliile facturii emise pe data de **{{ $invoice->issue_date?->format('d.m.Y') }}**.

## Informații generale

@component('mail::table')
| Detalii | Valoare |
| :--- | :--- |
| **Serie / Număr** | {{ $invoice->series }}{{ $invoice->number }} |
| **Data Emiterii** | {{ $invoice->issue_date?->format('d.m.Y') }} |
| **Data Scadență** | {{ $invoice->due_date?->format('d.m.Y') ?? '-' }} |
| **Monedă** | {{ $invoice->currency }} |
@endcomponent

## Produse și Servicii

@component('mail::table')
| Descriere | Cant. | Preț un. | TVA | Total |
| :--- | :---: | :---: | :---: | :---: |
@foreach(($invoice->lines ?? []) as $item)
| {{ $item->product_name_snapshot }} | {{ $item->quantity }} | {{ number_format($item->unit_price_snapshot, 2) }} | {{ number_format($item->line_vat, 2) }} | {{ number_format($item->line_total, 2) }} |
@endforeach
| **Subtotal** | | | | **{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}** |
| **TVA Total** | | | | **{{ number_format($invoice->vat_total, 2) }} {{ $invoice->currency }}** |
| **TOTAL DE PLATĂ** | | | | **{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}** |
@endcomponent

Vă mulțumim,<br>
**{{ optional($invoice->company)->name ?? config('app.name') }}**
@endcomponent
