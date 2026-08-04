@component('mail::message')
# Plată confirmată

Bună ziua, {{ $client->name }},

Am înregistrat cu succes plata dumneavoastră pentru factura **{{ $invoice->series }}{{ $invoice->number }}**.

@component('mail::table')
| Detalii plată | |
| :--- | :--- |
| **Sumă încasată** | {{ number_format($payment->amount, 2) }} {{ $payment->currency }} |
| **Data plății** | {{ \Carbon\Carbon::parse($payment->payment_date)->format('d.m.Y') }} |
| **Metodă** | {{ ucfirst($payment->payment_method->value ?? $payment->payment_method) }} |
| **Status factură** | {{ $invoice->status === 'fully_paid' ? 'Achitată integral' : 'Achitată parțial' }} |
@endcomponent

@component('mail::button', ['url' => route('invoices.show', $invoice->id)])
Vezi Factura
@endcomponent

Vă mulțumim,<br>
{{ $invoice->company->name ?? config('app.name') }}
@endcomponent
