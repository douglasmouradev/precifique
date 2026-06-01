@props([
    'icon' => 'products',
    'title' => 'Nada por aqui ainda',
    'description' => '',
])

<div {{ $attributes->merge(['class' => 'ui-card p-10 md:p-12 text-center']) }}>
    <div class="w-14 h-14 rounded-2xl bg-brand/10 text-brand-dark flex items-center justify-center mx-auto mb-4 ring-1 ring-brand/20">
        <x-ui.nav-icon :name="$icon" class="w-7 h-7" />
    </div>
    <h3 class="font-display font-semibold text-lg text-ink">{{ $title }}</h3>
    @if($description)
    <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">{{ $description }}</p>
    @endif
    @if($slot->isNotEmpty())
    <div class="mt-6 flex flex-wrap justify-center gap-3">
        {{ $slot }}
    </div>
    @endif
</div>
