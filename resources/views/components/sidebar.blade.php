{{-- Sidebar vertical refolosibil --}}
@php
    $currentUser = $user ?? auth()->user();

    // Every role reaches a different set of routes, so the menu is built per
    // role instead of listing everything and hiding the rest. A link that
    // leads to a 403 is worse than a link that is not there.
    // Heroicons outline paths, kept here so every screen draws the same menu.
    $icons = [
        'dashboard' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'products' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'reports' => 'M9 17v-6h6v6m-9 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'invoices' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z',
        'audit' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    ];

    $links = match ($currentUser->role ?? null) {
        'contabil' => [
            ['label' => 'Dashboard', 'url' => route('dashboard.contabil'), 'match' => 'dashboard.contabil', 'icon' => $icons['dashboard']],
            ['label' => 'Rapoarte', 'url' => route('dashboard.contabil.reports.index'), 'match' => 'dashboard.contabil.reports.*', 'icon' => $icons['reports']],
            ['label' => 'Facturi', 'url' => route('dashboard.contabil.invoices'), 'match' => 'dashboard.contabil.invoices', 'icon' => $icons['invoices']],
            // read-only by NFR, but the catalogue is what he audits invoices against
            ['label' => 'Produse', 'url' => route('products.index'), 'match' => 'products.*', 'icon' => $icons['products']],
            ['label' => 'Jurnal de audit', 'url' => route('audit-log.index'), 'match' => 'audit-log.*', 'icon' => $icons['audit']],
        ],
        'operator' => [
            ['label' => 'Dashboard', 'url' => route('dashboard.operator'), 'match' => 'dashboard.operator', 'icon' => $icons['dashboard']],
            ['label' => 'Produse', 'url' => route('products.index'), 'match' => 'products.*', 'icon' => $icons['products']],
        ],
        default => [
            ['label' => 'Dashboard', 'url' => route('dashboard.administrator'), 'match' => 'dashboard.administrator', 'icon' => $icons['dashboard']],
            ['label' => 'Produse', 'url' => route('products.index'), 'match' => 'products.*', 'icon' => $icons['products']],
            ['label' => 'Jurnal de audit', 'url' => route('audit-log.index'), 'match' => 'audit-log.*', 'icon' => $icons['audit']],
        ],
    };
@endphp

<aside class="hidden lg:flex w-64 shrink-0 flex-col border-r border-slate-200 bg-white sticky top-0 h-screen self-start overflow-y-auto">
    <div class="flex items-center gap-2 px-6 h-16 border-b border-slate-200">
        <div class="grid place-items-center w-9 h-9 rounded-lg bg-indigo-600 text-white font-bold">B</div>
        <span class="font-semibold text-lg">BFMS</span>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        @foreach ($links as $link)
            @php $isActive = request()->routeIs($link['match']); @endphp
            <a href="{{ $link['url'] }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 {{ $isActive ? 'text-indigo-600' : 'text-slate-400' }}" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}" />
                </svg>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- setarile sunt rezervate administratorului, restul ar primi 403 --}}
    @if (($currentUser->role ?? null) === 'administrator')
    <div class="px-3 pb-2">
    <a href="{{ route('administrator.settings.company') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('administrator.settings.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('administrator.settings.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Setări
    </a>
</div>
    @endif

    @if ($currentUser)
    <div class="p-4 border-t border-slate-200 space-y-3">
        <div class="flex items-center gap-3">
            <div class="grid place-items-center w-9 h-9 rounded-full bg-slate-200 text-slate-600 font-semibold text-sm">{{ Str::substr($currentUser->first_name ?? $currentUser['first_name'] ?? 'U', 0, 1) }}{{ Str::substr($currentUser->last_name ?? $currentUser['last_name'] ?? 'N', 0, 1) }}</div>
            <div class="min-w-0">
                <p class="text-sm font-medium truncate">{{ $currentUser->first_name ?? $currentUser['first_name'] ?? 'User' }} {{ Str::substr($currentUser->last_name ?? $currentUser['last_name'] ?? 'Name', 0, 1) }}.</p>
                <p class="text-xs text-slate-500">{{ $currentUser->role ?? $currentUser['role'] ?? 'Operator' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg transition text-left">
                Deconectare
            </button>
        </form>
    </div>
    @endif
</aside>
