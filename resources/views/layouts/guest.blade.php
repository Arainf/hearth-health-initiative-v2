<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preload" as="image" href="{{ asset('img/background-login.webp') }}">
    <link rel="preload" as="image" href="{{ asset('img/application-logo-seal-white.webp') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans  text-gray-900 antialiased">
<div style="background-image: url('{{ asset('img/background-login.webp') }}')"  class="min-h-screen  flex flex-col bg-cover sm:justify-center  items-center pt-6 sm:pt-0 dark:bg-gray-900">

    <div>
        <img
            class="h-auto w-auto z-50 transition-all duration-300 md:scale-75 md:mb-[-46px]"
            src="{{ asset('img/application-logo-seal-white.webp') }}"
            alt="default logo"
        >
    </div>
    <div class="w-auto sm:max-w-md mt-2 px-6 py-6 overflow-hidden md:scale-75">
        {{ $slot }}
    </div>

</div>
</body>
</html>
