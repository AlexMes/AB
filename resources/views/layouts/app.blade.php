<!doctype html>
<html class="overflow-x-hidden" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/icons/safari-pinned-tab.svg" color="#2c9cab">
    <link rel="shortcut icon" href="/icons/favicon.ico">
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="msapplication-config" content="/icons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
</head>
<body class="bg-gray-50 h-screen antialiased leading-none font-sans overflow-x-hidden">
<div id="app">
    @yield('content')
</div>

<!-- Scripts -->
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
