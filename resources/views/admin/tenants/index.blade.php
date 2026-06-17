<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header :title="__('admin.tenants.title')" :subtitle="__('admin.tenants_page.subtitle')" />
    </x-slot>
    <div class="mb-6 flex flex-wrap gap-3 items-center justify-between">
        <x-ui.button variant="secondary" :href="route('admin.tenants.create')">{{ __('admin.tenants_page.create') }}</x-ui.button>
        <x-ui.button variant="outline" :href="route('admin.tenants.export')">{{ __('admin.tenants_page.export_csv') }}</x-ui.button>
    </div>

        <form method="GET" class="ui-card-premium p-4 mb-6 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="ui-label text-xs">{{ __('admin.tenants_page.search') }}</label>
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('admin.tenants_page.search_placeholder') }}" class="ui-input py-2">
            </div>
            <div class="w-36">
                <label class="ui-label text-xs">{{ __('admin.tenants_page.plan') }}</label>
                <select name="plan" class="ui-input py-2">
                    <option value="">{{ __('admin.tenants_page.all') }}</option>
                    <option value="basic" @selected(($filters['plan'] ?? '') === 'basic')>Basic</option>
                    <option value="premium" @selected(($filters['plan'] ?? '') === 'premium')>Premium</option>
                </select>
            </div>
            <div class="w-36">
                <label class="ui-label text-xs">{{ __('admin.tenants_page.status') }}</label>
                <select name="status" class="ui-input py-2">
                    <option value="">{{ __('admin.tenants_page.all') }}</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>{{ __('admin.tenants_page.active') }}</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>{{ __('admin.tenants_page.inactive') }}</option>
                    <option value="trial" @selected(($filters['status'] ?? '') === 'trial')>{{ __('admin.tenants_page.trial') }}</option>
                </select>
            </div>
            <x-ui.button type="submit" variant="secondary">{{ __('admin.tenants_page.filter') }}</x-ui.button>
            <a href="{{ route('admin.tenants.index') }}" class="text-sm text-slate-500 hover:text-brand">{{ __('admin.tenants_page.clear') }}</a>
        </form>

        <x-ui.card class="overflow-x-auto p-0 shadow-sm">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.tenants_page.name') }}</th>
                        <th>{{ __('admin.tenants_page.email') }}</th>
                        <th>{{ __('admin.tenants_page.plan') }}</th>
                        <th>{{ __('admin.tenants_page.trial_until') }}</th>
                        <th>{{ __('admin.tenants_page.active_col') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tenants as $tenant)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="font-medium text-ink">
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="hover:text-brand hover:underline">{{ $tenant->name }}</a>
                    </td>
                    <td>{{ $tenant->email }}</td>
                    <td>
                        <span class="ui-badge-brand">{{ $tenant->plan?->value ?? $tenant->plan }}</span>
                    </td>
                    <td class="text-slate-500">
                        @if($tenant->onTrial())
                        até {{ $tenant->trial_ends_at->format('d/m/Y') }}
                        @else
                        —
                        @endif
                    </td>
                    <td>{{ $tenant->is_active ? __('admin.tenants_page.yes') : __('admin.tenants_page.no') }}</td>
                    <td>
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-brand text-xs font-semibold hover:underline mr-3">{{ __('admin.tenants_page.details') }}</a>
                        <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}" class="inline">@csrf @method('PATCH')
                            <button class="text-slate-600 text-xs font-semibold hover:underline">{{ $tenant->is_active ? __('admin.tenants_page.deactivate') : __('admin.tenants_page.activate') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-0"><x-ui.empty-state icon="products" :title="__('admin.tenants_page.empty')" class="border-0 shadow-none" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </x-ui.card>
        <div class="mt-6">{{ $tenants->links() }}</div>
</x-app-layout>
