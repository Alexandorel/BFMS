<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clienți · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    <x-app-shell>

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
                <x-company-switcher :companies="$companies" :active-company="$company" />
            </header>

            <main class="app-page-content space-y-6">

                <x-page-header title="Clienți" description="Catalogul de clienți">
                    @if (in_array(auth()->user()->role, ['administrator', 'operator'], true))
                        <x-slot:actions>
                            <x-button :href="route('clients.create')">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Adaugă client
                            </x-button>
                        </x-slot:actions>
                    @endif
                </x-page-header>

                @if (session('status'))
                    <div class="ui-alert ui-alert-success" role="status">{{ session('status') }}</div>
                @endif

                @if (session('error'))
                    <div class="ui-alert ui-alert-danger" role="alert">{{ session('error') }}</div>
                @endif

                <div class="ui-card divide-y divide-slate-100 overflow-hidden">
                    @forelse ($clients as $client)
                        <div class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $client->full_name }}</p>
                                    <span class="ui-badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        {{ $client->client_type === 'company' ? 'Juridic' : 'Fizic' }}
                                    </span>
                                </div>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    @if ($client->tax_id)
                                        {{ $client->client_type === 'company' ? 'CUI' : 'CNP' }}: {{ $client->tax_id }} &nbsp;•&nbsp;
                                    @endif
                                    {{ $client->address }}, {{ $client->city }}, {{ $client->county }} &nbsp;•&nbsp;
                                    {{ $client->phone ?: '—' }}
                                </p>
                            </div>
                            <div class="ui-button-group shrink-0 sm:flex-nowrap">
                                @if (in_array(auth()->user()->role, ['administrator', 'operator'], true))
                                    <a href="{{ route('clients.edit', $client) }}" class="ui-action-link">Editează</a>
                                @endif
                                @if (auth()->user()->role === 'administrator')
                                    <x-confirm-action action="{{ route('clients.destroy', $client) }}"
                                                      confirm-text="Ștergi clientul?"></x-confirm-action>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="ui-empty-state">Nu există clienți încă.</div>
                    @endforelse
                </div>

            </main>
    </x-app-shell>

</body>
</html>
