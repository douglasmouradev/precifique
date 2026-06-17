@if(config('precifique.analytics.provider') && config('precifique.analytics.id'))
    @if(config('precifique.analytics.provider') === 'plausible')
        <script defer data-domain="{{ config('precifique.analytics.domain', parse_url(config('app.url'), PHP_URL_HOST)) }}" src="https://plausible.io/js/script.js"></script>
        <script>
            window.precifiqueTrack = function (eventName, props) {
                if (typeof plausible === 'function') {
                    plausible(eventName, props ? { props: props } : undefined);
                }
            };
        </script>
    @elseif(config('precifique.analytics.provider') === 'gtag')
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('precifique.analytics.id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('precifique.analytics.id') }}', { anonymize_ip: true });
            window.precifiqueTrack = function (eventName, params) {
                if (typeof gtag === 'function') {
                    gtag('event', eventName, params || {});
                }
            };
        </script>
    @endif
@else
    <script>window.precifiqueTrack = function () {};</script>
@endif
