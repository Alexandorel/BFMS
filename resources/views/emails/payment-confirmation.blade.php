@component('mail::message')
    # Plată confirmată

    Bună ziua, {{ $client->name }},

    Am înregistrat cu succes plata dumneavoastră pentru factura {{ $invoice->series }}{{ $invoice->number }}.

    @component('mail::table')
    | Detalii plată | |
    | -------------- | --------- |
    | Sumă încasată | {{ number_format($payment->amount, 2) }} {{ $payment->currency }} |
    | Data plății | {{ $payment->payment_date->format('d.m.Y') }} |
    | Metodă | {{ $payment->payment_method }} |
    | Status factură | {{ $invoice->status === 'fully_paid' ? 'Achitată integral' : 'Achitată parțial' }} |
    @endcomponent

    Vă mulțumim,<br>
    {{ $invoice->company->name }}
@endcomponent
