<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\SystemSetting::get('crm_name', config('app.name', 'Laravel')) }} - {{ ucfirst($userRole ?? 'User') }}</title>

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
    <body class="font-sans antialiased bg-gray-100">
        @if(request()->routeIs('donate'))
            <!-- No sidebar layout for donate page -->
            <div class="min-h-screen bg-gray-100">
                <livewire:layout.navigation />
                <main>
                    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                        @if (isset($header))
                            <div class="mb-6">
                                {{ $header }}
                            </div>
                        @endif
                        {{ $slot }}
                    </div>
                </main>
            </div>
        @else
            <!-- Normal layout with sidebar -->
            <div class="min-h-screen flex">
                <!-- Sidebar -->
                <div class="hidden md:flex md:w-64 md:flex-col">
                    <div class="flex flex-col flex-grow pt-5 bg-white overflow-y-auto border-r border-gray-200">
                        <div class="flex items-center flex-shrink-0 px-4">
                            @php($crmLogo = \App\Models\SystemSetting::get('crm_logo'))
                            @if($crmLogo)
                                <img src="{{ Storage::url($crmLogo) }}" alt="Logo" class="h-8 w-auto" />
                            @endif
                            <h1 class="text-xl font-bold text-gray-900">{{ \App\Models\SystemSetting::get('crm_name', config('app.name', 'Laravel')) }}</h1>
                        </div>
                        <div class="mt-5 flex-grow flex flex-col">
                            <x-unified-navigation
                                :userRole="$userRole ?? 'user'"
                                :dashboardRoute="$dashboardRoute ?? route('dashboard')"
                                :dashboardRouteName="$dashboardRouteName ?? 'dashboard'" />
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
        @endif
    </body>
</html>
