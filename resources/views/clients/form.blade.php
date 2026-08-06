@php
    $client = $client ?? new \App\Models\Client();
    // F-202: toate persoanele de contact (din old() la eroare de validare, altfel din model)
    $contactRows = old('contacts', $client->exists
        ? $client->contacts->map(fn ($c) => [
            'name' => $c->name, 'role' => $c->role, 'email' => $c->email, 'phone' => $c->phone,
        ])->all()
        : []);

    $counties = [
        'Alba', 'Arad', 'Argeș', 'Bacău', 'Bihor', 'Bistrița-Năsăud', 'Botoșani',
        'Brăila', 'Brașov', 'București', 'Buzău', 'Călărași', 'Caraș-Severin',
        'Cluj', 'Constanța', 'Covasna', 'Dâmbovița', 'Dolj', 'Galați', 'Giurgiu',
        'Gorj', 'Harghita', 'Hunedoara', 'Ialomița', 'Iași', 'Ilfov', 'Maramureș',
        'Mehedinți', 'Mureș', 'Neamț', 'Olt', 'Prahova', 'Satu Mare', 'Sălaj',
        'Sibiu', 'Suceava', 'Teleorman', 'Timiș', 'Tulcea', 'Vaslui', 'Vâlcea', 'Vrancea',
    ];
@endphp

{{-- Selectorul de tip client --}}
<div>
    <label for="client_type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tip client</label>
    <select name="client_type" id="client_type"
            class="form-input">
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
            <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Prenume</label>
            <input type="text" name="first_name" id="first_name"
                   value="{{ old('first_name', $client->first_name ?? '') }}"
                   class="form-input">
            @error('first_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nume</label>
            <input type="text" name="last_name" id="last_name"
                   value="{{ old('last_name', $client->last_name ?? '') }}"
                   class="form-input">
            @error('last_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="cnp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">CNP</label>
        <input type="text" name="cnp" id="cnp" maxlength="20"
               value="{{ old('cnp', $client->cnp ?? '') }}"
               class="form-input">
        @error('cnp') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Câmpurile pentru clientul companie --}}
<div id="company_fields" class="space-y-4" style="display: none;">
    <div>
        <label for="company_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Denumire companie</label>
        <input type="text" name="name" id="company_name"
               value="{{ old('name', $client->name ?? '') }}"
               class="form-input">
        @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="cui" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">CUI</label>
            <input type="text" name="cui" id="cui" maxlength="20"
                   value="{{ old('cui', $client->cui ?? '') }}"
                   class="form-input">
            @error('cui') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="trade_registry_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nr. Reg. Comerțului</label>
            <input type="text" name="trade_registry_number" id="trade_registry_number" maxlength="20"
                   value="{{ old('trade_registry_number', $client->trade_registry_number ?? '') }}"
                   class="form-input">
            @error('trade_registry_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="vat_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nr. TVA (opțional)</label>
        <input type="text" name="vat_number" id="vat_number" maxlength="20"
               value="{{ old('vat_number', $client->vat_number ?? '') }}"
               class="form-input">
        @error('vat_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Câmpurile comune --}}
<div id="common_fields" class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="county" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Județ</label>
            <select name="county" id="county"
                    class="form-input">
                <option value="">Selectează...</option>
                @foreach ($counties as $county)
                    <option value="{{ $county }}" @selected(old('county', $client->county ?? '') == $county)>{{ $county }}</option>
                @endforeach
            </select>
            @error('county') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="city" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Oraș</label>
            <input type="text" name="city" id="city"
                   value="{{ old('city', $client->city ?? '') }}"
                   class="form-input">
            @error('city') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Adresă</label>
        <input type="text" name="address" id="address"
               value="{{ old('address', $client->address ?? '') }}"
               class="form-input">
        @error('address') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="delivery_address" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Adresă de livrare (opțional)</label>
        <input type="text" name="delivery_address" id="delivery_address" data-optional
               value="{{ old('delivery_address', $client->delivery_address ?? '') }}"
               class="form-input">
        @error('delivery_address') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Telefon</label>
            <input type="text" name="phone" id="phone" maxlength="20"
                   value="{{ old('phone', $client->phone ?? '') }}"
                   class="form-input">
            @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
            <input type="email" name="email" id="email"
                   value="{{ old('email', $client->email ?? '') }}"
                   class="form-input">
            @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- F-202: persoane de contact (relație 1:N) — doar la companie --}}
