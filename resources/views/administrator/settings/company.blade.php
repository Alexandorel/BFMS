<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Firmă · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @php
        $formeJuridice = ['SRL', 'SA', 'PFA', 'II', 'IF', 'SRL-D'];
    @endphp

    <x-app-shell>

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
                <x-company-switcher :companies="$companies" :active-company="$company"
                                    :add-href="route('administrator.settings.addcompany')">
                    <x-slot:meta>
                        @if ($company?->vat_payer)
                            <span class="ui-badge bg-emerald-50 text-emerald-700">Plătitor TVA</span>
                        @elseif ($company)
                            <span class="ui-badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Neplătitor TVA</span>
                        @endif
                    </x-slot:meta>
                </x-company-switcher>
            </header>

            {{-- Content --}}
            <main class="app-page-content space-y-6">

                <x-page-header title="Setări" description="Contul tău și configurările firmei" />

                @if (session('success'))
                    <div class="ui-alert ui-alert-success" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Settings sub-nav --}}
                    <x-settings-nav active="company" />

                    <div class="lg:col-span-3 space-y-6">

                        @if (! $company)

                            {{-- Userul nu are inca nicio firma --}}
                            <div class="ui-card ui-empty-state">
                                <p class="text-sm text-slate-600 dark:text-slate-300">Nu ai încă nicio firmă adăugată.</p>
                                <a href="{{ route('administrator.settings.addcompany') }}" class="ui-btn ui-btn-primary mt-4">
                                    Adaugă prima firmă
                                </a>
                            </div>

                        @else

                            {{-- Alegerea firmei editate --}}
                            @if ($companies->count() > 1)
                                <div class="ui-card px-5 py-4">
                                    <form action="{{ route('administrator.settings.company') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                                        <div class="flex-1">
                                            <label for="firma" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Firma pe care o editezi</label>
                                            <select id="firma" name="firma"
                                                    class="form-input">
                                                @foreach ($companies as $c)
                                                    <option value="{{ $c->id }}" @selected($c->id === $company->id)>{{ $c->name }} · CUI {{ $c->cui }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="ui-btn ui-btn-secondary">
                                            Încarcă
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- Date de identificare --}}
                            <div class="ui-card overflow-hidden">
                                <div class="ui-card-header block">
                                    <h2 class="ui-section-title">Date de identificare</h2>
                                    <p class="ui-section-description">Apar pe fiecare factură emisă de această firmă</p>
                                </div>
                                <form action="{{ route('administrator.companies.update', $company) }}" method="POST" class="px-5 py-5 space-y-5" data-partial>
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="sm:col-span-2">
                                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Denumire</label>
                                            <input type="text" id="name" name="name" required maxlength="255"
                                                   value="{{ old('name', $company->name) }}"
                                                   class="form-input">
                                            @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label for="juridical_form" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Formă juridică</label>
                                            <select id="juridical_form" name="juridical_form" required
                                                    class="form-input">
                                                @foreach ($formeJuridice as $forma)
                                                    <option value="{{ $forma }}" @selected(old('juridical_form', $company->juridical_form) === $forma)>{{ $forma }}</option>
                                                @endforeach
                                            </select>
                                            @error('juridical_form') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="cui" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">CUI</label>
                                            <input type="text" id="cui" name="cui" required maxlength="20"
                                                   value="{{ old('cui', $company->cui) }}"
                                                   class="form-input">
                                            @error('cui') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label for="trade_registry_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nr. Registrul Comerțului</label>
                                            <input type="text" id="trade_registry_number" name="trade_registry_number" required maxlength="20"
                                                   value="{{ old('trade_registry_number', $company->trade_registry_number) }}"
                                                   class="form-input">
                                            @error('trade_registry_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="social_capital" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Capital social (RON)</label>
                                        <input type="number" id="social_capital" name="social_capital" required step="0.01" min="0"
                                               value="{{ old('social_capital', $company->social_capital) }}"
                                               class="form-input sm:w-64">
                                        @error('social_capital') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex justify-end pt-1">
                                        <button type="submit" class="ui-btn ui-btn-primary">
                                            Salvează modificările
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Sediu social --}}
                            <div class="ui-card overflow-hidden">
                                <div class="ui-card-header">
                                    <h2 class="ui-section-title">Sediu social</h2>
                                </div>
                                <form action="{{ route('administrator.companies.update', $company) }}" method="POST" class="px-5 py-5 space-y-5" data-partial>
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="county" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Județ</label>
                                            <input type="text" id="county" name="county" required maxlength="255"
                                                   value="{{ old('county', $company->county) }}"
                                                   class="form-input">
                                            @error('county') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label for="city" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Localitate</label>
                                            <input type="text" id="city" name="city" required maxlength="255"
                                                   value="{{ old('city', $company->city) }}"
                                                   class="form-input">
                                            @error('city') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Adresă</label>
                                        <input type="text" id="address" name="address" required maxlength="255"
                                               value="{{ old('address', $company->address) }}"
                                               class="form-input">
                                        @error('address') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex justify-end pt-1">
                                        <button type="submit" class="ui-btn ui-btn-primary">
                                            Salvează modificările
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- TVA: schimbarea afecteaza toate facturile emise de acum inainte --}}
                            <div class="ui-card overflow-hidden">
                                <div class="ui-card-header block">
                                    <h2 class="ui-section-title">TVA</h2>
                                    <p class="ui-section-description">Determină dacă facturile noi se emit cu TVA</p>
                                </div>
                                <form action="{{ route('administrator.companies.update', $company) }}" method="POST" class="px-5 py-5 space-y-4" data-partial>
                                    @csrf
                                    @method('PUT')

                                    {{-- Checkbox-ul nedebifat nu se trimite; hidden-ul asigura valoarea 0 --}}
                                    <input type="hidden" name="vat_payer" value="0" data-pair-for="vat_payer">

                                    <label class="flex items-start gap-3">
                                        <input type="checkbox" name="vat_payer" value="1" @checked(old('vat_payer', $company->vat_payer))
                                               class="mt-0.5 w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span>
                                            <span class="block text-sm font-medium text-slate-900 dark:text-slate-100">Firmă înregistrată în scopuri de TVA</span>
                                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5">Bifează doar după ce ai primit codul de TVA de la ANAF.</span>
                                        </span>
                                    </label>

                                    <div class="flex items-start gap-2 px-3 py-2 rounded-lg bg-amber-50 text-amber-800">
                                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.48 0L3.16 16.25A2 2 0 005 19z"/></svg>
                                        <p class="text-xs">Modificarea se aplică doar facturilor emise de acum înainte. Facturile deja emise își păstrează regimul de TVA de la data emiterii.</p>
                                    </div>

                                    <div class="flex justify-end pt-1">
                                        <button type="submit" class="ui-btn ui-btn-primary">
                                            Salvează
                                        </button>
                                    </div>
                                </form>
                            </div>

                        @endif

                    </div>
                </div>

            </main>
    </x-app-shell>

