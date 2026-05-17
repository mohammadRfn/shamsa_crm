<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'سیستم مدیریت') }}</title>

    <!-- فونت فارسی -->
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            direction: rtl;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-dark-900 text-cream-100 font-sans antialiased">
    {{ $slot }}
</body>

</html>