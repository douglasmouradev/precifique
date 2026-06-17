@props(['chartId', 'type' => 'line', 'minHeight' => '12rem'])

<div
    data-chart-skeleton="{{ $chartId }}"
    class="ui-chart-skeleton absolute inset-0 z-[1] pointer-events-none"
    style="min-height: {{ $minHeight }}"
    aria-hidden="true"
>
    @if($type === 'line')
        <div class="flex items-end justify-between gap-1.5 h-full px-1 pb-1">
            @foreach([42, 68, 52, 88, 58, 76, 48, 82, 55, 72, 60, 90] as $h)
            <div class="flex-1 max-w-8 rounded-t-md ui-shimmer-bar" style="height: {{ $h }}%"></div>
            @endforeach
        </div>
    @elseif($type === 'doughnut')
        <div class="flex items-center justify-center h-full">
            <div class="w-36 h-36 rounded-full border-[14px] border-transparent ui-shimmer-ring ring-4 ring-slate-100"></div>
        </div>
    @else
        <div class="flex flex-col justify-center gap-3 h-full py-2">
            @foreach([92, 78, 65, 50, 38] as $w)
            <div class="h-7 rounded-lg ui-shimmer-bar" style="width: {{ $w }}%"></div>
            @endforeach
        </div>
    @endif
</div>
