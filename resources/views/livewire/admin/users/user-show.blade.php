<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                    <span class="sr-only">Users</span>
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ route('admin.users.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Users</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-4 text-sm font-medium text-gray-500">{{ $user->name }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        User Profile & Activity Report
                    </h2>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit User
                    </a>
                    <button wire:click="loadUserData"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Data
                    </button>
                </div>
            </div>

            <!-- User Profile Card -->
            <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-20 w-20">
                            @if($user->avatar_url)
                                <img class="h-20 w-20 rounded-full object-cover"
                                     src="{{ Storage::url($user->avatar_url) }}"
                                     alt="{{ $user->name }}">
                            @else
                                <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                    <svg class="h-10 w-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $user->name }}</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">User ID: {{ $user->id }}</p>
                            <div class="mt-2 flex space-x-2">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                                @if($user->is_disabled)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Blocked
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Full name</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->name }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Email address</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->email ?: 'Not provided' }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Phone number</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->phone ? '+91' . $user->phone : 'Not provided' }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if($user->city && $user->state && $user->country)
                                    {{ $user->city->name }}, {{ $user->state->name }}, {{ $user->country->name }}
                                @else
                                    Not provided
                                @endif
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Member since</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Last updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Statistics Overview -->
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Donations -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Donations</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $userStats['total_donations'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Donation Amount -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                                    <dd class="text-lg font-medium text-gray-900">${{ number_format($userStats['total_donation_amount'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Requests -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Requests</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $userStats['total_requests'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Achievements -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Achievements</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $userStats['total_achievements'] }} ({{ $userStats['achievement_points'] }} pts)</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Volunteer Statistics (if user is a volunteer) -->
            @if($user->hasRole('VOLUNTEER'))
                <div class="mt-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Volunteer Performance</h3>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Assigned Donations -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Assigned Donations</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ $userStats['assigned_donations'] }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Donations -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Completed Donations</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ $userStats['completed_donations'] }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Requests -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Assigned Requests</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ $userStats['assigned_requests'] }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Requests -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Completed Requests</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ $userStats['completed_requests'] }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Activity Tabs -->
            <div class="mt-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button class="whitespace-nowrap py-2 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600" onclick="showTab('donations')">
                            Donations ({{ $donations->count() }})
                        </button>
                        <button class="whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" onclick="showTab('requests')">
                            Requests ({{ $beneficiaryRequests->count() }})
                        </button>
                        <button class="whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" onclick="showTab('achievements')">
                            Achievements ({{ $achievements->count() }})
                        </button>
                        @if($user->hasRole('VOLUNTEER'))
                            <button class="whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" onclick="showTab('assigned')">
                                Assigned Tasks
                            </button>
                            <button class="whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" onclick="showTab('assignments')">
                                Volunteer Assignments
                            </button>
                        @endif
                    </nav>
                </div>

                <!-- Donations Tab -->
                <div id="donations-tab" class="tab-content">
                    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Donations</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Donations made by this user</p>
                        </div>
                        @if($donations->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($donations as $donation)
                                    <li class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    @if($donation->type === 'monetary')
                                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                            <span class="text-green-600 text-sm font-medium">$</span>
                                                        </div>
                                                    @elseif($donation->type === 'materialistic')
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <span class="text-blue-600 text-sm font-medium">📦</span>
                                                        </div>
                                                    @else
                                                        <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                                            <span class="text-purple-600 text-sm font-medium">⚙️</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ ucfirst($donation->type) }} Donation
                                                        @if($donation->type === 'monetary' && is_array($donation->details) && isset($donation->details['amount']))
                                                            - ${{ number_format($donation->details['amount'], 2) }}
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $donation->created_at->format('M d, Y H:i') }}
                                                        @if($donation->city && $donation->state)
                                                            • {{ $donation->city->name }}, {{ $donation->state->name }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $donation->status_badge_class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $donation->status)) }}
                                                </span>
                                                @if($donation->assignedTo)
                                                    <span class="text-xs text-gray-500">Assigned to {{ $donation->assignedTo->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="px-4 py-8 text-center">
                                <p class="text-sm text-gray-500">No donations found</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Requests Tab -->
                <div id="requests-tab" class="tab-content hidden">
                    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Requests</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Assistance requests made by this user</p>
                        </div>
                        @if($beneficiaryRequests->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($beneficiaryRequests as $request)
                                    <li class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center">
                                                        <span class="text-orange-600 text-sm font-medium">📋</span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $request->name }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ ucfirst($request->category) }} • {{ $request->created_at->format('M d, Y H:i') }}
                                                        @if($request->city && $request->state)
                                                            • {{ $request->city->name }}, {{ $request->state->name }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->status_badge_class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                </span>
                                                @if($request->assignedTo)
                                                    <span class="text-xs text-gray-500">Assigned to {{ $request->assignedTo->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="px-4 py-8 text-center">
                                <p class="text-sm text-gray-500">No requests found</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Achievements Tab -->
                <div id="achievements-tab" class="tab-content hidden">
                    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Achievements</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Achievements earned by this user</p>
                        </div>
                        @if($achievements->count() > 0)
                            <div class="px-4 py-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($achievements as $userAchievement)
                                        @php
                                            $achievement = $userAchievement->achievement;
                                            $rarityColors = [
                                                'common' => 'border-gray-300 bg-gray-50',
                                                'uncommon' => 'border-green-300 bg-green-50',
                                                'rare' => 'border-blue-300 bg-blue-50',
                                                'epic' => 'border-purple-300 bg-purple-50',
                                                'legendary' => 'border-yellow-300 bg-yellow-50',
                                            ];
                                        @endphp
                                        <div class="border-2 {{ $rarityColors[$achievement->rarity] ?? 'border-gray-300 bg-gray-50' }} rounded-lg p-4">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ $achievement->icon_image_url }}" alt="{{ $achievement->name }}" class="w-10 h-10 rounded-lg">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-sm font-medium text-gray-900">{{ $achievement->name }}</h4>
                                                    <p class="text-xs text-gray-600 mt-1">{{ $achievement->description }}</p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $rarityColors[$achievement->rarity] ?? 'bg-gray-100 text-gray-800' }}">
                                                            {{ $achievement->rarity_label }}
                                                        </span>
                                                        <span class="text-xs font-medium text-gray-900">{{ $achievement->points }} pts</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Earned {{ $userAchievement->earned_at->format('M d, Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="px-4 py-8 text-center">
                                <p class="text-sm text-gray-500">No achievements found</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assigned Tasks Tab (for volunteers) -->
                @if($user->hasRole('VOLUNTEER'))
                    <div id="assigned-tab" class="tab-content hidden">
                        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- Assigned Donations -->
                            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 sm:px-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Assigned Donations</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Donations assigned to this volunteer</p>
                                </div>
                                @if($assignedDonations->count() > 0)
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($assignedDonations as $donation)
                                            <li class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0">
                                                            @if($donation->type === 'monetary')
                                                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                                    <span class="text-green-600 text-sm font-medium">$</span>
                                                                </div>
                                                            @elseif($donation->type === 'materialistic')
                                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                                    <span class="text-blue-600 text-sm font-medium">📦</span>
                                                                </div>
                                                            @else
                                                                <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                                                    <span class="text-purple-600 text-sm font-medium">⚙️</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ ucfirst($donation->type) }} Donation
                                                                @if($donation->type === 'monetary' && is_array($donation->details) && isset($donation->details['amount']))
                                                                    - ${{ number_format($donation->details['amount'], 2) }}
                                                                @endif
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                From {{ $donation->donor->name }} • {{ $donation->created_at->format('M d, Y') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $donation->status_badge_class }}">
                                                        {{ ucfirst(str_replace('_', ' ', $donation->status)) }}
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="px-4 py-8 text-center">
                                        <p class="text-sm text-gray-500">No assigned donations</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Assigned Requests -->
                            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 sm:px-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Assigned Requests</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Requests assigned to this volunteer</p>
                                </div>
                                @if($assignedRequests->count() > 0)
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($assignedRequests as $request)
                                            <li class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center">
                                                                <span class="text-orange-600 text-sm font-medium">📋</span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $request->name }}</div>
                                                            <div class="text-sm text-gray-500">
                                                                From {{ $request->requestedBy->name }} • {{ $request->created_at->format('M d, Y') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->status_badge_class }}">
                                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="px-4 py-8 text-center">
                                        <p class="text-sm text-gray-500">No assigned requests</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Volunteer Assignments Tab -->
                    <div id="assignments-tab" class="tab-content hidden">
                        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Volunteer Assignments</h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">Geographic and role assignments for this volunteer</p>
                            </div>
                            @if($volunteerAssignments->count() > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach($volunteerAssignments as $assignment)
                                        <li class="px-4 py-4 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                            <span class="text-indigo-600 text-sm font-medium">📍</span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ ucfirst(str_replace('_', ' ', $assignment->assignment_type)) }} - {{ ucfirst(str_replace('_', ' ', $assignment->role)) }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            @if($assignment->city)
                                                                {{ $assignment->city->name }}, {{ $assignment->state->name }}, {{ $assignment->country->name }}
                                                            @elseif($assignment->state)
                                                                {{ $assignment->state->name }}, {{ $assignment->country->name }}
                                                            @else
                                                                {{ $assignment->country->name }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    @if($assignment->is_active)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Inactive
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="px-4 py-8 text-center">
                                    <p class="text-sm text-gray-500">No volunteer assignments found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('nav button').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.remove('hidden');

            // Add active class to clicked button
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-indigo-500', 'text-indigo-600');
        }
    </script>
</div>
