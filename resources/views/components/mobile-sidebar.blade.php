@props(['showUserInfo' => false])

<!-- Mobile sidebar overlay -->
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 flex z-40 md:hidden"
     style="display: none;">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
</div>

<!-- Mobile sidebar -->
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 flex flex-col max-w-xs w-full bg-white md:hidden z-50"
     style="display: none;">

    <!-- Close button -->
    <div class="absolute top-0 right-0 -mr-12 pt-2">
        <button @click="sidebarOpen = false"
                class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
            <span class="sr-only">Close sidebar</span>
            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

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
            {{ $slot }}
        </nav>
    </div>

    <!-- Footer section (fixed at bottom) -->
    @if($showUserInfo && Auth::check())
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
    @endif
</div>
