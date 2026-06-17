<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center" style="padding-top: 28px; border-top: 1px solid #e2e8f0;">
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 12px; color: #64748b; line-height: 1.7; margin: 0;">
<strong style="color: #0D0D0D;">Precifique</strong><br>
{{ __('mail.footer.tagline') }}<br>
<a href="{{ rtrim(config('app.url'), '/') }}" style="color: #00C896; text-decoration: none;">{{ rtrim(config('app.url'), '/') }}</a>
</p>
<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 11px; color: #94a3b8; margin: 12px 0 0;">
<a href="{{ rtrim(config('app.url'), '/').'/privacidade' }}" style="color: #00C896; text-decoration: none;">{{ __('mail.footer.privacy') }}</a>
&nbsp;·&nbsp;
<a href="{{ rtrim(config('app.url'), '/').'/termos' }}" style="color: #00C896; text-decoration: none;">{{ __('mail.footer.terms') }}</a>
</p>
@if(isset($slot) && trim($slot) !== '')
<div style="margin-top: 16px; font-size: 12px; color: #64748b;">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</div>
@endif
</td>
</tr>
</table>
</td>
</tr>
