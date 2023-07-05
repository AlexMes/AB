<!doctype html>
<html class="overflow-x-hidden" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CRM</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script> UID = {{ auth()->id() ?? 'null' }}</script>

</head>
<body class="bg-gray-100 h-screen antialiased leading-none font-sans overflow-x-hidden">
<script src="{{ mix('js/crm.js') }}"></script>
<div id="app">
    @yield('application')
</div>

<!-- Scripts -->
</body>
</html>
