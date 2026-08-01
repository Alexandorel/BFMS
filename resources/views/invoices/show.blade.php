<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->series }}-{{ $invoice->number }} · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">

        {{-- Side Bar --}}
        <x-sidebar />

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top Bar --}}
            <header class="flex items-center gap-4 h-16 px-4 sm:px-6 border-b border-slate-200 bg-white">
                <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Înapoi
                </a>
                {{-- pe o ciorna editabila eticheta ar contrazice butoanele de mai jos --}}
                @unless ($invoice->status->isDraft() && in_array(auth()->user()->role, ['administrator', 'operator'], true))
                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium">Doar vizualizare</span>
                @endunless
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 space-y-6 max-w-5xl w-full">

                @if (session('success'))
                    <div class="px-4 py-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="px-4 py-3 rounded-lg bg-rose-50 text-rose-800 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="px-4 py-3 rounded-lg bg-rose-50 text-rose-800 text-sm">
                        <p class="font-medium mb-1">Verifică datele introduse:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Antet --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-bold text-slate-900">
                                    {{ $invoice->number ? $invoice->series . '-' . $invoice->number : 'Ciornă (fără număr)' }}
                                </h1>
                                <x-invoice-status-badge :status="$invoice->status" />
                            </div>
                            <p class="text-sm text-slate-500 mt-1">{{ $invoice->document_type->label() }}</p>
                        </div>
                        <div class="text-sm text-slate-600 space-y-1 sm:text-right">
                            <p><span class="text-slate-400">Data emiterii:</span>
                                {{ $invoice->issue_date?->format('d.m.Y') ?? '—' }}</p>
                            <p><span class="text-slate-400">Scadență:</span>
                                {{ $invoice->due_date?->format('d.m.Y') ?? '—' }}</p>

                            {{-- ciornele se editeaza si se sterg; contabilul e read-only --}}
                            @if ($invoice->status->isDraft() && in_array(auth()->user()->role, ['administrator', 'operator'], true))
                                <div class="flex flex-wrap gap-2 pt-3 sm:justify-end">
                                    <a href="{{ route('invoices.edit', $invoice) }}"
                                       class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                        Editează
                                    </a>

                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                                          onsubmit="return confirm('Ștergi definitiv această ciornă?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-rose-600 text-sm font-medium hover:bg-rose-50 transition">
                                            Șterge
                                        </button>
                                    </form>

                                    <form action="{{ route('invoices.issue', $invoice) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                                            Emite documentul
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($invoice->creditedInvoice)
                        <div class="mt-4 text-sm px-3 py-2 rounded-lg bg-rose-50 text-rose-700">
                            Această factură stornează
                            <a href="{{ route('invoices.show', $invoice->creditedInvoice) }}" class="font-medium underline">
                                {{ $invoice->creditedInvoice->series }}-{{ $invoice->creditedInvoice->number }}
                            </a>.
                        </div>
                    @endif
                </div>

                {{-- Emitent + Client --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl border border-slate-200 p-5">
                        <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Emitent</h2>
                        <p class="font-medium text-slate-900">{{ $invoice->company->name }}</p>
                        <p class="text-sm text-slate-600 mt-1">CUI: {{ $invoice->company->cui ?? '—' }}</p>
                        @if ($invoice->company->trade_registry_number)
                            <p class="text-sm text-slate-600">Reg. com.: {{ $invoice->company->trade_registry_number }}</p>
                        @endif
                        <p class="text-sm text-slate-600">
                            {{ collect([$invoice->company->address, $invoice->company->city, $invoice->company->county])->filter()->implode(', ') ?: '—' }}
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 p-5">
                        <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Client</h2>
                        <p class="font-medium text-slate-900">{{ $invoice->client?->full_name ?? '—' }}</p>
                        <p class="text-sm text-slate-600 mt-1">
                            {{ $invoice->client?->client_type === 'individual' ? 'CNP' : 'CUI' }}:
                            {{ $invoice->client?->tax_id ?? '—' }}
                        </p>
                        <p class="text-sm text-slate-600">
                            {{ collect([$invoice->client?->address, $invoice->client?->city, $invoice->client?->county])->filter()->implode(', ') ?: '—' }}
                        </p>
                        @if ($invoice->client?->email)
                            <p class="text-sm text-slate-600">{{ $invoice->client->email }}</p>
                        @endif
                    </div>
                </div>

                {{-- Linii --}}
                <div class="bg-white rounded-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h2 class="font-semibold text-slate-900">Linii factură</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500 border-b border-slate-100">
                                    <th class="px-5 py-3 font-medium">#</th>
                                    <th class="px-5 py-3 font-medium">Produs / serviciu</th>
                                    <th class="px-5 py-3 font-medium text-right">Cant.</th>
                                    <th class="px-5 py-3 font-medium text-right">Preț unitar</th>
                                    <th class="px-5 py-3 font-medium text-right">TVA %</th>
                                    <th class="px-5 py-3 font-medium text-right">Total linie</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($invoice->lines as $line)
                                    <tr>
                                        <td class="px-5 py-3 text-slate-500">{{ $line->position }}</td>
                                        <td class="px-5 py-3 text-slate-900">
                                            {{ $line->product_name_snapshot }}
                                            @if ($line->sku_snapshot)
                                                <span class="text-xs text-slate-400">({{ $line->sku_snapshot }})</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-right text-slate-600">
                                            {{ number_format($line->quantity, 2, ',', '.') }} {{ $line->unit_measure_snapshot }}
                                        </td>
                                        <td class="px-5 py-3 text-right text-slate-600">
                                            {{ number_format($line->unit_price_snapshot, 2, ',', '.') }}
                                        </td>
                                        <td class="px-5 py-3 text-right text-slate-600">
                                            {{ number_format($line->vat_rate_snapshot, 2, ',', '.') }}%
                                        </td>
                                        <td class="px-5 py-3 text-right font-medium text-slate-900">
                                            {{ number_format($line->line_total, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                            Factura nu are linii.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Totaluri --}}
                    <div class="border-t border-slate-200 px-5 py-4 flex justify-end">
                        <dl class="w-full sm:w-72 space-y-1 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Subtotal</dt>
                                <dd class="text-slate-900">{{ number_format($invoice->subtotal, 2, ',', '.') }} {{ $invoice->currency }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">TVA</dt>
                                <dd class="text-slate-900">{{ number_format($invoice->vat_total, 2, ',', '.') }} {{ $invoice->currency }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-slate-100 pt-1 font-semibold">
                                <dt class="text-slate-900">Total</dt>
                                <dd class="text-slate-900">{{ number_format($invoice->total, 2, ',', '.') }} {{ $invoice->currency }}</dd>
                            </div>
                            @if ($invoice->currency !== 'RON')
                                <div class="flex justify-between text-xs text-slate-400">
                                    <dt>Curs valutar</dt>
                                    <dd>{{ number_format($invoice->exchange_rate, 4, ',', '.') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Plăți --}}
                <div class="bg-white rounded-xl border border-slate-200">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="font-semibold text-slate-900">Plăți</h2>
                        <span class="text-sm text-slate-500">
                            Rest de plată:
                            <span class="font-semibold {{ $invoice->balance() > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                                {{ number_format($invoice->balance(), 2, ',', '.') }} {{ $invoice->currency }}
                            </span>
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500 border-b border-slate-100">
                                    <th class="px-5 py-3 font-medium">Data</th>
                                    <th class="px-5 py-3 font-medium">Metodă</th>
                                    <th class="px-5 py-3 font-medium">Referință</th>
                                    <th class="px-5 py-3 font-medium text-right">Sumă</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($invoice->payments as $payment)
                                    <tr>
                                        <td class="px-5 py-3 text-slate-600">{{ $payment->payment_date?->format('d.m.Y') }}</td>
                                        <td class="px-5 py-3 text-slate-600">{{ $payment->payment_method->label() }}</td>
                                        <td class="px-5 py-3 text-slate-600">{{ $payment->reference ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right font-medium text-slate-900">
                                            {{ number_format($payment->amount, 2, ',', '.') }} {{ $payment->currency }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-slate-400">
                                            Nicio plată înregistrată.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>

</html>
