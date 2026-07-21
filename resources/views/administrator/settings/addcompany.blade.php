<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Add firma</title>

    @vite(['resources/css/app.css'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased">
<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white border border-slate-200 p-5">

        <form action="{{ route('administrator.companies.store') }}"
              method="POST"
              class="space-y-6">

            @csrf

            @if ($errors->any())
                <div class="border border-red-300 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Formularul conține erori.</p>
                    <p>Verifică informațiile marcate mai jos.</p>
                </div>
            @endif

            {{-- Date firma --}}
            <div class="space-y-5">
                <h2 class="form-section-title">Firma</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="name" class="form-label">
                            Nume Firmă
                        </label>

                        <input type="text"
                               id="name"
                               name="name"
                               required
                               maxlength="255"
                               value="{{ old('name') }}"
                               placeholder="SC..."
                               class="form-input @error('name') !border-red-500 @enderror">

                        @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label for="juridical_form" class="form-label">
                            Forma Juridică
                        </label>

                        <select id="juridical_form"
                                name="juridical_form"
                                required
                                class="form-input @error('juridical_form') !border-red-500 @enderror">

                            <option value=""
                                    disabled
                                @selected(!old('juridical_form'))>
                                ...
                            </option>

                            <option value="SRL"
                                @selected(old('juridical_form') === 'SRL')>
                                SRL
                            </option>

                            <option value="SA"
                                @selected(old('juridical_form') === 'SA')>
                                SA
                            </option>

                            <option value="PFA"
                                @selected(old('juridical_form') === 'PFA')>
                                PFA
                            </option>

                            <option value="II"
                                @selected(old('juridical_form') === 'II')>
                                II
                            </option>

                            <option value="IF"
                                @selected(old('juridical_form') === 'IF')>
                                IF
                            </option>

                            <option value="SRL-D"
                                @selected(old('juridical_form') === 'SRL-D')>
                                SRL-D
                            </option>
                        </select>

                        @error('juridical_form')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                {{-- CUI / CIF --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="cui" class="form-label">
                            CUI / CIF
                        </label>

                        <input type="text"
                               id="cui"
                               name="cui"
                               required
                               pattern="RO?[0-9]{2,10}"
                               maxlength="12"
                               value="{{ old('cui') }}"
                               placeholder="RO11111111"
                               class="form-input @error('cui') !border-red-500 @enderror">

                        @error('cui')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label for="trade_registry_number" class="form-label">
                            Număr Registru
                        </label>

                        <input type="text"
                               id="trade_registry_number"
                               name="trade_registry_number"
                               required
                               maxlength="20"
                               value="{{ old('trade_registry_number') }}"
                               placeholder="J12/345/2026"
                               class="form-input @error('trade_registry_number') !border-red-500 @enderror">

                        @error('trade_registry_number')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Locatie firma --}}
            <hr class="border-slate-200">

            <div class="space-y-5">
                <h2 class="form-section-title">Locație Sediu</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="county" class="form-label">
                            Județ
                        </label>

                        <input type="text"
                               id="county"
                               name="county"
                               required
                               maxlength="255"
                               value="{{ old('county') }}"
                               placeholder="Maramureș"
                               class="form-input @error('county') !border-red-500 @enderror">

                        @error('county')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="form-label">
                            Oraș
                        </label>

                        <input type="text"
                               id="city"
                               name="city"
                               required
                               maxlength="255"
                               value="{{ old('city') }}"
                               placeholder="Baia Mare"
                               class="form-input @error('city') !border-red-500 @enderror">

                        @error('city')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="form-label">
                        Adresă
                    </label>

                    <input type="text"
                           id="address"
                           name="address"
                           required
                           maxlength="255"
                           value="{{ old('address') }}"
                           placeholder="Str. ... nr. 11"
                           class="form-input @error('address') !border-red-500 @enderror">

                    @error('address')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            {{-- Capital + TVA --}}
            <hr class="border-slate-200">

            <div class="space-y-5">
                <h2 class="form-section-title">Capital</h2>

                <div>
                    <label for="social_capital" class="form-label">
                        Capital
                    </label>

                    <input type="number"
                           id="social_capital"
                           name="social_capital"
                           required
                           step="0.01"
                           min="0"
                           value="{{ old('social_capital') }}"
                           placeholder="200.00"
                           class="form-input @error('social_capital') !border-red-500 @enderror">

                    @error('social_capital')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <label class="flex items-start gap-3">
                    <input type="checkbox"
                           name="vat_payer"
                           value="1"
                           @checked(old('vat_payer'))
                           class="mt-0.5 w-4 h-4 border-slate-300 text-indigo-600 focus:ring-indigo-500">

                    <span>
                        <span class="block text-sm font-medium text-slate-900">
                            Înregistrare pentru TVA
                        </span>

                        <span class="block text-xs text-slate-500 mt-0.5">
                            Bifează dacă are cod TVA deja.
                        </span>
                    </span>
                </label>

                @error('vat_payer')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Conturi bancare --}}
            <hr class="border-slate-200">

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="form-section-title">
                        Conturi Bancare
                    </h2>

                    <button type="button"
                            id="add-account-btn"
                            class="form-btn-link">
                        + Adaugă
                    </button>
                </div>

                @php
                    $oldIbans = old('iban', ['']);
                    $oldBankNames = old('bank_name', ['']);

                    $numberOfAccounts = max(
                        count($oldIbans),
                        count($oldBankNames),
                        1
                    );
                @endphp

                <div id="bank-accounts-container" class="space-y-3">
                    @for ($index = 0; $index < $numberOfAccounts; $index++)
                        <div class="bank-account-row grid grid-cols-1 sm:grid-cols-5 gap-3">
                            <div class="sm:col-span-3">
                                <input type="text"
                                       name="iban[]"
                                       value="{{ $oldIbans[$index] ?? '' }}"
                                       pattern="RO[0-9]{2}[A-Z0-9]{4}[0-9]{16}"
                                       title="IBAN"
                                       maxlength="24"
                                       placeholder="ROxxxxxxxxxxxxxxxxxxxxxx"
                                       class="form-input @error('iban.' . $index) !border-red-500 @enderror">

                                @error('iban.' . $index)
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <div class="flex gap-2">
                                    <input type="text"
                                           name="bank_name[]"
                                           value="{{ $oldBankNames[$index] ?? '' }}"
                                           placeholder="Banca"
                                           class="form-input @error('bank_name.' . $index) !border-red-500 @enderror">

                                    <button type="button"
                                            class="remove-account-btn form-btn-del">
                                        X
                                    </button>
                                </div>

                                @error('bank_name.' . $index)
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('dashboard.administrator') }}"
                   class="form-btn-secondary">
                    Anulează
                </a>

                <button type="submit" class="form-btn-primary">
                    Add firma
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('add-account-btn').addEventListener('click', function () {
        const container = document.getElementById('bank-accounts-container');
        const row = document.createElement('div');

        row.className = 'bank-account-row grid grid-cols-1 sm:grid-cols-5 gap-3';

        row.innerHTML = `
            <div class="sm:col-span-3">
                <input type="text"
                       name="iban[]"
                       pattern="RO[0-9]{2}[A-Z0-9]{4}[0-9]{16}"
                       title="IBAN"
                       maxlength="24"
                       placeholder="ROxxxxxxxxxxxxxxxxxxxxxx"
                       class="form-input">
            </div>

            <div class="sm:col-span-2">
                <div class="flex gap-2">
                    <input type="text"
                           name="bank_name[]"
                           placeholder="Banca"
                           class="form-input">

                    <button type="button"
                            class="remove-account-btn form-btn-del">
                        X
                    </button>
                </div>
            </div>
        `;

        container.appendChild(row);
    });

    document
        .getElementById('bank-accounts-container')
        .addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-account-btn')) {
                return;
            }

            const rows = document.querySelectorAll('.bank-account-row');

            if (rows.length > 1) {
                event.target.closest('.bank-account-row').remove();
            }
        });
</script>
</body>
</html>
