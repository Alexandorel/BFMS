@props([
    'href' => null,
    'variant' => 'primary',
    'type' => 'button',
    'iconOnly' => false,
])

@php
    $variantClasses = match ($variant) {
        'secondary' => 'ui-btn-secondary',
        'ghost' => 'ui-btn-ghost',
        'danger' => 'ui-btn-danger',
        'danger-ghost' => 'ui-btn-danger-ghost',
        default => 'ui-btn-primary',
    };

    $classes = trim('ui-btn ' . $variantClasses . ($iconOnly ? ' ui-btn-icon' : ''));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
