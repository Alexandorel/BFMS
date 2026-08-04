@props(['status'])

@php
    // statusul vine ca enum de pe model, dar acceptam si string
    $status = $status instanceof \App\Enums\InvoiceStatus
        ? $status
        : \App\Enums\InvoiceStatus::tryFrom((string) $status);
@endphp

<span {{ $attributes->merge(['class' => 'text-xs px-2 py-1 rounded-full font-medium '.($status?->badgeClasses() ?? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300')]) }}>
    {{ $status?->label() ?? '—' }}
</span>
