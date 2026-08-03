@props([
    'action',
    'method' => 'DELETE',
    'label' => 'Șterge',
    'confirmText' => 'Sigur?',
    'variant' => 'action',
])

@php
    $triggerClass = $variant === 'button'
        ? 'ui-btn ui-btn-danger-ghost'
        : 'ui-action-danger';
@endphp

<div data-confirm-action {{ $attributes->class('inline-flex items-center gap-2') }}>
    <button type="button" data-confirm-start class="{{ $triggerClass }}" aria-expanded="false">
        {{ $label }}
    </button>

    <div data-confirm-panel class="hidden flex-wrap items-center gap-2">
        <span class="text-sm text-slate-500">{{ $confirmText }}</span>
        <form action="{{ $action }}" method="POST" class="inline-flex">
            @csrf
            @if (strtoupper($method) !== 'POST')
                @method($method)
            @endif
            <button type="submit" class="ui-btn ui-btn-danger">Da, șterge</button>
        </form>
        <button type="button" data-confirm-cancel class="ui-btn ui-btn-secondary">Nu</button>
    </div>
</div>

@once
    <script>
        document.addEventListener('click', function (event) {
            const startButton = event.target.closest('[data-confirm-start]');
            const cancelButton = event.target.closest('[data-confirm-cancel]');
            const root = (startButton || cancelButton)?.closest('[data-confirm-action]');

            if (! root) return;

            const panel = root.querySelector('[data-confirm-panel]');
            const trigger = root.querySelector('[data-confirm-start]');

            if (startButton) {
                trigger.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'true');
                panel.classList.remove('hidden');
                panel.classList.add('flex');
                panel.querySelector('[data-confirm-cancel]')?.focus();
            }

            if (cancelButton) {
                panel.classList.add('hidden');
                panel.classList.remove('flex');
                trigger.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'false');
                trigger.focus();
            }
        });
    </script>
@endonce
