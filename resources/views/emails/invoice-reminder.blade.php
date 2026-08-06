@component('mail::message')
# Reamintire Factura {{ $invoice->series }}{{ $invoice->number }}

Bună ziua, {{ $client->name ?? 'Client' }},

@if($type === 'reminder_before_due')
Vă reamintim că factura de mai jos are scadența în **{{ now()->diffInDays($invoice->due_date) }} zile** (pe data de {{ $invoice->due_date?->format('d.m.Y') }}).
@elseif($type === 'reminder_due')
Vă reamintim că factura de mai jos este **scadentă astăzi** ({{ $invoice->due_date?->format('d.m.Y') }}).
@else
Factura de mai jos este **restantă de {{ $invoice->due_date?->diffInDays(now()) }} zile**. Vă rugăm să efectuați plata cât mai curând.
@endif

## Sumar Factură

@component('mail::table')
| Detalii | Valoare |
| :--- | :--- |
| **Serie / Număr** | {{ $invoice->series }}{{ $invoice->number }} |
| **Data Emiterii** | {{ $invoice->issue_date?->format('d.m.Y') }} |
| **Data Scadență** | {{ $invoice->due_date?->format('d.m.Y') }} |
| **Total de plată** | **{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}** |
@endcomponent

## Detalii Produse / Servicii

@component('mail::table')
| Descriere | Cant. | Preț un. | Total |
| :--- | :---: | :---: | :---: |
@foreach(($invoice->lines ?? []) as $item)
| {{ $item->product_name_snapshot }} | {{ $item->quantity }} | {{ number_format($item->unit_price_snapshot, 2) }} | {{ number_format($item->line_total, 2) }} |
@endforeach
| **TOTAL DE PLATĂ** | | | **{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}** |
@endcomponent

Vă mulțumim,<br>
**{{ optional($invoice->company)->name ?? config('app.name') }}**
@endcomponent
