<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapoarte · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    <x-app-shell :user="$user">
        <header class="app-page-toolbar">
            <x-company-switcher :companies="$companies" :active-company="$company">
                <x-slot:meta>
                    <span
                        class="ui-badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ $accessLabel }}</span>
                </x-slot:meta>
            </x-company-switcher>
        </header>

        <main class="app-page-content">
            <div class="max-w-5xl mx-auto space-y-6">
                <x-page-header title="Rapoarte financiare" :description="'Generează rapoarte pentru ' . $company->name . ' în format PDF sau Excel.'" />

                @if ($errors->any())
                    <div class="ui-alert ui-alert-danger" role="alert">
                        <p class="font-medium">Raportul nu a putut fi generat:</p>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <section class="ui-card p-5 sm:p-6 flex flex-col">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-semibold text-slate-900 dark:text-slate-100">Fișă client</h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    Total facturat, total plătit, sold și istoricul facturilor clientului.
                                </p>
                            </div>
                            <div
                                class="grid place-items-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>

                        <form action="{{ route($clientSheetRoute) }}" method="GET"class="mt-5 flex flex-col flex-1">
                            <label class="block mt-5">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Client</span>
                                <select name="client_id" required @disabled($clients->isEmpty())
                                    class="form-input mt-1.5">
                                    <option value="">Selectează clientul</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" @selected((string) old('client_id') === (string) $client->id)>
                                            {{ $client->full_name }}{{ $client->tax_id ? ' · ' . $client->tax_id : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            @if ($clients->isEmpty())
                                <p class="text-xs text-amber-700">
                                    Firma activă nu are clienți înregistrați.
                                </p>
                            @endif

                            <div class="mt-auto pt-4 grid grid-cols-2 gap-3">
                                <button type="submit" name="format" value="pdf" @disabled($clients->isEmpty())
                                    class="ui-btn ui-btn-secondary w-full">
                                    Descarcă PDF
                                </button>

                                <button type="submit" name="format" value="xlsx" @disabled($clients->isEmpty())
                                    class="ui-btn ui-btn-primary w-full">
                                    Descarcă Excel
                                </button>
                            </div>
                        </form>
                    </section>

                    <section class="ui-card p-5 sm:p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-semibold text-slate-900 dark:text-slate-100">Închidere lună</h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    Facturi, încasări, sold la final de lună și defalcare pe cote de TVA.
                                </p>
                            </div>
                            <div
                                class="grid place-items-center w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <form action="{{ route($monthCloseRoute) }}" method="GET" class="mt-5 space-y-4">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Luna
                                    raportată</span>
                                <input type="month" name="month" value="{{ old('month', $defaultMonth) }}" required
                                    class="form-input mt-1.5">
                            </label>

                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Soldul este calculat istoric, folosind numai încasările înregistrate până la ultima zi a
                                lunii.
                            </p>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="submit" name="format" value="pdf"
                                    class="ui-btn ui-btn-secondary w-full">
                                    Descarcă PDF
                                </button>
                                <button type="submit" name="format" value="xlsx"
                                    class="ui-btn ui-btn-primary w-full">
                                    Descarcă Excel
                                </button>
                            </div>
                        </form>
                    </section>
                </div>

                <div class="ui-alert ui-alert-info" role="note">
                    Sumele din monede diferite sunt convertite în echivalent RON folosind cursul salvat pe fiecare
                    factură sau încasare. În Excel, valorile sunt exportate ca numere și pot fi folosite direct în
                    formule.
                </div>
            </div>
        </main>
    </x-app-shell>

</body>

</html>
