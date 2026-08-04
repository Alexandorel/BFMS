@component('mail::message')
    # Factura {{ $invoice->series }}{{ $invoice->number }}

    Bună ziua, {{ $client->name }},

    @if($type === 'reminder_before_due')
    Vă reamintim că factura de mai jos are scadența în {{ now()->diffInDays($invoice->due_date) }} zile.
    @elseif($type === 'reminder_due')
    Vă reamintim că factura de mai jos este scadentă astăzi.
    @else
    Factura de mai jos este restantă de {{ $invoice->due_date->diffInDays(now()) }} zile. Vă rugăm să efectuați plata cât mai curând.
    @endif

    @component('mail::table')
    | Detalii | |
    | ------- | --------- |
    | Serie/Număr | {{ $invoice->series }}{{ $invoice->number }} |
    | Scadență | {{ $invoice->due_date->format('d.m.Y') }} |
    | Total de plată | {{ number_format($invoice->total, 2) }} {{ $invoice->currency }} |
    @endcomponent

    Vă mulțumim,<br>
    {{ $invoice->company->name }}
@endcomponent
