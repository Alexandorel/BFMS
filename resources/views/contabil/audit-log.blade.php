<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jurnal de audit · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

@php
    // The screen is shared by administrator and accountant, so the way back
    // cannot be hardcoded to one dashboard.
    $backRoute = auth()->user()?->role === 'contabil'
        ? route('dashboard.contabil')
        : route('dashboard.administrator');

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

<div class="flex min-h-screen">

    <x-sidebar />

    <div class="flex-1 flex flex-col min-w-0">

    <header class="flex items-center justify-between gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
        <a href="{{ $backRoute }}" class="text-sm text-indigo-600 hover:underline">
            &larr; Înapoi la dashboard
        </a>
        <span class="text-sm text-slate-500">{{ $company->name }}</span>
    </header>

    <main class="flex-1 p-4 sm:p-6 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-900">Jurnal de audit</h1>
            <p class="text-slate-500 text-sm mt-1">
                Cine ce a modificat, pe firma {{ $company->name }}
            </p>
        </div>

        {{-- Filtre --}}
        <form method="GET" action="{{ route('audit-log.index') }}"
              class="bg-white rounded-xl border border-slate-200 p-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-6 items-end">

            <label class="text-sm">
                <span class="block text-slate-600 mb-1">De la</span>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </label>

            <label class="text-sm">
                <span class="block text-slate-600 mb-1">Până la</span>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </label>

            <label class="text-sm">
                <span class="block text-slate-600 mb-1">Utilizator</span>
                <select name="user_id"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toți</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" @selected(($filters['user_id'] ?? null) == $u->id)>
                            {{ $u->first_name }} {{ $u->last_name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm">
                <span class="block text-slate-600 mb-1">Entitate</span>
                <select name="auditable_type"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toate</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(($filters['auditable_type'] ?? null) === $type)>
                            {{ \App\Models\Audit::ENTITY_LABELS[$type] ?? class_basename($type) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm">
                <span class="block text-slate-600 mb-1">Acțiune</span>
                <select name="event"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toate</option>
                    @foreach (\App\Models\Audit::EVENT_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['event'] ?? null) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                    Filtrează
                </button>
                <a href="{{ route('audit-log.index') }}"
                   class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    Resetează
                </a>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
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
                                <td class="px-5 py-4 whitespace-nowrap text-slate-500">
                                    {{ $audit->created_at?->format('d.m.Y H:i') }}
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
                                            default    => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-1 rounded-md text-xs font-medium {{ $tone }}">
                                        {{ $audit->eventLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="font-medium text-slate-900">{{ $audit->entityLabel() }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $audit->entityName() }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    @php $modified = $audit->getModified(); @endphp

                                    @if (empty($modified))
                                        <span class="text-slate-400">—</span>
                                    @else
                                        <ul class="space-y-1">
                                            @foreach ($modified as $field => $change)
                                                <li class="text-xs">
                                                    <span class="text-slate-500">{{ \App\Models\Audit::fieldLabel($field) }}:</span>
                                                    @if (array_key_exists('old', $change))
                                                        <span class="line-through text-slate-400">{{ $show($change['old']) }}</span>
                                                    @endif
                                                    @if (array_key_exists('new', $change))
                                                        <span class="font-medium text-slate-900">{{ $show($change['new']) }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">
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
    </div>
</div>

</body>
</html>
