<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'POS Cafe') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-amber-50 via-white to-orange-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 min-h-screen antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="w-full px-6 py-4">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <a href="/" class="text-xl font-bold text-amber-700 dark:text-amber-400 tracking-tight">
                    {{ config('app.name', 'POS Cafe') }}
                </a>
                <nav class="flex items-center gap-4">
                    <a href="/menu" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                        Digital Menu
                    </a>
                    @auth
                        <a href="/admin" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-amber-600 text-white hover:bg-amber-700 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="/admin/login" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-amber-600 text-white hover:bg-amber-700 transition-colors">
                            Staff Login
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center px-6">
            <div class="max-w-4xl w-full text-center">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-100 dark:bg-amber-900/30 mb-6">
                        <svg class="w-10 h-10 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white tracking-tight mb-4">
                        Welcome to
                        <span class="text-amber-600 dark:text-amber-400">{{ config('app.name', 'POS Cafe') }}</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                        Your all-in-one point of sale system. Manage orders, inventory, and serve your customers with ease.
                    </p>
                </div>

                <div class="grid sm:grid-cols-3 gap-4 max-w-2xl mx-auto mb-12">
                    <a href="/menu" class="group p-6 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-amber-300 dark:hover:border-amber-700 shadow-sm hover:shadow-md transition-all">
                        <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Digital Menu</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Browse our menu and place orders</p>
                    </a>

                    <a href="/admin" class="group p-6 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-amber-300 dark:hover:border-amber-700 shadow-sm hover:shadow-md transition-all">
                        <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Dashboard</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage orders, inventory, and reports</p>
                    </a>

                    <a href="/pos" class="group p-6 rounded-xl bg-amber-600 text-white hover:bg-amber-700 shadow-md hover:shadow-lg transition-all">
                        <div class="w-12 h-12 rounded-lg bg-amber-500/30 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-1">POS Terminal</h3>
                        <p class="text-sm text-amber-100">Process orders and take payments</p>
                    </a>
                </div>
            </div>
        </main>

        <footer class="px-6 py-4 text-center text-sm text-gray-400 dark:text-gray-600">
            &copy; {{ date('Y') }} {{ config('app.name', 'POS Cafe') }}. All rights reserved.
        </footer>
    </div>
</body>
</html>
