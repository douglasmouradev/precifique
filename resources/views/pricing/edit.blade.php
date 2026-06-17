@extends('layouts.tenant')
@section('title', __('pricing.title', ['name' => $product->name]))
@section('breadcrumb') {{ __('pricing.breadcrumb', ['name' => $product->name]) }} @endsection

@section('content')
@php $beginner = ($tenant->usage_mode ?? 'avancado') === 'iniciante'; @endphp
<x-ui.page-header :title="__('pricing.page_title', ['name' => $product->name])" :subtitle="$beginner ? __('pricing.subtitle_beginner') : __('pricing.subtitle_advanced')">
    <x-slot:actions>
        @if($product->selling_price)
        <x-ui.button variant="outline" :href="route('tenant.quotes.pdf', $product)">{{ __('pricing.export_pdf') }}</x-ui.button>
        @endif
        <x-ui.button variant="outline" :href="route('tenant.products.index')">{{ __('pricing.back') }}</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

@if($beginner)
<x-ui.alert class="mb-6">
    <strong>{{ __('pricing.beginner_alert') }}</strong> {{ __('pricing.beginner_alert_text') }}
</x-ui.alert>
@endif

@if($tenant->onTrial())
<x-ui.alert type="warning" class="mb-6">
    {{ __('pricing.trial_alert', ['date' => $tenant->trial_ends_at->format('d/m/Y')]) }}
</x-ui.alert>
@endif

{{-- Indicador de etapas + formulário --}}
@php
    $wizardSteps = $beginner
        ? [__('pricing.steps.basic'), __('pricing.steps.materials'), __('pricing.steps.labor'), __('pricing.steps.margin')]
        : [__('pricing.steps.basic'), __('pricing.steps.niche'), __('pricing.steps.materials'), __('pricing.steps.costs'), __('pricing.steps.margin'), __('pricing.steps.stock')];
    $materialsData = $product->technicalSheets->map(fn ($t) => [
        'material_name' => $t->material_name,
        'quantity' => $t->quantity,
        'unit' => $t->unit,
        'unit_cost' => $t->unit_cost,
    ])->values();
    $variableCostsData = $product->variableCosts->map(fn ($c) => ['name' => $c->name, 'amount' => $c->amount])->values();
    $additionalCostsData = $product->additionalCosts->map(fn ($c) => ['name' => $c->name, 'amount' => $c->amount])->values();
    $pricingConfig = [
        'materials' => $materialsData,
        'variableCosts' => $variableCostsData,
        'additionalCosts' => $additionalCostsData,
        'hourlyRate' => $product->laborCosts->first()?->hourly_rate ?? 0,
        'hoursSpent' => $product->laborCosts->first()?->hours_spent ?? 0,
        'selectedMargin' => (int) ($product->profit_margin_percent ?? 50),
        'maxStep' => count($wizardSteps),
        'laborStep' => $beginner ? 3 : 4,
        'margins' => array_map(fn ($m) => $m->value, $margins),
        'previewUrl' => route('tenant.pricing.preview', $product),
        'compareUrl' => route('tenant.pricing.compare', $product),
        'aiUrl' => $tenant->isPremium() ? route('tenant.pricing.ai', $product) : null,
        'csrf' => csrf_token(),
        'labels' => [
            'previewError' => __('pricing.preview_error'),
            'compareError' => __('pricing.compare_error'),
            'aiError' => __('pricing.ai_error'),
            'aiEmpty' => __('pricing.ai_empty'),
            'aiStep1' => __('pricing.ai_step_1'),
            'aiStep2' => __('pricing.ai_step_2'),
            'aiStep3' => __('pricing.ai_step_3'),
        ],
    ];
@endphp
<div
    id="pricing-wizard"
    data-config="{{ json_encode($pricingConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
>
<div class="ui-card p-4 mb-8 overflow-x-auto pricing-wizard-sticky">
    <div class="flex min-w-max gap-2 text-xs font-semibold">
        @foreach($wizardSteps as $i => $step)
        <span
            data-wizard-step="{{ $i + 1 }}"
            class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm transition-colors bg-slate-100 text-slate-600"
        >
            <span
                data-wizard-badge
                class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] bg-brand/20 text-brand-dark"
            >{{ $i + 1 }}</span>
            {{ $step }}
        </span>
        @if($i < count($wizardSteps) - 1)<span class="text-slate-300 self-center">→</span>@endif
        @endforeach
    </div>
