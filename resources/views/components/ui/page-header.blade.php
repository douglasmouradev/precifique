@props(['title', 'subtitle' => null])
<div {{ $attributes->merge(['class' => 'mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4']) }}>
    <div>
        <h1 class="ui-page-title">{{ $title }}</h1>
        @if($subtitle)
        <p class="ui-page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
    <div class="flex flex-wrap items-center gap-2 shrink-0">{{ $actions }}</div>
    @endif
</div>
