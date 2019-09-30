<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="/images/favicon.png" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? env('APP_NAME', '') }}</title>

    <link href="{{ mix('/css/index.css') }}" rel="stylesheet">
    <script defer src="{{ mix('/js/index.js') }}"></script>
</head>
<body class="bg-white">
    <main id="app" role="main"></main>
</body>
</html>
