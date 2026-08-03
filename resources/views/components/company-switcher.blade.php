@props([
    'companies',
    'activeCompany' => null,
    'addHref' => null,
])

<div {{ $attributes->class('flex min-w-0 items-center gap-2 sm:gap-3') }}>
    <label class="relative min-w-0">
        <span class="sr-only">Firma activă</span>
        <select id="companySelect" data-company-switcher class="ui-toolbar-select">
            @forelse ($companies as $company)
                <option value="{{ $company->id }}" @selected($activeCompany?->id === $company->id)>
                    {{ $company->name }}
                </option>
            @empty
                <option value="">Nicio firmă</option>
            @endforelse
        </select>
        <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-slate-400"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </label>

    @if ($addHref)
        <x-button :href="$addHref" variant="secondary" icon-only aria-label="Adaugă firmă" title="Adaugă firmă">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </x-button>
    @endif

    @isset($meta)
        <div class="hidden items-center gap-2 sm:flex">
            {{ $meta }}
        </div>
    @endisset
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-company-switcher]').forEach(function (select) {
                select.addEventListener('change', function () {
                    if (this.value) {
                        window.location.href = '/company/switch/' + encodeURIComponent(this.value);
                    }
                });
            });
        });
    </script>
@endonce
