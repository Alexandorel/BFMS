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
                <x-company-switcher :companies="$companies" :active-company="$company"
                                    :add-href="route('administrator.settings.addcompany')">
                    <x-slot:meta>
                        <span class="ui-badge bg-emerald-50 text-emerald-700">Plătitor TVA</span>
                    </x-slot:meta>
                </x-company-switcher>
            </header>

            {{-- Content --}}
            <main class="app-page-content space-y-6">

                <x-page-header :title="'Bună, '.$user->first_name"
                               :description="'Iată situația firmei '.$companyName.':'">
                    <x-slot:actions>
                        <x-button :href="route('invoices.create')">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
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
                            <div id="invoices-scroll"
                                class="ui-table-wrap overflow-y-auto transition-[max-height] duration-300 ease-out"
                                tabindex="0" role="region" aria-label="Facturi recente"
                                style="max-height: 17rem;">
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
                                                <td class="font-medium text-slate-900">
                                                    {{ $inv->number ? $inv->series . '-' . $inv->number : '—' }}
                                                </td>
                                                <td class="text-slate-600">{{ $inv->client?->full_name ?? '—' }}</td>
                                                <td class="text-slate-900">
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
                            @if ($invoices->count() > 5)
                                <div class="px-5 py-4 border-t border-slate-200">
                                    <button type="button" id="invoices-toggle"
                                        class="ui-action-link" aria-expanded="false"
                                        aria-controls="invoices-scroll">Vezi toate</button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Jurnal de audit — preview, ecranul complet e la audit-log.index (F-101) --}}
                    <div class="ui-card self-start overflow-hidden">
                        <div class="ui-card-header">
                            <h2 class="ui-section-title">Activitate recentă</h2>
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

            </main>
    </x-app-shell>

<script>
    // extend/ shorten invoices list
    const invoicesToggle = document.getElementById('invoices-toggle');
    const invoicesScroll = document.getElementById('invoices-scroll');

    if (invoicesToggle && invoicesScroll) {
        const COLLAPSED = '17rem';
        const EXPANDED = '32rem';

        invoicesToggle.addEventListener('click', function() {
            const expanded = invoicesScroll.style.maxHeight === EXPANDED;

            invoicesScroll.style.maxHeight = expanded ? COLLAPSED : EXPANDED;
            invoicesToggle.textContent = expanded ? 'Vezi toate' : 'Vezi mai puține';
            invoicesToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');

            if (expanded) {
                invoicesScroll.scrollTop = 0;
            }
        });
    }
</script>
</body>
</html>