<script>
    /*
     * Trimite catre server doar campurile modificate.
     * Campurile neatinse sunt dezactivate inainte de submit, deci nu ajung in
     * request; backend-ul le trateaza ca absente si le lasa nemodificate.
     */
    document.querySelectorAll('form[data-partial]').forEach(function (form) {
        const fields = Array.from(
            form.querySelectorAll('input[name], select[name], textarea[name]')
        ).filter(function (field) {
            // _token si _method trebuie sa plece mereu
            return field.type !== 'hidden';
        });

        const initial = new Map();
        fields.forEach(function (field) {
            initial.set(field, field.type === 'checkbox' ? String(field.checked) : field.value);
        });

        form.addEventListener('submit', function (event) {
            let modificate = 0;

            fields.forEach(function (field) {
                const acum = field.type === 'checkbox' ? String(field.checked) : field.value;

                if (acum === initial.get(field)) {
                    field.disabled = true;
                } else {
                    modificate++;
                }
            });

            // Hidden-ul pereche al unui checkbox are sens doar daca checkbox-ul s-a schimbat
            form.querySelectorAll('input[data-pair-for]').forEach(function (hidden) {
                const pereche = form.querySelector(
                    'input[type="checkbox"][name="' + hidden.dataset.pairFor + '"]'
                );
                hidden.disabled = !pereche || pereche.disabled;
            });

            if (modificate === 0) {
                event.preventDefault();
                fields.forEach(function (field) { field.disabled = false; });
                form.querySelectorAll('input[data-pair-for]').forEach(function (hidden) {
                    hidden.disabled = false;
                });

                const buton = form.querySelector('button[type="submit"]');
                const textInitial = buton.textContent;
                buton.textContent = 'Nimic de salvat';
                setTimeout(function () { buton.textContent = textInitial; }, 1500);
            }
        });
    });
</script>
</body>
</html>
