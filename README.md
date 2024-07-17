# blackbeetle.de API

## blackbeetle.de

Blackbeetle is a small private website project that I started before my trip to Australia. In order to keep my family and friends up to date without sending everyone the same messages over and over again, I set up this little travel blog. The project has changed a lot over time. Whenever I try out a new technology, Blackbeetle is my test object. Initially, the entire page was implemented with Laravel framework. I later separated the API and created two frontends, one for the public and one for the administration.

## The Backend API

The backend is the oldest part of the project and is currently realised with Laravel 9. This is where the data is processed, e.g. generating image variations, sending emails and communicating with the database. The admin frontend is authenticated with Laravel Sanctum.
In the long term, this part should be replaced by a [NestJS](https://nestjs.com/) backend, because I would like to get a little deeper into NestJs, expand my Typescript skills and because I actually want to get away from PHP.

## Technologies & Frameworks

- [Laravel 9](https://laravel.com/)
- [Laravel Sanctum](https://laravel.com/docs/9.x/sanctum)
- for more infos check out the package.json

