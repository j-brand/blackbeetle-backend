@component('mail::message')

Hallo **{{ $details['name'] }}**,  
toll das du dich für unseren Newsletter angemeldet hast.

Bevor es losgehen kann, musst du erst deine E-Mail-Adresse bestätigen.
Dazu klickst du enfach auf den folgenden Link:


@component('mail::button', ['url' =>  $details['verifyLink']])
    E-Mail verifizieren
@endcomponent

Beste Grüße,  
**Blackbeetle** 

<small>
Falls du diese Nachrichten nicht mehr erhalten möchtest, kannst du dich <a href="{{ $details['manageLink'] }}">hier</a> vom Verteiler abmelden.
</small> 

@endcomponent