<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Firmă · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    @php
        // Placeholder pana la modelul Company: firmele la care userul are acces.
        $firme = [
            1 => [
                'name' => 'SC Exemplu SRL',
                'juridical_form' => 'SRL',
                'cui' => '12345678',
                'trade_registry_number' => 'J12/345/2020',
                'county' => 'Cluj',
                'city' => 'Cluj-Napoca',
                'address' => 'Str. Memorandumului nr. 28',
                'social_capital' => '200.00',
                'vat_payer' => true,
            ],
            2 => [
                'name' => 'Demo Consulting SRL',
                'juridical_form' => 'SRL',
                'cui' => '87654321',
                'trade_registry_number' => 'J12/999/2023',
                'county' => 'Bihor',
                'city' => 'Oradea',
                'address' => 'Str. Republicii nr. 5',
                'social_capital' => '1000.00',
                'vat_payer' => false,
            ],
        ];

        $formeJuridice = ['SRL', 'SA', 'PFA', 'II', 'IF', 'SRL-D'];

        // Firma editata acum: din query string, cu fallback pe prima.
        $firmaId = (int) request('firma', array_key_first($firme));
        $firmaId = isset($firme[$firmaId]) ? $firmaId : array_key_first($firme);
        $firma = $firme[$firmaId];
    @endphp

    <div class="flex min-h-screen">

        {{-- Side Bar --}}
        <aside class="hidden lg:flex w-64 flex-col border-r border-slate-200 bg-white">
            <div class="flex items-center gap-2 px-6 h-16 border-b border-slate-200">
                <div class="grid place-items-center w-9 h-9 rounded-lg bg-indigo-600 text-white font-bold">B</div>
                <span class="font-semibold text-lg">BFMS</span>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="{{ url('/dashboard/operator') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-50">
                    <span class="w-2 h-2 rounded-full bg-slate-300"></span> Dashboard
                </a>
            </nav>

            <div class="px-3 pb-2">
                <a href="{{ route('operator.settings.company') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-medium text-sm">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Setări
                </a>
            </div>

            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="grid place-items-center w-9 h-9 rounded-full bg-slate-200 text-slate-600 font-semibold text-sm">AV</div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">Alexandru V.</p>
                        <p class="text-xs text-slate-500">Operator</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top Bar --}}
            <header class="flex items-center gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
                <div class="flex items-center gap-3">
                    <label class="relative">
                        <select class="appearance-none pl-3 pr-9 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($firme as $id => $f)
                                <option @selected($id === $firmaId)>{{ $f['name'] }}</option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                    <button type="button" class="p-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-700 transition" title="Adaugă firmă">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    @if ($firma['vat_payer'])
                        <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">Plătitor TVA</span>
                    @else
                        <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium">Neplătitor TVA</span>
                    @endif
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 space-y-6">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Setări</h1>
                    <p class="text-slate-500 text-sm mt-1">Contul tău și configurările firmei</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Settings sub-nav --}}
                    <nav class="lg:col-span-1 space-y-1 text-sm">
                        <a href="{{ route('operator.settings.profile') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Profil</a>
                        <a href="{{ route('operator.settings.company') }}" class="block px-3 py-2 rounded-lg bg-white border border-slate-200 text-indigo-700 font-medium">Firmă</a>
                        <a href="{{ route('operator.settings.team') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Echipă</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Conturi bancare</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Serii documente</a>
                    </nav>

                    <div class="lg:col-span-3 space-y-6">

                        {{-- Alegerea firmei editate --}}
                        <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
                            <form action="{{ route('operator.settings.company') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                                <div class="flex-1">
                                    <label for="firma" class="block text-sm font-medium text-slate-700 mb-1">Firma pe care o editezi</label>
                                    <select id="firma" name="firma"
                                            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        @foreach ($firme as $id => $f)
                                            <option value="{{ $id }}" @selected($id === $firmaId)>{{ $f['name'] }} · CUI {{ $f['cui'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                    Încarcă
                                </button>
                            </form>
                        </div>

                        {{-- Date de identificare --}}
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Date de identificare</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Apar pe fiecare factură emisă de această firmă</p>
                            </div>
                            <form action="#" method="POST" class="px-5 py-5 space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="company_id" value="{{ $firmaId }}">

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="sm:col-span-2">
                                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Denumire</label>
                                        <input type="text" id="name" name="name" required maxlength="255"
                                               value="{{ $firma['name'] }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label for="juridical_form" class="block text-sm font-medium text-slate-700 mb-1">Formă juridică</label>
                                        <select id="juridical_form" name="juridical_form" required
                                                class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            @foreach ($formeJuridice as $forma)
                                                <option @selected($firma['juridical_form'] === $forma)>{{ $forma }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="cui" class="block text-sm font-medium text-slate-700 mb-1">CUI</label>
                                        <input type="text" id="cui" name="cui" required maxlength="20"
                                               value="{{ $firma['cui'] }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label for="trade_registry_number" class="block text-sm font-medium text-slate-700 mb-1">Nr. Registrul Comerțului</label>
                                        <input type="text" id="trade_registry_number" name="trade_registry_number" required maxlength="20"
                                               value="{{ $firma['trade_registry_number'] }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div>
                                    <label for="social_capital" class="block text-sm font-medium text-slate-700 mb-1">Capital social (RON)</label>
                                    <input type="number" id="social_capital" name="social_capital" required step="0.01" min="0"
                                           value="{{ $firma['social_capital'] }}"
                                           class="w-full sm:w-64 px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                        Salvează modificările
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Sediu social --}}
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Sediu social</h2>
                            </div>
                            <form action="#" method="POST" class="px-5 py-5 space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="company_id" value="{{ $firmaId }}">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="county" class="block text-sm font-medium text-slate-700 mb-1">Județ</label>
                                        <input type="text" id="county" name="county" required maxlength="255"
                                               value="{{ $firma['county'] }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-slate-700 mb-1">Localitate</label>
                                        <input type="text" id="city" name="city" required maxlength="255"
                                               value="{{ $firma['city'] }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-slate-700 mb-1">Adresă</label>
                                    <input type="text" id="address" name="address" required maxlength="255"
                                           value="{{ $firma['address'] }}"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                        Salvează modificările
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TVA: schimbarea afecteaza toate facturile emise de acum inainte --}}
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">TVA</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Determină dacă facturile noi se emit cu TVA</p>
                            </div>
                            <form action="#" method="POST" class="px-5 py-5 space-y-4">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="company_id" value="{{ $firmaId }}">

                                <label class="flex items-start gap-3">
                                    <input type="checkbox" name="vat_payer" value="1" @checked($firma['vat_payer'])
                                           class="mt-0.5 w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>
                                        <span class="block text-sm font-medium text-slate-900">Firmă înregistrată în scopuri de TVA</span>
                                        <span class="block text-xs text-slate-500 mt-0.5">Bifează doar după ce ai primit codul de TVA de la ANAF.</span>
                                    </span>
                                </label>

                                <div class="flex items-start gap-2 px-3 py-2 rounded-lg bg-amber-50 text-amber-800">
                                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.48 0L3.16 16.25A2 2 0 005 19z"/></svg>
                                    <p class="text-xs">Modificarea se aplică doar facturilor emise de acum înainte. Facturile deja emise își păstrează regimul de TVA de la data emiterii.</p>
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                        Salvează
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </main>
        </div>
    </div>

</body>
</html>
