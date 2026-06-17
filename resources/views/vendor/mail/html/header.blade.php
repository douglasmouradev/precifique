@props(['url'])
<tr>
<td class="header" style="border-bottom: 3px solid #00C896; padding-bottom: 20px;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<img src="{{ rtrim(config('app.url'), '/') }}/images/icon-192.png" width="48" height="48" alt="Precifique" style="display: inline-block; vertical-align: middle; border: 0; border-radius: 12px;">
<span style="display: inline-block; vertical-align: middle; margin-left: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 22px; font-weight: 700; color: #0D0D0D; letter-spacing: -0.02em;">
Preci<span style="color: #00C896;">$</span>ique
</span>
</a>
</td>
</tr>
