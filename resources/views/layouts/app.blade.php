<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CRRMS') }} - {{ $title ?? 'Campus Resource Reservation Management System' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div id="app" class="min-h-screen flex">
            <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Top Navigation -->
                @include('components.topbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
                    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                        <!-- Breadcrumbs -->
                        @if(isset($breadcrumbs))
                            @include('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
                        @endif

                        <!-- Page Header -->
                        @isset($header)
                            <header class="mb-6">
                                {{ $header }}
                            </header>
                        @endisset

                        <!-- Flash Messages -->
                        @if(session('success'))
                            @include('components.toast', ['type' => 'success', 'message' => session('success')])
                        @endif
                        @if(session('error'))
                            @include('components.toast', ['type' => 'error', 'message' => session('error')])
                        @endif
                        @if(session('warning'))
                            @include('components.toast', ['type' => 'warning', 'message' => session('warning')])
                        @endif
                        @if(session('info'))
                            @include('components.toast', ['type' => 'info', 'message' => session('info')])
                        @endif

                        <!-- Page Content -->
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts

        <!-- Global Search Modal -->
        @include('components.global-search')

        <!-- Toast Container -->
        <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

        <!-- Scripts Stack -->
        @stack('scripts')
    </body>
</html>
