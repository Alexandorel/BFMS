<x-auth-layout page-title="Înregistrare" title="Creează contul"
               description="Completează datele de mai jos pentru a începe.">
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="first_name" class="form-label">Prenume</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                       required autofocus autocomplete="given-name" class="form-input">
                @error('first_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="last_name" class="form-label">Nume</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                       required autocomplete="family-name" class="form-input">
                @error('last_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autocomplete="email" class="form-input">
            @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="form-label">Parolă</label>
            <input id="password" type="password" name="password" required
                   autocomplete="new-password" class="form-input">
            @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirmă parola</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   autocomplete="new-password" class="form-input">
        </div>

        <button type="submit" class="ui-btn ui-btn-primary w-full">Creează contul</button>
    </form>

    <x-slot:footer>
        Ai deja cont?
        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Autentifică-te</a>
    </x-slot:footer>
</x-auth-layout>
