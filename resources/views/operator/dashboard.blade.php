@php
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
<body>

    <x-app-shell>

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
                {{-- Company Select Label (read-only pentru operator) --}}
                <div class="flex items-center gap-3">
                    <label class="relative">
                        <select class="ui-toolbar-select" disabled>
                            <option>{{ $company->name }}</option>
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                    <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">Plătitor TVA</span>
                    <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium">Operator</span>
                </div>
            </header>

            {{-- Content --}}
            <main class="app-page-content space-y-6">

                <x-page-header :title="'Bună, '.$user->first_name"
                               :description="'Iată situația firmei '.$company->name.':'" />

                {{-- Acțiuni rapide --}}
                <div class="ui-button-group">
                    <a href="{{ route('invoices.create') }}" class="ui-btn ui-btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Factură nouă
                    </a>
                    <a href="{{ route('invoices.index') }}" class="ui-btn ui-btn-secondary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Înregistrează plată
                    </a>
                    <a href="{{ route('products.create') }}" class="ui-btn ui-btn-secondary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Produs nou
                    </a>
                </div>

                {{-- Rezumat --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="ui-stat-card">
                        <p class="ui-stat-label">Facturi luna asta</p>
                        <p class="ui-stat-value">{{ $stats['invoices_month'] ?? 18 }}</p>
                    </div>
                    <div class="ui-stat-card">
                        <p class="ui-stat-label">Restante</p>
                        <p class="ui-stat-value text-rose-600">{{ $stats['overdue'] ?? 3 }}</p>
                    </div>
                    <div class="ui-stat-card">
                        <p class="ui-stat-label">Clienți activi</p>
                        <p class="ui-stat-value">{{ $stats['clients'] ?? 42 }}</p>
                    </div>
                    <div class="ui-stat-card">
                        <p class="ui-stat-label">Produse active</p>
                        <p class="ui-stat-value">{{ $stats['products'] ?? 27 }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    {{-- Recent Invoices --}}
                    <div class="xl:col-span-2 space-y-4">
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header">
                                <h2 class="ui-section-title">Facturi recente</h2>
                            </div>
                            <div class="ui-table-wrap" tabindex="0" role="region" aria-label="Facturi recente">
                                <table class="ui-table">
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
                                                    <a href="{{ route('invoices.index') }}" class="ui-action-link">Vezi</a>
                                                    {{-- Operatorul nu poate șterge facturi --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-5 py-4 border-t border-slate-200">
                                <a href="{{ route('invoices.index') }}" class="ui-action-link">Vezi toate</a>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Payments --}}
                    <div class="space-y-4">
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header">
                                <h2 class="ui-section-title">Plăți recente</h2>
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
                            <div class="px-5 py-4 border-t border-slate-200">
                                <a href="{{ route('invoices.index') }}" class="ui-action-link">Vezi facturile</a>
                            </div>
                        </div>
                    </div>

                </div>

            </main>
    </x-app-shell>

</body>
</html>
