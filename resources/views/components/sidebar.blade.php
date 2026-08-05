@php
    $currentUser = $user ?? auth()->user();

    $icons = [
        'dashboard' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'clients' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z',
        'products' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'reports' => 'M9 17v-6h6v6m-9 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'invoices' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z',
        'audit' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'settings' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        'logout' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
        'clients' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z',
    ];

    $links = match ($currentUser->role ?? null) {
        'contabil' => [
            ['label' => 'Dashboard', 'url' => route('dashboard.contabil'), 'match' => 'dashboard.contabil', 'icon' => $icons['dashboard']],
            ['label' => 'Rapoarte', 'url' => route('dashboard.contabil.reports.index'), 'match' => 'dashboard.contabil.reports.*', 'icon' => $icons['reports']],
            ['label' => 'Facturi', 'url' => route('dashboard.contabil.invoices'), 'match' => 'dashboard.contabil.invoices', 'icon' => $icons['invoices']],
            ['label' => 'Clienți', 'url' => route('clients.index'), 'match' => 'clients.*', 'icon' => $icons['clients']],
            ['label' => 'Produse', 'url' => route('products.index'), 'match' => 'products.*', 'icon' => $icons['products']],
            ['label' => 'Jurnal de audit', 'url' => route('audit-log.index'), 'match' => 'audit-log.*', 'icon' => $icons['audit']],
        ],
        'operator' => [
            ['label' => 'Dashboard', 'url' => route('operator.dashboard'), 'match' => 'operator.dashboard', 'icon' => $icons['dashboard']],
            ['label' => 'Facturi', 'url' => route('invoices.index'), 'match' => 'invoices.*', 'icon' => $icons['invoices']],
            ['label' => 'Clienți', 'url' => route('clients.index'), 'match' => 'clients.*', 'icon' => $icons['clients']],
            ['label' => 'Produse', 'url' => route('products.index'), 'match' => 'products.*', 'icon' => $icons['products']],
        ],
        default => [
            ['label' => 'Dashboard', 'url' => route('dashboard.administrator'), 'match' => 'dashboard.administrator', 'icon' => $icons['dashboard']],
            ['label' => 'Clienți', 'url' => route('clients.index'), 'match' => 'clients.*', 'icon' => $icons['clients']],
            ['label' => 'Facturi', 'url' => route('invoices.index'), 'match' => 'invoices.*', 'icon' => $icons['invoices']],
            ['label' => 'Produse', 'url' => route('products.index'), 'match' => 'products.*', 'icon' => $icons['products']],
            ['label' => 'Jurnal de audit', 'url' => route('audit-log.index'), 'match' => 'audit-log.*', 'icon' => $icons['audit']],
        ],
    };

    $initials = mb_strtoupper(
        mb_substr($currentUser->first_name ?? 'U', 0, 1)
        . mb_substr($currentUser->last_name ?? 'N', 0, 1)
    );
@endphp

