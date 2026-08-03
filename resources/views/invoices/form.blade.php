<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factura noua · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

@php
    // acelasi formular serveste crearea si editarea unei ciorne
    $isEdit = ($invoice ?? null) !== null;

    $currentType = old('document_type', $isEdit ? $invoice->document_type->value : 'invoice');
    $currentCurrency = old('currency', $isEdit ? $invoice->currency : 'RON');
    $currentSeriesId = old('document_series_id', $isEdit ? $invoice->document_series_id : null);

    // liniile vin din inputul respins de validare, din ciorna, sau un rand gol la creare
    if (old('product_name')) {
        $lineRows = collect(old('product_name'))->map(fn ($name, $i) => [
            'name' => $name,
            'product_id' => old('product_id')[$i] ?? '',
            'quantity' => old('quantity')[$i] ?? '',
            'unit_price' => old('unit_price')[$i] ?? '',
            'vat_rate' => old('vat_rate')[$i] ?? 21,
        ])->all();
    } elseif ($isEdit) {
        $lineRows = $invoice->lines->map(fn ($line) => [
            'name' => $line->product_name_snapshot,
            'product_id' => $line->product_id ?? '',
            'quantity' => $line->quantity,
            'unit_price' => $line->unit_price_snapshot,
            'vat_rate' => $line->vat_rate_snapshot,
        ])->all();
    } else {
        $lineRows = [['name' => '', 'product_id' => '', 'quantity' => 1, 'unit_price' => '', 'vat_rate' => 21]];
    }
