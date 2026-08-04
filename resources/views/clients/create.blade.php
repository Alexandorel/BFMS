<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Adauga produs · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 dark:text-slate-200 antialiased">

    <div class="flex min-h-screen">
        <x-sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <main class="flex-1 p-4 sm:p-6">
                <div class="max-w-lg mx-auto">
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mb-6">Adauga client</h1>

                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-lg bg-rose-50 border border-rose-200 text-sm text-rose-700">
                            <p class="font-semibold mb-2">Erori de validare:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('clients.store') }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
                        @csrf
                        @include('clients.form')
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                            Salveaza
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
