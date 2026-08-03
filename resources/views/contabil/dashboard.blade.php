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

        <x-sidebar :user="$user" />

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top Bar --}}
            <header class="flex items-center gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
                {{-- Company Select Label (fara buton de adaugare, doar vizualizare) --}}
                <div class="flex items-center gap-3">
                    <label class="relative">
                        <select id="companySelect"
                            class="appearance-none pl-3 pr-9 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}" {{ $company?->id === $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </label>
                    <span
                        class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">Plătitor
                        TVA</span>
                    <span
                        class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium">Doar
                        vizualizare</span>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 space-y-6">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Bună, {{ $user->first_name }}</h1>
                    <p class="text-slate-500 text-sm mt-1">Situația firmei {{ $companyName }}:</p>
                </div>

                {{-- Rapoarte --}}
                <div>
                    <h2 class="font-semibold text-slate-900 mb-3">Rapoarte</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Fisa Client --}}
                        <div class="bg-white rounded-xl border border-slate-200 p-2">
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
                                        class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        @foreach ($clients ?? [] as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <div class="flex gap-2 pt-3">
                                    <button type="submit" name="format" value="pdf"
                                        class="flex-1 px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        PDF
                                    </button>
                                    <button type="submit" name="format" value="xlsx"
                                        class="flex-1 px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        Excel
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Inchidere Luna --}}
                        <div class="bg-white rounded-xl border border-slate-200 p-2">
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
                                        class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </label>

                                <div class="flex gap-2 pt-3">
                                    <button type="submit" name="format" value="pdf"
                                        class="flex-1 px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        PDF
                                    </button>
                                    <button type="submit" name="format" value="xlsx"
                                        class="flex-1 px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">
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
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-3 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h2 class="font-semibold text-slate-900">Facturi recente</h2>
                                <label class="relative">
                                    <select
                                        class="appearance-none pl-3 pr-8 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option>Toate stările</option>
                                        <option>Emisă</option>
                                        <option>Încasată parțial</option>
                                        <option>Încasată total</option>
                                        <option>Stornată</option>
                                    </select>
                                    <svg class="w-3.5 h-3.5 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </label>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-slate-500 border-b border-slate-100">
                                            <th class="px-3 py-2 font-medium">Nr.</th>
                                            <th class="px-3 py-2 font-medium">Client</th>
                                            <th class="px-3 py-2 font-medium">Valoare</th>
                                            <th class="px-3 py-2 font-medium">Status</th>
                                            <th class="px-3 py-2 font-medium text-right">Detalii</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse ($invoices as $inv)
                                            <tr class="hover:bg-slate-50">
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
                                                        class="text-indigo-600 hover:underline text-sm">Vezi</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-3 py-8 text-center text-slate-400">
                                                    Nicio factură înregistrată.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-3 py-3 border-t border-slate-200">
                                <a href="{{ route('dashboard.contabil.invoices') }}"
                                    class="text-sm text-indigo-600 hover:underline">Vezi toate</a>
                            </div>
                        </div>
                    </div>

                    {{-- Jurnal de audit (preview) --}}
                    <div class="space-y-4">
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Jurnal de audit</h2>
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
                                    class="text-sm text-indigo-600 hover:underline">Vezi jurnalul complet</a>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        document.getElementById('companySelect').addEventListener('change', function() {
            const companyId = this.value;
            window.location.href = `/company/switch/${companyId}`;
        });
    </script>
</body>

</html>
