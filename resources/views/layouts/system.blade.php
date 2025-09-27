<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\SystemSetting::get('crm_name', config('app.name')) }} - System</title>

        <!-- Favicon -->
        @include('partials.favicon')

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.brand-colors')
    </head>
    <body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex">
            <!-- Mobile Sidebar Component -->
            <x-mobile-sidebar :show-user-info="true">
                <x-system-admin-navigation />
            </x-mobile-sidebar>

            <!-- Desktop Sidebar -->
            <div class="hidden md:flex md:w-64 md:flex-col">
                <div class="flex flex-col h-screen bg-white border-r border-gray-200">
                    <!-- Header section (fixed at top) -->
                    <div class="flex-shrink-0 flex items-center px-4 pt-5 pb-4 border-b border-gray-200">
                        <div class="flex items-center space-x-2">
                            @php($logo = \App\Models\SystemSetting::get('crm_logo'))
                            @if($logo)
                                <img src="{{ Storage::url($logo) }}" class="h-8 w-auto" alt="Logo" />
                            @endif
                            <h1 class="text-xl font-bold text-gray-900">System Admin</h1>
                        </div>
                    </div>

                    <!-- Navigation section (scrollable middle) -->
                    <div class="flex-1 overflow-y-auto">
                        <nav class="px-2 py-4 space-y-1">
                            <x-system-admin-navigation />
                        </nav>
                    </div>

                    <!-- Footer section (fixed at bottom) -->
                    <div class="flex-shrink-0 border-t border-gray-200 bg-white">
                        <!-- User info -->
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-700">{{ auth('system')->user()->name ?? 'System' }}</div>
                                    <div class="text-xs text-gray-500">{{ auth('system')->user()->email ?? '' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Logout button -->
                        <div class="px-4 pb-4">
                            <form method="POST" action="{{ route('system.logout') }}">
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
                <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                    <button @click="sidebarOpen = true" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex-1 px-4 flex items-center justify-between">
                        <div class="text-sm text-gray-500">System Panel</div>
                        <div></div>
                    </div>
                </div>

                <main class="flex-1 relative overflow-y-auto focus:outline-none">
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            @yield('content')
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>


