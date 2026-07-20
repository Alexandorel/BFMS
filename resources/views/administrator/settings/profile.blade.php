<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil · Setări · {{ config('app.name', 'BFMS') }}</title>
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
                    <label class="relative">
                        <select id="companySelect" class="appearance-none pl-3 pr-9 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @forelse ($companies as $c)
                                <option value="{{ $c->id }}" @selected($activeCompany?->id === $c->id)>{{ $c->name }}</option>
                            @empty
                                <option value="">Nicio firmă</option>
                            @endforelse
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                    <a href="{{ route('administrator.settings.addcompany') }}" class="inline-flex items-center p-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-700 transition" title="Adaugă firmă">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </a>
                    @if ($activeCompany?->vat_payer)
                        <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">Plătitor TVA</span>
                    @elseif ($activeCompany)
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

                @if (session('success'))
                    <div class="px-4 py-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Settings sub-nav --}}
                    <nav class="lg:col-span-1 space-y-1 text-sm">
                        <a href="{{ route('administrator.settings.profile') }}" class="block px-3 py-2 rounded-lg bg-white border border-slate-200 text-indigo-700 font-medium">Profil</a>
                        <a href="{{ route('administrator.settings.company') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Firmă</a>
                        <a href="{{ route('administrator.settings.team') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Echipă</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Conturi bancare</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Serii documente</a>
                    </nav>

                    <div class="lg:col-span-3 space-y-6">

                        {{-- Date personale --}}
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Date personale</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Numele apare pe documentele pe care le emiți</p>
                            </div>
                            <form action="{{ route('administrator.profile.update') }}" method="POST" class="px-5 py-5 space-y-5" data-partial>
                                @csrf
                                @method('PUT')

                                <div class="flex items-center gap-4">
                                    <div class="grid place-items-center w-16 h-16 rounded-full bg-slate-200 text-slate-600 font-semibold text-lg shrink-0">
                                        {{ Str::substr($user->first_name, 0, 1) }}{{ Str::substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                        <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">{{ $user->role }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">Prenume</label>
                                        <input type="text" id="first_name" name="first_name" required maxlength="255"
                                               value="{{ old('first_name', $user->first_name) }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        @error('first_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Nume</label>
                                        <input type="text" id="last_name" name="last_name" required maxlength="255"
                                               value="{{ old('last_name', $user->last_name) }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        @error('last_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                    <input type="email" id="email" name="email" required maxlength="255"
                                           value="{{ old('email', $user->email) }}"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <p class="mt-1 text-xs text-slate-400">Emailul e și numele tău de utilizator la autentificare.</p>
                                    @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                        Salvează modificările
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Schimbare parola --}}
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Parolă</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Confirmă parola actuală pentru a o schimba</p>
                            </div>
                            <form action="{{ route('administrator.profile.password') }}" method="POST" class="px-5 py-5 space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1">Parola actuală</label>
                                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @error('current_password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Parola nouă</label>
                                        <input type="password" id="password" name="password" required autocomplete="new-password"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <p class="mt-1 text-xs text-slate-400">Minimum 8 caractere.</p>
                                        @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirmă parola nouă</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                        Schimbă parola
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </main>
        </div>
    </div>

<script>
    document.getElementById('companySelect')?.addEventListener('change', function () {
        if (this.value) {
            window.location.href = `/company/switch/${this.value}`;
        }
    });

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
