<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Conturi bancare · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <x-app-shell>

        {{-- Top Bar --}}
        <header class="app-page-toolbar">
            <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                <form id="topCompanyForm" action="{{ route('administrator.bank-accounts.index') }}" method="GET">
                    <label class="relative block">
                        <span class="sr-only">Firma activă</span>
                        <select id="companySelect" name="firma" class="ui-toolbar-select">
                            @forelse ($companies as $c)
                                <option value="{{ $c->id }}" @selected($company?->id === $c->id)>
                                    {{ $c->name }}
                                </option>
                            @empty
                                <option value="">Nicio firmă</option>
                            @endforelse
                        </select>

                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </label>
                </form>

                <x-role-badge :role="auth()->user()->role" />
            </div>
        </header>

        {{-- Content --}}
        <main class="app-page-content space-y-6">

            <x-page-header title="Setări" description="Contul tău și configurările firmei" />

            @if (session('success'))
                <div class="ui-alert ui-alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="ui-alert ui-alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="ui-alert ui-alert-danger" role="alert">
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
                <x-settings-nav active="bank-accounts" />

                <div class="lg:col-span-3 space-y-6">

                    @if (!$company)

                        <div class="ui-card ui-empty-state">
                            <p class="text-sm text-slate-600 dark:text-slate-300">Nu ai încă nicio firmă adăugată.</p>
                            <a href="{{ route('administrator.settings.addcompany') }}"
                                class="ui-btn ui-btn-primary mt-4">
                                Adaugă prima firmă
                            </a>
                        </div>
                    @else
                        {{-- Alegerea firmei configurate --}}
                        @if ($companies->count() > 1)
                            <div class="ui-card px-5 py-4">
                                <form action="{{ route('administrator.bank-accounts.index') }}" method="GET"
                                    class="flex flex-col sm:flex-row sm:items-end gap-3">
                                    <div class="flex-1">
                                        <label for="firma"
                                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                            Firma pentru care gestionezi conturile
                                        </label>
                                        <select id="firma" name="firma" class="form-input">
                                            @foreach ($companies as $c)
                                                <option value="{{ $c->id }}" @selected($c->id === $company->id)>
                                                    {{ $c->name }} · CUI {{ $c->cui }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="ui-btn ui-btn-secondary">
                                        Încarcă
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Adăugarea unui cont nou --}}
                        <section class="ui-card overflow-hidden">
                            <div class="ui-card-header block">
                                <h2 class="ui-section-title">Adaugă un cont bancar</h2>
                                <p class="ui-section-description">
                                    IBAN-ul românesc trebuie să conțină 24 de caractere și să înceapă cu RO
                                </p>
                            </div>

                            <form action="{{ route('administrator.bank-accounts.store') }}" method="POST"
                                class="px-5 py-5 grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                @csrf

                                <input type="hidden" name="company_id" value="{{ $company->id }}">

                                <div class="md:col-span-2">
                                    <label for="bank_name"
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        Bancă
                                    </label>
                                    <input type="text" id="bank_name" name="bank_name"
                                        value="{{ old('bank_name') }}" maxlength="255" required
                                        placeholder="Ex: Banca Transilvania" class="form-input">
                                </div>

                                <div class="md:col-span-3">
                                    <label for="iban"
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        IBAN
                                    </label>
                                    <input type="text" id="iban" name="iban" value="{{ old('iban') }}"
                                        maxlength="24" required spellcheck="false" autocomplete="off"
                                        placeholder="RO00AAAA0000000000000000" class="form-input font-mono uppercase">
                                </div>

                                <div>
                                    <label for="currency"
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        Monedă
                                    </label>
                                    <input type="text" id="currency" name="currency" value="RON" readonly
                                        class="form-input">
                                </div>

                                <div class="md:col-span-6 flex justify-end">
                                    <button type="submit" class="ui-btn ui-btn-primary">
                                        Adaugă contul
                                    </button>
                                </div>
                            </form>
                        </section>

                        {{-- Conturile existente --}}
                        <section class="space-y-4">
                            <div class="ui-card px-5 py-4">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h2 class="ui-section-title">Conturi bancare existente</h2>
                                        <p class="ui-section-description">
                                            Conturile bancare înregistrate pentru {{ $company->name }}
                                        </p>
                                    </div>

                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                                        {{ $bankAccounts->count() }}
                                        {{ $bankAccounts->count() === 1 ? 'cont' : 'conturi' }}
                                    </span>
                                </div>
                            </div>

                            @forelse ($bankAccounts as $account)
                                <article class="ui-card overflow-hidden">
                                    <div
                                        class="flex items-center justify-between gap-3 px-5 py-3 bg-slate-50 border-b border-slate-200">
                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                                Cont bancar #{{ $loop->iteration }}
                                            </h3>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                Editează informațiile contului și salvează modificările
                                            </p>
                                        </div>

                                        <span
                                            class="shrink-0 inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                            {{ $account->currency }}
                                        </span>
                                    </div>

                                    <div class="px-5 py-5">
                                        <form id="update-bank-account-{{ $account->id }}"
                                            action="{{ route('administrator.bank-accounts.update', $account) }}"
                                            method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                            @csrf
                                            @method('PUT')

                                            <div class="md:col-span-2">
                                                <label for="bank-name-{{ $account->id }}"
                                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                                    Bancă
                                                </label>
                                                <input type="text" id="bank-name-{{ $account->id }}"
                                                    name="bank_name" value="{{ $account->bank_name }}"
                                                    maxlength="255" required class="form-input">
                                            </div>

                                            <div class="md:col-span-3">
                                                <label for="iban-{{ $account->id }}"
                                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                                    IBAN
                                                </label>
                                                <input type="text" id="iban-{{ $account->id }}" name="iban"
                                                    value="{{ $account->iban }}" maxlength="24" required
                                                    spellcheck="false" autocomplete="off"
                                                    class="form-input font-mono uppercase">
                                            </div>

                                            <div>
                                                <label for="currency-{{ $account->id }}"
                                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                                    Monedă
                                                </label>
                                                <input type="text" id="currency-{{ $account->id }}"
                                                    name="currency" value="RON" readonly class="form-input">
                                            </div>
                                        </form>

                                        <div
                                            class="ui-button-group mt-5 border-t border-slate-100 pt-4 sm:justify-end">
                                            <x-confirm-action
                                                action="{{ route('administrator.bank-accounts.destroy', $account) }}"
                                                variant="button"
                                                confirm-text="Ștergi contul bancar?"></x-confirm-action>

                                            <button type="submit" form="update-bank-account-{{ $account->id }}"
                                                class="ui-btn ui-btn-primary w-full sm:w-auto">
                                                Salvează modificările
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="ui-card ui-empty-state">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        Firma selectată nu are încă niciun cont bancar.
                                    </p>
                                </div>
                            @endforelse
                        </section>

                    @endif
                </div>
            </div>
        </main>
    </x-app-shell>

    <script>
        document.getElementById('companySelect')?.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('topCompanyForm').submit();
            }
        });
    </script>
</body>

</html>
