@extends('layouts.landing')
@section('title', __('docs.api.title'))
@section('meta_description', __('docs.api.meta'))

@section('content')
<x-landing.header />
<article class="max-w-3xl mx-auto pt-[6.5rem] pb-20 px-4">
    <div class="landing-card prose prose-slate max-w-none">
        <h1 class="ui-page-title !mb-2">{{ __('docs.api.title') }}</h1>
        <p class="ui-page-subtitle">{{ __('docs.api.subtitle') }}</p>
        <p class="mt-6">{{ __('docs.api.base_url') }} <code class="text-sm bg-slate-100 px-2 py-1 rounded">{{ url('/api/v1') }}</code></p>

        <h2 class="font-display font-semibold text-lg mt-8 mb-3">{{ __('docs.api.auth_title') }}</h2>
        <pre class="text-sm bg-slate-900 text-slate-100 p-4 rounded-xl overflow-x-auto"><code>POST /api/v1/auth/token
{
  "email": "you@example.com",
  "password": "...",
  "device_name": "my-integration",
  "abilities": ["dashboard:read", "products:read", "sales:write"]
}</code></pre>

        <h2 class="font-display font-semibold text-lg mt-8 mb-3">{{ __('docs.api.endpoints_title') }}</h2>
        <ul class="space-y-2 text-sm text-slate-600">
            <li><code>GET /dashboard/summary</code> — dashboard:read</li>
            <li><code>GET /products</code> — products:read</li>
            <li><code>PATCH /products/{id}/stock</code> — products:write</li>
            <li><code>GET /sales</code> — sales:read</li>
            <li><code>POST /sales</code> — sales:write</li>
        </ul>

        <p class="mt-8 text-sm">{{ __('docs.api.manage_tokens') }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('tenant.login') }}" class="landing-btn-brand">{{ __('docs.api.login_cta') }}</a>
            <a href="{{ asset('openapi.yaml') }}" class="landing-btn-ghost !text-ink !border-slate-200">{{ __('docs.api.openapi') }}</a>
        </div>
    </div>
</article>
<x-landing.footer />
@endsection
