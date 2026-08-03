@props([
    'pageTitle',
    'title',
    'description' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }} · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="grid min-h-screen lg:grid-cols-[minmax(0,1fr)_minmax(28rem,0.8fr)]">
        <aside class="relative hidden overflow-hidden bg-brand-700 p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-14">
            <div class="absolute -top-24 -right-24 size-80 rounded-full border-[3rem] border-white/5" aria-hidden="true"></div>
            <div class="relative flex items-center gap-3">
                <span class="grid size-12 place-items-center rounded-2xl bg-white shadow-lg">
                    <x-brand-mark class="size-10" />
                </span>
                <span class="font-display text-xl font-bold tracking-wide">BFMS</span>
            </div>

            <div class="relative max-w-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-200">Management financiar</p>
                <h2 class="mt-4 font-display text-4xl font-bold leading-tight xl:text-5xl">
                    Documentele firmei, într-un singur loc.
                </h2>
                <p class="mt-5 max-w-lg text-base leading-7 text-brand-100">
                    Emite facturi, urmărește încasările și păstrează activitatea financiară organizată.
                </p>
            </div>

            <div aria-hidden="true"></div>
        </aside>

        <main class="flex items-center justify-center bg-app-canvas px-4 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <x-brand-mark class="size-10" />
                    <span class="font-display text-lg font-bold tracking-wide text-ink-950">BFMS</span>
                </div>

                <h1 class="ui-page-title">{{ $title }}</h1>
                @if ($description)
                    <p class="ui-page-description">{{ $description }}</p>
                @endif

                <div class="ui-card mt-6 p-5 sm:p-7">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="mt-6 text-center text-sm text-slate-500">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </main>
    </div>
</body>
</html>
