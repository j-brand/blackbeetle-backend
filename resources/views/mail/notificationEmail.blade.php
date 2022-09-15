@component('mail::message')

Hallo **{{ $details['name'] }}**,  
es gibt Neuhigkeiten!

@if($details['image'])
@component('mail::image', ['src' => $details['image'], 'alt' => 'Post image'])
@endcomponent
@endif


{{ $details['content'] }}

@component('mail::button', ['url' =>  $details['link']])
Bring mich hin 🐌
@endcomponent


Beste Grüße,  
**Blackbeetle** 

<small>
Falls du diese Nachrichten nicht mehr erhalten möchtest, kannst du dich <a href="{{ $details['manageLink'] }}">hier</a> vom Verteiler abmelden.
</small> 

@endcomponent