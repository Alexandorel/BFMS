<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Conturi bancare · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex min-h-screen">

    {{-- Side Bar --}}
    <x-sidebar />

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top Bar --}}
        <header class="flex items-center gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
            <div class="flex items-center gap-3">
                <form id="topCompanyForm"
                      action="{{ route('administrator.bank-accounts.index') }}"
                      method="GET">
                    <label class="relative block">
                        <span class="sr-only">Firma activă</span>
                        <select id="companySelect"
                                name="firma"
                                class="appearance-none pl-3 pr-9 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @forelse ($companies as $c)
                                <option value="{{ $c->id }}" @selected($company?->id === $c->id)>
                                    {{ $c->name }}
                                </option>
                            @empty
                                <option value="">Nicio firmă</option>
                            @endforelse
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </label>
                </form>

                <a href="{{ route('administrator.settings.addcompany') }}"
                   class="inline-flex items-center p-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-700 transition"
                   title="Adaugă firmă">
                    <svg class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                </a>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-4 sm:p-6 space-y-6">

            <div>
                <h1 class="text-2xl font-bold text-slate-900">Setări</h1>
                <p class="text-slate-500 text-sm mt-1">Contul tău și configurările firmei</p>
            </div>

            @if (session('success'))
                <div class="px-4 py-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="px-4 py-3 rounded-lg bg-rose-50 text-rose-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="px-4 py-3 rounded-lg bg-rose-50 text-rose-800 text-sm">
                    <p class="font-medium mb-1">Verifică datele introduse:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Settings sub-nav --}}
                <nav class="lg:col-span-1 space-y-1 text-sm">
                    <a href="{{ route('administrator.settings.profile') }}"
                       class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">
                        Profil
                    </a>
                    <a href="{{ route('administrator.settings.company') }}"
                       class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">
                        Firmă
                    </a>
                    <a href="{{ route('administrator.settings.team') }}"
                       class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">
                        Echipă
                    </a>
                    <a href="{{ route('administrator.bank-accounts.index') }}"
                       class="block px-3 py-2 rounded-lg bg-white border border-slate-200 text-indigo-700 font-medium">
                        Conturi bancare
                    </a>
                    <a href="{{ route('administrator.series.index') }}"
                       class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">
                        Serii documente
                    </a>
                </nav>

                <div class="lg:col-span-3 space-y-6">

                    @if (! $company)

                        <div class="bg-white rounded-xl border border-slate-200 px-5 py-8 text-center">
                            <p class="text-sm text-slate-600">Nu ai încă nicio firmă adăugată.</p>
                            <a href="{{ route('administrator.settings.addcompany') }}"
                               class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                Adaugă prima firmă
                            </a>
                        </div>

                    @else

                        {{-- Alegerea firmei configurate --}}
                        @if ($companies->count() > 1)
                            <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
                                <form action="{{ route('administrator.bank-accounts.index') }}"
                                      method="GET"
                                      class="flex flex-col sm:flex-row sm:items-end gap-3">
                                    <div class="flex-1">
                                        <label for="firma"
                                               class="block text-sm font-medium text-slate-700 mb-1">
                                            Firma pentru care gestionezi conturile
                                        </label>
                                        <select id="firma"
                                                name="firma"
                                                class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            @foreach ($companies as $c)
                                                <option value="{{ $c->id }}" @selected($c->id === $company->id)>
                                                    {{ $c->name }} · CUI {{ $c->cui }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                        Încarcă
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Adăugarea unui cont nou --}}
                        <section class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Adaugă un cont bancar</h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    IBAN-ul românesc trebuie să conțină 24 de caractere și să înceapă cu RO
                                </p>
                            </div>

                            <form action="{{ route('administrator.bank-accounts.store') }}"
                                  method="POST"
                                  class="px-5 py-5 grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                @csrf

                                <input type="hidden" name="company_id" value="{{ $company->id }}">

                                <div class="md:col-span-2">
                                    <label for="bank_name"
                                           class="block text-sm font-medium text-slate-700 mb-1">
                                        Bancă
                                    </label>
                                    <input type="text"
                                           id="bank_name"
                                           name="bank_name"
                                           value="{{ old('bank_name') }}"
                                           maxlength="255"
                                           required
                                           placeholder="Ex: Banca Transilvania"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div class="md:col-span-3">
                                    <label for="iban"
                                           class="block text-sm font-medium text-slate-700 mb-1">
                                        IBAN
                                    </label>
                                    <input type="text"
                                           id="iban"
                                           name="iban"
                                           value="{{ old('iban') }}"
                                           maxlength="24"
                                           required
                                           spellcheck="false"
                                           autocomplete="off"
                                           placeholder="RO00AAAA0000000000000000"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="currency"
                                           class="block text-sm font-medium text-slate-700 mb-1">
                                        Monedă
                                    </label>
                                    <input type="text"
                                           id="currency"
                                           name="currency"
                                           value="RON"
                                           readonly
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-100 text-slate-600 text-sm cursor-not-allowed">
                                </div>

                                <div class="md:col-span-6 flex justify-end">
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                        Adaugă contul
                                    </button>
                                </div>
                            </form>
                        </section>

                        {{-- Conturile existente --}}
                        <section class="space-y-4">
                            <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h2 class="font-semibold text-slate-900">Conturi bancare existente</h2>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Conturile bancare înregistrate pentru {{ $company->name }}
                                        </p>
                                    </div>

                                    <span class="text-xs font-medium text-slate-500">
                                            {{ $bankAccounts->count() }}
                                        {{ $bankAccounts->count() === 1 ? 'cont' : 'conturi' }}
                                        </span>
                                </div>
                            </div>

                            @forelse ($bankAccounts as $account)
                                <article class="overflow-hidden bg-white rounded-xl border border-slate-200 shadow-sm">
                                    <div class="flex items-center justify-between gap-3 px-5 py-3 bg-slate-50 border-b border-slate-200">
                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-800">
                                                Cont bancar #{{ $loop->iteration }}
                                            </h3>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                Editează informațiile contului și salvează modificările
                                            </p>
                                        </div>

                                        <span class="shrink-0 inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                                {{ $account->currency }}
                                            </span>
                                    </div>

                                    <div class="px-5 py-5">
                                        <form id="update-bank-account-{{ $account->id }}"
                                              action="{{ route('administrator.bank-accounts.update', $account) }}"
                                              method="POST"
                                              class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                            @csrf
                                            @method('PUT')

                                            <div class="md:col-span-2">
                                                <label for="bank-name-{{ $account->id }}"
                                                       class="block text-sm font-medium text-slate-700 mb-1">
                                                    Bancă
                                                </label>
                                                <input type="text"
                                                       id="bank-name-{{ $account->id }}"
                                                       name="bank_name"
                                                       value="{{ $account->bank_name }}"
                                                       maxlength="255"
                                                       required
                                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            </div>

                                            <div class="md:col-span-3">
                                                <label for="iban-{{ $account->id }}"
                                                       class="block text-sm font-medium text-slate-700 mb-1">
                                                    IBAN
                                                </label>
                                                <input type="text"
                                                       id="iban-{{ $account->id }}"
                                                       name="iban"
                                                       value="{{ $account->iban }}"
                                                       maxlength="24"
                                                       required
                                                       spellcheck="false"
                                                       autocomplete="off"
                                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            </div>

                                            <div>
                                                <label for="currency-{{ $account->id }}"
                                                       class="block text-sm font-medium text-slate-700 mb-1">
                                                    Monedă
                                                </label>
                                                <input type="text"
                                                       id="currency-{{ $account->id }}"
                                                       name="currency"
                                                       value="RON"
                                                       readonly
                                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-100 text-slate-600 text-sm cursor-not-allowed">
                                            </div>
                                        </form>

                                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 mt-5 pt-4 border-t border-slate-100">
                                            <form action="{{ route('administrator.bank-accounts.destroy', $account) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Sigur vrei să ștergi acest cont bancar?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="w-full sm:w-auto px-4 py-2 rounded-lg border border-rose-200 bg-white text-rose-600 text-sm font-medium hover:bg-rose-50 transition">
                                                    Șterge
                                                </button>
                                            </form>

                                            <button type="submit"
                                                    form="update-bank-account-{{ $account->id }}"
                                                    class="w-full sm:w-auto px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                                Salvează modificările
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="bg-white rounded-xl border border-slate-200 px-5 py-8 text-center">
                                    <p class="text-sm text-slate-500">
                                        Firma selectată nu are încă niciun cont bancar.
                                    </p>
                                </div>
                            @endforelse
                        </section>

                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('companySelect')?.addEventListener('change', function () {
        if (this.value) {
            document.getElementById('topCompanyForm').submit();
        }
    });
</script>
</body>
</html>
