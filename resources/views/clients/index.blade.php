<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produse · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">

        <x-sidebar />

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
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 space-y-6">

                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Clienți</h1>
                        <p class="text-slate-500 text-sm mt-1">Catalogul de clienți</p>
                    </div>
                    <a href="{{ route('clients.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Adaugă client
                    </a>
                </div>

                @if (session('status'))
                    <div class="p-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm">{{ session('status') }}</div>
                @endif

                @if (session('error'))
                    <div class="p-3 rounded-lg bg-rose-50 text-rose-700 text-sm">{{ session('error') }}</div>
                @endif

                <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        <div class="flex items-center justify-between px-5 py-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-slate-900">{{ $client->full_name }}</p>
                                    <span class="text-[11px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-medium">
                                        {{ $client->client_type === 'company' ? 'Juridic' : 'Fizic' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    @if ($client->tax_id)
                                        {{ $client->client_type === 'company' ? 'CUI' : 'CNP' }}: {{ $client->tax_id }} &nbsp;•&nbsp;
                                    @endif
                                    {{ $client->address }}, {{ $client->city }}, {{ $client->county }} &nbsp;•&nbsp;
                                    {{ $client->phone ?: '—' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                @if (in_array(auth()->user()->role, ['administrator', 'operator'], true))
                                    <a href="{{ route('clients.edit', $client) }}" class="text-xs text-indigo-600 hover:underline">Editează</a>
                                @endif
                                @if (auth()->user()->role === 'administrator')
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Sigur ștergi acest client?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600 hover:underline">Șterge</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-slate-500">Nu există clienți încă.</div>
                    @endforelse
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
</script>
</body>
</html>
