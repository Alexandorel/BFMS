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
            </nav>

            <div class="px-3 pb-2">
                <a href="{{ route('administrator.settings.team') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 text-sm hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Setări
                </a>
            </div>

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
            <header class="flex items-center gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
                {{-- Company Select Label --}}
                <div class="flex items-center gap-3">
                    <label class="relative">
                        <select id="companySelect" class="appearance-none pl-3 pr-9 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}" {{ $company?->id === $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                    <button type="button" class="p-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-700 transition" title="Adaugă firmă">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">Plătitor TVA</span>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 space-y-6">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Bună, {{ $user->first_name }}</h1>
                    <p class="text-slate-500 text-sm mt-1">Iată situația firmei {{ $companyName }}:</p>
                </div>

                {{-- Invoices --}}
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    {{-- Recent Invoices --}}
                    <div class="xl:col-span-2 space-y-4">
                        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                            Factură nouă
                        </a>

                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Facturi recente</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-slate-500 border-b border-slate-100">
                                            <th class="px-5 py-3 font-medium">Nr.</th>
                                            <th class="px-5 py-3 font-medium">Client</th>
                                            <th class="px-5 py-3 font-medium">Valoare</th>
                                            <th class="px-5 py-3 font-medium">Status</th>
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-5 py-4 border-t border-slate-200">
                                <a href="#" class="text-sm text-indigo-600 hover:underline">Vezi toate</a>
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