<div id="contact_person_fields" class="space-y-4" style="display: none;">
    <div class="flex items-center justify-between">
        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Persoane de contact</p>
        <button type="button" id="add_contact_row" class="ui-btn ui-btn-secondary text-xs">
            + Adaugă contact
        </button>
    </div>

    @error('contacts') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

    <div id="contacts_container" class="space-y-4">
        @foreach ($contactRows as $i => $contact)
            <div class="rounded-lg border border-app-border p-4 space-y-4 dark:border-slate-700" data-contact-row>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Contact #{{ $i + 1 }}</span>
                    <button type="button" class="text-xs text-rose-600 hover:underline" data-remove-contact>Șterge</button>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nume</label>
                        <input type="text" name="contacts[{{ $i }}][name]" value="{{ $contact['name'] ?? '' }}"
                               class="form-input" data-contact-input>
                        @error("contacts.$i.name") <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Rol</label>
                        <input type="text" name="contacts[{{ $i }}][role]" maxlength="100" value="{{ $contact['role'] ?? '' }}"
                               class="form-input" data-contact-input>
                        @error("contacts.$i.role") <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Telefon</label>
                        <input type="text" name="contacts[{{ $i }}][phone]" maxlength="20" value="{{ $contact['phone'] ?? '' }}"
                               class="form-input" data-contact-input>
                        @error("contacts.$i.phone") <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                        <input type="email" name="contacts[{{ $i }}][email]" value="{{ $contact['email'] ?? '' }}"
                               class="form-input" data-contact-input>
                        @error("contacts.$i.email") <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Șablon pentru un rând nou de contact (clonat din JS; __INDEX__ e înlocuit cu poziția) --}}
<template id="contact_row_template">
    <div class="rounded-lg border border-app-border p-4 space-y-4 dark:border-slate-700" data-contact-row>
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500">Contact nou</span>
            <button type="button" class="text-xs text-rose-600 hover:underline" data-remove-contact>Șterge</button>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nume</label>
                <input type="text" name="contacts[__INDEX__][name]" class="form-input" data-contact-input>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Rol</label>
                <input type="text" name="contacts[__INDEX__][role]" maxlength="100" class="form-input" data-contact-input>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Telefon</label>
                <input type="text" name="contacts[__INDEX__][phone]" maxlength="20" class="form-input" data-contact-input>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input type="email" name="contacts[__INDEX__][email]" class="form-input" data-contact-input>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clientType = document.getElementById('client_type');
        const contactsContainer = document.getElementById('contacts_container');
        const addContactBtn = document.getElementById('add_contact_row');
        const contactTemplate = document.getElementById('contact_row_template');
        let contactIndex = {{ count($contactRows) }};

        const groups = {
            individual: document.getElementById('individual_fields'),
            company: document.getElementById('company_fields'),
            contact: document.getElementById('contact_person_fields'),
            common: document.getElementById('common_fields'),
        };

        function setRequired(container, isRequired) {
            container.querySelectorAll('input, select').forEach(el => {
                if (el.hasAttribute('data-optional')) return;
                if (isRequired) {
                    el.setAttribute('required', 'required');
                } else {
                    el.removeAttribute('required');
                }
            });
        }

        // Contactele sunt doar pentru companie. Cand nu-s vizibile, dezactivam
        // inputurile ca sa NU fie trimise (si sa nu blocheze submit-ul cu required ascuns).
        function syncContactSection() {
            const isCompany = clientType.value === 'company';
            groups.contact.style.display = isCompany ? 'block' : 'none';
            groups.contact.querySelectorAll('[data-contact-input]').forEach(el => {
                el.disabled = !isCompany;
                el.required = isCompany;
            });
        }

        function toggleFields() {
            groups.individual.style.display = 'none';
            groups.company.style.display = 'none';
            groups.common.style.display = 'none';
            setRequired(groups.individual, false);
            setRequired(groups.company, false);
            setRequired(groups.common, false);

            if (clientType.value) {
                groups.common.style.display = 'block';
                setRequired(groups.common, true);
            }

            if (clientType.value === 'individual') {
                groups.individual.style.display = 'block';
                setRequired(groups.individual, true);
            } else if (clientType.value === 'company') {
                groups.company.style.display = 'block';
                setRequired(groups.company, true);
            }

            syncContactSection();
        }

        addContactBtn.addEventListener('click', function () {
            const clone = contactTemplate.content.cloneNode(true);
            clone.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace('__INDEX__', contactIndex);
            });
            contactsContainer.appendChild(clone);
            contactIndex++;
            syncContactSection();
            contactsContainer.lastElementChild?.querySelector('input')?.focus();
        });

        // Stergerea unui rand (delegare de eveniment)
        contactsContainer.addEventListener('click', function (event) {
            const removeBtn = event.target.closest('[data-remove-contact]');
            if (removeBtn) {
                removeBtn.closest('[data-contact-row]').remove();
            }
        });

        clientType.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
