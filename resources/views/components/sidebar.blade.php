{{-- Sidebar vertical refolosibil pentru admin pages --}}
@php
    $currentUser = $user ?? auth()->user();
@endphp

<aside class="hidden lg:flex w-64 shrink-0 flex-col border-r border-slate-200 bg-white sticky top-0 h-screen self-start overflow-y-auto">
    <div class="flex items-center gap-2 px-6 h-16 border-b border-slate-200">
        <div class="grid place-items-center w-9 h-9 rounded-lg bg-indigo-600 text-white font-bold">B</div>
        <span class="font-semibold text-lg">BFMS</span>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <a href="{{ url('/dashboard/administrator') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard.administrator') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="w-2 h-2 rounded-full {{ request()->routeIs('dashboard.administrator') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
            Dashboard
        </a>
        <a href="{{ route('products.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="w-2 h-2 rounded-full {{ request()->routeIs('products.*') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
            Produse
        </a>
    </nav>

    <div class="px-3 pb-2">
    <a href="{{ route('administrator.settings.company') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('administrator.settings.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
        <svg class="w-4 h-4 {{ request()->routeIs('administrator.settings.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Setări
    </a>
</div>

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
