<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-xl font-semibold text-slate-900">Pagina Dashboard Contabil</h1>
            <p class="text-slate

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
                                <a href="{{ route('dashboard.contabil.invoices') }}" class="text-sm text-indigo-600 hover:underline">Vezi toate</a>
                            </div>
                        </div>
                    </div>

</body>
</html>