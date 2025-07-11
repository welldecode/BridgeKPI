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
   
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="/assets/css/tabler.css?time=<?php echo time() ?>" rel="stylesheet" />
    <link href="/assets/css/tabler-vendors.min.css" rel="stylesheet" />
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="page">
      <header class="navbar navbar-expand-md d-print-none">
        <div class="container-xl">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{route('admin.index')}}">
              <img src="https://i.imgur.com/hgXV0Wu.png"  width="310" height="42" alt="Tabler" class="navbar-brand-image">
                 
            </a>
          </h1>
          <div class="navbar-nav flex-row order-md-last">
         
            <div class="d-none d-md-flex">
       
             
              <a href="{{route('cart.index')}}" class="nav-link px-0"  >
                <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                <svg  xmlns="http://www.w3.org/2000/svg"  width="26"  height="26"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                       
              </a>
            </div> 
          </div>
        </div>
      </header>
    <!-- Page Content -->
    <main class="page-body">
        {{ $slot }}
    </main>

  </div>
  <footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
      <div class="row text-center align-items-center flex-row-reverse">
        <div class="col-lg-auto ms-lg-auto">
          <ul class="list-inline list-inline-dots mb-0"> 
           
          </ul>
        </div>
        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
          <ul class="list-inline list-inline-dots mb-0">
            <li class="list-inline-item">
              Copyright © 2025
              <a href="." class="link-secondary">Bridges KPI</a>.
              {{__('All rights reserved.')}}
            </li>
            <li class="list-inline-item">
              <a href="./changelog.html" class="link-secondary" rel="noopener">
                v1.0.0-beta24
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
    @yield('script')
    <!-- Tabler Core -->
    <script src="/assets/libs/tom-select/dist/js/tom-select.base.min.js" defer></script>
    <script src="/assets/js/tabler.min.js?1692870487" defer></script>
    <script src="/assets/js/demo.min.js?1692870487" defer></script> 
    <x-toaster-hub /> <!-- 👈 -->
</body> 

</html>
