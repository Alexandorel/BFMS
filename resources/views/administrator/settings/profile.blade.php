<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    <x-app-shell>

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
                <x-company-switcher :companies="$companies" :active-company="$activeCompany"
                                    :add-href="route('administrator.settings.addcompany')">
                    <x-slot:meta>
                        @if ($activeCompany?->vat_payer)
                            <span class="ui-badge bg-emerald-50 text-emerald-700">Plătitor TVA</span>
                        @elseif ($activeCompany)
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
                    <x-settings-nav active="profile" />

                    <div class="lg:col-span-3 space-y-6">

                        {{-- Date personale --}}
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header block">
                                <h2 class="ui-section-title">Date personale</h2>
                                <p class="ui-section-description">Numele apare pe documentele pe care le emiți</p>
                            </div>
                            <form action="{{ route('administrator.profile.update') }}" method="POST" class="px-5 py-5 space-y-5" data-partial>
                                @csrf
                                @method('PUT')

                                <div class="flex items-center gap-4">
                                    <div class="grid place-items-center w-16 h-16 rounded-full bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold text-lg shrink-0">
                                        {{ Str::substr($user->first_name, 0, 1) }}{{ Str::substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $user->first_name }} {{ $user->last_name }}</p>
                                        <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">{{ $user->role }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Prenume</label>
                                        <input type="text" id="first_name" name="first_name" required maxlength="255"
                                               value="{{ old('first_name', $user->first_name) }}"
                                               class="form-input">
                                        @error('first_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nume</label>
                                        <input type="text" id="last_name" name="last_name" required maxlength="255"
                                               value="{{ old('last_name', $user->last_name) }}"
                                               class="form-input">
                                        @error('last_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                                    <input type="email" id="email" name="email" required maxlength="255"
                                           value="{{ old('email', $user->email) }}"
                                           class="form-input">
                                    <p class="mt-1 text-xs text-slate-400">Emailul e și numele tău de utilizator la autentificare.</p>
                                    @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="ui-btn ui-btn-primary">
                                        Salvează modificările
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Schimbare parola --}}
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header block">
                                <h2 class="ui-section-title">Parolă</h2>
                                <p class="ui-section-description">Confirmă parola actuală pentru a o schimba</p>
                            </div>
                            <form action="{{ route('administrator.profile.password') }}" method="POST" class="px-5 py-5 space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Parola actuală</label>
                                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                                           class="form-input">
                                    @error('current_password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Parola nouă</label>
                                        <input type="password" id="password" name="password" required autocomplete="new-password"
                                               class="form-input">
                                        <p class="mt-1 text-xs text-slate-400">Minimum 8 caractere.</p>
                                        @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirmă parola nouă</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                               class="form-input">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="ui-btn ui-btn-primary">
                                        Schimbă parola
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </main>
    </x-app-shell>

<script>
    /*
     * Trimite catre server doar campurile modificate.
     * Formularul de parola nu are data-partial: acolo toate campurile
     * sunt necesare la fiecare trimitere.
     */
    document.querySelectorAll('form[data-partial]').forEach(function (form) {
        const fields = Array.from(
            form.querySelectorAll('input[name], select[name], textarea[name]')
        ).filter(function (field) {
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

            if (modificate === 0) {
                event.preventDefault();
                fields.forEach(function (field) { field.disabled = false; });

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
