@component('mail::message')
    # Factura {{ $invoice->series }}{{ $invoice->number }}

    Bună ziua, {{ $client->name }},

    Atașat găsiți factura emisă pe data de {{ $invoice->issue_date->format('d.m.Y') }}.

    @component('mail::table')
    | Detalii | |
    | ------- | --------- |
    | Serie/Număr | {{ $invoice->series }}{{ $invoice->number }} |
    | Data emiterii | {{ $invoice->issue_date->format('d.m.Y') }} |
    | Scadență | {{ $invoice->due_date?->format('d.m.Y') ?? '-' }} |
    | Total de plată | {{ number_format($invoice->total, 2) }} {{ $invoice->currency }} |
    @endcomponent

    Vă mulțumim,<br>
    {{ $invoice->company->name }}
@endcomponent
