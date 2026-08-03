<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->series }}-{{ $invoice->number }} · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <x-app-shell>

            {{-- Top Bar --}}
            <header class="app-page-toolbar">
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
            <main class="app-page-content mx-auto w-full max-w-6xl space-y-6">

                @if (session('success'))
                    <div class="ui-alert ui-alert-success" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="ui-alert ui-alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="ui-alert ui-alert-danger" role="alert">
                        <p class="font-medium mb-1">Verifică datele introduse:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Antet --}}
                <div class="ui-card p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="ui-page-title">
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
                                <div class="ui-button-group pt-3 sm:justify-end">
                                    <a href="{{ route('invoices.edit', $invoice) }}"
                                       class="ui-btn ui-btn-secondary">
                                        Editează
                                    </a>

                                    <x-confirm-action action="{{ route('invoices.destroy', $invoice) }}"
                                                      variant="button"
                                                      confirm-text="Ștergi definitiv ciorna?"></x-confirm-action>

                                    <form action="{{ route('invoices.issue', $invoice) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="ui-btn ui-btn-primary">
                                            Emite documentul
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- Actiuni pe factura emisa: anulare (doar ultima din serie) si stornare --}}
                            @php
                                $canManageInvoice = in_array(auth()->user()->role, ['administrator', 'operator'], true)
                                    && $invoice->document_type === \App\Enums\DocumentType::Invoice
                                    && ! $invoice->isCreditNote();
                                $isLastInSeries = $invoice->documentSeries
                                    && (int) $invoice->number === (int) $invoice->documentSeries->current_number;
                                $canCancel = $canManageInvoice && $invoice->status->canBeCancelled() && $isLastInSeries;
                                $canStorno = $canManageInvoice && $invoice->status->canBeCredited() && ! $invoice->creditNote;
                            @endphp
                            @if ($canCancel || $canStorno)
                                <div class="ui-button-group pt-3 sm:justify-end">
                                    @if ($canStorno)
                                        <x-confirm-action action="{{ route('invoices.storno', $invoice) }}"
                                                          method="POST"
                                                          variant="button"
                                                          trigger-class="ui-btn ui-btn-secondary"
                                                          confirm-class="ui-btn ui-btn-primary"
                                                          label="Stornează"
                                                          confirm-text="Emiți o factură de storno pentru acest document?"
                                                          confirm-label="Da, stornează" />
                                    @endif

                                    @if ($canCancel)
                                        <x-confirm-action action="{{ route('invoices.cancel', $invoice) }}"
                                                          method="POST"
                                                          variant="button"
                                                          label="Anulează"
                                                          confirm-text="Anulezi definitiv factura?"
                                                          confirm-label="Da, anulează" />
                                    @endif
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

                    @if ($invoice->creditNote)
                        <div class="mt-4 text-sm px-3 py-2 rounded-lg bg-slate-100 text-slate-600">
                            Această factură a fost stornată prin
                            <a href="{{ route('invoices.show', $invoice->creditNote) }}" class="font-medium underline">
                                {{ $invoice->creditNote->series }}-{{ $invoice->creditNote->number }}
                            </a>.
                        </div>
                    @endif
                </div>

                {{-- Emitent + Client --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="ui-card p-5">
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

                    <div class="ui-card p-5">
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
                <div class="ui-card overflow-hidden">
                    <div class="ui-card-header">
                        <h2 class="ui-section-title">Linii factură</h2>
                    </div>
                    <div class="ui-table-wrap" tabindex="0" role="region" aria-label="Liniile facturii">
                        <table class="ui-table">
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
                <div class="ui-card overflow-hidden">
                    <div class="ui-card-header">
                        <h2 class="ui-section-title">Plăți</h2>
                        <span class="text-sm text-slate-500">
                            Rest de plată:
                            <span class="font-semibold {{ $invoice->balance() > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                                {{ number_format($invoice->balance(), 2, ',', '.') }} {{ $invoice->currency }}
                            </span>
                        </span>
                    </div>
                    @php
                        // NFR-1: contabilul vede platile, dar nu le gestioneaza
                        $canRecordPayments = $invoice->status->acceptsPayments()
                            && in_array(auth()->user()->role, ['administrator', 'operator'], true);
                        $canDeletePayments = auth()->user()->role === 'administrator';
                    @endphp

                    <div class="ui-table-wrap" tabindex="0" role="region" aria-label="Plățile facturii">
                        <table class="ui-table">
                            <thead>
                                <tr class="text-left text-slate-500 border-b border-slate-100">
                                    <th class="px-5 py-3 font-medium">Data</th>
                                    <th class="px-5 py-3 font-medium">Metodă</th>
                                    <th class="px-5 py-3 font-medium">Referință</th>
                                    <th class="px-5 py-3 font-medium">Chitanță</th>
                                    <th class="px-5 py-3 font-medium text-right">Sumă</th>
                                    @if ($canDeletePayments)
                                        <th class="px-5 py-3 font-medium text-right">
                                            <span class="sr-only">Acțiuni</span>
                                        </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($invoice->payments as $payment)
                                    <tr>
                                        <td class="px-5 py-3 text-slate-600">{{ $payment->payment_date?->format('d.m.Y') }}</td>
                                        <td class="px-5 py-3 text-slate-600">{{ $payment->payment_method->label() }}</td>
                                        <td class="px-5 py-3 text-slate-600">{{ $payment->reference ?? '—' }}</td>
                                        <td class="px-5 py-3">
                                            @if ($payment->receipt_label)
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                                    {{ $payment->receipt_label }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-right font-medium text-slate-900">
                                            {{ number_format($payment->amount, 2, ',', '.') }} {{ $payment->currency }}
                                        </td>
                                        @if ($canDeletePayments)
                                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                                {{-- confirmare in doi pasi: NFR-3 interzice confirm() nativ --}}
                                                <div class="inline-flex items-center gap-2" data-delete-cell>
                                                    <button type="button"
                                                            data-delete-trigger
                                                            class="ui-action-danger">
                                                        Șterge
                                                    </button>

                                                    <span class="hidden items-center gap-2" data-delete-confirm>
                                                        <span class="text-xs text-slate-500">Sigur?</span>
                                                        <form action="{{ route('invoices.payments.destroy', $payment) }}"
                                                              method="POST"
                                                              class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="ui-btn ui-btn-danger">
                                                                Da
                                                            </button>
                                                        </form>
                                                        <button type="button"
                                                                data-delete-cancel
                                                                class="ui-btn ui-btn-secondary">
                                                            Nu
                                                        </button>
                                                    </span>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $canDeletePayments ? 6 : 5 }}" class="px-5 py-8 text-center text-slate-400">
                                            Nicio plată înregistrată.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($canRecordPayments)
                        <div class="px-5 py-5 border-t border-slate-200 bg-slate-50/60">
                            <h3 class="text-sm font-semibold text-slate-800 mb-3">Înregistrează o încasare</h3>

                            <form action="{{ route('invoices.payments.store', $invoice) }}"
                                  method="POST"
                                  class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                @csrf

                                <div class="md:col-span-3">
                                    <label for="payment_date" class="block text-xs font-medium text-slate-600 mb-1">
                                        Data plății
                                    </label>
                                    <input type="date"
                                           id="payment_date"
                                           name="payment_date"
                                           value="{{ old('payment_date', now()->toDateString()) }}"
                                           @if ($invoice->issue_date) min="{{ $invoice->issue_date->toDateString() }}" @endif
                                           max="{{ now()->toDateString() }}"
                                           required
                                           class="form-input">
                                </div>

                                <div class="md:col-span-3">
                                    <label for="amount" class="block text-xs font-medium text-slate-600 mb-1">
                                        Sumă ({{ $invoice->currency }})
                                    </label>
                                    {{-- pre-completata cu restul de plata: cazul frecvent e achitarea integrala --}}
                                    <input type="number"
                                           id="amount"
                                           name="amount"
                                           value="{{ old('amount', number_format($invoice->balance(), 2, '.', '')) }}"
                                           step="0.01"
                                           min="0.01"
                                           max="{{ number_format($invoice->balance(), 2, '.', '') }}"
                                           required
                                           class="form-input text-right">
                                </div>

                                <div class="md:col-span-3">
                                    <label for="payment_method" class="block text-xs font-medium text-slate-600 mb-1">
                                        Metodă
                                    </label>
                                    <select id="payment_method"
                                            name="payment_method"
                                            class="form-input">
                                        @foreach (\App\Enums\PaymentMethod::cases() as $method)
                                            <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>
                                                {{ $method->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-3" data-reference-field>
                                    <label for="reference" class="block text-xs font-medium text-slate-600 mb-1">
                                        Referință <span data-reference-hint class="text-slate-400">(opțional)</span>
                                    </label>
                                    <input type="text"
                                           id="reference"
                                           name="reference"
                                           value="{{ old('reference') }}"
                                           maxlength="100"
                                           placeholder="Nr. extras de cont"
                                           class="form-input">
                                </div>

                                {{-- F-401: chitanta insoteste doar incasarile in numerar --}}
                                <div class="md:col-span-12 hidden" data-receipt-field>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox"
                                               id="issue_receipt"
                                               name="issue_receipt"
                                               value="1"
                                               @checked(old('issue_receipt'))
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-slate-700">Emite și o chitanță</span>
                                        <span class="text-xs text-slate-400">
                                            (primește un număr din seria de chitanțe a firmei)
                                        </span>
                                    </label>
                                </div>

                                <div class="md:col-span-12 flex justify-end">
                                    <button type="submit"
                                            class="ui-btn ui-btn-primary">
                                        Înregistrează plata
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

            </main>
    </x-app-shell>

    <script>
        // Confirmare de stergere in doi pasi. NFR-3 interzice confirm() nativ,
        // deci butonul isi schimba starea in loc sa blocheze firul de executie.
        document.querySelectorAll('[data-delete-cell]').forEach(function (cell) {
            const trigger = cell.querySelector('[data-delete-trigger]');
            const confirm = cell.querySelector('[data-delete-confirm]');
            const cancel = cell.querySelector('[data-delete-cancel]');

            trigger?.addEventListener('click', function () {
                trigger.classList.add('hidden');
                confirm.classList.remove('hidden');
                confirm.classList.add('inline-flex');
            });

            cancel?.addEventListener('click', function () {
                confirm.classList.add('hidden');
                confirm.classList.remove('inline-flex');
                trigger.classList.remove('hidden');
            });
        });

        // Referinta e obligatorie doar la ordin de plata (vezi StorePaymentRequest).
        const methodSelect = document.getElementById('payment_method');
        const referenceInput = document.getElementById('reference');
        const referenceHint = document.querySelector('[data-reference-hint]');

        // Chitanta se emite doar la incasarile in numerar (F-401).
        const receiptField = document.querySelector('[data-receipt-field]');
        const receiptCheckbox = document.getElementById('issue_receipt');

        function syncMethodDependentFields() {
            const isBankTransfer = methodSelect.value === 'bank_transfer';
            const isCash = methodSelect.value === 'cash';

            referenceInput.required = isBankTransfer;
            referenceHint.textContent = isBankTransfer ? '(obligatorie)' : '(opțional)';
            referenceHint.classList.toggle('text-rose-500', isBankTransfer);
            referenceHint.classList.toggle('text-slate-400', !isBankTransfer);

            receiptField?.classList.toggle('hidden', !isCash);

            // pe alta metoda bifa ar fi respinsa la validare, deci o curatam
            if (! isCash && receiptCheckbox) {
                receiptCheckbox.checked = false;
            }
        }

        if (methodSelect && referenceInput && referenceHint) {
            methodSelect.addEventListener('change', syncMethodDependentFields);
            syncMethodDependentFields();
        }
    </script>
</body>

</html>
