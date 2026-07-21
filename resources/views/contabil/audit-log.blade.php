<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jurnal de audit · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-xl font-semibold text-slate-900">Pagina Jurnal de audit</h1>
            <p class="text-slate-500 text-sm mt-1">
                Jurnalul de audit - de completat
            </p>
            <a href="{{ route('dashboard.contabil') }}"
               class="inline-block mt-4 text-sm text-indigo-600 hover:underline">
                &larr; Înapoi la dashboard
            </a>
        </div>
    </div>
</body>
</html>