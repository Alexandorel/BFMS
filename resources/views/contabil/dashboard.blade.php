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
                        <span class="ui-badge bg-emerald-50 text-emerald-700">Plătitor TVA</span>
                        <span class="ui-badge bg-slate-100 text-slate-600">Doar vizualizare</span>
                    </x-slot:meta>
                </x-company-switcher>
            </header>

            {{-- Content --}}
            <main class="app-page-content space-y-6">

                <x-page-header :title="'Bună, '.$user->first_name"
                               :description="'Situația firmei '.$companyName.':'" />

                {{-- Rapoarte --}}
                <div>
                    <h2 class="font-semibold text-slate-900 mb-3">Rapoarte</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Fisa Client --}}
                        <div class="ui-card p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-slate-900">Fișă client</h3>
                                    <p class="text-sm text-slate-500 mt-1">Total facturat, plătit, sold de încasat și
                                        istoric per client.</p>
                                </div>
                                <svg class="w-7 h-7 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>

                            <form action="{{ route('dashboard.contabil.reports.client-sheet') }}" class="mt-3">
                                <label class="block">
                                    <span class="text-xs font-medium text-slate-500">Client</span>
                                    <select name="client_id"
                                        class="form-input mt-1">
                                        @foreach ($clients ?? [] as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <div class="grid grid-cols-2 gap-2 pt-3">
                                    <button type="submit" name="format" value="pdf"
                                        class="ui-btn ui-btn-secondary w-full">
                                        PDF
                                    </button>
                                    <button type="submit" name="format" value="xlsx"
                                        class="ui-btn ui-btn-primary w-full">
                                        Excel
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Inchidere Luna --}}
                        <div class="ui-card p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-slate-900">Închidere lună</h3>
                                    <p class="text-sm text-slate-500 mt-1">Încasări lunare, sume rămase de încasat și
                                        defalcare pe cote de TVA.</p>
                                </div>
                                <svg class="w-7 h-7 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <form action="{{ route('dashboard.contabil.reports.month-close') }}" method="GET"
                                class="mt-3">
                                <label class="block">
                                    <span class="text-xs font-medium text-slate-500">Luna</span>
                                    <input type="month" name="month"
                                        class="form-input mt-1">
                                </label>

                                <div class="grid grid-cols-2 gap-2 pt-3">
                                    <button type="submit" name="format" value="pdf"
                                        class="ui-btn ui-btn-secondary w-full">
                                        PDF
                                    </button>
                                    <button type="submit" name="format" value="xlsx"
                                        class="ui-btn ui-btn-primary w-full">
                                        Excel
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- Facturi (read-only) --}}
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="xl:col-span-2 space-y-4">
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header">
                                <h2 class="ui-section-title">Facturi recente</h2>
                                <x-invoice-status-filter target="recent-invoice-list" compact :disabled="$invoices->isEmpty()" />
                            </div>
                            <div class="ui-table-wrap" tabindex="0" role="region" aria-label="Facturi recente">
                                <table class="ui-table">
                                    <thead>
                                        <tr class="text-left text-slate-500 border-b border-slate-100">
                                            <th class="px-3 py-2 font-medium">Nr.</th>
                                            <th class="px-3 py-2 font-medium">Client</th>
                                            <th class="px-3 py-2 font-medium">Valoare</th>
                                            <th class="px-3 py-2 font-medium">Status</th>
                                            <th class="px-3 py-2 font-medium text-right">Detalii</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-invoice-list" class="divide-y divide-slate-100">
                                        @forelse ($invoices as $inv)
                                            <tr data-invoice-status="{{ $inv->status->value }}">
                                                <td class="px-3 py-2 font-medium text-slate-900">
                                                    {{ $inv->number ? $inv->series . '-' . $inv->number : '—' }}
                                                </td>
                                                <td class="px-3 py-2 text-slate-600">{{ $inv->client?->full_name ?? '—' }}</td>
                                                <td class="px-3 py-2 text-slate-900">
                                                    {{ number_format($inv->total, 2, ',', '.') }} {{ $inv->currency }}
                                                </td>
                                                <td class="px-3 py-2">
                                                    <x-invoice-status-badge :status="$inv->status" />
                                                </td>
                                                <td class="px-3 py-2 text-right">
                                                    <a href="{{ route('invoices.show', $inv) }}"
                                                        class="ui-action-link">Vezi</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-3 py-8 text-center text-slate-400">
                                                    Nicio factură înregistrată.
                                                </td>
                                            </tr>
                                        @endforelse
                                        <tr data-filter-empty class="hidden">
                                            <td colspan="5" class="ui-empty-state">Nicio factură cu starea selectată.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-3 py-3 border-t border-slate-200">
                                <a href="{{ route('dashboard.contabil.invoices') }}"
                                    class="ui-action-link">Vezi toate</a>
                            </div>
                        </div>
                    </div>

                    {{-- Jurnal de audit (preview) --}}
                    <div class="space-y-4">
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header">
                                <h2 class="ui-section-title">Jurnal de audit</h2>
                            </div>
                            <ul class="divide-y divide-slate-100 text-sm">
                                @forelse ($audits as $entry)
                                    <li class="px-5 py-3">
                                        <p class="text-slate-800">
                                            <span class="font-medium">
                                                {{ $entry->user ? trim($entry->user->first_name . ' ' . $entry->user->last_name) : 'Sistem' }}
                                            </span>
                                            {{ mb_strtolower($entry->eventLabel()) }}
                                            {{ mb_strtolower($entry->entityLabel()) }}
                                            {{ $entry->entityName() }}
                                        </p>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $entry->created_at?->diffForHumans() }}
                                        </p>
                                    </li>
                                @empty
                                    <li class="px-5 py-3 text-slate-500">
                                        Nicio modificare înregistrată încă.
                                    </li>
                                @endforelse
                            </ul>
                            <div class="px-5 py-4 border-t border-slate-200">
                                <a href="{{ route('audit-log.index') }}"
                                    class="ui-action-link">Vezi jurnalul complet</a>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
    </x-app-shell>

</body>

</html>
