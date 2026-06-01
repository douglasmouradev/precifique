@extends('layouts.tenant')
@section('title', 'PIX Premium')
@section('breadcrumb') Assinatura / PIX @endsection

@section('content')
<div class="max-w-lg mx-auto text-center py-6 animate-fade-in">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand/15 text-3xl mb-6">📱</div>
    <h1 class="ui-page-title">Pague via PIX</h1>

    @if(isset($pix['error']))
    <x-ui.alert type="warning" class="mt-6 text-left">{{ $pix['error'] }}</x-ui.alert>
    @else
    <p class="ui-page-subtitle mt-2">Valor: <strong class="text-ink">R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}</strong></p>

    <x-ui.card class="mt-8">
        @if(!empty($pix['qr_code_base64']))
        <img src="data:image/png;base64,{{ $pix['qr_code_base64'] }}" alt="QR Code PIX" class="mx-auto mb-6 max-w-[220px] rounded-xl ring-1 ring-slate-200">
        @endif
        @if(!empty($pix['qr_code']))
        <label class="ui-label text-left">Copia e cola</label>
        <textarea readonly class="ui-input text-xs font-mono" rows="4">{{ $pix['qr_code'] }}</textarea>
        <p class="text-sm text-slate-500 mt-4">Após o pagamento, seu plano Premium será ativado automaticamente.</p>
        @endif
    </x-ui.card>
    @endif

    <x-ui.button variant="ghost" :href="route('tenant.dashboard')" class="mt-8">← Voltar ao dashboard</x-ui.button>
</div>
@endsection
