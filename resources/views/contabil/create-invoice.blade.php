<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factura noua · {{ config('app.name', 'BFMS') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
 <div class="max-w-5xl mx-auto p-6">
    <div class="bg-white border border-slate-200 p-5">

        <h1 class="text-2xl font-bold text-slate-900 mb-1">Factura noua</h1>

        <form action="#" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="client_id" class="form-label">Client</label>
                    <select id="client_id" name="client_id" required class="form-input">
                        <option value="" disabled selected>Alege client</option>
                        
                    </select>
                </div>
            </div>
            <hr class="border-slate-200">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="form-section-title">Produse</h2>
                    <button type="button" id="add-line-btn" class="form-btn-link">+</button>
                </div>
                <div id="invoice-lines-container" class="space-y-3">
                    <div class="invoice-line-row grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-4">
                            <input type="text" name="product_name[]" placeholder="..." required class="form-input">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="number" name="quantity[]" placeholder="Cantitate" step="0.5" min="0" required class="form-input quantity-input">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="number" name="unit_price[]" placeholder="Pret unitar" step="0.01" min="0" required class="form-input price-input">
                        </div>
                        <div class="sm:col-span-2">
                            <select name="vat_rate[]" required class="form-input vat-input">
                            <option value="19">19%</option>
                            <option value="9">9%</option>
                            <option value="5">5%</option>
                            <option value="0">0%</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2 flex gap-2">
                            <input type="text" class="line-total form-input" placeholder="Total" readonly>
                            <button type="button" class="remove-line-btn form-btn-del">X</button>
                        </div>
                    </div>
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
                </div>
            </div>
            <input type="hidden" name="subtotal" id="subtotal-input">
            <input type="hidden" name="vat_total" id="vat-total-input">
            <input type="hidden" name="total" id="total-input">
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('dashboard.contabil.invoices') }}" class="form-btn-secondary">Anuleaza</a>
                <button type="submit" class="form-btn-primary">Salveaza</button>
            </div>
        </form>
    </div>
 </div>
 <script>
    const container = document.getElementById('invoice-lines-container');
    const addBtn = document.getElementById('add-line-btn');
    function lineRow(){
        return `
            <div class="invoice-line-row grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-4">
                    <input type="text" name="product_name[]" placeholder="..." required
                           class="form-input">
                </div>
                <div class="sm:col-span-2">
                    <input type="number" name="quantity[]" placeholder="Cantitate" step="0.5" min="0" required
                           class="form-input quantity-input">
                </div>
                <div class="sm:col-span-2">
                    <input type="number" name="unit_price[]" placeholder="Preț unitar" step="0.01" min="0" required
                           class="form-input price-input">
                </div>
                <div class="sm:col-span-2">
                    <select name="vat_rate[]" required class="form-input vat-input">
                        <option value="19">19%</option>
                        <option value="9">9%</option>
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
        document.querySelectorAll('.invoice-line-row').forEach(function(row){
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const vatr = parseFloat(row.querySelector('.vat-input').value) || 0;
            const lsubtotal= qty*price;
            const lvat= lsubtotal*(vatr/100);
            const ltotal= lsubtotal+lvat;
            row.querySelector('.line-total').value=ltotal.toFixed(2)+ ' RON';
            subtotal += lsubtotal;
            vattotal += lvat;
        });
        const total = subtotal +vattotal;
        document.getElementById('subtotal-display').textContent = subtotal.toFixed(2)+ ' RON';
        document.getElementById('vat-display').textContent = vattotal.toFixed(2)+ ' RON';
        document.getElementById('total-display').textContent = total.toFixed(2)+ ' RON';
        document.getElementById('subtotal-input').value = subtotal.toFixed(2);
        document.getElementById('vat-total-input').value = vattotal.toFixed(2);
        document.getElementById('total-input').value = total.toFixed(2);
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
 </script>
</body>
</html>