</div>

@if($product->priceHistories->isNotEmpty())
<x-ui.card class="mb-8">
    <h2 class="ui-section-title">{{ __('pricing.price_history') }}</h2>
    <div class="overflow-x-auto">
        <table class="ui-table text-sm">
            <thead><tr><th>{{ __('pricing.history_date') }}</th><th>{{ __('pricing.history_price') }}</th><th>{{ __('pricing.history_margin') }}</th><th>{{ __('pricing.history_cost') }}</th></tr></thead>
            <tbody>
            @foreach($product->priceHistories->take(8) as $history)
            <tr>
                <td class="text-slate-500">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                <td class="font-semibold text-brand-dark">R$ {{ number_format($history->selling_price, 2, ',', '.') }}</td>
                <td>{{ $history->profit_margin_percent ? number_format($history->profit_margin_percent, 0).'%' : '—' }}</td>
                <td>{{ $history->production_cost ? 'R$ '.number_format($history->production_cost, 2, ',', '.') : '—' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-ui.card>
@endif

<div class="lg:grid lg:grid-cols-[minmax(0,1fr)_16rem] lg:gap-8 lg:items-start">
<aside class="pricing-sticky-summary order-first lg:order-last mb-6 lg:mb-0">
    <x-ui.card class="shadow-premium-glow border-brand/10">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('pricing.suggested_price') }}</p>
        <div data-pricing-preview-loading class="hidden h-10 ui-shimmer-bar rounded-lg mt-3"></div>
        <p data-pricing-sticky-price class="text-3xl font-display font-bold text-brand mt-2 tabular-nums">—</p>
        <p data-pricing-sticky-margin class="text-xs text-slate-500 mt-2"></p>
    </x-ui.card>
</aside>

