@component('mail::message')

Hallo **{{ $details['name'] }}**,  
toll das du dich für unseren Newsletter angemeldet hast.

Bevor es losgehen kann, musst du erst deine E-Mail-Adresse bestätigen.
Dazu klickst du enfach auf den folgenden Link:


@component('mail::button', ['url' =>  $details['link']])
    E-Mail verifizieren
@endcomponent

Beste Grüße,  
**Blackbeetle** 

@endcomponent