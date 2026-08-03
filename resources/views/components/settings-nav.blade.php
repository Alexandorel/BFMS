@props(['active'])

@php
    $links = [
        ['key' => 'profile', 'label' => 'Profil', 'route' => 'administrator.settings.profile'],
        ['key' => 'company', 'label' => 'Firmă', 'route' => 'administrator.settings.company'],
        ['key' => 'team', 'label' => 'Echipă', 'route' => 'administrator.settings.team'],
        ['key' => 'bank-accounts', 'label' => 'Conturi bancare', 'route' => 'administrator.bank-accounts.index'],
        ['key' => 'series', 'label' => 'Serii documente', 'route' => 'administrator.series.index'],
    ];
@endphp

<nav class="ui-settings-nav" aria-label="Secțiuni setări">
    @foreach ($links as $link)
        <a href="{{ route($link['route']) }}"
           @if ($active === $link['key']) aria-current="page" @endif
           class="ui-settings-link {{ $active === $link['key'] ? 'ui-settings-link-active' : '' }}">
            {{ $link['label'] }}
        </a>
    @endforeach
</nav>
