@extends('layouts.auth')
@section('title', 'Consentimento LGPD')
@section('content')
<h1 class="font-display text-xl font-semibold mb-2">Consentimento de dados</h1>
<p class="text-sm text-slate-500 mb-6">Para usar o Precifique, aceite nossos termos conforme a LGPD (Lei 13.709/2018).</p>

<form method="POST" action="{{ route('lgpd.consent.store') }}" class="space-y-4">
    @csrf
    <label class="flex gap-3 text-sm text-slate-700 items-start">
        <input type="checkbox" name="terms" value="1" required class="mt-0.5 rounded border-slate-300 text-brand focus:ring-brand/30">
        <span>Li e aceito os <a href="{{ route('terms') }}" target="_blank" class="text-brand font-medium hover:underline">Termos de Uso</a></span>
    </label>
    <label class="flex gap-3 text-sm text-slate-700 items-start">
        <input type="checkbox" name="privacy" value="1" required class="mt-0.5 rounded border-slate-300 text-brand focus:ring-brand/30">
        <span>Li e aceito a <a href="{{ route('privacy') }}" target="_blank" class="text-brand font-medium hover:underline">Política de Privacidade</a></span>
    </label>
    <label class="flex gap-3 text-sm text-slate-500 items-start">
        <input type="checkbox" name="marketing" value="1" class="mt-0.5 rounded border-slate-300 text-brand focus:ring-brand/30">
        <span>Aceito receber dicas por e-mail (opcional)</span>
    </label>
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">Continuar para o painel</x-ui.button>
</form>
@endsection
