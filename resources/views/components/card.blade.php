@props(['padding' => true])

<section {{ $attributes->class(['ui-card', 'p-5 sm:p-6' => $padding]) }}>
    {{ $slot }}
</section>
