<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\SystemSetting::get('crm_name', config('app.name', 'Laravel')) }} - Volunteer</title>

        <!-- Favicon -->
        @include('partials.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Fancybox CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.brand-colors')

        <!-- Fancybox JS -->
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex">
            <!-- Mobile Sidebar Component -->
            <x-mobile-sidebar :show-user-info="true">
                <x-volunteer-navigation />
            </x-mobile-sidebar>

            <!-- Sidebar -->
            <div class="hidden md:flex md:w-64 md:flex-col">
                <div class="flex flex-col flex-grow pt-5 bg-white overflow-y-auto border-r border-gray-200">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <h1 class="text-xl font-bold text-gray-900">Foundation CRM</h1>
                    </div>
                    <div class="mt-5 flex-grow flex flex-col">
                        <x-volunteer-navigation />
                    </div>
                    <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                        <div class="flex-shrink-0 w-full group block">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->phone_country_code }}{{ Auth::user()->phone }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Main content -->
            <div class="flex flex-col w-0 flex-1 overflow-hidden">
                <!-- Top navigation -->
                <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                    <button @click="sidebarOpen = true" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex-1 px-4 flex justify-end">
                        <div class="flex items-center">
                            <livewire:layout.navigation />
                        </div>
                    </div>
                </div>

                <!-- Page content -->
                <main class="flex-1 relative overflow-y-auto focus:outline-none">
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            @if (isset($header))
                                <div class="mb-6">
                                    {{ $header }}
                                </div>
                            @endif
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
