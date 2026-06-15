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
<div x-data="pricingWizard">
@php
    $wizardSteps = $beginner
        ? [__('pricing.steps.basic'), __('pricing.steps.materials'), __('pricing.steps.labor'), __('pricing.steps.margin')]
        : [__('pricing.steps.basic'), __('pricing.steps.niche'), __('pricing.steps.materials'), __('pricing.steps.costs'), __('pricing.steps.margin'), __('pricing.steps.stock')];
@endphp
<div class="ui-card p-4 mb-8 overflow-x-auto">
    <div class="flex min-w-max gap-2 text-xs font-semibold">
        @foreach($wizardSteps as $i => $step)
        <span
            class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm transition-colors"
            :class="activeWizardStep >= {{ $i + 1 }} ? 'bg-brand text-ink font-semibold' : 'bg-slate-100 text-slate-600'"
        >
            <span
                class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]"
                :class="activeWizardStep >= {{ $i + 1 }} ? 'bg-ink/20 text-ink' : 'bg-brand/20 text-brand-dark'"
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

<form method="POST" action="{{ route('tenant.pricing.update', $product) }}" class="space-y-6 animate-fade-in">
    @csrf @method('PUT')

    <x-ui.card>
        <h2 class="ui-section-title">{{ __('pricing.section_basic') }}</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="ui-label">{{ __('pricing.name') }}</label><input name="name" value="{{ $product->name }}" required class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.production_time') }}</label><input type="number" name="production_time_minutes" value="{{ $product->production_time_minutes }}" class="ui-input"></div>
            <div class="md:col-span-2"><label class="ui-label">{{ __('pricing.description') }}</label><textarea name="description" rows="2" class="ui-input">{{ $product->description }}</textarea></div>
            <label class="flex items-center gap-2 text-sm text-slate-600 md:col-span-2">
                <input type="checkbox" name="is_custom_order" value="1" @checked($product->is_custom_order) class="rounded border-slate-300 text-brand focus:ring-brand/30">
                {{ __('pricing.custom_order') }}
            </label>
        </div>
    </x-ui.card>

    @php $nf = $product->niche_fields ?? []; @endphp
    @if(!$beginner && $tenant->interface_mode === 'alimentos')
    <x-ui.card>
        <h2 class="ui-section-title">Campos — Alimentos</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="ui-label">Porção / rendimento</label><input name="niche_fields[portion_yield]" value="{{ $nf['portion_yield'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">Validade</label><input name="niche_fields[shelf_life]" value="{{ $nf['shelf_life'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">Temperatura armazenamento</label><input name="niche_fields[storage_temp]" value="{{ $nf['storage_temp'] ?? '' }}" class="ui-input"></div>
            <div class="md:col-span-2"><label class="ui-label">Rótulo nutricional</label><textarea name="niche_fields[nutrition_label]" rows="2" class="ui-input">{{ $nf['nutrition_label'] ?? '' }}</textarea></div>
        </div>
    </x-ui.card>
    @elseif(!$beginner && $tenant->interface_mode === 'servico')
    <x-ui.card>
        <h2 class="ui-section-title">Campos — Serviços</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div><label class="ui-label">Taxa mínima visita (R$)</label><input type="number" step="0.01" name="niche_fields[minimum_visit_fee]" value="{{ $nf['minimum_visit_fee'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">Deslocamento (R$)</label><input type="number" step="0.01" name="niche_fields[travel_cost]" value="{{ $nf['travel_cost'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">Ferramentas (R$)</label><input type="number" step="0.01" name="niche_fields[tools_cost]" value="{{ $nf['tools_cost'] ?? '' }}" class="ui-input"></div>
        </div>
        <x-ui.button variant="outline" :href="route('tenant.quotes.pdf', $product)" class="mt-4">Baixar orçamento PDF</x-ui.button>
    </x-ui.card>
    @elseif(!$beginner && $tenant->interface_mode === 'artesanato')
    <x-ui.card>
        <h2 class="ui-section-title">Campos — Artesanato</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="ui-label">Coleção</label><input name="niche_fields[collection]" value="{{ $nf['collection'] ?? '' }}" class="ui-input"></div>
            <div><label class="ui-label">Linha de produtos</label><input name="niche_fields[production_line]" value="{{ $nf['production_line'] ?? '' }}" class="ui-input"></div>
        </div>
    </x-ui.card>
    @endif

    @if($tenant->interface_mode !== 'servico')
    <x-ui.card>
        <h2 class="ui-section-title">
            2. Ficha técnica (materiais)
            @if($beginner)
            <span class="text-slate-400 font-normal text-sm">— liste ingredientes ou materiais principais</span>
            @endif
        </h2>
        <template x-for="(row, i) in materials" :key="'m'+i">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-2">
                <input :name="'materials['+i+'][material_name]'" x-model="row.material_name" @input.debounce.400ms="updatePreview()" placeholder="Material" class="ui-input md:col-span-2">
                <input :name="'materials['+i+'][quantity]'" x-model="row.quantity" @input.debounce.400ms="updatePreview()" type="number" step="0.0001" placeholder="Qtd" class="ui-input">
                <input :name="'materials['+i+'][unit]'" x-model="row.unit" placeholder="Un" class="ui-input">
                <input :name="'materials['+i+'][unit_cost]'" x-model="row.unit_cost" @input.debounce.400ms="updatePreview()" type="number" step="0.0001" placeholder="R$/un" class="ui-input">
            </div>
        </template>
        <button type="button" @click="materials.push({material_name:'',quantity:0,unit:'g',unit_cost:0}); updatePreview()" class="text-brand text-sm font-semibold hover:text-brand-dark">+ Adicionar material</button>
    </x-ui.card>
    @endif

    @if(!$beginner)
    <x-ui.card>
        <h2 class="ui-section-title">3. Custos variáveis e adicionais</h2>
        <p class="text-sm text-slate-500 mb-3">Variáveis (energia, gás, etc.)</p>
        <template x-for="(row, i) in variableCosts" :key="'v'+i">
            <div class="grid grid-cols-3 gap-2 mb-2">
                <input :name="'variable_costs['+i+'][name]'" x-model="row.name" @input.debounce.400ms="updatePreview()" placeholder="Nome" class="ui-input col-span-2">
                <input :name="'variable_costs['+i+'][amount]'" x-model="row.amount" @input.debounce.400ms="updatePreview()" type="number" step="0.01" placeholder="R$" class="ui-input">
            </div>
        </template>
        <button type="button" @click="variableCosts.push({name:'',amount:0})" class="text-brand text-sm font-semibold mb-6 block hover:text-brand-dark">+ Custo variável</button>

        <p class="text-sm text-slate-500 mb-3">Adicionais (embalagem, etiqueta…)</p>
        <template x-for="(row, i) in additionalCosts" :key="'a'+i">
            <div class="grid grid-cols-3 gap-2 mb-2">
                <input :name="'additional_costs['+i+'][name]'" x-model="row.name" @input.debounce.400ms="updatePreview()" placeholder="Nome" class="ui-input col-span-2">
                <input :name="'additional_costs['+i+'][amount]'" x-model="row.amount" @input.debounce.400ms="updatePreview()" type="number" step="0.01" placeholder="R$" class="ui-input">
            </div>
        </template>
        <button type="button" @click="additionalCosts.push({name:'',amount:0})" class="text-brand text-sm font-semibold block hover:text-brand-dark">+ Custo adicional</button>
    </x-ui.card>
    @endif

    <x-ui.card>
        <h2 class="ui-section-title">
            @if($beginner)3. @endif {{ __('pricing.section_labor') }}
            @if($beginner)
            <span class="text-slate-400 font-normal text-sm">{{ __('pricing.labor_hint') }}</span>
            @endif
        </h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="ui-label">{{ __('pricing.hourly_rate') }}</label><input name="hourly_rate" x-model="hourlyRate" @input.debounce.400ms="updatePreview()" type="number" step="0.01" class="ui-input"></div>
            <div><label class="ui-label">{{ __('pricing.hours_spent') }}</label><input name="hours_spent" x-model="hoursSpent" @input.debounce.400ms="updatePreview()" type="number" step="0.01" class="ui-input"></div>
        </div>
    </x-ui.card>

    <x-ui.card class="border-brand/20 bg-gradient-to-br from-brand/[0.03] to-transparent">
        <h2 class="ui-section-title">{{ __('pricing.section_margin') }}</h2>
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($margins as $margin)
            <label class="cursor-pointer">
                <input
                    type="radio"
                    name="profit_margin_percent"
                    value="{{ $margin->value }}"
                    class="sr-only peer"
                    @checked($product->profit_margin_percent == $margin->value)
                    @change="setMargin($event.target.value)"
                >
                <span class="block px-4 py-2.5 rounded-xl border border-slate-200 bg-white peer-checked:bg-brand peer-checked:border-brand peer-checked:text-ink peer-checked:font-bold text-sm transition-colors">
                    {{ $margin->label() }}
                    @if($margin->isPremiumOnly()) ⭐ @endif
                </span>
            </label>
            @endforeach
        </div>

        <div class="mb-6">
            <button type="button" @click="compareMargins()" :disabled="compareLoading" class="text-sm font-semibold text-brand-dark hover:text-brand inline-flex items-center gap-2">
                <span x-show="!compareLoading">{{ __('pricing.compare_margins') }}</span>
                <span x-show="compareLoading" x-cloak>{{ __('pricing.comparing') }}</span>
            </button>
        </div>
        <div x-show="compareScenarios.length" x-cloak class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
            <template x-for="scenario in compareScenarios" :key="scenario.margin">
                <button
                    type="button"
                    @click="setMargin(String(scenario.margin))"
                    class="text-left p-3 rounded-xl border border-slate-200 bg-white hover:border-brand/40 transition-colors"
                    :class="Number(selectedMargin) === Number(scenario.margin) ? 'ring-2 ring-brand/40' : ''"
                >
                    <p class="text-xs text-slate-500 uppercase tracking-wide">Margem <span x-text="scenario.margin"></span>%</p>
                    <p class="text-lg font-bold text-brand-dark mt-1" x-text="formatBrl(scenario.breakdown?.final_price)"></p>
                    <p class="text-xs text-slate-500 mt-1">Lucro: <span x-text="formatBrl(scenario.breakdown?.profit_absolute)"></span></p>
                </button>
            </template>
        </div>

        <div x-show="breakdown" x-cloak class="mb-6 space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                <div class="bg-white/80 p-3 rounded-xl border border-slate-100">
                    <span class="text-slate-500 block text-xs uppercase tracking-wide">Custo produção</span>
                    <strong class="text-ink" x-text="formatBrl(breakdown?.total_production)"></strong>
                </div>
                <div class="bg-brand/10 p-3 rounded-xl border border-brand/20">
                    <span class="text-slate-500 block text-xs uppercase tracking-wide">Lucro (<span x-text="selectedMargin"></span>%)</span>
                    <strong class="text-brand" x-text="formatBrl(breakdown?.profit_absolute)"></strong>
                </div>
                <div class="bg-white/80 p-3 rounded-xl border border-slate-100 col-span-2 md:col-span-1">
                    <span class="text-slate-500 block text-xs uppercase tracking-wide">Preço sugerido</span>
                    <strong class="text-ink text-lg" x-text="formatBrl(breakdown?.final_price)"></strong>
                </div>
            </div>
            <details class="text-xs text-slate-500">
                <summary class="cursor-pointer hover:text-brand font-medium">Ver detalhamento dos custos</summary>
                <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2 pt-2">
                    <span>Materiais: <strong x-text="formatBrl(breakdown?.materials_cost)"></strong></span>
                    <span>Mão de obra: <strong x-text="formatBrl(breakdown?.labor_cost)"></strong></span>
                    <span>Custos fixos: <strong x-text="formatBrl(breakdown?.fixed_cost_share)"></strong></span>
                    <span>Variáveis: <strong x-text="formatBrl(breakdown?.variable_costs)"></strong></span>
                    <span>Adicionais: <strong x-text="formatBrl(breakdown?.additional_costs)"></strong></span>
                </div>
            </details>
        </div>

        <p x-show="breakdown" x-cloak class="text-4xl font-display font-bold text-brand mb-4" x-text="formatBrl(breakdown?.final_price)"></p>
        <p x-show="!breakdown && {{ $product->selling_price ? 'true' : 'false' }}" class="text-4xl font-display font-bold text-brand mb-4">R$ {{ number_format($product->selling_price, 2, ',', '.') }}</p>

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
        <button type="button" @click="fetchAi()" :disabled="aiLoading" class="text-sm text-brand font-semibold hover:text-brand-dark disabled:opacity-50 inline-flex items-center gap-2">
            <x-ui.nav-icon name="spark" class="w-4 h-4" x-show="!aiLoading" />
            <span x-show="!aiLoading">Calcular com IA</span>
            <span x-show="aiLoading" x-cloak class="flex items-center gap-2">
                <span class="w-4 h-4 border-2 border-brand/30 border-t-brand rounded-full animate-spin"></span>
                <span x-text="aiStep"></span>
            </span>
        </button>
        <div x-show="aiLoading" x-cloak class="mt-2 text-xs text-slate-500">Usando custos e margem selecionada…</div>
        <div x-show="aiText" x-cloak class="mt-3 p-4 bg-brand/10 rounded-xl text-sm whitespace-pre-wrap border border-brand/20" x-text="aiText"></div>
        @endif
    </x-ui.card>

    @if(!$beginner)
    <x-ui.card>
        <h2 class="ui-section-title">5. Estoque</h2>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="ui-label">Quantidade</label><input name="stock_quantity" type="number" value="{{ $product->stock_quantity }}" class="ui-input"></div>
            <div><label class="ui-label">Alerta mínimo</label><input name="min_stock_alert" type="number" value="{{ $product->min_stock_alert }}" class="ui-input"></div>
        </div>
    </x-ui.card>
    @endif

    <div class="sticky bottom-4 z-20 flex justify-end">
        <x-ui.button type="submit" class="py-3 px-10 shadow-lg shadow-brand/20 w-full md:w-auto">Salvar e calcular preço</x-ui.button>
    </div>
