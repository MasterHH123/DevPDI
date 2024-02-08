<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link rel="icon" href="{{ asset('img/branding/icon.png') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('img/branding/icon.png') }}" type="image/x-icon">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        @yield('content')
	</body>
</html>
