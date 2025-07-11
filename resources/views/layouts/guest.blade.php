<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf_token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->

    <link href="/assets/css/tabler.min.css" rel="stylesheet" />
    <link href="/assets/css/tabler-flags.min.css" rel="stylesheet" />
    <link href="/assets/css/tabler-payments.min.css" rel="stylesheet" />
    <link href="/assets/css/tabler-vendors.min.css" rel="stylesheet" /> 
    <link href="/assets/css/demo.min.css" rel="stylesheet" /> 
    <script src="https://sdk.mercadopago.com/js/v2"></script>
 
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="antialiased">
    {{ $slot }}
    @livewireScripts
    @yield('script')
    <!-- Tabler Core -->
    <script src="/assets/libs/tom-select/dist/js/tom-select.base.min.js" defer></script>
    <script src="/assets/js/tabler.min.js?1692870487" defer></script>
    <script src="/assets/js/demo.min.js?1692870487" defer></script> 
    <script src="/assets/libs/jquery/jquery.min.js"></script> 
    <x-toaster-hub /> <!-- 👈 -->
</body>

</html>
