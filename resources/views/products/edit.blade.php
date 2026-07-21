<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editeaza Produs · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        <x-sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <main class="flex-1 p-4 sm:p-6">
                <div class="max-w-lg mx-auto">
                    <h1 class="text-2xl font-bold text-slate-900 mb-6">Editeaza Produs</h1>
                    <form method="POST" action="{{ route('products.update', $product) }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        @include('products.form')
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                            Actualizeaza
                        </button>
                    </form> 
                </div>
            </main>
        </div>
    </div>
</body>
</html>