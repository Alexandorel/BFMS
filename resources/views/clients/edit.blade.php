<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editează client · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    <x-app-shell>
            <main class="app-page-content">
                <div class="mx-auto max-w-2xl space-y-6">
                    <x-page-header title="Editează client" description="Actualizează datele clientului." />

                    @if ($errors->any())
                        <div class="ui-alert ui-alert-danger">
                            <p class="mb-2 font-semibold">Erori de validare:</p>
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('clients.update', $client) }}" class="ui-card p-5 sm:p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        @include('clients.form')
                        <div class="ui-button-group pt-2 sm:justify-end">
                            <x-button :href="route('clients.index')" variant="secondary">Anulează</x-button>
                            <x-button type="submit">Actualizează clientul</x-button>
                        </div>
                    </form>
                </div>
            </main>
    </x-app-shell>
</body>
</html>
