@props(['default' => 'profile', 'labels' => []])

<div class="space-y-6" data-ui-tabs data-default="{{ $default }}">
    <div class="flex flex-wrap gap-1 border-b border-slate-200/80" role="tablist">
        @foreach($labels as $key => $label)
        <button
            type="button"
            role="tab"
            data-tab-trigger="{{ $key }}"
            class="ui-tab-btn px-4 py-2.5 text-sm font-semibold rounded-t-xl transition-colors"
        >{{ $label }}</button>
        @endforeach
    </div>

    @foreach($labels as $key => $label)
    @if(isset($$key))
    <div data-tab-panel="{{ $key }}" role="tabpanel" class="ui-tab-panel">
        {{ $$key }}
    </div>
    @endif
    @endforeach
</div>

@once
@push('scripts')
<script>
document.querySelectorAll('[data-ui-tabs]').forEach((root) => {
    const defaultTab = root.getAttribute('data-default') || 'profile';
    const triggers = root.querySelectorAll('[data-tab-trigger]');
    const panels = root.querySelectorAll('[data-tab-panel]');
    const activate = (key) => {
        triggers.forEach((t) => {
            const on = t.getAttribute('data-tab-trigger') === key;
            t.setAttribute('aria-selected', on ? 'true' : 'false');
            t.classList.toggle('text-brand-dark', on);
            t.classList.toggle('border-b-2', on);
            t.classList.toggle('border-brand', on);
            t.classList.toggle('-mb-px', on);
            t.classList.toggle('bg-brand/5', on);
            t.classList.toggle('text-slate-500', !on);
        });
        panels.forEach((p) => p.classList.toggle('hidden', p.getAttribute('data-tab-panel') !== key));
    };
    triggers.forEach((btn) => btn.addEventListener('click', () => activate(btn.getAttribute('data-tab-trigger'))));
    activate(defaultTab);
});
</script>
@endpush
@endonce
