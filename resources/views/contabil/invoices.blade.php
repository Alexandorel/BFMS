<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facturi · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    <x-app-shell :user="$user">

        <header class="app-page-toolbar">
            <x-company-switcher :companies="$companies" :active-company="$company">
            </x-company-switcher>
        </header>

        <main class="app-page-content space-y-6">
            <main class="app-page-content space-y-6">
                <x-page-header title="Facturi" description="Documentele firmei active">
                    @if (in_array(auth()->user()->role, ['administrator', 'operator'], true))
                        <x-slot:actions>
                            <x-button :href="route('invoices.create')">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Factură nouă
                            </x-button>
                        </x-slot:actions>
                    @endif
                </x-page-header>

                <section class="ui-card overflow-hidden">
                    <div class="ui-card-header flex-col items-stretch sm:flex-row sm:items-center">
                        <div>
                            <h2 class="ui-section-title">
                                {{ $paymentMode ? 'Facturi eligibile pentru plată' : 'Toate facturile' }}
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $invoices->count() }}
                                documente</p>
                        </div>
                        <x-invoice-status-filter target="invoice-list" :statuses="$filterStatuses" :all-label="$paymentMode ? 'Toate facturile eligibile' : 'Toate stările'"
                            :disabled="$invoices->isEmpty()" />
                    </div>

                    <div class="ui-table-wrap" tabindex="0" role="region" aria-label="Lista facturilor">
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
                            <tbody id="invoice-list" class="divide-y divide-slate-100">
                                @forelse ($invoices as $invoice)
                                    <tr data-invoice-status="{{ $invoice->status->value }}">
                                        <td class="whitespace-nowrap font-semibold text-ink-950 dark:text-slate-100">
                                            {{ $invoice->number ? $invoice->series . '-' . $invoice->number : '—' }}
                                        </td>
                                        <td class="text-slate-600 dark:text-slate-300">
                                            {{ $invoice->client?->full_name ?? '—' }}</td>
                                        <td class="whitespace-nowrap font-medium text-ink-950 dark:text-slate-100">
                                            {{ number_format($invoice->total, 2, ',', '.') }} {{ $invoice->currency }}
                                        </td>
                                        <td><x-invoice-status-badge :status="$invoice->status" /></td>
                                        <td class="text-right">
                                            <a href="{{ route('invoices.show', $invoice) }}"
                                                class="ui-action-link">Vezi</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="ui-empty-state">Nicio factură înregistrată.</td>
                                    </tr>
                                @endforelse
                                <tr data-filter-empty class="hidden">
                                    <td colspan="5" class="ui-empty-state">Nicio factură cu starea selectată.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
    </x-app-shell>
</body>

</html>
