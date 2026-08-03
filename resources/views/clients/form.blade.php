@php
    $client = $client ?? new \App\Models\Client();
    $existingContact = $client->exists ? $client->contacts->first() : null;
@endphp

@php
    $counties = [
        'Alba', 'Arad', 'Argeș', 'Bacău', 'Bihor', 'Bistrița-Năsăud', 'Botoșani',
        'Brăila', 'Brașov', 'București', 'Buzău', 'Călărași', 'Caraș-Severin',
        'Cluj', 'Constanța', 'Covasna', 'Dâmbovița', 'Dolj', 'Galați', 'Giurgiu',
        'Gorj', 'Harghita', 'Hunedoara', 'Ialomița', 'Iași', 'Ilfov', 'Maramureș',
        'Mehedinți', 'Mureș', 'Neamț', 'Olt', 'Prahova', 'Satu Mare', 'Sălaj',
        'Sibiu', 'Suceava', 'Teleorman', 'Timiș', 'Tulcea', 'Vaslui', 'Vâlcea', 'Vrancea',
    ];

    $existingContact = $client->contacts->first() ?? null;
@endphp

{{-- Selectorul de tip client --}}
<div>
    <label for="client_type" class="block text-sm font-medium text-slate-700 mb-1">Tip client</label>
    <select name="client_type" id="client_type"
            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Selectează...</option>
        <option value="individual" @selected(old('client_type', $client->client_type ?? '') == 'individual')>Persoană fizică</option>
        <option value="company" @selected(old('client_type', $client->client_type ?? '') == 'company')>Persoană juridică</option>
    </select>
    @error('client_type') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Câmpurile pentru clientul individual --}}
<div id="individual_fields" class="space-y-4" style="display: none;">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">Prenume</label>
            <input type="text" name="first_name" id="first_name"
                   value="{{ old('first_name', $client->first_name ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('first_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Nume</label>
            <input type="text" name="last_name" id="last_name"
                   value="{{ old('last_name', $client->last_name ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('last_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="cnp" class="block text-sm font-medium text-slate-700 mb-1">CNP</label>
        <input type="text" name="cnp" id="cnp" maxlength="20"
               value="{{ old('cnp', $client->cnp ?? '') }}"
               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('cnp') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Câmpurile pentru clientul companie --}}
<div id="company_fields" class="space-y-4" style="display: none;">
    <div>
        <label for="company_name" class="block text-sm font-medium text-slate-700 mb-1">Denumire companie</label>
        <input type="text" name="name" id="company_name"
               value="{{ old('name', $client->name ?? '') }}"
               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="cui" class="block text-sm font-medium text-slate-700 mb-1">CUI</label>
            <input type="text" name="cui" id="cui" maxlength="20"
                   value="{{ old('cui', $client->cui ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('cui') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="trade_registry_number" class="block text-sm font-medium text-slate-700 mb-1">Nr. Reg. Comerțului</label>
            <input type="text" name="trade_registry_number" id="trade_registry_number" maxlength="20"
                   value="{{ old('trade_registry_number', $client->trade_registry_number ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('trade_registry_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="vat_number" class="block text-sm font-medium text-slate-700 mb-1">Nr. TVA (opțional)</label>
        <input type="text" name="vat_number" id="vat_number" maxlength="20"
               value="{{ old('vat_number', $client->vat_number ?? '') }}"
               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('vat_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Toggle persoană de contact — doar la companie --}}
<div id="contact_toggle_wrapper" class="flex items-center gap-2" style="display: none;">
    <input type="checkbox" name="add_contact" id="add_contact" value="1"
           @checked(old('add_contact', $existingContact ? 1 : 0))>
    <label for="add_contact" class="text-sm text-slate-700">Adaugă persoană de contact</label>
</div>

<div id="contact_person_fields" class="space-y-4" style="display: none;">
    <p class="text-sm font-medium text-slate-700">Persoană de contact</p>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="contact_name" class="block text-sm font-medium text-slate-700 mb-1">Nume</label>
            <input type="text" name="contacts[0][name]" id="contact_name"
                   value="{{ old('contacts.0.name', $existingContact->name ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('contacts.0.name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="contact_role" class="block text-sm font-medium text-slate-700 mb-1">Rol</label>
            <input type="text" name="contacts[0][role]" id="contact_role" maxlength="100"
                   value="{{ old('contacts.0.role', $existingContact->role ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('contacts.0.role') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="contact_phone" class="block text-sm font-medium text-slate-700 mb-1">Telefon</label>
            <input type="text" name="contacts[0][phone]" id="contact_phone" maxlength="20"
                   value="{{ old('contacts.0.phone', $existingContact->phone ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('contacts.0.phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="contact_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="contacts[0][email]" id="contact_email"
                   value="{{ old('contacts.0.email', $existingContact->email ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('contacts.0.email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Câmpurile comune --}}
<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="county" class="block text-sm font-medium text-slate-700 mb-1">Județ</label>
            <select name="county" id="county"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Selectează...</option>
                @foreach ($counties as $county)
                    <option value="{{ $county }}" @selected(old('county', $client->county ?? '') == $county)>{{ $county }}</option>
                @endforeach
            </select>
            @error('county') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="city" class="block text-sm font-medium text-slate-700 mb-1">Oraș</label>
            <input type="text" name="city" id="city"
                   value="{{ old('city', $client->city ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('city') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-slate-700 mb-1">Adresă</label>
        <input type="text" name="address" id="address"
               value="{{ old('address', $client->address ?? '') }}"
               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('address') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="delivery_address" class="block text-sm font-medium text-slate-700 mb-1">Adresă de livrare (opțional)</label>
        <input type="text" name="delivery_address" id="delivery_address"
               value="{{ old('delivery_address', $client->delivery_address ?? '') }}"
               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('delivery_address') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Telefon</label>
            <input type="text" name="phone" id="phone" maxlength="20"
                   value="{{ old('phone', $client->phone ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" id="email"
                   value="{{ old('email', $client->email ?? '') }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clientType = document.getElementById('client_type');
        const addContact = document.getElementById('add_contact');
        const contactToggleWrapper = document.getElementById('contact_toggle_wrapper');
        const groups = {
            individual: document.getElementById('individual_fields'),
            company: document.getElementById('company_fields'),
            contact: document.getElementById('contact_person_fields'),
        };

        function setRequired(container, isRequired) {
            container.querySelectorAll('input, select').forEach(el => {
                if (isRequired) {
                    el.setAttribute('required', 'required');
                } else {
                    el.removeAttribute('required');
                }
            });
        }

        function toggleContactFields() {
            if (clientType.value === 'company' && addContact.checked) {
                groups.contact.style.display = 'block';
                setRequired(groups.contact, true);
            } else {
                groups.contact.style.display = 'none';
                setRequired(groups.contact, false);
            }
        }

        function toggleFields() {
            groups.individual.style.display = 'none';
            groups.company.style.display = 'none';
            contactToggleWrapper.style.display = 'none';
            setRequired(groups.individual, false);
            setRequired(groups.company, false);

            if (clientType.value === 'individual') {
                groups.individual.style.display = 'block';
                setRequired(groups.individual, true);
                addContact.checked = false;
            } else if (clientType.value === 'company') {
                groups.company.style.display = 'block';
                contactToggleWrapper.style.display = 'flex';
                setRequired(groups.company, true);
            }

            toggleContactFields();
        }

        clientType.addEventListener('change', toggleFields);
        addContact.addEventListener('change', toggleContactFields);
        toggleFields();
    });
</script>
