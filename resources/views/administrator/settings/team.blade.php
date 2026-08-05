<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Echipă · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <x-app-shell>

        {{-- Top Bar --}}
        <header class="app-page-toolbar">
            <x-company-switcher :companies="$companies" :active-company="$company" :add-href="route('administrator.settings.addcompany')" />
        </header>

        {{-- Content --}}
        <main class="app-page-content space-y-6">

            <x-page-header title="Setări" :description="'Configurările firmei ' . ($company?->name ?? '—')" />

            @if (session('status'))
                <div id="status-banner"
                    class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Settings sub-nav --}}
                <x-settings-nav active="team" />

                <div class="lg:col-span-3 space-y-6">

                    {{-- Card 1: Lista echipei --}}
                    <div class="ui-card overflow-hidden">
                        <div class="ui-card-header flex items-center justify-between gap-3 flex-wrap">
                            <h2 class="ui-section-title">Echipă</h2>
                        </div>

                        {{-- Search + sortare --}}
                        <div
                            class="px-5 py-3 border-b border-slate-100 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                            <div class="relative flex-1 max-w-xs">
                                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.6 0 7.5 7.5 0 0010.6 0z" />
                                </svg>
                                <input type="text" id="team-search" placeholder="Caută nume sau email..."
                                    class="form-input text-sm !pl-10">
                            </div>

                            <div class="flex items-center gap-2 text-sm">
                                <label for="team-sort"
                                    class="text-slate-500 dark:text-slate-400 shrink-0">Sortează:</label>
                                <select id="team-sort" class="form-input py-1.5 text-sm">
                                    <option value="recent">Recent adăugate</option>
                                    <option value="az">Nume A–Z</option>
                                    <option value="za">Nume Z–A</option>
                                </select>
                            </div>
                        </div>

                        {{-- Tabel scrollabil — ~4 rânduri vizibile, restul la scroll --}}
                        <div id="team-list" class="max-h-[19rem] overflow-y-auto divide-y divide-slate-100">

                            @foreach ($allUsers as $membru)
                                <div class="team-row flex items-center justify-between gap-3 px-5 py-4"
                                    data-name="{{ Str::lower($membru['name']) }}"
                                    data-email="{{ Str::lower($membru['email']) }}"
                                    data-created="{{ $membru['created_at'] }}">

                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="grid place-items-center w-9 h-9 rounded-full bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold text-sm shrink-0">
                                            {{ $membru['initials'] }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">
                                                {{ $membru['name'] }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                                {{ $membru['email'] }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0">
                                        <span
                                            class="text-xs px-2 py-1 rounded-full font-medium
                                            @if ($membru['role'] === 'administrator') bg-indigo-50 text-indigo-700
                                            @elseif($membru['role'] === 'operator')
                                                bg-emerald-50 text-emerald-700
                                            @else
                                                bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 @endif
                                        ">
                                            {{ ucfirst($membru['role']) }}
                                        </span>

                                        <a href="{{ route('administrator.team.edit', $membru['id']) }}"
                                            class="ui-action-link">
                                            Editează
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                            <div id="team-empty"
                                class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400 hidden">
                                Niciun cont găsit.
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Formular de creare cont --}}
                    <div class="ui-card overflow-hidden">
                        <div class="ui-card-header">
                            <h2 class="ui-section-title">Creează cont nou</h2>
                        </div>

                        <div class="px-5 py-4">
                            @if ($errors->any())
                                <div class="mb-3 text-sm text-red-600 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            <form method="POST" action="{{ route('administrator.team.store') }}"
                                class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @csrf
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                    placeholder="Prenume" class="form-input" required>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                    placeholder="Nume" class="form-input" required>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                                    class="form-input sm:col-span-2" required>
                                <div class="relative">
                                    <input id="password" type="password" name="password" placeholder="Parolă"
                                        class="form-input pr-10" required>

                                    <button type="button" id="toggle-password"
                                        class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-slate-700">

                                        <svg id="password-eye-open" xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9.27-3.11-11-7 1.01-2.273 2.77-4.143 4.98-5.286M9.88 9.88a3 3 0 104.24 4.24M6.228 6.228A9.956 9.956 0 0112 5c5 0 9.27 3.11 11 7a11.05 11.05 0 01-4.223 5.592M3 3l18 18" />
                                        </svg>

                                        <svg id="password-eye-closed" xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                                        </svg>

                                    </button>
                                </div>
                                <div class="relative">
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                        placeholder="Confirmă parola" class="form-input pr-10" required>

                                    <button type="button" id="toggle-password-confirmation"
                                        class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-slate-700">

                                        <svg id="password-confirmation-eye-open" xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9.27-3.11-11-7 1.01-2.273 2.77-4.143 4.98-5.286M9.88 9.88a3 3 0 104.24 4.24M6.228 6.228A9.956 9.956 0 0112 5c5 0 9.27 3.11 11 7a11.05 11.05 0 01-4.223 5.592M3 3l18 18" />
                                        </svg>

                                        <svg id="password-confirmation-eye-closed" xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                                        </svg>

                                    </button>
                                </div>

                                <select name="role" class="form-input">
                                    <option value="" disabled selected>Alege rol</option>
                                    <option value="operator" @selected(old('role') === 'operator')>Operator</option>
                                    <option value="contabil" @selected(old('role') === 'contabil')>Contabil</option>
                                </select>

                                <select name="company_id" class="form-input" required>
                                    <option value="" disabled selected>Alege firma</option>
                                    @foreach ($companies as $c)
                                        <option value="{{ $c->id }}" @selected((int) old('company_id') === $c->id)>
                                            {{ $c->name }}</option>
                                    @endforeach
                                </select>

                                <button type="submit"
                                    class="ui-btn ui-btn-primary sm:col-span-2">Înregistrează</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </x-app-shell>

    <script>
        (function() {
            const searchInput = document.getElementById('team-search');
            const sortSelect = document.getElementById('team-sort');
            const list = document.getElementById('team-list');
            const empty = document.getElementById('team-empty');

            function apply() {
                const rows = Array.from(list.querySelectorAll('.team-row'));
                const query = searchInput.value.toLowerCase().trim();
                const sortBy = sortSelect.value;

                // Filtrare
                let visible = 0;
                rows.forEach((row) => {
                    const match = row.dataset.name.includes(query) || row.dataset.email.includes(query);
                    row.classList.toggle('hidden', !match);
                    if (match) visible++;
                });

                // Sortare (doar rândurile vizibile contează pentru ordine)
                rows.sort((a, b) => {
                    if (sortBy === 'az') return a.dataset.name.localeCompare(b.dataset.name);
                    if (sortBy === 'za') return b.dataset.name.localeCompare(a.dataset.name);
                    return new Date(b.dataset.created) - new Date(a.dataset.created); // recent
                });
                rows.forEach((row) => list.insertBefore(row, empty));

                empty.classList.toggle('hidden', visible !== 0);
            }

            searchInput.addEventListener('input', apply);
            sortSelect.addEventListener('change', apply);
        })();
    </script>

    <script>
        const banner = document.getElementById('status-banner');
        if (banner) {
            setTimeout(() => banner.remove(), 4000);
        }
    </script>

    <script>
        function setupPasswordToggle(inputId, buttonId, openId, closedId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);
            const eyeOpen = document.getElementById(openId);
            const eyeClosed = document.getElementById(closedId);

            button.addEventListener('click', () => {
                const visible = input.type === 'text';

                input.type = visible ? 'password' : 'text';

                eyeOpen.classList.toggle('hidden', visible);
                eyeClosed.classList.toggle('hidden', !visible);
            });
        }

        setupPasswordToggle(
            'password',
            'toggle-password',
            'password-eye-open',
            'password-eye-closed'
        );

        setupPasswordToggle(
            'password_confirmation',
            'toggle-password-confirmation',
            'password-confirmation-eye-open',
            'password-confirmation-eye-closed'
        );
    </script>

</body>

</html>