<div class="min-w-0">
<form method="POST" action="{{ route('tenant.pricing.update', $product) }}" class="space-y-6">
    @csrf @method('PUT')

    <x-ui.card>
        <details class="pricing-collapsible" open>
        <summary>{{ __('pricing.section_basic') }}</summary>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="ui-label">{{ __('pricing.name') }}</label><input name="name" value="{{ $product->name }}" required class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.production_time') }}</label><input type="number" name="production_time_minutes" value="{{ $product->production_time_minutes }}" class="ui-input"></div>
            <div class="md:col-span-2"><label class="ui-label">{{ __('pricing.description') }}</label><textarea name="description" rows="2" class="ui-input">{{ $product->description }}</textarea></div>
            <label class="flex items-center gap-2 text-sm text-slate-600 md:col-span-2">
                <input type="checkbox" name="is_custom_order" value="1" @checked($product->is_custom_order) class="rounded border-slate-300 text-brand focus:ring-brand/30">
                {{ __('pricing.custom_order') }}
            </label>
        </div>
        </details>
    </x-ui.card>

    @php $nf = $product->niche_fields ?? []; @endphp
    @if(!$beginner && $tenant->interface_mode === 'alimentos')
    <x-ui.card>
        <h2 class="ui-section-title">{{ __('pricing.niche_food.title') }}</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="ui-label">{{ __('pricing.niche_food.portion_yield') }}</label><input name="niche_fields[portion_yield]" value="{{ $nf['portion_yield'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.niche_food.shelf_life') }}</label><input name="niche_fields[shelf_life]" value="{{ $nf['shelf_life'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.niche_food.storage_temp') }}</label><input name="niche_fields[storage_temp]" value="{{ $nf['storage_temp'] ?? '' }}" class="ui-input"></div>
            <div class="md:col-span-2"><label class="ui-label">{{ __('pricing.niche_food.nutrition_label') }}</label><textarea name="niche_fields[nutrition_label]" rows="2" class="ui-input">{{ $nf['nutrition_label'] ?? '' }}</textarea></div>
        </div>
    </x-ui.card>
    @elseif(!$beginner && $tenant->interface_mode === 'servico')
    <x-ui.card>
        <h2 class="ui-section-title">{{ __('pricing.niche_service.title') }}</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div><label class="ui-label">{{ __('pricing.niche_service.minimum_visit_fee') }}</label><input type="number" step="0.01" name="niche_fields[minimum_visit_fee]" value="{{ $nf['minimum_visit_fee'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.niche_service.travel_cost') }}</label><input type="number" step="0.01" name="niche_fields[travel_cost]" value="{{ $nf['travel_cost'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.niche_service.tools_cost') }}</label><input type="number" step="0.01" name="niche_fields[tools_cost]" value="{{ $nf['tools_cost'] ?? '' }}" class="ui-input"></div>
        </div>
        <x-ui.button variant="outline" :href="route('tenant.quotes.pdf', $product)" class="mt-4">{{ __('pricing.download_quote_pdf') }}</x-ui.button>
    </x-ui.card>
    @elseif(!$beginner && $tenant->interface_mode === 'artesanato')
    <x-ui.card>
        <h2 class="ui-section-title">{{ __('pricing.niche_handmade.title') }}</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="ui-label">{{ __('pricing.niche_handmade.collection') }}</label><input name="niche_fields[collection]" value="{{ $nf['collection'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.niche_handmade.production_line') }}</label><input name="niche_fields[production_line]" value="{{ $nf['production_line'] ?? '' }}" class="ui-input"></div>
        </div>
    </x-ui.card>
    @endif

    @if($tenant->interface_mode !== 'servico')
    <x-ui.card>
        <details class="pricing-collapsible" open>
        <summary>
            2. Ficha técnica (materiais)
            @if($beginner)
            <span class="text-slate-400 font-normal text-sm">— liste ingredientes ou materiais principais</span>
            @endif
        </summary>
        <div data-pricing-materials></div>
        <button type="button" data-pricing-add-material class="text-brand text-sm font-semibold hover:text-brand-dark">+ {{ __('pricing.add_material') }}</button>
        </details>
    </x-ui.card>
    @endif

    @if(!$beginner)
    <x-ui.card>
        <details class="pricing-collapsible" open>
        <summary>3. Custos variáveis e adicionais</summary>
        <p class="text-sm text-slate-500 mb-3">{{ __('pricing.variables_hint') }}</p>
        <div data-pricing-variable-costs></div>
        <button type="button" data-pricing-add-variable class="text-brand text-sm font-semibold mb-6 block hover:text-brand-dark">+ {{ __('pricing.add_variable') }}</button>

        <p class="text-sm text-slate-500 mb-3">{{ __('pricing.additional_hint') }}</p>
        <div data-pricing-additional-costs></div>
        <button type="button" data-pricing-add-additional class="text-brand text-sm font-semibold block hover:text-brand-dark">+ {{ __('pricing.add_additional') }}</button>
        </details>
    </x-ui.card>
    @endif

    <x-ui.card>
        <details class="pricing-collapsible" open>
        <summary>
            @if($beginner)3. @endif {{ __('pricing.section_labor') }}
            @if($beginner)
            <span class="text-slate-400 font-normal text-sm">{{ __('pricing.labor_hint') }}</span>
            @endif
        </summary>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="ui-label">{{ __('pricing.hourly_rate') }}</label><input name="hourly_rate" data-pricing-hourly-rate type="number" step="0.01" value="{{ $product->laborCosts->first()?->hourly_rate ?? 0 }}" class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.hours_spent') }}</label><input name="hours_spent" data-pricing-hours-spent type="number" step="0.01" value="{{ $product->laborCosts->first()?->hours_spent ?? 0 }}" class="ui-input"></div>
        </div>
        </details>
    </x-ui.card>

    <x-ui.card class="border-brand/20 bg-gradient-to-br from-brand/[0.03] to-transparent">
        <details class="pricing-collapsible" open>
        <summary>{{ __('pricing.section_margin') }}</summary>
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($margins as $margin)
            <label class="cursor-pointer">
                <input
                    type="radio"
                    name="profit_margin_percent"
                    value="{{ $margin->value }}"
                    class="sr-only peer"
                    @checked($product->profit_margin_percent == $margin->value)
                >
                <span class="block px-4 py-2.5 rounded-xl border border-slate-200 bg-white peer-checked:bg-brand peer-checked:border-brand peer-checked:text-ink peer-checked:font-bold text-sm transition-colors">
                    {{ $margin->label() }}
                    @if($margin->isPremiumOnly()) ⭐ @endif
                </span>
            </label>
            @endforeach
        </div>

        <div class="mb-6">
            <button type="button" data-pricing-compare-btn class="text-sm font-semibold text-brand-dark hover:text-brand inline-flex items-center gap-2 touch-manipulation">
                {{ __('pricing.compare_margins') }}
            </button>
        </div>
        <div data-pricing-compare class="hidden mb-6">
            <div data-pricing-compare-grid class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>
        </div>

        <div data-pricing-breakdown class="hidden mb-6 space-y-4">
            <div data-pricing-preview-loading-inline class="hidden h-24 ui-shimmer-bar rounded-xl mb-4"></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                <div class="bg-white/80 p-3 rounded-xl border border-slate-100">
                    <span class="text-slate-500 block text-xs uppercase tracking-wide">{{ __('pricing.production_cost') }}</span>
                    <strong class="text-ink" data-bd-production></strong>
                </div>
                <div class="bg-brand/10 p-3 rounded-xl border border-brand/20">
                    <span class="text-slate-500 block text-xs uppercase tracking-wide">{{ __('pricing.profit') }} (<span data-bd-margin></span>%)</span>
                    <strong class="text-brand" data-bd-profit></strong>
                </div>
                <div class="bg-white/80 p-3 rounded-xl border border-slate-100 col-span-2 md:col-span-1">
                    <span class="text-slate-500 block text-xs uppercase tracking-wide">{{ __('pricing.suggested_price') }}</span>
                    <strong class="text-ink text-lg" data-bd-price></strong>
                </div>
            </div>
            <details class="text-xs text-slate-500">
                <summary class="cursor-pointer hover:text-brand font-medium">{{ __('pricing.cost_breakdown') }}</summary>
                <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2 pt-2">
                    <span>{{ __('pricing.materials_cost') }}: <strong data-bd-materials></strong></span>
                    <span>{{ __('pricing.labor_cost') }}: <strong data-bd-labor></strong></span>
                    <span>{{ __('pricing.fixed_cost') }}: <strong data-bd-fixed></strong></span>
                    <span>{{ __('pricing.variable_costs') }}: <strong data-bd-variable></strong></span>
                    <span>{{ __('pricing.additional_costs') }}: <strong data-bd-additional></strong></span>
                </div>
            </details>
        </div>

        <p data-pricing-price-hero class="hidden text-4xl font-display font-bold text-brand mb-4"></p>
        <p data-pricing-saved-price class="text-4xl font-display font-bold text-brand mb-4 {{ $product->selling_price ? '' : 'hidden' }}">@if($product->selling_price)R$ {{ number_format($product->selling_price, 2, ',', '.') }}@endif</p>

        @if(session('pricing'))
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm mb-6">
            @foreach(session('pricing') as $key => $val)
            @if(is_numeric($val))
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="text-slate-500 block text-xs uppercase tracking-wide">{{ str_replace('_',' ',$key) }}</span>
                <strong class="text-ink">R$ {{ number_format($val, 2, ',', '.') }}</strong>
            </div>
            @endif
            @endforeach
        </div>
        @endif
        @if($tenant->isPremium())
        <button type="button" data-pricing-ai class="text-sm text-brand font-semibold hover:text-brand-dark inline-flex items-center gap-2 touch-manipulation">
            <x-ui.nav-icon name="spark" class="w-4 h-4" />
            {{ __('pricing.ai_calculate') }}
        </button>
        <div data-pricing-ai-loading class="hidden mt-2 text-xs text-slate-500 flex items-center gap-2">
            <span class="w-4 h-4 border-2 border-brand/30 border-t-brand rounded-full animate-spin"></span>
            <span data-pricing-ai-step>{{ __('pricing.ai_step_1') }}</span>
        </div>
        <div data-pricing-ai-text class="hidden mt-3 p-4 bg-brand/10 rounded-xl text-sm whitespace-pre-wrap border border-brand/20"></div>
        @endif
        </details>
    </x-ui.card>

    @if(!$beginner)
    <x-ui.card>
        <details class="pricing-collapsible" open>
        <summary>5. Estoque</summary>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="ui-label">Quantidade</label><input name="stock_quantity" type="number" value="{{ $product->stock_quantity }}" class="ui-input"></div>
            <div><label class="ui-label">Alerta mínimo</label><input name="min_stock_alert" type="number" value="{{ $product->min_stock_alert }}" class="ui-input"></div>
        </div>
        </details>
    </x-ui.card>
    @endif

    <div class="form-sticky-submit lg:static lg:p-0 lg:border-0 lg:shadow-none lg:bg-transparent lg:backdrop-blur-none">
        <x-ui.button type="submit" class="py-3 px-10 shadow-lg shadow-brand/20 w-full">{{ __('pricing.save_and_calculate') }}</x-ui.button>
    </div>
</form>
</div>
</div>
</div>
@endsection
