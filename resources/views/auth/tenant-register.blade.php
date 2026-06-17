@extends('layouts.auth')
@section('title', __('auth.register.title'))
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">{{ __('auth.register.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.register.subtitle') }}</p>

<form method="POST" action="{{ route('tenant.register.store') }}" class="space-y-4">
    @csrf
    <div class="hidden" aria-hidden="true">
        <label for="company_website">{{ __('auth.register.business_name') }}</label>
        <input type="text" name="company_website" id="company_website" tabindex="-1" autocomplete="off" value="">
    </div>
    <x-ui.input :label="__('auth.register.business_name')" name="name" value="{{ old('name') }}" required />
    <x-ui.input :label="__('auth.register.email')" name="email" type="email" value="{{ old('email') }}" required />
    <x-ui.input :label="__('auth.register.password')" name="password" type="password" required />
    <x-ui.input :label="__('auth.register.password_confirmation')" name="password_confirmation" type="password" required />
    <x-ui.select :label="__('auth.register.niche')" name="niche">
        <option value="alimentos" @selected(old('niche') === 'alimentos')>{{ __('app.niches.alimentos') }}</option>
        <option value="servico" @selected(old('niche') === 'servico')>{{ __('app.niches.servico') }}</option>
        <option value="artesanato" @selected(old('niche') === 'artesanato')>{{ __('app.niches.artesanato') }}</option>
        <option value="outro" @selected(old('niche') === 'outro')>{{ __('app.niches.outro') }}</option>
    </x-ui.select>
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">{{ __('auth.register.submit') }}</x-ui.button>
</form>
@endsection
@section('footer')
<p>{{ __('auth.register.has_account') }} <a href="{{ route('tenant.login') }}" class="text-brand font-semibold hover:underline">{{ __('auth.register.sign_in') }}</a></p>
@endsection
