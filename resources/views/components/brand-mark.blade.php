@props(['labelled' => false])

<svg {{ $attributes->class('shrink-0') }} viewBox="0 0 40 40" fill="none"
     @if ($labelled) role="img" aria-label="BFMS" @else aria-hidden="true" @endif>
    <rect width="40" height="40" rx="12" fill="#0077C8" />
    <path d="M12.75 9.75h9.5l5 5v15.5h-14.5V9.75Z" stroke="white" stroke-width="2.25" stroke-linejoin="round" />
    <path d="M22.25 9.75v5h5" stroke="white" stroke-width="2.25" stroke-linejoin="round" />
    <path d="M16.5 20h7M16.5 24.5h4.5" stroke="white" stroke-width="2.25" stroke-linecap="round" />
    <circle cx="25.75" cy="27.25" r="3.25" fill="#83C5E7" stroke="white" stroke-width="1.5" />
    <path d="m24.5 27.2.85.9 1.7-1.85" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
</svg>
