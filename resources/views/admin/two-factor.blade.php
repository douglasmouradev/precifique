<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Autenticação em duas etapas</h2>
    </x-slot>

    <div class="py-6 max-w-lg mx-auto px-4 sm:px-6 space-y-6">
        @if(session('success'))
        <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
        @endif

        @if($enabled)
        <x-ui.card>
            <p class="text-sm text-slate-600 mb-4">2FA está <strong>ativo</strong> na sua conta admin.</p>
            <form method="POST" action="{{ route('admin.two-factor.destroy') }}" class="space-y-4" onsubmit="return confirm('Desativar 2FA?');">
                @csrf
                @method('DELETE')
                <div>
                    <x-input-label for="password" value="Senha atual para desativar" />
                    <x-text-input id="password" type="password" name="password" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <x-ui.button variant="outline" type="submit">Desativar 2FA</x-ui.button>
            </form>
        </x-ui.card>
        @else
        <x-ui.card>
            <p class="text-sm text-slate-600 mb-4">Escaneie o QR no Google Authenticator, 1Password ou similar.</p>
            <div class="flex justify-center mb-4 p-4 bg-white rounded-xl border border-slate-100">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrUri) }}" alt="QR Code 2FA" width="200" height="200" loading="lazy">
            </div>
            <p class="text-xs text-slate-500 break-all mb-4">Chave manual: <code class="bg-slate-100 px-1 rounded">{{ $secret }}</code></p>

            <form method="POST" action="{{ route('admin.two-factor.confirm') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="code" value="Código de confirmação" />
                    <x-text-input id="code" name="code" class="mt-1 block w-full" maxlength="6" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <x-ui.button type="submit">Ativar 2FA</x-ui.button>
            </form>
        </x-ui.card>
        @endif
    </div>
</x-app-layout>
