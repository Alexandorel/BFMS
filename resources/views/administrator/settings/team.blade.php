<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Echipă · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    @php
        
        $contabil = null;
    @endphp

    <div class="flex min-h-screen">

        {{-- Side Bar --}}
        <aside class="hidden lg:flex w-64 flex-col border-r border-slate-200 bg-white">
            <div class="flex items-center gap-2 px-6 h-16 border-b border-slate-200">
                <div class="grid place-items-center w-9 h-9 rounded-lg bg-indigo-600 text-white font-bold">B</div>
                <span class="font-semibold text-lg">BFMS</span>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="{{ url('/dashboard/administrator') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-50">
                    <span class="w-2 h-2 rounded-full bg-slate-300"></span> Dashboard
                </a>
            </nav>

            <div class="px-3 pb-2">
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-medium text-sm">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Setări
                </a>
            </div>

            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="grid place-items-center w-9 h-9 rounded-full bg-slate-200 text-slate-600 font-semibold text-sm">AV</div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">Alexandru V.</p>
                        <p class="text-xs text-slate-500">Operator</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top Bar --}}
            <header class="flex items-center gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
                <div class="flex items-center gap-3">
                    <label class="relative">
                        <select class="appearance-none pl-3 pr-9 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option>SC Exemplu SRL</option>
                            <option>Demo Consulting SRL</option>
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                    <button type="button" class="p-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-700 transition" title="Adaugă firmă">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <span class="hidden sm:inline text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">Plătitor TVA</span>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 space-y-6">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Setări</h1>
                    <p class="text-slate-500 text-sm mt-1">Configurările firmei SC Exemplu SRL</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Settings sub-nav --}}
                    <nav class="lg:col-span-1 space-y-1 text-sm">
                        <a href="{{ route('administrator.settings.profile') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Profil</a>
                        <a href="{{ route('administrator.settings.company') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Firmă</a>
                        <a href="{{ route('administrator.settings.team') }}" class="block px-3 py-2 rounded-lg bg-white border border-slate-200 text-indigo-700 font-medium">Echipă</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Conturi bancare</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Serii documente</a>
                    </nav>

                    {{-- Echipa --}}
                    <div class="lg:col-span-3 space-y-6">

                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Echipă</h2>    
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
                                            <button type="button" class="text-xs text-rose-600 hover:underline">Revocă acces</button>
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

                        {{-- Adaugare contabil --}}
                        <div class="bg-white rounded-xl border border-slate-200">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <h2 class="font-semibold text-slate-900">Adaugă contabil</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Trimite o invitație pe email</p>
                            </div>
                            <form action="#" method="POST" class="px-5 py-4">
                                @csrf
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex-1">
                                        <label for="email_contabil" class="sr-only">Email contabil</label>
                                        <input type="email" id="email_contabil" name="email" required
                                               placeholder="contabil@exemplu.ro"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Trimite invitația
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">Dacă persoana nu are cont BFMS, va primi un link de înregistrare.</p>
                            </form>
                        </div>

                    </div>
                </div>

            </main>
        </div>
    </div>

</body>
</html>
