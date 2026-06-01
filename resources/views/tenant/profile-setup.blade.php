@extends('layouts.tenant-minimal')
@section('title', 'Monte seu perfil')

@section('content')
<div class="max-w-3xl mx-auto py-8 md:py-12 px-4">
    <div class="text-center mb-8">
        <span class="ui-badge-brand mb-4 inline-block">Monte seu perfil</span>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-ink">Qual é o nicho do seu negócio?</h1>
        <p class="text-slate-500 mt-2 text-sm md:text-base max-w-lg mx-auto">
            Escolha o tipo de negócio para adaptarmos campos, precificação e relatórios ao seu dia a dia.
        </p>
    </div>

    <x-ui.card class="p-6 md:p-8">
        <form method="POST" action="{{ route('tenant.profile.setup.store') }}" class="space-y-6">
            @csrf
            <div>
                <label class="ui-label">Nome do negócio</label>
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="ui-input" placeholder="Ex.: Doceria da Ana">
            </div>

            <div>
                <label class="ui-label mb-3 block">Seu nicho</label>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach([
                        ['alimentos', 'food', 'Alimentos', 'Doces, marmitas, produtos artesanais'],
                        ['servico', 'service', 'Serviços', 'Valor/hora, deslocamento, orçamentos'],
                        ['artesanato', 'craft', 'Artesanato', 'Materiais, tempo e coleções'],
                        ['outro', 'edit', 'Outro', 'Descreva abaixo'],
                    ] as $n)
                    <label class="cursor-pointer block p-4 rounded-xl border-2 transition-colors
                        {{ ($selectedNiche ?? '') === $n[0] ? 'border-brand bg-brand/5' : 'border-slate-200 hover:border-brand/40' }}">
                        <input type="radio" name="niche" value="{{ $n[0] }}" class="sr-only" required
                            @checked(old('niche', $selectedNiche) === $n[0])>
                        <div class="w-10 h-10 rounded-lg bg-brand/10 text-brand flex items-center justify-center mb-2">
                            <x-ui.nav-icon :name="$n[1]" class="w-5 h-5" />
                        </div>
                        <p class="font-semibold text-sm">{{ $n[2] }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $n[3] }}</p>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="ui-label">Se escolheu Outro, descreva</label>
                <input type="text" name="niche_other" value="{{ old('niche_other', $tenant->niche_metadata['other'] ?? '') }}" class="ui-input" placeholder="Opcional">
            </div>

            <x-ui.button type="submit" variant="secondary" class="w-full py-3 text-base">
                Salvar e entrar no app
            </x-ui.button>
        </form>
    </x-ui.card>
</div>
@endsection
