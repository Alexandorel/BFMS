@props(['title', 'description' => null])

<div {{ $attributes->class('flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between') }}>
    <div class="min-w-0">
        <h1 class="ui-page-title">{{ $title }}</h1>
        @if ($description)
            <p class="ui-page-description">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:justify-end">
            {{ $actions }}
        </div>
    @endisset
</div>