@endphp

 <div class="max-w-5xl mx-auto p-6">
    <div class="bg-white rounded-lg border border-slate-200 p-5">

        <h1 class="text-2xl font-bold text-slate-900 mb-1">
            {{ $isEdit ? 'Editare ciornă' : 'Factura noua' }}
        </h1>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-rose-50 text-rose-800 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $isEdit ? route('invoices.update', $invoice) : route('invoices.store') }}" method="POST" class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <div class="space-y-4">
                <div class="relative">
                    <label for="client_search" class="form-label">Client</label>
                    <input type="text" id="client_search" autocomplete="off" required class="form-input"
                           value="{{ $isEdit ? $invoice->client?->full_name : '' }}"
                           placeholder="Caută client după nume...">
                    <input type="hidden" id="client_id" name="client_id"
                           value="{{ old('client_id', $isEdit ? $invoice->client_id : '') }}">
                    <div id="client-suggestions" class="hidden absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                </div>
            </div>
            <hr class="border-slate-200">
            <div class="space-y-4">
                <h2 class="form-section-title">Date</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="document_type" class="form-label">Tip document</label>
                        <select id="document_type" name="document_type" required class="form-input">
                            <option value="invoice" @selected($currentType === 'invoice')>Factura</option>
                            <option value="proforma" @selected($currentType === 'proforma')>Proforma</option>
                            <option value="receipt" @selected($currentType === 'receipt')>Chitanta</option>
                        </select>
                    </div>
                    <div>
                        <label for="document_series_id" class="form-label">Serie</label>
                        <select id="document_series_id" name="document_series_id" required class="form-input">
                            @foreach ($seriesByType[$currentType] ?? [] as $s)
                                <option value="{{ $s['id'] }}" @selected((int) $currentSeriesId === $s['id'])>{{ $s['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="currency" class="form-label">Moneda</label>
                        <select id="currency" name="currency" required class="form-input">
                            <option value="RON" @selected($currentCurrency === 'RON')>RON</option>
                            <option value="EUR" @selected($currentCurrency === 'EUR')>EUR</option>
                            <option value="USD" @selected($currentCurrency === 'USD')>USD</option>
                        </select>
                    </div>
                </div>
                <div id="exchange-rate-wrapper" class="{{ $currentCurrency === 'RON' ? 'hidden' : '' }}">
                    <label for="exchange_rate" class="form-label">Curs Valutar</label>
                    <input type="number" id="exchange_rate" name="exchange_rate" step="0.0001" min="0" placeholder="5.1202" class="form-input"
                           value="{{ old('exchange_rate', $isEdit ? $invoice->exchange_rate : '') }}">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="issue_date" class="form-label">Data emiterii</label>
                        <input type="date" id="issue_date" name="issue_date" required class="form-input"
                               value="{{ old('issue_date', $isEdit ? $invoice->issue_date?->format('Y-m-d') : now()->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label for="due_date" class="form-label">Data scadenta</label>
                        <input type="date" id="due_date" name="due_date" required class="form-input"
                               value="{{ old('due_date', $isEdit ? $invoice->due_date?->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
            <hr class="border-slate-200">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="form-section-title">Produse</h2>
                    <button type="button" id="add-line-btn" class="form-btn-link">+</button>
                </div>
                <div id="invoice-lines-container" class="space-y-3">
                    @foreach ($lineRows as $row)
                        <div class="invoice-line-row grid grid-cols-1 sm:grid-cols-12 gap-3">
                            <div class="sm:col-span-4 relative">
                                <input type="text" name="product_name[]" placeholder="Caută produs sau scrie liber..."
                                       autocomplete="off" required class="form-input product-input"
                                       value="{{ $row['name'] }}">
                                <input type="hidden" name="product_id[]" class="product-id"
                                       value="{{ $row['product_id'] }}">
                                <div class="product-suggestions hidden absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                            </div>
                            <div class="sm:col-span-2">
                                <input type="number" name="quantity[]" placeholder="Cantitate" step="0.01" min="0.01" required class="form-input quantity-input"
                                       value="{{ $row['quantity'] }}">
                            </div>
                            <div class="sm:col-span-2">
                                <input type="number" name="unit_price[]" placeholder="Pret unitar" step="0.01" min="0" required class="form-input price-input"
                                       value="{{ $row['unit_price'] }}">
                            </div>
                            <div class="sm:col-span-2">
                                <select name="vat_rate[]" required class="form-input vat-input">
                                    <option value="21" @selected($row['vat_rate'] == 21)>21%</option>
                                    <option value="11" @selected($row['vat_rate'] == 11)>11%</option>
                                    <option value="5" @selected($row['vat_rate'] == 5)>5%</option>
                                    <option value="0" @selected($row['vat_rate'] == 0)>0%</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2 flex gap-2">
                                <input type="text" class="line-total form-input" placeholder="Total" readonly>
                                <button type="button" class="remove-line-btn form-btn-del">X</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <hr class="border-slate-200">
            <div class="flex justify-end">
                <div class="w-full sm:w-72 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Subtotal</span>
                        <span id="subtotal-display" class="font-medium text-slate-900">0.00 RON</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">TVA</span>
                        <span id="vat-display" class="font-medium text-slate-900">0.00 RON</span>
                    </div>
                    <div class="flex justify-between text-base font-semibold border-t border-slate-200 pt-2">
                        <span class="text-slate-900">Total</span>
                        <span id="total-display" class="text-indigo-600">0.00 RON</span>
                    </div>
                    <div id="ron-equiv-wrapper" class="hidden flex justify-between text-xs text-slate-400 pt-1">
                        <span>In RON</span>
                        <span id="ron-equiv-display">0.00 RON</span>
                    </div>
                </div>
            </div>
            <input type="hidden" name="subtotal" id="subtotal-input">
            <input type="hidden" name="vat_total" id="vat-total-input">
            <input type="hidden" name="total" id="total-input">
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ $isEdit ? route('invoices.show', $invoice) : route(auth()->user()->dashboardRoute()) }}" class="form-btn-secondary">Anuleaza</a>
                <button type="submit" name="action" value="draft" class="form-btn-secondary">
                    {{ $isEdit ? 'Salveaza ciorna' : 'Salveaza ca ciorna' }}
                </button>
                <button type="submit" name="action" value="issue" class="form-btn-primary">Emite</button>
            </div>
        </form>
    </div>
 </div>
 <script>
    const currencySelect = document.getElementById('currency');
    const exchangeRateWrapper = document.getElementById('exchange-rate-wrapper');
    const exchangeRateInput = document.getElementById('exchange_rate');
    const container = document.getElementById('invoice-lines-container');
    const addBtn = document.getElementById('add-line-btn');
    const clientSearch = document.getElementById('client_search');
    const clientIdInput = document.getElementById('client_id');
    const clientSuggestions = document.getElementById('client-suggestions');
    let searchTimeout;

    // seriile disponibile depind de tipul documentului ales
    const seriesByType = @json($seriesByType);
    const documentTypeSelect = document.getElementById('document_type');
    const seriesSelect = document.getElementById('document_series_id');

    function refreshSeriesOptions(){
        const options = seriesByType[documentTypeSelect.value] || [];

        seriesSelect.innerHTML = options.length
            ? options.map(s => `<option value="${s.id}">${s.label}</option>`).join('')
            : '<option value="">Nicio serie activa pentru acest tip</option>';
    }

    documentTypeSelect.addEventListener('change', refreshSeriesOptions);
    function lineRow(){
        return `
            <div class="invoice-line-row grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-4 relative">
                    <input type="text" name="product_name[]" placeholder="Caută produs sau scrie liber..."
                           autocomplete="off" required
                           class="form-input product-input">
                    <input type="hidden" name="product_id[]" class="product-id">
                    <div class="product-suggestions hidden absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                </div>
                <div class="sm:col-span-2">
                    <input type="number" name="quantity[]" placeholder="Cantitate" step="0.01" min="0.01" required
                           value="1" class="form-input quantity-input">
                </div>
                <div class="sm:col-span-2">
                    <input type="number" name="unit_price[]" placeholder="Preț unitar" step="0.01" min="0" required
                           class="form-input price-input">
                </div>
                <div class="sm:col-span-2">
                    <select name="vat_rate[]" required class="form-input vat-input">
                        <option value="21">21%</option>
                        <option value="11">11%</option>
                        <option value="5">5%</option>
                        <option value="0">0%</option>
                    </select>
                </div>
                <div class="sm:col-span-2 flex gap-2">
                    <input type="text" class="line-total form-input" placeholder="Total" readonly>
                    <button type="button" class="remove-line-btn form-btn-del">X</button>
                </div>
            </div>
            `;
    }
    function calcTotals(){
        let subtotal = 0;
        let vattotal = 0;
        const currency = document.getElementById('currency').value;
        document.querySelectorAll('.invoice-line-row').forEach(function(row){
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const vatr = parseFloat(row.querySelector('.vat-input').value) || 0;
            const lsubtotal= qty*price;
            const lvat= lsubtotal*(vatr/100);
            const ltotal= lsubtotal+lvat;
            row.querySelector('.line-total').value=ltotal.toFixed(2)+ ' ' +currency;
            subtotal += lsubtotal;
            vattotal += lvat;
        });
        const total = subtotal +vattotal;
        document.getElementById('subtotal-display').textContent = subtotal.toFixed(2)+ ' '+ currency;
        document.getElementById('vat-display').textContent = vattotal.toFixed(2)+ ' '+ currency;
        document.getElementById('total-display').textContent = total.toFixed(2)+ ' '+ currency;
        document.getElementById('subtotal-input').value = subtotal.toFixed(2);
        document.getElementById('vat-total-input').value = vattotal.toFixed(2);
        document.getElementById('total-input').value = total.toFixed(2);
        
        const ronWrapper = document.getElementById('ron-equiv-wrapper');
        const ronDisplay = document.getElementById('ron-equiv-display');
        if(currency === 'RON'){
            ronWrapper.classList.add('hidden');
        }
        else{
            const rate = parseFloat(exchangeRateInput.value) || 0;
            const ronEquiv = total * rate;
            ronDisplay.textContent = ronEquiv.toFixed(2) +' RON';
            ronWrapper.classList.remove('hidden');
        }
    }
    addBtn.addEventListener('click', function(){
        const wrapper = document.createElement('div');
        wrapper.innerHTML = lineRow();
        container.appendChild(wrapper.firstElementChild);
    });
    container.addEventListener('click', function (e){
        if(e.target.classList.contains('remove-line-btn')){
            const rows = document.querySelectorAll('.invoice-line-row');
            if(rows.length>1){
                e.target.closest('.invoice-line-row').remove();
                calcTotals();
            }
        }
    });
    container.addEventListener('input', function (e){
        if(e.target.classList.contains('quantity-input') ||
    e.target.classList.contains('price-input') || e.target.classList.contains('vat-input')){
        calcTotals();
    }
    });
    currencySelect.addEventListener('change', function(){
    if(this.value ==='RON'){
        exchangeRateWrapper.classList.add('hidden');
        exchangeRateInput.value = '';
        calcTotals();
        return;
    }
    exchangeRateWrapper.classList.remove('hidden');
    fetch(`{{ route('invoices.exchange-rate') }}?currency=${this.value}`)
    .then(response=>response.json())
    .then(data=> {
        if(data.rate){
            exchangeRateInput.value = data.rate;
        }
        calcTotals();
    });
    });
    exchangeRateInput.addEventListener('input', calcTotals);
    clientSearch.addEventListener('input',function() {
        clearTimeout(searchTimeout);
        clientIdInput.value = '';
        const query = this.value.trim();
        if(query.length < 2){
            clientSuggestions.classList.add('hidden');
            return;
        }
        searchTimeout = setTimeout(function(){
            fetch(`{{ route('invoices.search-clients') }}?q=${encodeURIComponent(query)}`)
            .then(response =>response.json())
            .then(clients => {
                if(clients.length === 0){
                    clientSuggestions.innerHTML = '<div class="px-3 py-2 text-sm text-slate-400">Niciun client găsit</div>';
                }
                else {
                    clientSuggestions.innerHTML = clients.map(c =>
                        `<div class="client-option px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer" data-id="${c.id}" data-name="${c.name}">${c.name}</div>`).join('');
                }
                clientSuggestions.classList.remove('hidden');
            });
        }, 300);
    });
    clientSuggestions.addEventListener('click', function(e) {
        const option = e.target.closest('.client-option');
        if(option){
            clientIdInput.value = option.dataset.id;
            clientSearch.value = option.dataset.name;
            clientSuggestions.classList.add('hidden');
        }
    });
    document.addEventListener('click', function(e) {
        if(!clientSearch.contains(e.target) && !clientSuggestions.contains(e.target)){
            clientSuggestions.classList.add('hidden');
        }
    });
    container.addEventListener('keydown', function(e) {
        if(e.key === 'Enter' && e.target.classList.contains('vat-input') === false){
            const row = e.target.closest('.invoice-line-row');
            const isLastR =row === container.lastElementChild;
            const isLastFi = e.target.classList.contains('price-input') || e.target === row.querySelector('.line-total');
            if(isLastR && isLastFi){
                e.preventDefault();
                addBtn.click();
                const newRow = container.lastElementChild;
                newRow.querySelector('input[name="product_name[]"]').focus();
            }
        }
    });

    // ---- nomenclator de produse pe liniile de factura ----
    // delegare pe container: randurile se adauga si se sterg dinamic
    let productSearchTimeout;

    function esc(value){
        return String(value ?? '').replace(/[&<>"']/g, c =>
            ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function loadProducts(input){
        const box = input.closest('.relative').querySelector('.product-suggestions');

        fetch(`{{ route('invoices.search-products') }}?q=${encodeURIComponent(input.value.trim())}`)
            .then(response => response.json())
            .then(products => {
                box.innerHTML = products.length
                    ? products.map(p => `
                        <div class="product-option px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer flex justify-between gap-2"
                             data-id="${p.id}"
                             data-name="${esc(p.name)}"
                             data-price="${p.unit_price}"
                             data-vat="${p.is_vat_exempt ? 0 : parseFloat(p.vat_rate)}">
                            <span><span class="font-mono text-xs text-slate-400">${esc(p.sku)}</span> ${esc(p.name)}</span>
                            <span class="text-xs text-slate-400 shrink-0">${p.unit_price} / ${esc(p.unit_measure)}</span>
                        </div>`).join('')
                    : '<div class="px-3 py-2 text-sm text-slate-400">Niciun produs găsit</div>';

                box.classList.remove('hidden');
            });
    }

    // la focus se deschide tot nomenclatorul
    container.addEventListener('focusin', function(e){
        if(e.target.classList.contains('product-input')){
            loadProducts(e.target);
        }
    });

    // tastarea filtreaza si desface legatura cu catalogul: linia redevine text liber
    container.addEventListener('input', function(e){
        if(!e.target.classList.contains('product-input')) return;

        e.target.closest('.relative').querySelector('.product-id').value = '';

        clearTimeout(productSearchTimeout);
        productSearchTimeout = setTimeout(() => loadProducts(e.target), 300);
    });

    // selectia completeaza randul; pretul si TVA raman editabile
    container.addEventListener('click', function(e){
        const option = e.target.closest('.product-option');
        if(!option) return;

        const cell = option.closest('.relative');
        const row = option.closest('.invoice-line-row');

        cell.querySelector('.product-input').value = option.dataset.name;
        cell.querySelector('.product-id').value = option.dataset.id;
        row.querySelector('.price-input').value = option.dataset.price;
        row.querySelector('.vat-input').value = option.dataset.vat;

        cell.querySelector('.product-suggestions').classList.add('hidden');
        calcTotals();
    });

    document.addEventListener('click', function(e){
        if(!e.target.closest('.product-suggestions') && !e.target.classList.contains('product-input')){
            document.querySelectorAll('.product-suggestions').forEach(box => box.classList.add('hidden'));
        }
    });

    // la editare liniile vin deja completate, deci totalurile se calculeaza din start
    calcTotals();
 </script>
</body>
</html>