@props(['class' => ''])

<div
    class="inline-flex items-center gap-1 {{ $class }}"
    role="group"
    aria-label="{{ __('app.language') }}"
    data-locale-switcher
    data-url="{{ route('locale.update') }}"
    data-csrf="{{ csrf_token() }}"
    data-current="{{ app()->getLocale() }}"
>
    @foreach (['pt_BR' => 'PT', 'en' => 'EN'] as $code => $label)
        <button
            type="button"
            data-locale="{{ $code }}"
            class="px-2 py-1 text-xs rounded-md transition-colors touch-manipulation {{ app()->getLocale() === $code ? 'bg-brand/20 text-brand-dark font-semibold' : 'text-slate-500 hover:text-ink' }}"
            aria-pressed="{{ app()->getLocale() === $code ? 'true' : 'false' }}"
        >{{ $label }}</button>
    @endforeach
</div>
