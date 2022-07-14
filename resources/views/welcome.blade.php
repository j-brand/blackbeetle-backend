<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Blackbeetle</title>
    <style>
        body {
            margin: 0;
        }

        .container {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #373737;
        }
    </style>

</head>

<body>

    <div class="container">
        <div cass="img-wrapper">
            <a href="https://www.blackbeetle.de">
                <img src="{{ asset('Kaefer_2_400x400_white.png') }}" alt="blackbeetle logo" />
            </a>
        </div>
    </div>

</body>

</html>