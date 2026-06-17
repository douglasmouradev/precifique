<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="font-display text-xl font-semibold text-center text-slate-700 mb-2">Entrar como admin</h1>
    <p class="text-sm text-slate-500 text-center mb-6">
        Conta de loja/tenant? <a href="{{ route('tenant.login') }}" class="text-brand font-semibold hover:underline">Entrar em /entrar</a>
    </p>

    @if($errors->has('email') && $errors->first('email') === __('auth.tenant_login_hint'))
    <div class="mb-4 rounded-xl border border-brand/30 bg-brand/10 px-4 py-3 text-sm text-ink">
        {{ __('auth.tenant_login_hint') }}
        <a href="{{ route('tenant.login') }}" class="mt-2 inline-flex font-semibold text-brand-dark hover:underline">Ir para /entrar →</a>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-brand focus:ring-brand/30" name="remember">
                Lembrar-me
            </label>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
            @if (Route::has('password.request'))
                <a class="text-sm text-slate-500 hover:text-brand transition-colors" href="{{ route('password.request') }}">
                    Esqueci minha senha
                </a>
            @endif

            <x-primary-button class="w-full sm:w-auto justify-center py-2.5 px-6">
                Entrar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
