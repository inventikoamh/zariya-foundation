<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Localization Management
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage countries, states, cities and volunteer assignments
                    </p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mt-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button wire:click="setActiveTab('countries')"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'countries' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Countries
                        </button>
                        <button wire:click="setActiveTab('states')"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'states' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            States
                        </button>
                        <button wire:click="setActiveTab('cities')"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'cities' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Cities
                        </button>
                        <button wire:click="setActiveTab('volunteers')"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'volunteers' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Volunteer Assignments
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-6 bg-white shadow rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input
                            type="text"
                            id="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search..."
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                    </div>

                    <!-- Country Filter -->
                    <div>
                        <label for="selectedCountry" class="block text-sm font-medium text-gray-700">Country</label>
                        <select
                            id="selectedCountry"
                            wire:model.live="selectedCountry"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="">All Countries</option>
                            @foreach($countriesList as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- State Filter -->
                    <div>
                        <label for="selectedState" class="block text-sm font-medium text-gray-700">State</label>
                        <select
                            id="selectedState"
                            wire:model.live="selectedState"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            {{ !$selectedCountry ? 'disabled' : '' }}
                        >
                            <option value="">All States</option>
                            @foreach($statesList as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Add Button -->
                    <div class="flex items-end">
                        @if($activeTab === 'countries')
                            <a href="{{ route('admin.localization.countries') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Manage Countries
                            </a>
                        @elseif($activeTab === 'states')
                            <a href="{{ route('admin.localization.states') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Manage States
                            </a>
                        @elseif($activeTab === 'cities')
                            <a href="{{ route('admin.localization.cities') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Manage Cities
                            </a>
                        @elseif($activeTab === 'volunteers')
                            <a href="{{ route('admin.localization.volunteers') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Manage Assignments
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content based on active tab -->
            <div class="mt-6">
                @if($activeTab === 'countries')
                    @include('livewire.admin.localization.partials.countries-table')
                @elseif($activeTab === 'states')
                    @include('livewire.admin.localization.partials.states-table')
                @elseif($activeTab === 'cities')
                    @include('livewire.admin.localization.partials.cities-table')
                @elseif($activeTab === 'volunteers')
                    @include('livewire.admin.localization.partials.volunteers-table')
                @endif
            </div>
        </div>
    </div>
</div>
