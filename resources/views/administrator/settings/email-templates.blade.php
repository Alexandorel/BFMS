<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Șabloane email · Setări · {{ config('app.name', 'BFMS') }}</title>
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
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}" {{ $company?->id === $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </label>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 space-y-6">

                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Setări</h1>
                    <p class="text-slate-500 text-sm mt-1">Șabloane pentru emailurile trimise automat clienților</p>
                </div>

                @if (session('success'))
                    <div class="px-4 py-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Settings sub-nav --}}
                    <nav class="lg:col-span-1 space-y-1 text-sm">
                        <a href="{{ route('administrator.settings.profile') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Profil</a>
                        <a href="{{ route('administrator.settings.company') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Firmă</a>
                        <a href="{{ route('administrator.settings.team') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Echipă</a>
                        <a href="{{ route('administrator.bank-accounts.index') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Conturi bancare</a>
                        <a href="{{ route('administrator.series.index') }}" class="block px-3 py-2 rounded-lg text-slate-600 hover:bg-white hover:border-slate-200">Serii documente</a>
                        <a href="{{ route('administrator.email-templates.index') }}" class="block px-3 py-2 rounded-lg bg-white border border-slate-200 text-indigo-700 font-medium">Șabloane email</a>
                    </nav>

                    {{-- Sabloane --}}
                    <div class="lg:col-span-3 space-y-6">

                        @foreach ($templates as $tpl)
                            <div class="bg-white rounded-xl border border-slate-200">
                                <div class="px-5 py-4 border-b border-slate-200">
                                    <h2 class="font-semibold text-slate-900">{{ $tpl['label'] }}</h2>
                                </div>

                                <form action="{{ route('administrator.email-templates.update', $tpl['type']) }}" method="POST" class="px-5 py-5 space-y-4">
                                    @csrf
                                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                                    <input type="hidden" name="type" value="{{ $tpl['type'] }}">

                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                        <div class="lg:col-span-2 space-y-4">
                                            <div>
                                                <label for="subject_{{ $tpl['type'] }}" class="form-label">Subiect</label>
                                                <input type="text" id="subject_{{ $tpl['type'] }}" name="subject" required maxlength="255"
                                                       value="{{ old('subject', $tpl['subject']) }}"
                                                       class="form-input template-subject">
                                            </div>
                                            <div>
                                                <label for="body_{{ $tpl['type'] }}" class="form-label">Mesaj</label>
                                                <textarea id="body_{{ $tpl['type'] }}" name="body" required rows="16"
                                                          class="form-input template-body">{{ old('body', $tpl['body']) }}</textarea>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="submit" class="form-btn-primary">Salvează</button>
                                            </div>
                                        </div>

                                        {{-- Variabile disponibile --}}
                                        <div class="lg:col-span-1">
                                            <p class="form-label">Variabile disponibile</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($variables as $variable)
                                                    <button type="button"
                                                            class="variable-btn text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium hover:bg-indigo-100"
                                                            data-target="body_{{ $tpl['type'] }}"
                                                            data-value="{{ $variable }}">
                                                        {{ $variable }}
                                                    </button>
                                                @endforeach
                                            </div>
                                            <p class="text-xs text-slate-400 mt-2">Click pe o variabilă o inserează în mesaj, la poziția cursorului.</p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endforeach

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

    document.querySelectorAll('.variable-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const textarea = document.getElementById(this.dataset.target);
            const value = this.dataset.value;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;

            textarea.value = text.slice(0, start) + value + text.slice(end);
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = start + value.length;
        });
    });
</script>
<style>
    .template-body{
        min-height: 260px;
        width: 100%;
        resize: vertical;
    }
</style>
</body>
</html>