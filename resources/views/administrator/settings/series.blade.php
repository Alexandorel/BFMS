<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serii documente · Setări · {{ config('app.name', 'BFMS') }}</title>
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
                                <option value="{{ $c->id }}" @selected($company?->id === $c->id)>{{ $c->name }}</option>
                            @empty
                                <option value="">Nicio firmă</option>
                            @endforelse
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                    <a href="{{ route('administrator.settings.addcompany') }}" class="inline-flex items-center p-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-700 transition" title="Adaugă firmă">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
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

                {{-- pagina are mai multe formulare, asa ca erorile se afiseaza centralizat --}}
                @if ($errors->any())
                    <div class="px-4 py-3 rounded-lg bg-rose-50 text-rose-800 text-sm">
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
                        <a href="{{ route('administrator.settings.profile') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Profil</a>
                        <a href="{{ route('administrator.settings.company') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Firmă</a>
                        <a href="{{ route('administrator.settings.team') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Echipă</a>
                        <a href="{{ route('administrator.bank-accounts.index') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white">Conturi bancare</a>
                        <a href="{{ route('administrator.series.index') }}" class="block px-3 py-2 rounded-lg bg-white border border-slate-200 text-indigo-700 font-medium">Serii documente</a>
                    </nav>

                    <div class="lg:col-span-3 space-y-6">

                        @if (! $company)

                            <div class="bg-white rounded-xl border border-slate-200 px-5 py-8 text-center">
                                <p class="text-sm text-slate-600">Nu ai încă nicio firmă adăugată.</p>
                                <a href="{{ route('administrator.settings.addcompany') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                    Adaugă prima firmă
                                </a>
                            </div>

                        @else

                            {{-- Alegerea firmei editate --}}
                            @if ($companies->count() > 1)
                                <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
                                    <form action="{{ route('administrator.series.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                                        <div class="flex-1">
                                            <label for="firma" class="block text-sm font-medium text-slate-700 mb-1">Firma pe care o configurezi</label>
                                            <select id="firma" name="firma"
                                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                @foreach ($companies as $c)
                                                    <option value="{{ $c->id }}" @selected($c->id === $company->id)>{{ $c->name }} · CUI {{ $c->cui }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                            Încarcă
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @foreach ($documentTypes as $type)
                                @php
                                    $list = $seriesByType[$type->value] ?? collect();
                                @endphp

                                <div class="bg-white rounded-xl border border-slate-200">

                                    <div class="px-5 py-4 border-b border-slate-200">
                                        <h2 class="font-semibold text-slate-900">{{ $type->label() }}</h2>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Seria implicită este cea folosită automat la emitere
                                        </p>
                                    </div>

                                    @forelse ($list as $s)
                                        <div class="px-5 py-4 border-b border-slate-100 flex flex-col xl:flex-row xl:items-end gap-4 {{ $s->is_active ? '' : 'bg-slate-50' }}">

                                            {{-- Editare prefix + numar de pornire --}}
                                            <form action="{{ route('administrator.series.update', $s) }}" method="POST"
                                                  class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                                                @csrf
                                                @method('PUT')

                                                <div>
                                                    <label class="block text-xs font-medium text-slate-500 mb-1">Prefix</label>
                                                    <input type="text" name="prefix" maxlength="10" required
                                                           value="{{ $s->prefix }}"
                                                           @disabled($s->is_used)
                                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-slate-100 disabled:text-slate-500">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium text-slate-500 mb-1">Nr. pornire</label>
                                                    <input type="number" name="start_number" min="1" required
                                                           value="{{ $s->start_number }}"
                                                           @disabled($s->is_used)
                                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-slate-100 disabled:text-slate-500">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium text-slate-500 mb-1">Următorul document</label>
                                                    <p class="px-3 py-2 text-sm font-mono text-slate-700">
                                                        {{ $s->prefix }}-{{ $s->next_number }}
                                                    </p>
                                                </div>

                                                <div>
                                                    @if ($s->is_used)
                                                        <p class="text-xs text-slate-400 leading-tight">
                                                            Serie folosită — prefixul și numărul de pornire nu se mai pot modifica.
                                                        </p>
                                                    @else
                                                        <button type="submit"
                                                                class="w-full px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                                                            Salvează
                                                        </button>
                                                    @endif
                                                </div>
                                            </form>

                                            {{-- Stare + actiuni --}}
                                            <div class="flex flex-wrap items-center gap-2 shrink-0">

                                                @if ($s->is_default)
                                                    <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">Implicită</span>
                                                @endif

                                                @if (! $s->is_active)
                                                    <span class="text-xs px-2 py-1 rounded-full bg-slate-200 text-slate-600 font-medium">Inactivă</span>
                                                @endif

                                                @if (! $s->is_default && $s->is_active)
                                                    <form action="{{ route('administrator.series.default', $s) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-medium hover:bg-slate-50 transition">
                                                            Fă implicită
                                                        </button>
                                                    </form>
                                                @endif

                                                @unless ($s->is_default && $s->is_active)
                                                    <form action="{{ route('administrator.series.status', $s) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-medium transition {{ $s->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-700 hover:bg-emerald-50' }}">
                                                            {{ $s->is_active ? 'Dezactivează' : 'Reactivează' }}
                                                        </button>
                                                    </form>
                                                @endunless
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-5 py-6 text-center text-sm text-slate-400">
                                            Nicio serie definită pentru {{ mb_strtolower($type->label()) }}.
                                        </div>
                                    @endforelse

                                    {{-- Adaugare serie noua --}}
                                    <form action="{{ route('administrator.series.store') }}" method="POST"
                                          class="px-5 py-4 grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                                        @csrf
                                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                                        <input type="hidden" name="document_type" value="{{ $type->value }}">

                                        <div>
                                            <label for="prefix-{{ $type->value }}" class="block text-xs font-medium text-slate-500 mb-1">Prefix serie nouă</label>
                                            <input type="text" id="prefix-{{ $type->value }}" name="prefix" maxlength="10" required
                                                   placeholder="{{ $type->defaultPrefix() }}"
                                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        </div>

                                        <div>
                                            <label for="start-{{ $type->value }}" class="block text-xs font-medium text-slate-500 mb-1">Nr. pornire</label>
                                            <input type="number" id="start-{{ $type->value }}" name="start_number" min="1" required
                                                   value="1"
                                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        </div>

                                        <div class="flex items-center gap-2 pb-2">
                                            <input type="hidden" name="is_default" value="0">
                                            <input type="checkbox" id="default-{{ $type->value }}" name="is_default" value="1"
                                                   class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <label for="default-{{ $type->value }}" class="text-sm text-slate-600">Serie implicită</label>
                                        </div>

                                        <div>
                                            <button type="submit"
                                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                                Adaugă serie
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach

                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

<script>
    document.getElementById('companySelect').addEventListener('change', function() {
        const companyId = this.value;
        window.location.href = `/company/switch/${companyId}`;
    });
</script>
</body>
</html>
