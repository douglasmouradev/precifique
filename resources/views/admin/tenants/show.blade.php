<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header :title="$tenant->name" :subtitle="__('admin.tenant_show.subtitle')" />
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <x-ui.button variant="ghost" :href="route('admin.tenants.index')">{{ __('admin.tenants_page.back') }}</x-ui.button>

        <x-ui.card class="p-6 space-y-4">
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-500">{{ __('admin.tenant_show.email') }}</span><p class="font-medium">{{ $tenant->email }}</p></div>
                <div><span class="text-slate-500">{{ __('admin.tenant_show.plan') }}</span><p class="font-medium capitalize">{{ $tenant->plan?->value ?? $tenant->plan }}</p></div>
                <div><span class="text-slate-500">{{ __('admin.tenant_show.niche') }}</span><p class="font-medium capitalize">{{ $tenant->niche?->value ?? $tenant->niche ?? '—' }}</p></div>
                <div><span class="text-slate-500">{{ __('admin.tenant_show.status') }}</span><p class="font-medium">{{ $tenant->is_active ? __('admin.tenant_show.active') : __('admin.tenant_show.inactive') }}</p></div>
                <div><span class="text-slate-500">{{ __('admin.tenant_show.trial_until') }}</span><p class="font-medium">{{ $tenant->trial_ends_at?->format('d/m/Y H:i') ?? '—' }}</p></div>
                <div><span class="text-slate-500">{{ __('admin.tenant_show.registered_at') }}</span><p class="font-medium">{{ $tenant->created_at?->format('d/m/Y') }}</p></div>
            </div>
        </x-ui.card>

        @if($tenant->subscription)
        <x-ui.card class="p-6">
            <h3 class="font-display font-bold text-lg mb-3">{{ __('admin.tenant_show.subscription') }}</h3>
            <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                <div><dt class="text-slate-500">{{ __('admin.tenant_show.plan') }}</dt><dd class="font-medium">{{ $tenant->subscription->plan?->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">{{ __('admin.tenant_show.status') }}</dt><dd class="font-medium capitalize">{{ $tenant->subscription->status }}</dd></div>
                <div><dt class="text-slate-500">{{ __('admin.tenant_show.starts_at') }}</dt><dd class="font-medium">{{ $tenant->subscription->starts_at?->format('d/m/Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">{{ __('admin.tenant_show.expires_at') }}</dt><dd class="font-medium">{{ $tenant->subscription->ends_at?->format('d/m/Y') ?? __('admin.tenant_show.recurring') }}</dd></div>
            </dl>
        </x-ui.card>
        @endif

        <x-ui.card class="p-6">
            <h3 class="ui-section-title">{{ __('admin.tenant_show.activity_timeline') }}</h3>
            <ul class="space-y-3 mt-4">
                @forelse($tenant->auditLogs as $log)
                <li class="flex gap-3 text-sm border-l-2 border-brand/30 pl-4 py-1">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-ink"><code class="text-xs">{{ $log->action }}</code></p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $log->created_at->format('d/m/Y H:i') }} · {{ $log->ip_address }}</p>
                    </div>
                </li>
                @empty
                <li class="text-sm text-slate-500">{{ __('admin.tenant_show.no_activity') }}</li>
                @endforelse
            </ul>
        </x-ui.card>

        <x-ui.card class="p-6">
            <h3 class="ui-section-title">{{ __('admin.tenant_show.lgpd_recent') }}</h3>
            @forelse($tenant->lgpdConsents as $consent)
            <p class="text-sm text-slate-600 mt-2">{{ $consent->consent_type }} — {{ $consent->consented_at?->format('d/m/Y H:i') }} (v{{ $consent->version }})</p>
            @empty
            <p class="text-sm text-slate-500 mt-2">{{ __('admin.tenant_show.no_consent') }}</p>
            @endforelse
        </x-ui.card>

        <x-ui.card class="p-6 space-y-4">
            <h3 class="ui-section-title">{{ __('admin.tenant_show.support_actions') }}</h3>
            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.tenants.impersonate', $tenant) }}" class="flex flex-wrap items-end gap-3">@csrf
                    <div class="w-48">
                        <label class="ui-label text-xs">{{ __('admin.tenant_show.admin_password') }}</label>
                        <input type="password" name="password" required class="ui-input py-2" autocomplete="current-password">
                        @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <x-ui.button type="submit" variant="secondary">{{ __('admin.tenants.impersonate') }}</x-ui.button>
                </form>
                <form method="POST" action="{{ route('admin.tenants.resend-welcome', $tenant) }}">@csrf
                    <x-ui.button type="submit" variant="outline">{{ __('admin.tenants.resend_welcome') }}</x-ui.button>
                </form>
            </div>
            <form method="POST" action="{{ route('admin.tenants.extend-trial', $tenant) }}" class="flex flex-wrap items-end gap-3 pt-2 border-t border-slate-100">
                @csrf @method('PATCH')
                <div class="w-28">
                    <label class="ui-label text-xs">{{ __('admin.tenant_show.extend_trial') }}</label>
                    <input type="number" name="days" value="7" min="1" max="90" class="ui-input py-2">
                </div>
                <x-ui.button type="submit" variant="outline">{{ __('admin.tenant_show.add_days') }}</x-ui.button>
            </form>
        </x-ui.card>

        <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}">
            @csrf @method('PATCH')
            <x-ui.button variant="secondary" type="submit">{{ $tenant->is_active ? __('admin.tenant_show.deactivate') : __('admin.tenant_show.reactivate') }}</x-ui.button>
        </form>
    </div>
</x-app-layout>
