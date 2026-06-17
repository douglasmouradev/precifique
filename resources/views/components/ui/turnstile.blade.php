@props(['siteKey' => config('security.turnstile.site_key')])

@if($siteKey)
<div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="light"></div>
@push('head')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush
@endif
