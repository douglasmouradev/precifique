@extends('layouts.onboarding', ['step' => 1])
@section('title', 'Seu nicho — Precifique')
@section('content')
<h1 class="font-display text-xl sm:text-2xl font-bold text-center mb-2">Qual é o seu nicho?</h1>
<p class="text-sm text-slate-500 text-center mb-8">Adaptamos campos e relatórios ao seu tipo de negócio.</p>

<form method="POST" action="{{ route('onboarding.niche.store') }}">
    @csrf
    <div class="grid sm:grid-cols-2 gap-4">
        @foreach([
            ['alimentos', 'food', 'Alimentos', 'Doceria, marmitas, produtos artesanais'],
            ['servico', 'service', 'Serviços', 'Valor/hora, deslocamento, orçamentos'],
            ['artesanato', 'craft', 'Artesanato', 'Materiais, tempo e coleções'],
            ['outro', 'edit', 'Outro', 'Descreva abaixo'],
        ] as $n)
        <label class="cursor-pointer block p-5 ui-card border-2 border-transparent hover:border-brand/40 has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-colors">
            <input type="radio" name="niche" value="{{ $n[0] }}" class="sr-only" required @checked(old('niche') === $n[0])>
            <div class="w-11 h-11 rounded-xl bg-brand/10 text-brand flex items-center justify-center mb-3">
                <x-ui.nav-icon :name="$n[1]" class="w-6 h-6" />
            </div>
            <p class="font-semibold">{{ $n[2] }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ $n[3] }}</p>
        </label>
        @endforeach
    </div>
    <x-ui.input name="niche_other" placeholder="Descreva seu nicho (se escolheu Outro)" class="mt-4" value="{{ old('niche_other') }}" />
    <x-ui.button variant="secondary" type="submit" class="w-full mt-8 py-3">Continuar</x-ui.button>
</form>
@endsection
