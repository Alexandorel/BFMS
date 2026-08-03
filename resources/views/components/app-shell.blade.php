@props(['user' => null])

@php
    $currentUser = $user ?? auth()->user();
    $initials = mb_strtoupper(
        mb_substr($currentUser->first_name ?? 'U', 0, 1)
        . mb_substr($currentUser->last_name ?? 'N', 0, 1)
    );
@endphp

<div class="app-shell" data-app-shell>
    <x-sidebar :user="$currentUser" />

    <div class="app-shell-content">
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
                <span class="font-display font-bold tracking-wide text-ink-950">BFMS</span>
            </div>

            <div class="grid size-9 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-800" aria-label="Utilizator {{ $currentUser?->first_name }}">
                {{ $initials }}
            </div>
        </header>

        {{ $slot }}
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shell = document.querySelector('[data-app-shell]');
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
