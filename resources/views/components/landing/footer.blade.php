<footer class="landing-footer">
    <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
        <div class="space-y-3">
            <x-ui.logo variant="full" size="md" dark />
            <p class="text-sm text-slate-500 max-w-xs leading-relaxed">{{ __('landing.footer_tagline') }}</p>
        </div>
        <nav class="flex flex-wrap gap-x-8 gap-y-3 text-sm" aria-label="{{ __('landing.footer_nav') }}">
            <a href="{{ route('privacy') }}" class="hover:text-brand transition-colors">{{ __('landing.footer_privacy') }}</a>
            <a href="{{ route('terms') }}" class="hover:text-brand transition-colors">{{ __('landing.footer_terms') }}</a>
            <a href="{{ route('docs.api') }}" class="hover:text-brand transition-colors">{{ __('landing.footer_api') }}</a>
            <a href="{{ route('tenant.login') }}" class="hover:text-brand transition-colors">{{ __('landing.login') }}</a>
        </nav>
        <p class="text-sm text-slate-500">© {{ date('Y') }} Precifique. {{ __('landing.footer_rights') }}</p>
    </div>
</footer>
