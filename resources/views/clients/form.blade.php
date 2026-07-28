@php
    $vatOptions = [19, 9, 5, 0];
@endphp


    {{-- Selectorul de tip client --}}
<div class="mb-3">
    <label for="client_type">Tip client</label>
    <select name="client_type" id="client_type" class="form-control" required>
        <option value="">Selectează...</option>
        <option value="individual" {{ old('client_type') == 'individual' ? 'selected' : '' }}>
            Persoană fizică
        </option>
        <option value="company" {{ old('client_type') == 'company' ? 'selected' : '' }}>
            Persoană juridică
        </option>
    </select>
</div>

    {{-- Câmpurile pentru clientul individual --}}
<div id="individual_fields" style="display: none;">
    <div class="mb-3">
        <label for="name">Nume complet</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
        <label for="address">Adresă</label>
        <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}" required>
    </div>
    <div class="mb-3">
        <label for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required>
    </div>
    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
    </div>
</div>


    {{-- Câmpurile pentru clientul companie --}}
<div id="company_fields" style="display: none;">
    <div class="mb-3">
        <label for="name">Denumire companie</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
        <label for="cif">CIF</label>
        <input type="text" name="cif" id="cif" class="form-control" value="{{ old('cif') }}" required>
    </div>
    <div class="mb-3">
        <label for="address">Adresă</label>
        <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}" required>
    </div>
    <div class="mb-3">
        <label for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required>
    </div>
    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
    </div>
    <div class="mb-3">
        <label for="vat_rate">Cota TVA</label>
        <select name="vat_rate" id="vat_rate" class="form-control" required>
            <option value="">Selectează...</option>
            @foreach ($vatOptions as $rate)
                <option value="{{ $rate }}" {{ old('vat_rate') == $rate ? 'selected' : '' }}>
                    {{ $rate }}%
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="is_vat_exempt">Scutit de TVA</label>
        <input type="checkbox" name="is_vat_exempt" id="is_vat_exempt" value="1" {{ old('is_vat_exempt') ? 'checked' : '' }}>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clientType = document.getElementById('client_type');
        const individualFields = document.getElementById('individual_fields');
        const companyFields = document.getElementById('company_fields');

        function toggleFields() {
            // ascunde tot
            individualFields.style.display = 'none';
            companyFields.style.display = 'none';

            if (clientType.value === 'individual') {
                individualFields.style.display = 'block';
            } else if (clientType.value === 'company') {
                companyFields.style.display = 'block';
            }
        }

        clientType.addEventListener('change', toggleFields);

        // rulează la încărcare, util la editare/old() input
        toggleFields();
    });
</script>
