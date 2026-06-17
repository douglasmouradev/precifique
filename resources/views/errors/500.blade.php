@extends('layouts.error')
@section('title', __('errors.500.title'))
@section('content')
<p class="text-5xl font-display font-bold text-slate-300 mb-2">500</p>
<h1 class="ui-page-title text-xl">{{ __('errors.500.heading') }}</h1>
<p class="ui-page-subtitle mt-2">{{ __('errors.500.message') }}</p>
<a href="{{ url('/') }}" class="ui-btn ui-btn-secondary inline-flex mt-8 px-6 py-2.5">{{ __('errors.500.go_home') }}</a>
@endsection
