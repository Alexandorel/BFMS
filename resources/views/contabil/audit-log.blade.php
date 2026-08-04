<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jurnal de audit · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@php
    // getModified() runs the values through the model casts, so what lands here
    // is not always a scalar: status is an enum, issue_date is a Carbon.
    $show = function ($value) {
        if ($value === null || $value === '') {
            return '—';
        }
        if (is_bool($value)) {
            return $value ? 'Da' : 'Nu';
        }
        if ($value instanceof \BackedEnum) {
            return method_exists($value, 'label') ? $value->label() : (string) $value->value;
        }
        if ($value instanceof \UnitEnum) {
            return $value->name;
        }
        // Dates are already serialized by getModified(), so what arrives is an
        // ISO string. The pattern is anchored on purpose: a free text field
        // that merely starts with a date must not be reformatted.
        if (is_string($value) && preg_match('/^(\d{4}-\d{2}-\d{2})(?:[T ](\d{2}:\d{2}):\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:?\d{2})?)?$/', $value, $matches)) {
            $date = \Illuminate\Support\Carbon::parse($value);

            return isset($matches[2]) && $matches[2] !== '00:00'
                ? $date->format('d.m.Y H:i')
                : $date->format('d.m.Y');
        }
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    };
@endphp

<x-app-shell>

    <header class="app-page-toolbar">
        <x-company-switcher :companies="$companies" :active-company="$company" />
    </header>

    <main class="app-page-content space-y-6">

        <x-page-header title="Jurnal de audit"
                       :description="'Cine și ce a modificat în firma '.$company->name" />

        @if ($errors->any())
            <div class="ui-alert ui-alert-danger" role="alert">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filtre --}}
        <form method="GET" action="{{ route('audit-log.index') }}"
              class="ui-card grid items-end gap-3 p-4 sm:grid-cols-2 lg:grid-cols-6">

            <label class="text-sm">
                <span class="block text-slate-600 dark:text-slate-300 mb-1">De la</span>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                       class="form-input">
            </label>

            <label class="text-sm">
                <span class="block text-slate-600 dark:text-slate-300 mb-1">Până la</span>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                       class="form-input">
            </label>

            <label class="text-sm">
                <span class="block text-slate-600 dark:text-slate-300 mb-1">Utilizator</span>
                <select name="user_id"
                        class="form-input">
                    <option value="">Toți</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" @selected(($filters['user_id'] ?? null) == $u->id)>
                            {{ $u->first_name }} {{ $u->last_name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm">
                <span class="block text-slate-600 dark:text-slate-300 mb-1">Entitate</span>
                <select name="auditable_type"
                        class="form-input">
                    <option value="">Toate</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(($filters['auditable_type'] ?? null) === $type)>
                            {{ \App\Models\Audit::ENTITY_LABELS[$type] ?? class_basename($type) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm">
                <span class="block text-slate-600 dark:text-slate-300 mb-1">Acțiune</span>
                <select name="event"
                        class="form-input">
                    <option value="">Toate</option>
                    @foreach (\App\Models\Audit::EVENT_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['event'] ?? null) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <div class="ui-button-group">
                <button type="submit"
                        class="ui-btn ui-btn-primary">
                    Filtrează
                </button>
                <a href="{{ route('audit-log.index') }}"
                   class="ui-btn ui-btn-secondary">
                    Resetează
                </a>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="ui-card overflow-hidden">
            <div class="ui-table-wrap" tabindex="0" role="region" aria-label="Înregistrări jurnal de audit">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="text-left font-medium px-5 py-3 whitespace-nowrap">Data</th>
                            <th class="text-left font-medium px-5 py-3 whitespace-nowrap">Utilizator</th>
                            <th class="text-left font-medium px-5 py-3 whitespace-nowrap">Acțiune</th>
                            <th class="text-left font-medium px-5 py-3 whitespace-nowrap">Entitate</th>
                            <th class="text-left font-medium px-5 py-3">Modificări</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($audits as $audit)
                            <tr class="align-top">
                                <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                    {{ $audit->created_at?->setTimezone(config('audit.display_timezone'))->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($audit->user)
                                        {{ $audit->user->first_name }} {{ $audit->user->last_name }}
                                    @else
                                        <span class="text-slate-400">Sistem</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @php
                                        $tone = match ($audit->event) {
                                            'created'  => 'bg-emerald-50 text-emerald-700',
                                            'deleted'  => 'bg-rose-50 text-rose-700',
                                            'restored' => 'bg-amber-50 text-amber-700',
                                            default    => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300',
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-1 rounded-md text-xs font-medium {{ $tone }}">
                                        {{ $audit->eventLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="font-medium text-slate-900 dark:text-slate-100">{{ $audit->entityLabel() }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $audit->entityName() }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    @php $modified = $audit->getModified(); @endphp

                                    @if (empty($modified))
                                        <span class="text-slate-400">—</span>
                                    @else
                                        <ul class="space-y-1">
                                            @foreach ($modified as $field => $change)
                                                <li class="text-xs">
                                                    <span class="text-slate-500 dark:text-slate-400">{{ \App\Models\Audit::fieldLabel($field) }}:</span>
                                                    @if (array_key_exists('old', $change))
                                                        <span class="line-through text-slate-400">{{ $show($change['old']) }}</span>
                                                    @endif
                                                    @if (array_key_exists('new', $change))
                                                        <span class="font-medium text-slate-900 dark:text-slate-100">{{ $show($change['new']) }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="ui-empty-state">
                                    Nicio înregistrare pentru filtrele alese.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($audits->hasPages())
            <div>{{ $audits->links() }}</div>
        @endif

    </main>
</x-app-shell>

</body>
</html>
