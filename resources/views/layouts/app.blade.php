<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset('img/branding/icon.png') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('img/branding/icon.png') }}" type="image/x-icon">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ config('app.name') }}</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <script>
            window.__user = @json($user);
            window.__authPath = "{{ route('auth') }}"
        </script>
        <script src="{{ asset('js/app.js') }}?ver={{ filemtime(public_path('js/app.js')) }}" defer></script>
    </head>
    <body>
        <div id="app">
            <app-layout></app-layout>
        </div>
	</body>
</html>
