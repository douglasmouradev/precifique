@props(['lines' => 1, 'class' => ''])

@for($i = 0; $i < $lines; $i++)
<div {{ $attributes->merge(['class' => 'h-4 rounded-lg bg-slate-200/80 animate-pulse '.$class]) }} style="width: {{ $i === $lines - 1 && $lines > 1 ? '70%' : '100%' }}"></div>
@if($i < $lines - 1)<div class="h-2"></div>@endif
@endfor
