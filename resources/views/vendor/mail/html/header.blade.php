@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<img src="{{ rtrim(config('app.url'), '/') }}/images/icon-192.png" width="48" height="48" alt="Precifique" style="display: inline-block; vertical-align: middle; border: 0;">
<span style="display: inline-block; vertical-align: middle; margin-left: 10px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 20px; font-weight: 700; color: #0D0D0D;">
Preci<span style="color: #00C896;">$</span>ique
</span>
</a>
</td>
</tr>
