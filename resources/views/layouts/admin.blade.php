<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="/assets/css/tabler.css?time=<?php echo time(); ?>" rel="stylesheet" />
    <link href="/assets/css/tabler-vendors.min.css" rel="stylesheet" />
    <link href="/assets/libs/apexcharts/dist/apexcharts.css" rel="stylesheet" />

    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="page">

        <x-header />

        <div class="page-wrapper">
            
            <!-- Page Header -->
            @if (isset($header))
                {{ $header }}
            @endif

            <!-- Page Content -->
            <div class="page-body">
                <div class="container-xl">
                    {{ $slot }}
                </div>
            </div> 
        </div>
    </div>
    @livewireScripts
    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="/assets/libs/tom-select/dist/js/tom-select.base.min.js" defer></script>
    <script src="/assets/js/tabler.min.js?1692870487" defer></script>
    <script src="/assets/js/demo.min.js?1692870487" defer></script>
    <script src="/assets/libs/jquery/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bignumber.js/9.0.2/bignumber.min.js"></script>
    @stack('scripts')
    <script>
        var $ = jQuery;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('script')
    <x-toaster-hub /> <!-- 👈 -->


    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
