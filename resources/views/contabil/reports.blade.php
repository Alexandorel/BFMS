<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapoarte · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="flex min-h-screen">
    <x-sidebar :user="$user" />

    <div class="flex-1 flex flex-col min-w-0">
        <header class="flex items-center justify-between gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
            <label class="relative">
                <select id="companySelect"
                        class="appearance-none pl-3 pr-9 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach ($companies as $availableCompany)
                        <option value="{{ $availableCompany->id }}" @selected($company->is($availableCompany))>
                            {{ $availableCompany->name }}
                        </option>
                    @endforeach
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </label>

            <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium">
                    Doar vizualizare
                </span>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="max-w-5xl mx-auto space-y-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Rapoarte financiare</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Generează rapoarte pentru {{ $company->name }} în format PDF sau Excel.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-medium">Raportul nu a putut fi generat:</p>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <section class="bg-white rounded-xl border border-slate-200 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-semibold text-slate-900">Fișă client</h2>
                                <p class="text-sm text-slate-500 mt-1">
                                    Total facturat, total plătit, sold și istoricul facturilor clientului.
                                </p>
                            </div>
                            <div class="grid place-items-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>

                        <form action="{{ route('dashboard.contabil.reports.client-sheet') }}" method="GET" class="mt-5 space-y-4">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Client</span>
                                <select name="client_id" required @disabled($clients->isEmpty())
                                class="mt-1.5 w-full rounded-lg border border-slate-200 bg-white text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-slate-100">
                                    <option value="">Selectează clientul</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" @selected((string) old('client_id') === (string) $client->id)>
                                            {{ $client->full_name }}{{ $client->tax_id ? ' · '.$client->tax_id : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            @if ($clients->isEmpty())
                                <p class="text-xs text-amber-700">Firma activă nu are clienți înregistrați.</p>
                            @endif

                            <div class="grid grid-cols-2 gap-3">
                                <button type="submit" name="format" value="pdf" @disabled($clients->isEmpty())
                                class="px-4 py-2.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50">
                                    Descarcă PDF
                                </button>
                                <button type="submit" name="format" value="xlsx" @disabled($clients->isEmpty())
                                class="px-4 py-2.5 rounded-lg bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                                    Descarcă Excel
                                </button>
                            </div>
                        </form>
                    </section>

                    <section class="bg-white rounded-xl border border-slate-200 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-semibold text-slate-900">Închidere lună</h2>
                                <p class="text-sm text-slate-500 mt-1">
                                    Facturi, încasări, sold la final de lună și defalcare pe cote de TVA.
                                </p>
                            </div>
                            <div class="grid place-items-center w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <form action="{{ route('dashboard.contabil.reports.month-close') }}" method="GET" class="mt-5 space-y-4">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Luna raportată</span>
                                <input type="month" name="month" value="{{ old('month', $defaultMonth) }}" required
                                       class="mt-1.5 w-full rounded-lg border border-slate-200 bg-white text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </label>

                            <p class="text-xs text-slate-500">
                                Soldul este calculat istoric, folosind numai încasările înregistrate până la ultima zi a lunii.
                            </p>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="submit" name="format" value="pdf"
                                        class="px-4 py-2.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    Descarcă PDF
                                </button>
                                <button type="submit" name="format" value="xlsx"
                                        class="px-4 py-2.5 rounded-lg bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700">
                                    Descarcă Excel
                                </button>
                            </div>
                        </form>
                    </section>
                </div>

                <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                    Sumele din monede diferite sunt convertite în echivalent RON folosind cursul salvat pe fiecare factură sau încasare. În Excel, valorile sunt exportate ca numere și pot fi folosite direct în formule.
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('companySelect').addEventListener('change', function () {
        window.location.href = `/company/switch/${this.value}`;
    });
</script>
</body>
</html>
