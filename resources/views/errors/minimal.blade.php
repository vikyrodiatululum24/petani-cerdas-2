<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts'])
    </head>
    <body>
        <div class="relative min-h-screen">
            <div class="absolute z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col items-center justify-center gap-4">
                <h1 class="mb-4 text-4xl font-bold text-[#2E7D32]">@yield('code')</h1>
                <h2 class="mb-6 text-2xl font-semibold text-gray-800">@yield('message')</h2>
                <img src="@yield('image')" alt="@yield('code') Not Authorized" class="mb-6 w-64">
                <div class="flex items-center justify-center gap-2">
                    <a href="/" class="bg-[#2E7D32] text-white hover:bg-[#256427] py-2 px-4 rounded-md transition duration-300">Beranda</a>
                    <a href="javascript:history.back()" class="bg-gray-500 text-white hover:bg-gray-600 py-2 px-4 rounded-md transition duration-300">Kembali</a>
                </div>
            </div>
            <img src="/assets/FooterError.png" alt="Footer Error" class="absolute bottom-0 left-0 w-full z-0">
        </div>
    </body>
</html>
