@props(['progress'])

@if($progress && ($progress['percent'] ?? 100) < 100)
<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <div class="ui-card-premium overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-100/80 bg-gradient-to-r from-brand/[0.06] via-transparent to-transparent">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-brand-dark">{{ __('app.setup_progress.journey') }}</p>
                    <h2 class="font-display text-lg font-bold text-ink mt-0.5">{{ __('app.setup_progress.title') }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ __('app.setup_progress.steps_completed', ['completed' => $progress['completed'], 'total' => $progress['total']]) }}</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="relative w-14 h-14">
                        <svg class="w-14 h-14 -rotate-90" viewBox="0 0 36 36" aria-hidden="true">
                            <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-slate-100" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-brand transition-all duration-700" stroke-width="3"
                                stroke-dasharray="97.4" stroke-dashoffset="{{ 97.4 - (97.4 * ($progress['percent'] / 100)) }}"
                                stroke-linecap="round"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-ink">{{ $progress['percent'] }}%</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-brand to-brand-dark rounded-full transition-all duration-700" style="width: {{ $progress['percent'] }}%"></div>
            </div>
        </div>
        <ul class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
            @foreach($progress['steps'] as $step)
            <li class="flex items-center gap-3 px-4 md:px-5 py-3 text-sm {{ $step['done'] ? 'bg-slate-50/50' : '' }}">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 {{ $step['done'] ? 'bg-brand text-ink' : 'ring-1 ring-slate-200 text-slate-400' }}">
                    {{ $step['done'] ? '✓' : '·' }}
                </span>
                @if(!$step['done'] && $step['url'])
                <a href="{{ $step['url'] }}" class="font-medium text-brand-dark hover:underline">{{ $step['label'] }}</a>
                @else
                <span class="{{ $step['done'] ? 'text-slate-500 line-through' : 'text-slate-700' }}">{{ $step['label'] }}</span>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
