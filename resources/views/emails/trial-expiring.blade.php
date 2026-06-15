<x-mail::message>
# {{ __('mail.trial_expiring.heading', ['name' => $tenant->name]) }}

{{ __('mail.trial_expiring.body', ['date' => $tenant->trial_ends_at?->format('d/m/Y')]) }}

{{ __('mail.trial_expiring.cta_intro') }}

<x-mail::button :url="route('tenant.billing.upgrade')">
{{ __('mail.trial_expiring.button') }}
</x-mail::button>

{{ __('mail.thanks') }}<br>
{{ config('app.name') }}
</x-mail::message>
