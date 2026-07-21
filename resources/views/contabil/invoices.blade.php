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
                                        @php
                                            $facturi = [
                                                [
                                                    'nr' => 'F-0142',
                                                    'client' => 'Alpha Tech SRL',
                                                    'val' => '4.760 RON',
                                                    'status' => 'platita',
                                                ],
                                                [
                                                    'nr' => 'F-0141',
                                                    'client' => 'Beta Media SA',
                                                    'val' => '2.100 RON',
                                                    'status' => 'trimisa',
                                                ],
                                                [
                                                    'nr' => 'F-0140',
                                                    'client' => 'Gamma Retail SRL',
                                                    'val' => '8.900 RON',
                                                    'status' => 'restanta',
                                                ],
                                                [
                                                    'nr' => 'F-0138',
                                                    'client' => 'Omega Design',
                                                    'val' => '3.400 RON',
                                                    'status' => 'platita',
                                                ],
                                            ];
                                            $badge = [
                                                'platita' => ['Încasată total', 'bg-emerald-50 text-emerald-700'],
                                                'trimisa' => ['Emisă', 'bg-sky-50 text-sky-700'],
                                                'restanta' => ['Încasată parțial', 'bg-amber-50 text-amber-700'],
                                            ];
                                        @endphp
                                        @foreach ($facturi as $f)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-3 py-2 font-medium text-slate-900">{{ $f['nr'] }}
                                                </td>
                                                <td class="px-3 py-2 text-slate-600">{{ $f['client'] }}</td>
                                                <td class="px-3 py-2 text-slate-900">{{ $f['val'] }}</td>
                                                <td class="px-3 py-2">
                                                    <span
                                                        class="text-xs px-2 py-1 rounded-full font-medium {{ $badge[$f['status']][1] }}">
                                                        {{ $badge[$f['status']][0] }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-right">
                                                    <a href="#"
                                                        class="text-indigo-600 hover:underline text-sm">Vezi</a>
                                                </td>
                                            </tr>
                                        @endforeach
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