<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Blackbeetle')
<img src="{{ url('/logo.png') }}" class="logo" alt="Blackbeetle Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
