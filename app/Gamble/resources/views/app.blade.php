<!doctype html>
<html class="overflow-x-hidden" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Gamble</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script> UID = @json(auth()->id());</script>

</head>
<body class="h-screen overflow-x-hidden font-sans antialiased leading-none bg-gray-100">
    <div id="app">
        <application-menu ref="auth" :user="{{ auth()->user() }}"></application-menu>
        <router-view></router-view>
    </div>
</body>

<script src="{{ mix('js/gamble.js') }}"></script>
</html>
