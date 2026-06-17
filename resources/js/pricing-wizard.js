/**
 * Wizard de precificação — JS puro.
 */
function debounce(fn, ms) {
    let timer = null;
    return (...args) => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => fn(...args), ms);
    };
}

function formatBrl(value) {
    const number = Number(value);
    if (Number.isNaN(number)) return 'R$ 0,00';
    return number.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

export function initPricingWizard() {
    const root = document.getElementById('pricing-wizard');
    if (!root) return;

    let config = {};
    try { config = JSON.parse(root.dataset.config || '{}'); } catch (_) { return; }

    const state = {
        materials: [...(config.materials || [])],
        variableCosts: [...(config.variableCosts || [])],
        additionalCosts: [...(config.additionalCosts || [])],
        hourlyRate: config.hourlyRate ?? 0,
        hoursSpent: config.hoursSpent ?? 0,
        selectedMargin: String(config.selectedMargin ?? 50),
        activeWizardStep: 1,
        breakdown: null,
        compareScenarios: [],
    };

    const materialsList = root.querySelector('[data-pricing-materials]');
    const variableList = root.querySelector('[data-pricing-variable-costs]');
    const additionalList = root.querySelector('[data-pricing-additional-costs]');
    const breakdownPanel = root.querySelector('[data-pricing-breakdown]');
    const comparePanel = root.querySelector('[data-pricing-compare]');
    const compareGrid = root.querySelector('[data-pricing-compare-grid]');
    const priceHero = root.querySelector('[data-pricing-price-hero]');
    const savedPrice = root.querySelector('[data-pricing-saved-price]');
    const aiTextEl = root.querySelector('[data-pricing-ai-text]');
    const aiLoadingEl = root.querySelector('[data-pricing-ai-loading]');
    const aiStepEl = root.querySelector('[data-pricing-ai-step]');
    const hourlyRateInput = root.querySelector('[data-pricing-hourly-rate]');
    const hoursSpentInput = root.querySelector('[data-pricing-hours-spent]');

    const ensureRows = () => {
        if (!state.materials.length) state.materials.push({ material_name: '', quantity: 0, unit: 'g', unit_cost: 0 });
        if (!state.variableCosts.length) state.variableCosts.push({ name: '', amount: 0 });
        if (!state.additionalCosts.length) state.additionalCosts.push({ name: '', amount: 0 });
    };

    const updateWizardSteps = () => {
        root.querySelectorAll('[data-wizard-step]').forEach((el) => {
            const step = Number(el.dataset.wizardStep);
            const active = state.activeWizardStep >= step;
            el.classList.toggle('bg-brand', active);
            el.classList.toggle('text-ink', active);
            el.classList.toggle('font-semibold', active);
            el.classList.toggle('bg-slate-100', !active);
            el.classList.toggle('text-slate-600', !active);
            el.querySelector('[data-wizard-badge]')?.classList.toggle('bg-ink/20', active);
            el.querySelector('[data-wizard-badge]')?.classList.toggle('text-ink', active);
            el.querySelector('[data-wizard-badge]')?.classList.toggle('bg-brand/20', !active);
            el.querySelector('[data-wizard-badge]')?.classList.toggle('text-brand-dark', !active);
        });
    };

    const refreshWizardStep = () => {
        const maxStep = Number(config.maxStep || 4);
        if (state.breakdown?.final_price > 0) state.activeWizardStep = maxStep;
        else if (Number(state.hourlyRate) > 0 || Number(state.hoursSpent) > 0) state.activeWizardStep = Math.min(maxStep, Number(config.laborStep || 3));
        else if (state.materials.some((r) => r.material_name)) state.activeWizardStep = 2;
        else state.activeWizardStep = 1;
        updateWizardSteps();
    };

    const renderBreakdown = () => {
        const has = Boolean(state.breakdown);
        breakdownPanel?.classList.toggle('hidden', !has);
        priceHero?.classList.toggle('hidden', !has);
        savedPrice?.classList.toggle('hidden', has);
        if (!has || !breakdownPanel) return;
        const set = (sel, val) => { const el = breakdownPanel.querySelector(sel); if (el) el.textContent = formatBrl(val); };
        set('[data-bd-production]', state.breakdown.total_production);
        set('[data-bd-profit]', state.breakdown.profit_absolute);
        set('[data-bd-price]', state.breakdown.final_price);
        set('[data-bd-materials]', state.breakdown.materials_cost);
        set('[data-bd-labor]', state.breakdown.labor_cost);
        set('[data-bd-fixed]', state.breakdown.fixed_cost_share);
        set('[data-bd-variable]', state.breakdown.variable_costs);
        set('[data-bd-additional]', state.breakdown.additional_costs);
        const marginEl = breakdownPanel.querySelector('[data-bd-margin]');
        if (marginEl) marginEl.textContent = state.selectedMargin;
        if (priceHero) priceHero.textContent = formatBrl(state.breakdown.final_price);
    };

    const renderCompare = () => {
        const has = state.compareScenarios.length > 0;
        comparePanel?.classList.toggle('hidden', !has);
        if (!compareGrid) return;
        compareGrid.innerHTML = '';
        state.compareScenarios.forEach((scenario) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `text-left p-3 rounded-xl border border-slate-200 bg-white hover:border-brand/40 transition-colors ${Number(state.selectedMargin) === Number(scenario.margin) ? 'ring-2 ring-brand/40' : ''}`;
            btn.innerHTML = `<p class="text-xs text-slate-500 uppercase tracking-wide">Margem ${scenario.margin}%</p><p class="text-lg font-bold text-brand-dark mt-1">${formatBrl(scenario.breakdown?.final_price)}</p><p class="text-xs text-slate-500 mt-1">Lucro: ${formatBrl(scenario.breakdown?.profit_absolute)}</p>`;
            btn.addEventListener('click', () => setMargin(String(scenario.margin)));
            compareGrid.appendChild(btn);
        });
    };

    const buildPayload = () => ({
        profit_margin_percent: Number(state.selectedMargin),
        materials: state.materials,
        variable_costs: state.variableCosts,
        additional_costs: state.additionalCosts,
        hourly_rate: Number(state.hourlyRate) || 0,
        hours_spent: Number(state.hoursSpent) || 0,
    });

    const runPreview = async () => {
        if (!config.previewUrl) return;
        try {
            const res = await fetch(config.previewUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf, Accept: 'application/json' }, body: JSON.stringify(buildPayload()) });
            const data = await res.json();
            if (data.breakdown) { state.breakdown = data.breakdown; refreshWizardStep(); renderBreakdown(); }
        } catch (_) { window.toast?.error(config.labels?.previewError || 'Não foi possível atualizar a prévia.'); }
    };

    const schedulePreview = debounce(runPreview, 350);

    const setMargin = (value) => {
        state.selectedMargin = String(value);
        root.querySelectorAll('input[name="profit_margin_percent"]').forEach((i) => { i.checked = i.value === state.selectedMargin; });
        schedulePreview();
        renderCompare();
    };

    const bindMaterialRow = (row, index, container) => {
        container.className = 'grid grid-cols-2 md:grid-cols-5 gap-2 mb-2';
        container.innerHTML = `<input name="materials[${index}][material_name]" class="ui-input md:col-span-2" data-f="material_name" placeholder="Material"><input name="materials[${index}][quantity]" type="number" step="0.0001" class="ui-input" data-f="quantity" placeholder="Qtd"><input name="materials[${index}][unit]" class="ui-input" data-f="unit" placeholder="Un"><input name="materials[${index}][unit_cost]" type="number" step="0.0001" class="ui-input" data-f="unit_cost" placeholder="R$/un">`;
        container.querySelectorAll('[data-f]').forEach((input) => {
            const f = input.dataset.f;
            input.value = row[f] ?? '';
            input.addEventListener('input', () => { row[f] = f === 'quantity' || f === 'unit_cost' ? Number(input.value) || 0 : input.value; schedulePreview(); });
        });
    };

    const bindNameAmountRow = (row, index, prefix, container) => {
        container.className = 'grid grid-cols-3 gap-2 mb-2';
        container.innerHTML = `<input name="${prefix}[${index}][name]" class="ui-input col-span-2" data-f="name" placeholder="Nome"><input name="${prefix}[${index}][amount]" type="number" step="0.01" class="ui-input" data-f="amount" placeholder="R$">`;
        container.querySelectorAll('[data-f]').forEach((input) => {
            const f = input.dataset.f;
            input.value = row[f] ?? '';
            input.addEventListener('input', () => { row[f] = f === 'amount' ? Number(input.value) || 0 : input.value; schedulePreview(); });
        });
    };

    const renderList = (list, rows, prefix, bind) => {
        if (!list) return;
        list.innerHTML = '';
        rows.forEach((row, i) => { const w = document.createElement('div'); bind(row, i, prefix, w); list.appendChild(w); });
    };

    const renderMaterials = () => renderList(materialsList, state.materials, null, (row, i, _, w) => bindMaterialRow(row, i, w));
    const renderVariableCosts = () => renderList(variableList, state.variableCosts, 'variable_costs', bindNameAmountRow);
    const renderAdditionalCosts = () => renderList(additionalList, state.additionalCosts, 'additional_costs', bindNameAmountRow);

    root.querySelector('[data-pricing-add-material]')?.addEventListener('click', () => { state.materials.push({ material_name: '', quantity: 0, unit: 'g', unit_cost: 0 }); renderMaterials(); schedulePreview(); });
    root.querySelector('[data-pricing-add-variable]')?.addEventListener('click', () => { state.variableCosts.push({ name: '', amount: 0 }); renderVariableCosts(); });
    root.querySelector('[data-pricing-add-additional]')?.addEventListener('click', () => { state.additionalCosts.push({ name: '', amount: 0 }); renderAdditionalCosts(); });
    root.querySelectorAll('input[name="profit_margin_percent"]').forEach((i) => i.addEventListener('change', () => setMargin(i.value)));
    hourlyRateInput?.addEventListener('input', () => { state.hourlyRate = Number(hourlyRateInput.value) || 0; schedulePreview(); });
    hoursSpentInput?.addEventListener('input', () => { state.hoursSpent = Number(hoursSpentInput.value) || 0; schedulePreview(); });

    root.querySelector('[data-pricing-compare-btn]')?.addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        if (!config.compareUrl) return;
        btn.disabled = true;
        try {
            const res = await fetch(config.compareUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf, Accept: 'application/json' }, body: JSON.stringify({ ...buildPayload(), margins: config.margins || [] }) });
            state.compareScenarios = (await res.json()).scenarios || [];
            renderCompare();
        } catch (_) { window.toast?.error(config.labels?.compareError || 'Não foi possível comparar margens.'); }
        finally { btn.disabled = false; }
    });

    root.querySelector('[data-pricing-ai]')?.addEventListener('click', async () => {
        if (!config.aiUrl) return;
        aiTextEl?.classList.add('hidden');
        aiLoadingEl?.classList.remove('hidden');
        const steps = [config.labels?.aiStep1, config.labels?.aiStep2, config.labels?.aiStep3].filter(Boolean);
        let idx = 0;
        const timer = window.setInterval(() => { idx = (idx + 1) % steps.length; if (aiStepEl) aiStepEl.textContent = steps[idx]; }, 1200);
        try {
            const res = await fetch(config.aiUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf, Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify(buildPayload()) });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || config.labels?.aiError);
            if (data.breakdown) { state.breakdown = data.breakdown; refreshWizardStep(); renderBreakdown(); }
            if (aiTextEl) { aiTextEl.textContent = data.suggestion || config.labels?.aiEmpty || ''; aiTextEl.classList.remove('hidden'); }
        } catch (err) {
            const msg = err.message || config.labels?.aiError || 'Erro ao consultar a IA.';
            if (aiTextEl) { aiTextEl.textContent = msg; aiTextEl.classList.remove('hidden'); }
            window.toast?.error(msg);
        } finally {
            window.clearInterval(timer);
            aiLoadingEl?.classList.add('hidden');
        }
    });

    ensureRows();
    renderMaterials();
    renderVariableCosts();
    renderAdditionalCosts();
    if (hourlyRateInput) hourlyRateInput.value = String(state.hourlyRate);
    if (hoursSpentInput) hoursSpentInput.value = String(state.hoursSpent);
    const checked = root.querySelector('input[name="profit_margin_percent"]:checked');
    if (checked) state.selectedMargin = checked.value;
    refreshWizardStep();
    runPreview();
}
