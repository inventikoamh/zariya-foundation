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

            <!-- Sidebar -->
            <div class="hidden md:flex md:w-64 md:flex-col">
                <div class="flex flex-col flex-grow pt-5 bg-white overflow-y-auto border-r border-gray-200">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <div class="flex items-center space-x-2">
                            @php($logo = \App\Models\SystemSetting::get('crm_logo'))
                            @if($logo)
                                <img src="{{ Storage::url($logo) }}" class="h-8 w-auto" alt="Logo" />
                            @endif
                            <h1 class="text-xl font-bold text-gray-900">System Admin</h1>
                        </div>
                    </div>
                    <div class="mt-5 flex-grow flex flex-col">
                        <x-system-admin-navigation />
                    </div>
                    <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                        <div class="flex-shrink-0 w-full group block">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-700">{{ auth('system')->user()->name ?? 'System' }}</div>
                                    <div class="text-xs text-gray-500">{{ auth('system')->user()->email ?? '' }}</div>
                                </div>
                                <form method="POST" action="{{ route('system.logout') }}">
                                    @csrf
                                    <button class="text-xs text-gray-600 hover:text-gray-900">Logout</button>
                                </form>
                            </div>
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


