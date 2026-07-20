<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add firma</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
 <div class = "max-w-3xl mx-auto p-6">
    <div class ="bg-white border border-slate-200 p-5">
     <form action="{{ route('administrator.companies.store') }}" method="POST" class="space-y-6">
        @csrf
        
         {{-- Date Firma --}}
     <div class = "space-y-5">
        <h2 class="form-section-title">Firma</h2>
        <div class = "grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class = "sm:col-span-2">
                <label for="name" class="form-label">Nume Firma</label>
                <input type="text" id="name" name="name" required maxlength="255"
                       placeholder="SC..."
                       class="form-input">
            </div>
            <div>
                <label for="juridical_form" class="form-label">Forma Juridica</label>
                <select id="juridical_form" name="juridical_form" required
                class="form-input">
            <option value="" disabled selected>...</option>
            <option value="SRL">SRL</option>
            <option value="SA">SA</option>
            <option value="PFA">PFA</option>
            <option value="II">II</option>
            <option value="IF">IF</option>
            <option value="SRL-D">SRL-D</option>
            </select>
            </div>
        </div>

         {{-- CUI / CIF --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="cui" class="form-label">CUI / CIF</label>
                <input type="text" id="cui" name="cui" required
                pattern="RO?[0-9]{2,10}"
                maxlength="12" placeholder="RO11111111"
                class="form-input">
            </div>
            <div>
                <label for="trade_registry_number" class="form-label">Numar Registru</label>
                 <input type="text" id="trade_registry_number" name="trade_registry_number" required maxlength="20" placeholder="J12/345/2026"
                 class="form-input">
            </div>
        </div>
     </div>

      {{-- Locatie Firma --}}
     <hr class="border-slate-200">
     <div class="space-y-5">
        <h2 class="form-section-title">Locatie Sediu</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="county" class="form-label">Judet</label>
                <input type="text" id="county" name="county" required maxlength="255" placeholder="Maramures"
                class="form-input">
            </div>
            <div>
               <label for="city" class="form-label">Oras</label>
               <input type="text" id="city" name="city" required maxlength="255" placeholder="Baia Mare"
               class="form-input" >
            </div>
        </div>
        <div>
            <label for="address" class="form-label">Adresa</label>
            <input type="text" id="address" name="address" required maxLength="255" placeholder="Str. ... nr. 11"
            class="form-input">
        </div>
     </div>

      {{-- Capital + TVA --}}
     <hr class="border-slate-200">
     <div class="space-y-5">
        <h2 class="form-section-title">Capital</h2>
        <div>
            <label for="social_capital" class="form-label">Capital</label>
            <input type="number" id="social_capital" name="social_capital" required step="0.01" min="0" placeholder="200.00"
            class="form-input">
        </div>
        <label class="flex items-start gap-3">
            <input type="checkbox" name="vat_payer" value="1"
            class="mt-0.5 w-4 h-4 border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <span>
                <span class="block text-sm font-medium text-slate-900">Inregistrare pentru TVA</span>
                <span class="block text-xs text-slate-500 mt-0.5">Bifeaza daca are cod TVA deja.</span>
            </span>
        </label>
     </div>

      {{-- Conturi Bancare --}}
     <hr class="border-slate-200">
     <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="form-section-title">Conturi Bancare</h2>
            <button type="button" id="add-account-btn"
            class="form-btn-link">+ Adauga</button>
        </div>
        <div id="bank-accounts-container" class="space-y-3">
            <div class="bank-account-row grid grid-cols-1 sm:grid-cols-5 gap-3">
                <div class="sm:col-span-3">
                    <input type="text" name="iban[]"
                    pattern="RO[0-9]{2}[A-Z0-9]{4}[0-9]{16}"
                    title="Iban" maxlength="24" placeholder="ROxxxxxxxxxxxxxxxxxxxxxx"
                    class="form-input">
                </div>
                <div class="sm:col-span-2 flex gap-2">
                    <input type="text" name="bank_name[]" placeholder="Banca"
                        class="form-input">
                    <button type="button" class="remove-account-btn form-btn-del">X</button>
                </div>
            </div>
        </div>
     </div>
     <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('dashboard.administrator') }}"  class="form-btn-secondary">Anuleaza</a>
        <button type="submit"  class="form-btn-primary">Add firma</button>
     </div>
     </form>
    </div>

 </div>
<script>
    document.getElementById('add-account-btn').addEventListener('click', function () {
        const container = document.getElementById('bank-accounts-container');
        const row = document.createElement('div');
        row.className = 'bank-account-row grid grid-cols-1 sm:grid-cols-5 gap-3';
        row.innerHTML = `
            <div class="sm:col-span-3">
                <input type="text" name="iban[]"
                       pattern="RO[0-9]{2}[A-Z0-9]{4}[0-9]{16}"
                       title="IBAN" maxlength="24" placeholder="ROxxxxxxxxxxxxxxxxxxxxxx"
                       class="form-input">
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <input type="text" name="bank_name[]" placeholder="Banca"
                       class="form-input">
                <button type="button" class="remove-account-btn form-btn-del">X</button>
            </div>
        `;
        container.appendChild(row);
    });

    document.getElementById('bank-accounts-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-account-btn')) {
            const rows = document.querySelectorAll('.bank-account-row');
            if (rows.length > 1) {
                e.target.closest('.bank-account-row').remove();
            }
        }
    });
</script>
</body>
</html>