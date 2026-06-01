@props(['padding' => true])
<div {{ $attributes->merge(['class' => 'ui-card overflow-hidden']) }}>
    @if(isset($header))
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
        {{ $header }}
    </div>
    @endif
    <div @class(['p-5' => $padding])>{{ $slot }}</div>
</div>
