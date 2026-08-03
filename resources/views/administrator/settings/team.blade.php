<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Echipă · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @php

        $contabil = null;
    @endphp

    <x-app-shell>

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
                <x-company-switcher :companies="$companies" :active-company="$company"
                                    :add-href="route('administrator.settings.addcompany')" />
            </header>

            {{-- Content --}}
            <main class="app-page-content space-y-6">

                <x-page-header title="Setări" :description="'Configurările firmei '.($company?->name ?? '—')" />

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Settings sub-nav --}}
                    <x-settings-nav active="team" />

                    {{-- Echipa --}}
                    <div class="lg:col-span-3 space-y-6">

                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header">
                                <h2 class="ui-section-title">Echipă</h2>
                            </div>

                            <ul class="divide-y divide-slate-100">
                                {{-- Operatorul: proprietarul firmei --}}
                                <li class="flex items-center justify-between gap-3 px-5 py-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="grid place-items-center w-9 h-9 rounded-full bg-slate-200 text-slate-600 font-semibold text-sm shrink-0">AV</div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-slate-900 truncate">Alexandru V.</p>
                                            <p class="text-xs text-slate-500 truncate">vintalexandru03@gmail.com</p>
                                        </div>
                                    </div>
                                    <span class="shrink-0 text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">Operator</span>
                                </li>

                                @if ($contabil)
                                    <li class="flex items-center justify-between gap-3 px-5 py-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="grid place-items-center w-9 h-9 rounded-full bg-slate-200 text-slate-600 font-semibold text-sm shrink-0">
                                                {{ Str::of($contabil['nume'])->explode(' ')->map(fn ($p) => Str::substr($p, 0, 1))->join('') }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-slate-900 truncate">{{ $contabil['nume'] }}</p>
                                                <p class="text-xs text-slate-500 truncate">{{ $contabil['email'] }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium">Contabil</span>
                                            <button type="button" class="ui-action-danger" disabled title="Funcție indisponibilă momentan">Revocă acces</button>
                                        </div>
                                    </li>
                                @endif
                            </ul>

                            {{-- Empty state (nu exista contabil) --}}
                            @unless ($contabil)
                                <div class="px-5 py-8 border-t border-slate-100 text-center">
                                    <div class="grid place-items-center w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-400">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                    </div>
                                    <p class="mt-3 text-sm font-medium text-slate-900">Niciun contabil asignat</p>
                                </div>
                            @endunless
                        </div>

                        {{-- Invitațiile vor fi activate când serviciul de email este disponibil. --}}
                        <div class="ui-card overflow-hidden">
                            <div class="ui-card-header block">
                                <h2 class="ui-section-title">Invită un contabil</h2>
                                <p class="ui-section-description">Funcția va fi disponibilă după configurarea serviciului de email.</p>
                            </div>
                            <div class="px-5 py-4">
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex-1">
                                        <label for="email_contabil" class="sr-only">Email contabil</label>
                                        <input type="email" id="email_contabil" name="email" disabled
                                               placeholder="contabil@exemplu.ro"
                                               class="form-input">
                                    </div>
                                    <button type="button" class="ui-btn ui-btn-secondary" disabled>
                                        Indisponibil momentan
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </main>
    </x-app-shell>

</body>
</html>
