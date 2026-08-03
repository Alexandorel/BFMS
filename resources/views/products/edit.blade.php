<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editeaza Produs · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    <x-app-shell>
            <main class="app-page-content">
                <div class="mx-auto max-w-2xl space-y-6">
                    <x-page-header title="Editează produs" description="Actualizează informațiile comerciale și fiscale." />
                    <form method="POST" action="{{ route('products.update', $product) }}" class="ui-card p-5 sm:p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        @include('products.form')
                        <div class="ui-button-group pt-2 sm:justify-end">
                            <x-button :href="route('products.index')" variant="secondary">Anulează</x-button>
                            <x-button type="submit">Salvează modificările</x-button>
                        </div>
                    </form> 
                </div>
            </main>
    </x-app-shell>
</body>
</html>
