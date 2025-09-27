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
     class="relative flex-1 flex flex-col max-w-xs w-full bg-white md:hidden z-50"
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

    <!-- Sidebar content -->
    <div class="flex-1 h-0 flex flex-col">
        <!-- Header section (fixed) -->
        <div class="flex-shrink-0 flex items-center px-4 pt-5">
            @php($crmLogo = \App\Models\SystemSetting::get('crm_logo'))
            @if($crmLogo)
                <img src="{{ Storage::url($crmLogo) }}" alt="Logo" class="h-8 w-auto" />
            @endif
            <h1 class="text-xl font-bold text-gray-900 ml-2">{{ \App\Models\SystemSetting::get('crm_name', config('app.name', 'Laravel')) }}</h1>
        </div>

        <!-- Navigation (scrollable) -->
        <div class="flex-1 overflow-y-auto">
            <nav class="mt-5 px-2 space-y-1 pb-4">
                {{ $slot }}
            </nav>
        </div>
    </div>

    <!-- User info section (sticky at bottom) -->
    @if($showUserInfo && Auth::check())
        <div class="flex-shrink-0 flex border-t border-gray-200 p-4 bg-white">
            <div class="flex-shrink-0 w-full group block">
                <div class="flex items-center">
                    <div>
                        <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->phone_country_code }}{{ Auth::user()->phone }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
