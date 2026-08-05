@php
    $vatOptions = [19, 9, 5, 0];
@endphp

<div>
    <label for="sku" class="form-label">Cod unic (SKU)</label>
    <input id="sku" type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
           class="form-input">
    @error('sku') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label for="name" class="form-label">Denumire</label>
    <input id="name" type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
           class="form-input">
    @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label for="unit_measure" class="form-label">Unitate de măsură</label>
    <select id="unit_measure" name="unit_measure" class="form-input">
        @foreach (['buc' => 'Bucata', 'ore' => 'Ore', 'kg' => 'Kilogram', 'l' => 'Litri', 'luni' => 'Luni'] as $value => $label)
            <option value="{{ $value }}" @selected(old('unit_measure', $product->unit_measure ?? '') == $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('unit_measure') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label for="unit_price" class="form-label">Preț unitar (fără TVA)</label>
    <input id="unit_price" type="number" step="0.01" name="unit_price" value="{{ old('unit_price', $product->unit_price ?? '') }}"
           class="form-input">
    @error('unit_price') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label for="quantity" class="form-label">Cantitate (stoc)</label>
    <input id="quantity" type="number" step="0.01" min="0" name="quantity" value="{{ old('quantity', $product->quantity ?? '0') }}"
           class="form-input">
    @error('quantity') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label for="vat_rate" class="form-label">Cota TVA</label>
    <select id="vat_rate" name="vat_rate" class="form-input">
        @foreach ($vatOptions as $rate)
            <option value="{{ $rate }}" @selected(old('vat_rate', $product->vat_rate ?? '') == $rate)>{{ $rate }}%</option>
        @endforeach
    </select>
    @error('vat_rate') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="flex min-h-11 items-center gap-3 rounded-lg border border-app-border bg-slate-50 px-3 dark:border-slate-700 dark:bg-slate-800">
    <input type="checkbox" name="is_vat_exempt" id="is_vat_exempt" value="1"
           class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-700"
           @checked(old('is_vat_exempt', $product->is_vat_exempt ?? false))>
    <label for="is_vat_exempt" class="text-sm text-slate-700 dark:text-slate-300">Produs scutit de TVA</label>
</div>
