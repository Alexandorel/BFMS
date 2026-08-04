<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    <x-app-shell :user="$user">

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
                <x-company-switcher :companies="$companies" :active-company="$company">
                    <x-slot:meta>
                        @if ($company?->vat_payer)
                            <span class="ui-badge bg-emerald-50 text-emerald-700">Plătitor TVA</span>
                        @endif
                        <span class="ui-badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Operator</span>
                    </x-slot:meta>
                </x-company-switcher>
            </header>

            {{-- Content --}}
            <main class="app-page-content space-y-6">

                <x-page-header :title="'Bună, '.$user->first_name"
                               :description="$company ? 'Iată situația firmei '.$company->name.':' : 'Nu ai nicio firmă asociată contului.'">
                    <x-slot:actions>
                        <x-button :href="route('invoices.create')">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Factură nouă
                        </x-button>
                        <x-button :href="route('invoices.index', ['payment' => 1])" variant="secondary">
                            Înregistrează plată
                        </x-button>
                        <x-button :href="route('products.create')" variant="secondary">
                            Produs nou
                        </x-button>
                    </x-slot:actions>
                </x-page-header>

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
                    <a href="{{ route('clients.index') }}"
                       class="ui-stat-card block transition-colors hover:border-brand-200 hover:bg-brand-50/40">
                        <p class="ui-stat-label">Clienți activi</p>
                        <p class="ui-stat-value">{{ $stats['clients'] }}</p>
                    </a>
                    <div class="ui-stat-card">
                        <p class="ui-stat-label">Produse active</p>
                        <p class="ui-stat-value">{{ $stats['products'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    {{-- Recent Invoices --}}
                    <div class="xl:col-span-2">
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header">
                                <h2 class="ui-section-title">Facturi recente</h2>
                                @if ($invoices->isNotEmpty())
                                    <span class="text-xs text-slate-400">{{ $invoices->count() }}
                                        {{ $invoices->count() === 1 ? 'factură' : 'facturi' }}</span>
                                @endif
                            </div>
                            <div class="ui-table-wrap" tabindex="0" role="region" aria-label="Facturi recente">
                                <table class="ui-table">
                                    <thead>
                                        <tr>
                                            <th>Nr.</th>
                                            <th>Client</th>
                                            <th>Valoare</th>
                                            <th>Status</th>
                                            <th class="text-right">Detalii</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse ($invoices as $invoice)
                                            <tr>
                                                <td class="font-medium text-slate-900 dark:text-slate-100">
                                                    {{ $invoice->number ? $invoice->series.'-'.$invoice->number : 'Ciornă' }}
                                                </td>
                                                <td class="text-slate-600 dark:text-slate-300">{{ $invoice->client?->full_name ?? '—' }}</td>
                                                <td class="text-slate-900 dark:text-slate-100">
                                                    {{ number_format((float) $invoice->total, 2, ',', '.') }} {{ $invoice->currency }}
                                                </td>
                                                <td>
                                                    <x-invoice-status-badge :status="$invoice->status" />
                                                </td>
                                                <td class="text-right">
                                                    <a href="{{ route('invoices.show', $invoice) }}" class="ui-action-link">Vezi</a>
                                                    {{-- Operatorul nu poate șterge facturi --}}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="ui-empty-state">
                                                    Nu există facturi încă.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="border-t border-app-border px-5 py-4">
                                <a href="{{ route('invoices.index') }}" class="ui-action-link">Vezi toate</a>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Payments --}}
                    <div>
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header">
                                <h2 class="ui-section-title">Plăți recente</h2>
                            </div>
                            <ul class="divide-y divide-slate-100 text-sm">
                                @forelse ($payments as $payment)
                                    <li class="flex items-center justify-between px-5 py-3">
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-slate-900 dark:text-slate-100">{{ $payment->invoice?->client?->full_name ?? '—' }}</p>
                                            <p class="mt-0.5 text-xs text-slate-400">{{ $payment->payment_date?->translatedFormat('d M') }}</p>
                                        </div>
                                        <span class="font-semibold text-emerald-600">
                                            {{ number_format((float) $payment->amount, 2, ',', '.') }} {{ $payment->currency }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="ui-empty-state">Nu există plăți încă.</li>
                                @endforelse
                            </ul>
                            <div class="border-t border-app-border px-5 py-4">
                                <a href="{{ route('invoices.index') }}" class="ui-action-link">Vezi facturile</a>
                            </div>
                        </div>
                    </div>

                </div>

            </main>
    </x-app-shell>

</body>
</html>
