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
            <x-company-switcher :companies="$companies" :active-company="$company" :add-href="route('administrator.settings.addcompany')">
                <x-slot:meta>
                    <x-role-badge :role="auth()->user()->role" />
                </x-slot:meta>
            </x-company-switcher>
        </header>

        {{-- Content --}}
        <main class="app-page-content space-y-6">

            <x-page-header :title="'Bună, ' . $user->first_name" :description="'Iată situația firmei ' . $companyName . ':'">
                <x-slot:actions>
                    <x-button :href="route('invoices.create')">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Factură nouă
                    </x-button>
                </x-slot:actions>
            </x-page-header>

            @if (session('success'))
                <div class="ui-alert ui-alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Invoices --}}
            <div class="grid grid-cols-1 gap-6">

                {{-- Recent Invoices --}}
                <div>
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
                                <thead class="sticky top-0 z-10">
                                    <tr>
                                        <th>Nr.</th>
                                        <th>Client</th>
                                        <th>Valoare</th>
                                        <th>Status</th>
                                        <th class="text-right">Detalii</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($invoices as $inv)
                                        <tr>
                                            <td class="font-medium text-slate-900 dark:text-slate-100">
                                                {{ $inv->number ? $inv->series . '-' . $inv->number : '—' }}
                                            </td>
                                            <td class="text-slate-600 dark:text-slate-300">
                                                {{ $inv->client?->full_name ?? '—' }}</td>
                                            <td class="text-slate-900 dark:text-slate-100">
                                                {{ number_format($inv->total, 2, ',', '.') }} {{ $inv->currency }}
                                            </td>
                                            <td>
                                                <x-invoice-status-badge :status="$inv->status" />
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('invoices.show', $inv) }}"
                                                    class="ui-action-link">Vezi</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="ui-empty-state">
                                                Nicio factură înregistrată.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="border-t border-app-border px-5 py-4">
                            <a href="{{ route('invoices.index') }}" class="ui-action-link">Vezi mai multe</a>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </x-app-shell>

</body>

</html>
