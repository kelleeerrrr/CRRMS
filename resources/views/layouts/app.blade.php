<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }" @keydown.window.prevent.ctrl.k="$dispatch('open-global-search')">
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
    <body class="font-sans antialiased" style="color: var(--text-primary);">
        <div id="app" class="flex h-screen overflow-hidden supports-[height:100dvh]:h-dvh" x-data="{ mobileSidebarOpen: false }" @keydown.escape.window="mobileSidebarOpen = false">
            <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Main Content -->
            <div class="flex min-h-0 min-w-0 flex-1 flex-col">
                <!-- Top Navigation -->
                @include('components.topbar')

                <!-- Page Content -->
                <main @class(['min-h-0 flex-1 overscroll-y-contain', 'overflow-hidden' => isset($fillHeight), 'overflow-y-auto' => !isset($fillHeight)])>
                    <div @class(['mx-auto max-w-7xl px-4 sm:px-6 lg:px-8', 'flex h-full flex-col pt-3 sm:pt-6' => isset($fillHeight), 'py-6' => !isset($fillHeight)])>
                        <!-- Breadcrumbs -->
                        @if(isset($breadcrumbs))
                            @include('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
                        @endif

                        <!-- Page Header -->
                        @isset($header)
                            <header @class(['mb-3 shrink-0 sm:mb-4' => isset($fillHeight), 'mb-6' => !isset($fillHeight)])>
                                {{ $header }}
                            </header>
                        @endisset

                        <!-- Flash Messages -->
                        @if(session('success'))
                            <x-toast type="success" :message="session('success')" />
                        @endif
                        @if(session('error'))
                            <x-toast type="error" :message="session('error')" />
                        @endif
                        @if(session('warning'))
                            <x-toast type="warning" :message="session('warning')" />
                        @endif
                        @if(session('info'))
                            <x-toast type="info" :message="session('info')" />
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
