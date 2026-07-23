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
            <main class="flex-1 p-4 sm:p-6 space-y-6">

                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Produse</h1>
                        <p class="text-slate-500 text-sm mt-1">Catalogul de produse si servicii</p>
                    </div>
                    <a href="{{ route('produse.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Adauga produs
                    </a>
                </div>

                @if (session('status'))
                    <div class="p-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm">{{ session('status') }}</div>
                @endif

                <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <div class="flex items-center justify-between px-5 py-4">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    SKU: {{ $product->sku }} &nbsp;•&nbsp; {{ $product->unit_measure }} &nbsp;•&nbsp;
                                    {{ number_format($product->unit_price, 2) }} RON &nbsp;•&nbsp;
                                    {{ $product->is_vat_exempt ? 'Scutit' : $product->vat_rate.'%' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('produse.edit', $product) }}" class="text-xs text-indigo-600 hover:underline">Editeaza</a>
                                <form method="POST" action="{{ route('produse.destroy', $product) }}" onsubmit="return confirm('Sigur stergi acest produs?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 hover:underline">Sterge</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-slate-500">Nu exista produse inca.</div>
                    @endforelse
                </div>

            </main>
        </div>
    </div>
</body>
</html>