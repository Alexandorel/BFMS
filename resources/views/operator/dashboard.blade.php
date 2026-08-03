@php
    // TODO: date hardcodate temporar, pana se conecteaza autentificarea si baza de date reala
    $user = (object) [
        'first_name' => 'Andrei',
        'last_name'  => 'Popescu',
        'role'       => 'Operator',
    ];

    $company = (object) [
        'name' => 'SC Exemplu SRL',
    ];

    $stats = [
        'invoices_month' => 18,
        'overdue'        => 3,
        'clients'        => 42,
        'products'       => 27,
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">

        {{-- Side Bar --}}
        <aside class="hidden lg:flex w-64 flex-col border-r border-slate-200 bg-white">
            <div class="flex items-center gap-2 px-6 h-16 border-b border-slate-200">
                <div class="grid place-items-center w-9 h-9 rounded-lg bg-indigo-600 text-white font-bold">B</div>
                <span class="font-semibold text-lg">BFMS</span>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Dashboard
                </a>
                <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Clienți
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Produse
                </a>
                <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Facturi
                </a>

            </nav>

            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="grid place-items-center w-9 h-9 rounded-full bg-slate-200 text-slate-600 font-semibold text-sm">{{ Str::substr($user->first_name, 0, 1) }}{{ Str::substr($user->last_name, 0, 1) }}</div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">{{ $user->first_name }} {{ Str::substr($user->last_name, 0, 1) }}.</p>
                        <p class="text-xs text-slate-500">{{ $user->role }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top Bar --}}
            <header class="flex items-center gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
                {{-- Company Select Label (read-only pentru operator) --}}
                <div class="flex items-center gap-3">
                    <label class="relative">
                        <select class="appearance-none pl-3 pr-9 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500" disabled>
                            <option>{{ $company->name }}</option>
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                    <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">Plătitor TVA</span>
                    <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium">Operator</span>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 space-y-6">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Bună, {{ $user->first_name }}</h1>
                    <p class="text-slate-500 text-sm mt-1">Iată situația firmei {{ $company->name }}:</p>
                </div>

                {{-- Acțiuni rapide --}}
                <div class="flex flex-wrap gap-3">
                    {{-- TODO: legați aceste butoane la rutele /adauga când vor fi create --}}
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Factură nouă
                    </a>
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Plată nouă
                    </a>
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Client nou
                    </a>
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Produs nou
                    </a>
                </div>

                {{-- Rezumat --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 font-medium">Facturi luna asta</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['invoices_month'] ?? 18 }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 font-medium">Restante</p>
                        <p class="text-2xl font-bold text-rose-600 mt-1">{{ $stats['overdue'] ?? 3 }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 font-medium">Clienți activi</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['clients'] ?? 42 }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 font-medium">Produse active</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['products'] ?? 27 }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    {{-- Recent Invoices --}}
                    <div class="xl:col-span-2 space-y-4">
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Facturi recente</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-slate-500 border-b border-slate-100">
                                            <th class="px-5 py-3 font-medium">Nr.</th>
                                            <th class="px-5 py-3 font-medium">Client</th>
                                            <th class="px-5 py-3 font-medium">Valoare</th>
                                            <th class="px-5 py-3 font-medium">Status</th>
                                            <th class="px-5 py-3 font-medium text-right">Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @php
                                            $facturi = [
                                                ['nr' => 'F-0142', 'client' => 'Alpha Tech SRL',   'val' => '4.760 RON', 'status' => 'platita'],
                                                ['nr' => 'F-0141', 'client' => 'Beta Media SA',    'val' => '2.100 RON', 'status' => 'trimisa'],
                                                ['nr' => 'F-0140', 'client' => 'Gamma Retail SRL', 'val' => '8.900 RON', 'status' => 'restanta'],
                                                ['nr' => 'F-0139', 'client' => 'Delta Prod SRL',   'val' => '1.250 RON', 'status' => 'ciorna'],
                                                ['nr' => 'F-0138', 'client' => 'Omega Design',     'val' => '3.400 RON', 'status' => 'platita'],
                                            ];
                                            $badge = [
                                                'platita'  => ['Plătită',  'bg-emerald-50 text-emerald-700'],
                                                'trimisa'  => ['Trimisă',  'bg-sky-50 text-sky-700'],
                                                'restanta' => ['Restantă', 'bg-rose-50 text-rose-700'],
                                                'ciorna'   => ['Ciornă',   'bg-slate-100 text-slate-600'],
                                            ];
                                        @endphp
                                        @foreach (array_slice($facturi, 0, 5) as $f)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-5 py-3 font-medium text-slate-900">{{ $f['nr'] }}</td>
                                                <td class="px-5 py-3 text-slate-600">{{ $f['client'] }}</td>
                                                <td class="px-5 py-3 text-slate-900">{{ $f['val'] }}</td>
                                                <td class="px-5 py-3">
                                                    <span class="text-xs px-2 py-1 rounded-full font-medium {{ $badge[$f['status']][1] }}">
                                                        {{ $badge[$f['status']][0] }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-3 text-right">
                                                    <a href="#" class="text-indigo-600 hover:underline text-xs font-medium">Vezi</a>
                                                    @if ($f['status'] !== 'platita')
                                                        <a href="#" class="ml-3 text-emerald-600 hover:underline text-xs font-medium">Marchează plătită</a>
                                                    @endif
                                                    {{-- Operatorul nu poate șterge facturi --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-5 py-4 border-t border-slate-200">
                                <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600 hover:underline">Vezi toate</a>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Payments --}}
                    <div class="space-y-4">
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Plăți recente</h2>
                            </div>
                            <ul class="divide-y divide-slate-100 text-sm">
                                @php
                                    $plati = [
                                        ['client' => 'Alpha Tech SRL',   'val' => '4.760 RON', 'data' => '12 iul'],
                                        ['client' => 'Omega Design',     'val' => '3.400 RON', 'data' => '10 iul'],
                                        ['client' => 'Sigma Logistics',  'val' => '1.980 RON', 'data' => '08 iul'],
                                    ];
                                @endphp
                                @foreach ($plati as $p)
                                    <li class="px-5 py-3 flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-slate-900">{{ $p['client'] }}</p>
                                            <p class="text-xs text-slate-500">{{ $p['data'] }}</p>
                                        </div>
                                        <span class="font-semibold text-emerald-600">{{ $p['val'] }}</span>
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>

                </div>

            </main>
        </div>
    </div>

</body>
</html>
