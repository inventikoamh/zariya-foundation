<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\SystemSetting::get('crm_name', config('app.name', 'Laravel')) }} - Admin</title>

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
                <x-super-admin-navigation />
            </x-mobile-sidebar>

            <!-- Desktop Sidebar -->
            <div class="hidden md:flex md:w-64 md:flex-col">
                <div class="flex flex-col h-full bg-white border-r border-gray-200">
                    <!-- Header section (fixed at top) -->
                    <div class="flex-shrink-0 flex items-center px-4 pt-5 pb-4 border-b border-gray-200">
                        @php($crmLogo = \App\Models\SystemSetting::get('crm_logo'))
                        @if($crmLogo)
                            <img src="{{ Storage::url($crmLogo) }}" alt="Logo" class="h-8 w-auto" />
                        @endif
                        <h1 class="text-xl font-bold text-gray-900 ml-2">{{ \App\Models\SystemSetting::get('crm_name', config('app.name', 'Laravel')) }}</h1>
                    </div>

                    <!-- Navigation section (scrollable middle) -->
                    <div class="flex-1 overflow-y-auto">
                        <nav class="px-2 py-4 space-y-1">
                            <x-super-admin-navigation />
                        </nav>
                    </div>

                    <!-- Footer section (fixed at bottom) -->
                    <div class="flex-shrink-0 border-t border-gray-200 bg-white">
                        <!-- User info -->
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->phone_country_code }}{{ Auth::user()->phone }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Logout button -->
                        <div class="px-4 pb-4">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
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
                            {{ $slot ?? '' }}
                            @yield('content')
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