{{-- Desktop and tablet navigation. Tablet uses an icon rail; xl expands it. --}}
<aside class="app-sidebar" aria-label="Navigație principală">
    <div class="flex h-16 shrink-0 items-center justify-center border-b border-app-border px-3 xl:justify-start xl:px-5">
        <x-brand-mark class="size-10" />
        <p class="ml-3 hidden min-w-0 font-display text-lg font-bold tracking-wide text-ink-950 dark:text-slate-100 xl:block">BFMS</p>
    </div>

    <nav class="flex-1 space-y-1 px-2 py-4 text-sm xl:px-3">
        @foreach ($links as $link)
            @php $isActive = request()->routeIs($link['match']); @endphp
            <a href="{{ $link['url'] }}"
               aria-label="{{ $link['label'] }}"
               title="{{ $link['label'] }}"
               @if ($isActive) aria-current="page" @endif
               class="group flex min-h-11 items-center justify-center gap-3 rounded-xl px-3 transition-colors xl:justify-start {{ $isActive ? 'bg-brand-50 font-semibold text-brand-800 dark:bg-brand-950/50 dark:text-brand-200' : 'text-slate-600 hover:bg-slate-50 hover:text-ink-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                <svg class="size-5 shrink-0 {{ $isActive ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}" />
                </svg>
                <span class="hidden truncate xl:inline">{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>

    @if (($currentUser->role ?? null) === 'administrator')
        <div class="border-t border-app-border px-2 py-2 xl:px-3">
            <a href="{{ route('administrator.settings.company') }}"
               aria-label="Setări"
               title="Setări"
               @if (request()->routeIs('administrator.settings.*') || request()->routeIs('administrator.*')) aria-current="page" @endif
               class="group flex min-h-11 items-center justify-center gap-3 rounded-xl px-3 transition-colors xl:justify-start {{ request()->routeIs('administrator.settings.*') || request()->routeIs('administrator.*') ? 'bg-brand-50 font-semibold text-brand-800 dark:bg-brand-950/50 dark:text-brand-200' : 'text-slate-600 hover:bg-slate-50 hover:text-ink-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                <svg class="size-5 shrink-0 text-slate-400 group-hover:text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['settings'] }}" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="hidden xl:inline">Setări</span>
            </a>
        </div>
    @endif

    @if ($currentUser)
        <div class="border-t border-app-border p-2 xl:p-4">
            <div class="flex items-center justify-center gap-3 xl:justify-start">
                <div class="grid size-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-800">{{ $initials }}</div>
                <div class="hidden min-w-0 flex-1 xl:block">
                    <p class="truncate text-sm font-semibold text-ink-950 dark:text-slate-100">{{ $currentUser->first_name }} {{ mb_substr($currentUser->last_name ?? '', 0, 1) }}.</p>
                    <p class="truncate text-xs capitalize text-slate-500 dark:text-slate-400">{{ $currentUser->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" aria-label="Deconectare" title="Deconectare"
                        class="flex min-h-11 w-full items-center justify-center gap-3 rounded-xl px-3 text-sm font-medium text-slate-600 dark:text-slate-300 transition-colors hover:bg-slate-50 hover:text-ink-950 xl:justify-start">
                    <svg class="size-5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['logout'] }}" />
                    </svg>
                    <span class="hidden xl:inline">Deconectare</span>
                </button>
            </form>
        </div>
    @endif
</aside>

{{-- Mobile drawer. Its trigger lives in x-app-shell. --}}
<div data-mobile-navigation class="fixed inset-0 z-50 hidden md:hidden" aria-hidden="true">
    <button type="button" data-navigation-backdrop class="absolute inset-0 bg-slate-950/35 backdrop-blur-[1px]" aria-label="Închide navigația"></button>

    <aside id="mobile-navigation" class="relative flex h-full w-[min(20rem,88vw)] flex-col bg-white dark:bg-slate-900 shadow-2xl" aria-label="Navigație mobilă">
        <div class="flex h-16 items-center justify-between border-b border-app-border px-4">
            <div class="flex items-center gap-3">
                <x-brand-mark class="size-10" />
                <p class="font-display text-lg font-bold tracking-wide text-ink-950 dark:text-slate-100">BFMS</p>
            </div>
            <button type="button" data-navigation-close class="ui-btn ui-btn-ghost ui-btn-icon" aria-label="Închide meniul">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm">
            @foreach ($links as $link)
                @php $isActive = request()->routeIs($link['match']); @endphp
                <a href="{{ $link['url'] }}"
                   @if ($isActive) aria-current="page" @endif
                   class="flex min-h-11 items-center gap-3 rounded-xl px-3 {{ $isActive ? 'bg-brand-50 font-semibold text-brand-800 dark:bg-brand-950/50 dark:text-brand-200' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    <svg class="size-5 shrink-0 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}" />
                    </svg>
                    {{ $link['label'] }}
                </a>
            @endforeach

            @if (($currentUser->role ?? null) === 'administrator')
                <a href="{{ route('administrator.settings.company') }}"
                   class="flex min-h-11 items-center gap-3 rounded-xl px-3 {{ request()->routeIs('administrator.*') ? 'bg-brand-50 font-semibold text-brand-800 dark:bg-brand-950/50 dark:text-brand-200' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    <svg class="size-5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['settings'] }}" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Setări
                </a>
            @endif
        </nav>

        @if ($currentUser)
            <div class="border-t border-app-border p-4">
                <div class="mb-3 flex items-center gap-3">
                    <div class="grid size-10 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-800">{{ $initials }}</div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-ink-950 dark:text-slate-100">{{ $currentUser->first_name }} {{ $currentUser->last_name }}</p>
                        <p class="text-xs capitalize text-slate-500 dark:text-slate-400">{{ $currentUser->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="ui-btn ui-btn-secondary w-full">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['logout'] }}" />
                        </svg>
                        Deconectare
                    </button>
                </form>
            </div>
        @endif
    </aside>
</div>
