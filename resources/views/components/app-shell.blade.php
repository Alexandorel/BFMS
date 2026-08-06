@props(['user' => null])

@php
    $currentUser = $user ?? auth()->user();
    $initials = mb_strtoupper(
        mb_substr($currentUser->first_name ?? 'U', 0, 1)
        . mb_substr($currentUser->last_name ?? 'N', 0, 1)
    );
    // Tema e citita din cookie pe server, ca sa fie randata corect din prima (fara flash).
    $isDark = request()->cookie('theme') === 'dark';
@endphp

<div class="app-shell {{ $isDark ? 'dark' : '' }}" data-app-shell>
    <x-sidebar :user="$currentUser" />

    <div class="app-shell-content">
        <x-button variant="ghost" icon-only data-theme-toggle
                  class="fixed top-2.5 right-4 z-40 hidden md:inline-flex"
                  aria-label="Comută tema" title="Comută tema">
            <svg class="size-5 text-slate-500 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg class="hidden size-5 text-amber-400 dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </x-button>

        <header class="app-mobile-header">
            <button type="button"
                    data-navigation-open
                    class="ui-btn ui-btn-ghost ui-btn-icon -ml-2"
                    aria-controls="mobile-navigation"
                    aria-expanded="false"
                    aria-label="Deschide meniul">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="flex items-center gap-2">
                <x-brand-mark class="size-8" />
                <span class="font-display font-bold tracking-wide text-ink-950 dark:text-slate-100">BFMS</span>
            </div>

            <div class="flex items-center gap-1">
                <x-button variant="ghost" icon-only data-theme-toggle aria-label="Comută tema" title="Comută tema">
                    <svg class="size-5 text-slate-500 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg class="hidden size-5 text-amber-400 dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </x-button>

                <div class="grid size-9 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-800" aria-label="Utilizator {{ $currentUser?->first_name }}">
                    {{ $initials }}
                </div>
            </div>
        </header>

        {{ $slot }}
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shell = document.querySelector('[data-app-shell]');

            // Dark mode: comuta clasa .dark pe shell si salveaza preferinta in cookie (1 an).
            document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const isDark = shell.classList.toggle('dark');
                    document.cookie = 'theme=' + (isDark ? 'dark' : 'light') + ';path=/;max-age=31536000;samesite=lax';
                });
            });

            const navigation = shell?.querySelector('[data-mobile-navigation]');
            const openButton = shell?.querySelector('[data-navigation-open]');
            const closeButtons = shell?.querySelectorAll('[data-navigation-close], [data-navigation-backdrop]');

            if (! navigation || ! openButton) return;

            function openNavigation() {
                navigation.classList.remove('hidden');
                navigation.setAttribute('aria-hidden', 'false');
                openButton.setAttribute('aria-expanded', 'true');
                document.documentElement.classList.add('overflow-hidden');
                navigation.querySelector('[data-navigation-close]')?.focus();
            }

            function closeNavigation() {
                navigation.classList.add('hidden');
                navigation.setAttribute('aria-hidden', 'true');
                openButton.setAttribute('aria-expanded', 'false');
                document.documentElement.classList.remove('overflow-hidden');
                openButton.focus();
            }

            openButton.addEventListener('click', openNavigation);
            closeButtons.forEach(function (button) {
                button.addEventListener('click', closeNavigation);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && navigation.getAttribute('aria-hidden') === 'false') {
                    closeNavigation();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768 && navigation.getAttribute('aria-hidden') === 'false') {
                    closeNavigation();
                }
            });
        });
    </script>
@endonce
