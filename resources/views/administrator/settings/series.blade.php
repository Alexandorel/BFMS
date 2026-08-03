<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serii documente · Setări · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    <x-app-shell>

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
                <x-company-switcher :companies="$companies" :active-company="$company"
                                    :add-href="route('administrator.settings.addcompany')" />
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

                {{-- pagina are mai multe formulare, asa ca erorile se afiseaza centralizat --}}
                @if ($errors->any())
                    <div class="ui-alert ui-alert-danger" role="alert">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Settings sub-nav --}}
                    <x-settings-nav active="series" />

                    <div class="lg:col-span-3 space-y-6">

                        @if (! $company)

                            <div class="ui-card ui-empty-state">
                                <p class="text-sm text-slate-600">Nu ai încă nicio firmă adăugată.</p>
                                <a href="{{ route('administrator.settings.addcompany') }}" class="ui-btn ui-btn-primary mt-4">
                                    Adaugă prima firmă
                                </a>
                            </div>

                        @else

                            {{-- Alegerea firmei editate --}}
                            @if ($companies->count() > 1)
                                <div class="ui-card px-5 py-4">
                                    <form action="{{ route('administrator.series.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                                        <div class="flex-1">
                                            <label for="firma" class="block text-sm font-medium text-slate-700 mb-1">Firma pe care o configurezi</label>
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

                            @foreach ($documentTypes as $type)
                                @php
                                    $list = $seriesByType[$type->value] ?? collect();
                                @endphp

                                <div class="ui-card overflow-hidden">

                                    <div class="ui-card-header block">
                                        <h2 class="ui-section-title">{{ $type->label() }}</h2>
                                        <p class="ui-section-description">
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
                                                    <label for="prefix-{{ $s->id }}" class="block text-xs font-medium text-slate-500 mb-1">Prefix</label>
                                                    <input id="prefix-{{ $s->id }}" type="text" name="prefix" maxlength="10" required
                                                           value="{{ $s->prefix }}"
                                                           @disabled($s->is_used)
                                                           class="form-input font-mono uppercase">
                                                </div>

                                                <div>
                                                    <label for="start-{{ $s->id }}" class="block text-xs font-medium text-slate-500 mb-1">Nr. pornire</label>
                                                    <input id="start-{{ $s->id }}" type="number" name="start_number" min="1" required
                                                           value="{{ $s->start_number }}"
                                                           @disabled($s->is_used)
                                                           class="form-input">
                                                </div>

                                                <div>
                                                    <span class="block text-xs font-medium text-slate-500 mb-1">Următorul document</span>
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
                                                                class="ui-btn ui-btn-primary w-full">
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
                                                                class="ui-btn ui-btn-secondary">
                                                            Fă implicită
                                                        </button>
                                                    </form>
                                                @endif

                                                @unless ($s->is_default && $s->is_active)
                                                    <form action="{{ route('administrator.series.status', $s) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="ui-btn {{ $s->is_active ? 'ui-btn-danger-ghost' : 'border border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50 hover:text-emerald-800' }}">
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
                                                   class="form-input font-mono uppercase">
                                        </div>

                                        <div>
                                            <label for="start-{{ $type->value }}" class="block text-xs font-medium text-slate-500 mb-1">Nr. pornire</label>
                                            <input type="number" id="start-{{ $type->value }}" name="start_number" min="1" required
                                                   value="1"
                                                   class="form-input">
                                        </div>

                                        <div class="flex items-center gap-2 pb-2">
                                            <input type="hidden" name="is_default" value="0">
                                            <input type="checkbox" id="default-{{ $type->value }}" name="is_default" value="1"
                                                   class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <label for="default-{{ $type->value }}" class="text-sm text-slate-600">Serie implicită</label>
                                        </div>

                                        <div>
                                            <button type="submit"
                                                    class="ui-btn ui-btn-secondary w-full">
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
    </x-app-shell>

<script>
</script>
</body>
</html>
