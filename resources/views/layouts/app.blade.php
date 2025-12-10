<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <style>
        .approve-btn {
            background-color: #16a34a !important;
            /* bg-green-600 */
            width: 220px !important;
            padding: 12px 0 !important;
            font-weight: 700;
            border-radius: 10px;
            color: white !important;
        }

        .approve-btn:hover {
            background-color: #15803d !important;
            /* hover-green-700 */
        }

        .reject-btn {
            background-color: #dc2626 !important;
            /* bg-red-600 */
            width: 220px !important;
            padding: 12px 0 !important;
            font-weight: 700;
            border-radius: 10px;
            color: white !important;
        }

        .reject-btn:hover {
            background-color: #b91c1c !important;
            /* hover-red-700 */
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>


</body>

</html>