@props(['class' => ''])

<div class="inline-flex items-center gap-1 {{ $class }}" role="group" aria-label="{{ __('app.language') }}">
    @foreach (['pt_BR' => 'PT', 'en' => 'EN'] as $code => $label)
        <form method="POST" action="{{ route('locale.update') }}" class="inline">
            @csrf
            <input type="hidden" name="locale" value="{{ $code }}">
            <button
                type="submit"
                class="px-2 py-1 text-xs rounded-md transition-colors {{ app()->getLocale() === $code ? 'bg-brand/20 text-brand-dark font-semibold' : 'text-slate-500 hover:text-ink' }}"
                aria-pressed="{{ app()->getLocale() === $code ? 'true' : 'false' }}"
            >{{ $label }}</button>
        </form>
    @endforeach
</div>
