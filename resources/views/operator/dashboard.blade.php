<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

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
            <header class="app-page-toolbar">
                {{-- Company Select Label (read-only pentru operator) --}}
                <div class="flex items-center gap-3">
                    <label class="relative">
                        <select class="ui-toolbar-select" disabled>
                            <option>{{ $company?->name ?? 'Fără firmă' }}</option>
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                    @if ($company?->vat_payer)
                        <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">Plătitor TVA</span>
                    @endif
                    <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium">Operator</span>
                </div>
            </header>

            {{-- Content --}}
            <main class="app-page-content space-y-6">

                <x-page-header :title="'Bună, '.$user->first_name"
                               :description="$company ? 'Iată situația firmei '.$company->name.':' : 'Nu ai nicio firmă asociată contului.'" />

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
                        <p class="ui-stat-value">{{ $stats['invoices_month'] }}</p>
                    </div>
                    <div class="ui-stat-card">
                        <p class="ui-stat-label">Restante</p>
                        <p class="ui-stat-value text-rose-600">{{ $stats['overdue'] }}</p>
                    </div>
                    <div class="ui-stat-card">
                        <p class="ui-stat-label">Clienți activi</p>
                        <p class="ui-stat-value">{{ $stats['clients'] }}</p>
                    </div>
                    <div class="ui-stat-card">
                        <p class="ui-stat-label">Produse active</p>
                        <p class="ui-stat-value">{{ $stats['products'] }}</p>
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
                                        @forelse ($invoices as $invoice)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-5 py-3 font-medium text-slate-900">
                                                    {{ $invoice->number ? $invoice->series.'-'.$invoice->number : 'Ciornă' }}
                                                </td>
                                                <td class="px-5 py-3 text-slate-600">{{ $invoice->client?->full_name ?? '—' }}</td>
                                                <td class="px-5 py-3 text-slate-900">
                                                    {{ number_format((float) $invoice->total, 2, ',', '.') }} {{ $invoice->currency }}
                                                </td>
                                                <td class="px-5 py-3">
                                                    <span class="text-xs px-2 py-1 rounded-full font-medium {{ $invoice->status->badgeClasses() }}">
                                                        {{ $invoice->status->label() }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-3 text-right">
                                                    <a href="{{ route('invoices.show', $invoice) }}" class="ui-action-link">Vezi</a>
                                                    {{-- Operatorul nu poate șterge facturi --}}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                                    Nu există facturi încă.
                                                </td>
                                            </tr>
                                        @endforelse
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
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header">
                                <h2 class="ui-section-title">Plăți recente</h2>
                            </div>
                            <ul class="divide-y divide-slate-100 text-sm">
                                @forelse ($payments as $payment)
                                    <li class="px-5 py-3 flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-slate-900">{{ $payment->invoice?->client?->full_name ?? '—' }}</p>
                                            <p class="text-xs text-slate-500">{{ $payment->payment_date?->translatedFormat('d M') }}</p>
                                        </div>
                                        <span class="font-semibold text-emerald-600">
                                            {{ number_format((float) $payment->amount, 2, ',', '.') }} {{ $payment->currency }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="px-5 py-8 text-center text-slate-500">Nu există plăți încă.</li>
                                @endforelse
                            </ul>

                        </div>
                    </div>

                </div>

            </main>
    </x-app-shell>

</body>
</html>
