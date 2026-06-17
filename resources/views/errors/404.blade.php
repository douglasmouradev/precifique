@extends('layouts.error')
@section('title', __('errors.404.title'))
@section('content')
<p class="text-5xl font-display font-bold text-brand mb-2">404</p>
<h1 class="ui-page-title text-xl">{{ __('errors.404.heading') }}</h1>
<p class="ui-page-subtitle mt-2">{{ __('errors.404.message') }}</p>
<div class="flex flex-wrap justify-center gap-3 mt-8">
    <a href="{{ url('/') }}" class="ui-btn ui-btn-outline px-5 py-2.5">{{ __('errors.404.go_home') }}</a>
    <a href="{{ url('/entrar') }}" class="ui-btn ui-btn-secondary px-5 py-2.5">{{ __('errors.404.sign_in') }}</a>
</div>
@endsection
