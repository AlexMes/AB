<!doctype html>
<html class="overflow-x-hidden" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Deluge</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script> UID = @json(auth()->id());</script>

</head>
<body class="h-screen overflow-x-hidden font-sans antialiased leading-none bg-gray-100">
<script src="{{ mix('js/deluge.js') }}"></script>
<div id="app">
    @yield('application')
</div>

<!-- Scripts -->
</body>
</html>
