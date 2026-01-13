<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://kit.fontawesome.com/05eb0a9091.js" crossorigin="anonymous"></script>
    <link rel="preload" as="image" href="{{ asset('images/background-login.png') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans  text-gray-900 antialiased">
<div style="background-image: url('{{ asset('img/background-login.webp') }}')"  class="min-h-screen  flex flex-col bg-cover sm:justify-center items-center pt-6 sm:pt-0 dark:bg-gray-900">


    <x-application-logo bw='login' />
    <div class="w-auto sm:max-w-md mt-2 px-6 py-6 overflow-hidden">
        {{ $slot }}
    </div>

</div>
</body>
</html>
