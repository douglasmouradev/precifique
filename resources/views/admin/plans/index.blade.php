<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header :title="__('admin.plans_page.title')" :subtitle="__('admin.plans_page.subtitle')" />
    </x-slot>
    <div class="space-y-6 max-w-3xl">
        @forelse($plans as $plan)
        <x-ui.card class="p-6">
            <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-4">
                @csrf @method('PATCH')
                <div class="flex items-center justify-between gap-4">
                    <h3 class="font-display font-bold text-lg capitalize">{{ $plan->slug }}</h3>
                    <span class="ui-badge-brand">{{ $plan->is_active ? __('admin.plans_page.active') : __('admin.plans_page.inactive') }}</span>
                </div>
                <x-ui.input :label="__('admin.plans_page.display_name')" name="name" value="{{ $plan->name }}" />
                <x-ui.input :label="__('admin.plans_page.price_monthly')" name="price_monthly" type="number" step="0.01" value="{{ $plan->price_monthly }}" />
                <x-ui.input :label="__('admin.plans_page.stripe_price_id')" name="stripe_price_id" value="{{ $plan->stripe_price_id }}" placeholder="price_..." />
                <x-ui.input :label="__('admin.plans_page.max_products')" name="max_products" type="number" value="{{ $plan->max_products }}" />
                <label class="flex gap-2 text-sm text-slate-700 items-center">
                    <input type="checkbox" name="has_ai" value="1" @checked($plan->has_ai) class="rounded border-slate-300 text-brand focus:ring-brand/30">
                    {{ __('admin.plans_page.has_ai') }}
                </label>
                <label class="flex gap-2 text-sm text-slate-700 items-center">
                    <input type="checkbox" name="is_active" value="1" @checked($plan->is_active) class="rounded border-slate-300 text-brand focus:ring-brand/30">
                    {{ __('admin.plans_page.is_active') }}
                </label>
                <x-ui.button variant="secondary" type="submit">{{ __('admin.plans_page.save') }}</x-ui.button>
            </form>
        </x-ui.card>
        @empty
        <x-ui.card class="p-6 text-center space-y-3">
            <x-ui.empty-state icon="goals" :title="__('admin.plans_page.empty')" class="border-0 shadow-none" />
            <p class="text-sm text-slate-500">{{ __('admin.plans_page.empty_hint') }}</p>
            <code class="inline-block text-sm bg-slate-100 px-3 py-2 rounded-lg">php artisan precifique:ensure-plans</code>
        </x-ui.card>
        @endforelse
    </div>
</x-app-layout>
