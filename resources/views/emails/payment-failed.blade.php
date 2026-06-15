<x-mail::message>
# {{ __('mail.payment_failed.title') }}

{{ __('mail.payment_failed.greeting', ['name' => $tenant->name]) }}

{{ __('mail.payment_failed.body') }}

<x-mail::button :url="route('tenant.billing.portal')">
{{ __('mail.payment_failed.button') }}
</x-mail::button>

{{ __('mail.payment_failed.thanks') }}<br>
{{ config('app.name') }}
</x-mail::message>
