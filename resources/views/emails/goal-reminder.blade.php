<x-mail::message>
# {{ __('mail.goal_reminder.title', ['name' => $tenant->name]) }}

{{ __('mail.goal_reminder.progress', ['progress' => number_format($progress, 0), 'goal' => number_format($goal->goal_amount, 2, ',', '.')]) }}

{{ __('mail.goal_reminder.revenue', ['revenue' => number_format($revenue, 2, ',', '.')]) }}

<x-mail::button :url="route('tenant.dashboard')">
{{ __('mail.goal_reminder.button') }}
</x-mail::button>
</x-mail::message>
