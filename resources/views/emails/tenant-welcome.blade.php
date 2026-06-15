<x-mail::message>
# {{ __('mail.tenant_welcome.heading', ['name' => $tenant->name]) }}

{{ __('mail.tenant_welcome.body_created') }}

**{{ __('mail.tenant_welcome.email_label') }}** {{ $tenant->email }}

{{ __('mail.tenant_welcome.password_instructions') }}

<x-mail::button :url="$resetUrl">
{{ __('mail.tenant_welcome.button') }}
</x-mail::button>

{{ __('mail.tenant_welcome.ignore') }}
</x-mail::message>
