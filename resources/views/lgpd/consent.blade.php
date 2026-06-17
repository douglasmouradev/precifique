@extends('layouts.auth')
@section('title', __('lgpd.consent.title'))
@section('content')
<div class="text-center mb-6">
    <div class="w-14 h-14 rounded-2xl bg-brand/10 text-brand flex items-center justify-center mx-auto mb-4">
        <x-ui.nav-icon name="spark" class="w-7 h-7" />
    </div>
    <h1 class="font-display text-xl font-semibold mb-2">{{ __('lgpd.consent.heading') }}</h1>
    <p class="text-sm text-slate-500">{{ __('lgpd.consent.subtitle') }}</p>
</div>

<form method="POST" action="{{ route('lgpd.consent.store') }}" class="space-y-4">
    @csrf
    <label class="flex gap-3 text-sm text-slate-700 items-start">
        <input type="checkbox" name="terms" value="1" required class="mt-0.5 rounded border-slate-300 text-brand focus:ring-brand/30">
        <span>{!! __('lgpd.consent.terms', ['link' => '<a href="'.route('terms').'" target="_blank" class="text-brand font-medium hover:underline">'.__('lgpd.consent.terms_link').'</a>']) !!}</span>
    </label>
    <label class="flex gap-3 text-sm text-slate-700 items-start">
        <input type="checkbox" name="privacy" value="1" required class="mt-0.5 rounded border-slate-300 text-brand focus:ring-brand/30">
        <span>{!! __('lgpd.consent.privacy', ['link' => '<a href="'.route('privacy').'" target="_blank" class="text-brand font-medium hover:underline">'.__('lgpd.consent.privacy_link').'</a>']) !!}</span>
    </label>
    <label class="flex gap-3 text-sm text-slate-500 items-start">
        <input type="checkbox" name="marketing" value="1" class="mt-0.5 rounded border-slate-300 text-brand focus:ring-brand/30">
        <span>{{ __('lgpd.consent.marketing') }}</span>
    </label>
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">{{ __('lgpd.consent.submit') }}</x-ui.button>
</form>
@endsection
