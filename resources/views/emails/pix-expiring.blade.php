<x-mail::message>
# {{ __('mail.pix_expiring.title') }}

{{ __('mail.pix_expiring.greeting', ['name' => $tenant->name]) }}

{{ __('mail.pix_expiring.body', ['date' => $subscription->ends_at?->format('d/m/Y')]) }}

<x-mail::button :url="route('tenant.billing.upgrade')">
{{ __('mail.pix_expiring.button') }}
</x-mail::button>

{{ __('mail.pix_expiring.thanks') }}<br>
{{ config('app.name') }}
</x-mail::message>
