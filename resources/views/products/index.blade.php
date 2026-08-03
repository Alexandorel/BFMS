<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produse · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    <x-app-shell>

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
                <x-company-switcher :companies="$companies" :active-company="$company" />
            </header>

            <main class="app-page-content space-y-6">

                <x-page-header title="Produse" description="Catalogul de produse și servicii">
                    @if (in_array(auth()->user()->role, ['administrator', 'operator'], true))
                        <x-slot:actions>
                            <x-button :href="route('products.create')">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Adaugă produs
                            </x-button>
                        </x-slot:actions>
                    @endif
                </x-page-header>

                @if (session('status'))
                    <div class="ui-alert ui-alert-success" role="status">{{ session('status') }}</div>
                @endif

                <div class="ui-card divide-y divide-slate-100 overflow-hidden">
                    @forelse ($products as $product)
                        <div class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-900">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    SKU: {{ $product->sku }} &nbsp;•&nbsp; {{ $product->unit_measure }} &nbsp;•&nbsp;
                                    {{ number_format($product->unit_price, 2) }} RON &nbsp;•&nbsp;
                                    {{ $product->is_vat_exempt ? 'Scutit' : $product->vat_rate.'%' }}
                                </p>
                            </div>
                            <div class="ui-button-group shrink-0 sm:flex-nowrap">
                                <a href="{{ route('products.edit', $product) }}" class="ui-action-link">Editează</a>
                                @if(auth()->user()->role === 'administrator')
                                    <x-confirm-action action="{{ route('products.destroy', $product) }}"
                                                    confirm-text="Ștergi produsul?"></x-confirm-action>
                                @endif
                            </div>
                        </div>  
                    @empty
                        <div class="ui-empty-state">Nu există produse încă.</div>
                    @endforelse
                </div>

            </main>
    </x-app-shell>

</body>
</html>
