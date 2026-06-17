<x-mail::message>
# {{ __('mail.trial_engagement.heading', ['name' => $tenant->name, 'day' => $day]) }}

@if($day === 3)
{{ __('mail.trial_engagement.day_3_body') }}
@else
{{ __('mail.trial_engagement.day_7_body', ['date' => $tenant->trial_ends_at?->format('d/m/Y')]) }}
@endif

<x-mail::button :url="$day === 3 ? route('tenant.products.create') : route('tenant.billing.upgrade')">
{{ $day === 3 ? __('mail.trial_engagement.button_product') : __('mail.trial_engagement.button_upgrade') }}
</x-mail::button>

{{ __('mail.thanks') }}<br>
{{ config('app.name') }}
</x-mail::message>
