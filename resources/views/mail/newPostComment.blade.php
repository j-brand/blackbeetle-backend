@component('mail::message')
{{ $greeting }}

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{!! $line !!}
@endforeach

{{-- Action Button --}}
@component('mail::button', ['url' => $actionUrl])
bring mich hin!
@endcomponent

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}
@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Beste Grüße'),<br>
euer Postman 📮
@endif

@endcomponent