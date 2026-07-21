@php
    $vatOptions = [19, 9, 5, 0];
@endphp

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Cod unic (SKU)</label>
    <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    @error('sku') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Denumire</label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Unitate de Masura</label>
    <select name="unit_measure" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @foreach (['buc' => 'Bucata', 'ore' => 'Ore', 'kg' => 'Kilogram', 'l' => 'Litri', 'luni' => 'Luni'] as $value => $label)
            <option value="{{ $value }}" @selected(old('unit_measure', $product->unit_measure ?? '') == $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('unit_measure') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Pret Unitar (fara TVA)</label>
    <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price', $product->unit_price ?? '') }}"
           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    @error('unit_price') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Cota TVA</label>
    <select name="vat_rate" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @foreach ($vatOptions as $rate)
            <option value="{{ $rate }}" @selected(old('vat_rate', $product->vat_rate ?? '') == $rate)>{{ $rate }}%</option>
        @endforeach
    </select>
    @error('vat_rate') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="flex items-center gap-2">
    <input type="checkbox" name="is_vat_exempt" id="is_vat_exempt" value="1"
           @checked(old('is_vat_exempt', $product->is_vat_exempt ?? false))>
    <label for="is_vat_exempt" class="text-sm text-slate-700">Produs scutit de TVA</label>
</div>