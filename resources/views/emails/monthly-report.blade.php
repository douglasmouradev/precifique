<x-mail::message>
# {{ __('mail.monthly_report.title') }}

{{ __('mail.monthly_report.greeting', ['name' => $tenant->name]) }}

{{ __('mail.monthly_report.body') }}

<x-mail::button :url="route('tenant.dashboard')">
{{ __('mail.monthly_report.button') }}
</x-mail::button>
</x-mail::message>