</form>
</div>
@endsection

@push('scripts')
@php
    $materialsData = $product->technicalSheets->map(fn ($t) => [
        'material_name' => $t->material_name,
        'quantity' => $t->quantity,
        'unit' => $t->unit,
        'unit_cost' => $t->unit_cost,
    ])->values();
    $variableCostsData = $product->variableCosts->map(fn ($c) => [
        'name' => $c->name,
        'amount' => $c->amount,
    ])->values();
    $additionalCostsData = $product->additionalCosts->map(fn ($c) => [
        'name' => $c->name,
        'amount' => $c->amount,
    ])->values();
@endphp
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pricingWizard', () => ({
        materials: @json($materialsData),
        variableCosts: @json($variableCostsData),
        additionalCosts: @json($additionalCostsData),
        hourlyRate: {{ $product->laborCosts->first()?->hourly_rate ?? 0 }},
        hoursSpent: {{ $product->laborCosts->first()?->hours_spent ?? 0 }},
        selectedMargin: '{{ (int) ($product->profit_margin_percent ?? 50) }}',
        activeWizardStep: 1,
        breakdown: null,
        aiText: '',
        aiLoading: false,
        aiStep: 'Calculando custos…',
        previewTimer: null,
        compareScenarios: [],
        compareLoading: false,
        init() {
            if (this.materials.length === 0) {
                this.materials.push({ material_name: '', quantity: 0, unit: 'g', unit_cost: 0 });
            }
            if (this.variableCosts.length === 0) {
                this.variableCosts.push({ name: '', amount: 0 });
            }
            if (this.additionalCosts.length === 0) {
                this.additionalCosts.push({ name: '', amount: 0 });
            }
            const checked = document.querySelector('input[name="profit_margin_percent"]:checked');
            if (checked) {
                this.selectedMargin = checked.value;
            }
            this.updatePreview();
        },
        formatBrl(value) {
            const n = Number(value);
            if (Number.isNaN(n)) return 'R$ 0,00';
            return n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        },
        setMargin(value) {
            this.selectedMargin = value;
            this.updatePreview();
        },
        buildPayload() {
            return {
                profit_margin_percent: Number(this.selectedMargin),
                materials: this.materials,
                variable_costs: this.variableCosts,
                additional_costs: this.additionalCosts,
                hourly_rate: Number(this.hourlyRate) || 0,
                hours_spent: Number(this.hoursSpent) || 0,
            };
        },
        refreshWizardStep() {
            const maxStep = {{ count($wizardSteps) }};
            if (this.breakdown?.final_price > 0) {
                this.activeWizardStep = maxStep;
            } else if (Number(this.hourlyRate) > 0 || Number(this.hoursSpent) > 0) {
                this.activeWizardStep = Math.min(maxStep, {{ $beginner ? 3 : 4 }});
            } else if (this.materials.some(m => m.material_name)) {
                this.activeWizardStep = 2;
            } else {
                this.activeWizardStep = 1;
            }
        },
        updatePreview() {
            clearTimeout(this.previewTimer);
            this.previewTimer = setTimeout(() => this.runPreview(), 350);
        },
        runPreview() {
            fetch('{{ route('tenant.pricing.preview', $product) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(this.buildPayload()),
            })
                .then(r => r.json())
                .then(data => {
                    if (data.breakdown) {
                        this.breakdown = data.breakdown;
                        this.refreshWizardStep();
                    }
                })
                .catch(() => { window.toast?.error('Não foi possível atualizar a prévia.'); });
        },
        compareMargins() {
            this.compareLoading = true;
            const margins = @json(array_map(fn ($m) => $m->value, $margins));
            fetch('{{ route('tenant.pricing.compare', $product) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ...this.buildPayload(), margins }),
            })
                .then(r => r.json())
                .then(data => { this.compareScenarios = data.scenarios || []; })
                .catch(() => { window.toast?.error('Não foi possível comparar margens.'); })
                .finally(() => { this.compareLoading = false; });
        },
        fetchAi() {
            this.aiLoading = true;
            this.aiText = '';
            this.aiStep = 'Calculando custos…';
            const stepTimer = setInterval(() => {
                if (this.aiStep === 'Calculando custos…') this.aiStep = 'Analisando margem…';
                else if (this.aiStep === 'Analisando margem…') this.aiStep = 'Gerando recomendações…';
            }, 1200);
            fetch('{{ route('tenant.pricing.ai', $product) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(this.buildPayload()),
            })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data.message || 'Não foi possível consultar a IA.');
                    }
                    if (data.breakdown) {
                        this.breakdown = data.breakdown;
                        this.refreshWizardStep();
                    }
                    this.aiText = data.suggestion || 'A IA não retornou uma sugestão.';
                })
                .catch((err) => {
                    const msg = err.message || 'Erro ao consultar a IA.';
                    this.aiText = msg;
                    window.toast?.error(msg);
                })
                .finally(() => {
                    clearInterval(stepTimer);
                    this.aiLoading = false;
                    this.aiStep = 'Calculando custos…';
                });
        },
    }));
});
</script>
@endpush
