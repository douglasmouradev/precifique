@props(['type' => 'success'])
<div {{ $attributes->merge(['class' => $type === 'warning' ? 'ui-alert-warning' : 'ui-alert-success']) }}>
    {{ $slot }}
</div>
