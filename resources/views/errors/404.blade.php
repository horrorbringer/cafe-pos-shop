<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Not Found | {{ config('app.name', 'POS Cafe') }}</title>
    @if(app()->getLocale() === 'km')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-amber-50 via-white to-orange-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 min-h-screen antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-6 text-center">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-amber-100 dark:bg-amber-900/30 mb-8">
            <span class="text-4xl font-bold text-amber-600 dark:text-amber-400">404</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight mb-3">
            {{ __('Page not found') }}
        </h1>
        <p class="text-gray-500 dark:text-gray-400 text-lg mb-8 max-w-md">
            {{ __('The page you\'re looking for doesn\'t exist or has been moved.') }}
        </p>
        <a href="/" class="inline-flex items-center px-6 py-3 rounded-lg text-sm font-semibold bg-amber-600 text-white hover:bg-amber-700 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('Back to Home') }}
        </a>
    </div>
</body>
</html>
