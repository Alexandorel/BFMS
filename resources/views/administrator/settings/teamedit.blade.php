<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editează cont · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <x-app-shell>

        <header class="app-page-toolbar">
            <x-company-switcher :companies="$companies" :active-company="$companies->first()">
                <x-slot:meta>
                    <x-role-badge :role="auth()->user()->role" />
                </x-slot:meta>
            </x-company-switcher>
        </header>

        <main class="app-page-content space-y-6">

            <x-page-header title="Editează cont" :description="'Modifică datele pentru ' . $user->first_name . ' ' . $user->last_name" />

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <x-settings-nav active="team" />

                <div class="lg:col-span-3">
                    <div class="ui-card overflow-hidden">
                        <div class="ui-card-header">
                            <h2 class="ui-section-title">Date cont</h2>
                        </div>

                        <div class="px-5 py-4">
                            <form method="POST" action="{{ route('administrator.team.update', $user) }}"
                                class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @csrf
                                @method('PUT')

                                <input type="text" name="first_name"
                                    value="{{ old('first_name', $user->first_name) }}" placeholder="Prenume"
                                    class="form-input" required>
                                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                    placeholder="Nume" class="form-input" required>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    placeholder="Email" class="form-input sm:col-span-2" required>

                                <select name="role" class="form-input">
                                    <option value="operator" @selected(old('role', $user->role) === 'operator')>Operator</option>
                                    <option value="contabil" @selected(old('role', $user->role) === 'contabil')>Contabil</option>
                                </select>

                                <select name="company_id" class="form-input" required>
                                    @foreach ($companies as $c)
                                        <option value="{{ $c->id }}" @selected((int) old('company_id', $user->companies->first()?->id) === $c->id)>
                                            {{ $c->name }}</option>
                                    @endforeach
                                </select>

                                <div class="sm:col-span-2 flex items-center justify-between gap-3 mt-2">
                                    <a href="{{ route('administrator.settings.team') }}"
                                        class="text-sm text-slate-500 hover:text-slate-700">Anulează</a>
                                    <button type="submit" class="ui-btn ui-btn-primary">Salvează</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </x-app-shell>

</body>

</html>
