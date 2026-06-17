@extends('layouts.tenant')
@section('title', __('app.account.title'))
@section('breadcrumb') {{ __('app.account.title') }} @endsection

@section('content')
<x-ui.page-header :title="__('app.account.title')" :subtitle="__('app.account.subtitle')" />

@if(!$tenant->hasVerifiedEmail())
<x-ui.alert variant="warning" class="mb-6 max-w-5xl">
    {{ __('app.account.email_unverified') }}
    <form method="POST" action="{{ route('tenant.verification.send') }}" class="inline ml-2">
        @csrf
        <button type="submit" class="underline font-medium">{{ __('app.account.resend_verification') }}</button>
    </form>
</x-ui.alert>
@endif

@if(session('api_token_plain'))
<x-ui.alert variant="success" class="mb-6 max-w-5xl">
    <p class="font-medium mb-2">{{ __('app.account.token_created') }}</p>
    <code class="block break-all text-sm bg-white/60 p-2 rounded">{{ session('api_token_plain') }}</code>
</x-ui.alert>
@endif

<div class="grid lg:grid-cols-2 gap-6 max-w-5xl">
    <x-ui.card>
        <h2 class="ui-section-title">{{ __('app.account.profile') }}</h2>
        <form method="POST" action="{{ route('tenant.account.profile') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="name" class="ui-label">{{ __('app.account.name') }}</label>
                <input id="name" name="name" value="{{ old('name', $tenant->name) }}" required class="ui-input">
            </div>
            <div>
                <label for="email" class="ui-label">{{ __('app.account.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email', $tenant->email) }}" required class="ui-input">
            </div>
            <div>
                <label for="niche" class="ui-label">{{ __('app.account.niche') }}</label>
                <select id="niche" name="niche" class="ui-input">
                    @foreach(__('app.niches') as $val => $label)
                    <option value="{{ $val }}" @selected(old('niche', $tenant->niche?->value ?? (string) $tenant->niche) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <x-ui.button type="submit">{{ __('app.account.save_profile') }}</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.card>
        <h2 class="ui-section-title">{{ __('app.account.password') }}</h2>
        <form method="POST" action="{{ route('tenant.account.password') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="current_password" class="ui-label">{{ __('app.account.current_password') }}</label>
                <input id="current_password" type="password" name="current_password" required class="ui-input" autocomplete="current-password">
                @error('current_password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="ui-label">{{ __('app.account.new_password') }}</label>
                <input id="password" type="password" name="password" required class="ui-input" autocomplete="new-password">
            </div>
            <div>
                <label for="password_confirmation" class="ui-label">{{ __('app.account.confirm_password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="ui-input" autocomplete="new-password">
            </div>
            <x-ui.button type="submit" variant="outline">{{ __('app.account.update_password') }}</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.card class="lg:col-span-2">
        <h2 class="ui-section-title">{{ __('app.account.plan') }}</h2>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            @if($tenant->isPremium())
            <span class="ui-badge-premium">{{ __('app.account.premium_active') }}</span>
            @elseif($tenant->onTrial())
            <span class="ui-badge-brand">{{ __('app.account.trial_until', ['date' => $tenant->trial_ends_at->format('d/m/Y')]) }}</span>
            @else
            <span class="text-sm text-slate-600">{{ __('app.account.plan_basic') }}</span>
            @endif
        </div>
        @if($subscription?->ends_at)
        <p class="text-sm text-slate-600 mb-4">{{ __('app.account.valid_until', ['date' => $subscription->ends_at->format('d/m/Y')]) }}</p>
        @endif
        <div class="flex flex-wrap gap-3">
            @if($tenant->isPremium() && $subscription?->stripe_subscription_id)
            <x-ui.button :href="route('tenant.billing.portal')">{{ __('app.account.manage_subscription') }}</x-ui.button>
            @elseif(!$tenant->isPremium())
            <x-ui.button :href="route('tenant.billing.upgrade')">{{ __('app.account.upgrade') }}</x-ui.button>
            @endif
            <x-ui.button variant="outline" :href="route('tenant.lgpd.portal')">{{ __('app.account.lgpd') }}</x-ui.button>
        </div>
    </x-ui.card>

    <x-ui.card class="lg:col-span-2">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="ui-section-title mb-1">{{ __('app.account.api_tokens') }}</h2>
                <p class="text-sm text-slate-500">{{ __('app.account.api_tokens_desc') }}</p>
            </div>
            <x-ui.button variant="outline" :href="route('docs.api')" target="_blank">{{ __('app.account.api_docs') }}</x-ui.button>
        </div>

        <form method="POST" action="{{ route('tenant.account.tokens.store') }}" class="space-y-4 mb-6 p-4 bg-slate-50 rounded-xl">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="ui-label">{{ __('app.account.token_name') }}</label>
                    <input name="name" required class="ui-input" placeholder="ERP, n8n, planilha...">
                </div>
                <div>
                    <label class="ui-label">{{ __('app.account.token_abilities') }}</label>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($apiAbilities as $ability => $label)
                        <label class="inline-flex items-center gap-1 text-xs bg-white border rounded px-2 py-1">
                            <input type="checkbox" name="abilities[]" value="{{ $ability }}" @checked(in_array($ability, ['dashboard:read','products:read','sales:read','sales:write'], true))>
                            {{ $ability }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <x-ui.button type="submit">{{ __('app.account.create_token') }}</x-ui.button>
        </form>

        @if($apiTokens->isEmpty())
        <p class="text-sm text-slate-500">{{ __('app.account.no_tokens') }}</p>
        @else
        <ul class="divide-y divide-slate-100">
            @foreach($apiTokens as $token)
            <li class="py-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="font-medium text-sm">{{ $token->name }}</p>
                    <p class="text-xs text-slate-400">{{ implode(', ', $token->abilities ?? []) }}</p>
                </div>
                <form method="POST" action="{{ route('tenant.account.tokens.destroy', $token) }}">
                    @csrf @method('DELETE')
                    <x-ui.button type="submit" variant="outline" size="sm">{{ __('app.account.revoke') }}</x-ui.button>
                </form>
            </li>
            @endforeach
        </ul>
        @endif
    </x-ui.card>

    <x-ui.card>
        <h2 class="ui-section-title">{{ __('app.account.notifications') }}</h2>
        <form method="POST" action="{{ route('tenant.account.notifications') }}" class="space-y-3">
            @csrf @method('PUT')
            @foreach([
                'email_low_stock' => 'notify_email_low_stock',
                'email_trial' => 'notify_email_trial',
                'email_payment_failed' => 'notify_email_payment',
                'in_app' => 'notify_in_app',
            ] as $key => $labelKey)
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="{{ $key }}" value="0">
                <input type="checkbox" name="{{ $key }}" value="1" @checked($notificationPrefs[$key] ?? true)>
                {{ __('app.account.'.$labelKey) }}
            </label>
            @endforeach
            <x-ui.button type="submit" variant="outline">{{ __('app.actions.save') }}</x-ui.button>
        </form>
    </x-ui.card>

    @unless($tenant->isDemoProfile())
    <x-ui.card>
        <h2 class="ui-section-title">{{ __('app.account.two_factor') }}</h2>
        <p class="text-sm text-slate-500 mb-4">{{ __('app.account.two_factor_desc') }}</p>
        @if($tenant->hasTwoFactorEnabled())
        <p class="text-sm text-brand-dark font-medium mb-3">2FA {{ __('app.account.premium_active') }}</p>
        <x-ui.button variant="outline" :href="route('tenant.account.two-factor')">{{ __('app.account.disable_2fa') }}</x-ui.button>
        @else
        <x-ui.button :href="route('tenant.account.two-factor')">{{ __('app.account.enable_2fa') }}</x-ui.button>
        @endif
    </x-ui.card>
    @endunless

    @if($isOwner)
    <x-ui.card class="lg:col-span-2">
        <h2 class="ui-section-title">{{ __('members.title') }}</h2>
        <p class="text-sm text-slate-500 mb-4">{{ __('members.subtitle') }}</p>
        <form method="POST" action="{{ route('tenant.account.members.store') }}" class="grid sm:grid-cols-2 gap-4 mb-6">
            @csrf
            <div><label class="ui-label">{{ __('members.name') }}</label><input name="name" required class="ui-input"></div>
            <div><label class="ui-label">{{ __('members.email') }}</label><input type="email" name="email" required class="ui-input"></div>
            <div><label class="ui-label">{{ __('members.password') }}</label><input type="password" name="password" required minlength="8" class="ui-input"></div>
            <div><label class="ui-label">{{ __('members.role') }}</label>
                <select name="role" class="ui-input">
                    @foreach(__('members.roles') as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2"><x-ui.button type="submit">{{ __('members.invite') }}</x-ui.button></div>
        </form>
        @if($members->isEmpty())
        <p class="text-sm text-slate-500">{{ __('members.no_members') }}</p>
        @else
        <ul class="divide-y divide-slate-100">
            @foreach($members as $member)
            <li class="py-2 flex justify-between items-center text-sm">
                <span>{{ $member->name }} &lt;{{ $member->email }}&gt; — {{ __('members.roles.'.$member->role) }}</span>
                <form method="POST" action="{{ route('tenant.account.members.destroy', $member) }}">@csrf @method('DELETE')
                    <button type="submit" class="text-red-600 text-xs">{{ __('members.remove') }}</button>
                </form>
            </li>
            @endforeach
        </ul>
        @endif
    </x-ui.card>

    <x-ui.card class="lg:col-span-2">
        <h2 class="ui-section-title">{{ __('members.webhooks_title') }}</h2>
        <p class="text-sm text-slate-500 mb-4">{{ __('members.webhooks_desc') }}</p>
        <form method="POST" action="{{ route('tenant.account.webhooks.store') }}" class="grid sm:grid-cols-2 gap-4 mb-4">
            @csrf
            <div class="sm:col-span-2"><label class="ui-label">{{ __('members.webhook_url') }}</label><input type="url" name="url" required class="ui-input" placeholder="https://"></div>
            <div class="sm:col-span-2"><label class="ui-label">{{ __('members.webhook_secret') }}</label><input name="secret" class="ui-input"></div>
            <div><x-ui.button type="submit">{{ __('members.add_webhook') }}</x-ui.button></div>
        </form>
        @foreach($webhooks as $hook)
        <div class="flex justify-between items-center py-2 border-t border-slate-100 text-sm">
            <code class="text-xs break-all">{{ $hook->url }}</code>
            <form method="POST" action="{{ route('tenant.account.webhooks.destroy', $hook) }}">@csrf @method('DELETE')
                <button type="submit" class="text-red-600 text-xs">{{ __('members.remove') }}</button>
            </form>
        </div>
        @endforeach
    </x-ui.card>
    @endif
</div>
@endsection
