<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>EliteCourses</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">

        {{-- 👇 THÊM DÒNG NÀY: Để hiển thị thanh Navbar --}}
        @include('layouts.navigation')

        {{-- Phần Form đăng ký sẽ nằm ở giữa --}}
        <div class="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

            {{-- Logo to ở giữa (Nếu thấy thừa vì Navbar đã có logo thì có thể xóa đoạn này) --}}
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
