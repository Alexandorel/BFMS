<x-auth-layout page-title="Autentificare" title="Bine ai revenit"
               description="Autentifică-te pentru a continua în aplicație.">
    @if (session('status'))
        <div class="ui-alert ui-alert-success mb-5" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="email" class="form-input">
            @error('email')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label">Parolă</label>
            <input id="password" type="password" name="password" required
                   autocomplete="current-password" class="form-input">
            @error('password')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm text-slate-600">
            <input id="remember" type="checkbox" name="remember"
                   class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            Ține-mă minte
        </label>

        <button type="submit" class="ui-btn ui-btn-primary w-full">Autentifică-te</button>
    </form>

    <x-slot:footer>
        Nu ai cont?
        <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:text-brand-800">Înregistrează-te</a>
    </x-slot:footer>
</x-auth-layout>
