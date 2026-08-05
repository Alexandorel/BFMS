@props(['role'])

<span class="ui-badge
    @if($role === 'administrator')
        bg-red-50 text-red-700
    @elseif($role === 'operator')
        bg-emerald-50 text-emerald-700
    @else
        bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300
    @endif
">
    {{ ucfirst($role) }}
</span>