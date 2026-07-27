@props(['status'])

@php
    // Mapping the 6 states
    $map = [
        'draft'          => ['Ciornă',            'bg-slate-100 text-slate-600'],
        'issued'         => ['Emisă',             'bg-sky-50 text-sky-700'],
        'partially_paid' => ['Încasată parțial',  'bg-amber-50 text-amber-700'],
        'fully_paid'     => ['Încasată total',    'bg-emerald-50 text-emerald-700'],
        'cancelled'      => ['Anulată',           'bg-rose-50 text-rose-700'],
        'credited'       => ['Stornată',          'bg-slate-100 text-slate-600'],
    ];

    [$label, $classes] = $map[$status] ?? [$status, 'bg-slate-100 text-slate-600'];
@endphp

<span {{ $attributes->merge(['class' => "text-xs px-2 py-1 rounded-full font-medium $classes"]) }}>
    {{ $label }}
</span>
