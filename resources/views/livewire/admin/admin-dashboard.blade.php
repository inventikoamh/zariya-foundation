<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="mt-2 text-sm text-gray-600">Welcome back! Here's what's happening with your CRM system.</p>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['totalUsers']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Donations -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Donations</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['totalDonations']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                            <dd class="text-2xl font-bold text-gray-900">${{ number_format($stats['totalAmount'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['pendingRequests']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Active Volunteers -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Volunteers</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['activeVolunteers']) }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['completedDonations']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Beneficiaries -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Beneficiaries</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['totalBeneficiaries']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Urgent Items -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Urgent Items</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['urgentItems']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="flex flex-wrap gap-4">
            <div>
                <label for="timeFilter" class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                <select wire:model.live="timeFilter" id="timeFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
                <select wire:model.live="statusFilter" id="statusFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all">All Statuses</option>
                    @foreach($statusCharts['donationStatuses'] as $status)
                        <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                    @foreach($statusCharts['beneficiaryStatuses'] as $status)
                        @if(!in_array($status, $statusCharts['donationStatuses']->toArray()))
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Status-based Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Donations by Status Over Time -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Donations by Status ({{ ucfirst($timeFilter) }})</h3>
            <div class="space-y-4">
                @foreach($statusCharts['periods'] as $period)
                    <div class="border-l-4 border-indigo-400 pl-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">{{ $period }}</span>
                            <span class="text-xs text-gray-500">{{ $timeFilter === 'monthly' ? 'Month' : 'Year' }}</span>
                        </div>
                        <div class="space-y-2">
                            @if(isset($statusCharts['donations'][$period]))
                                @foreach($statusCharts['donations'][$period] as $statusData)
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 rounded-full mr-2 {{ $statusData->status === 'completed' ? 'bg-green-500' : ($statusData->status === 'pending' ? 'bg-yellow-500' : ($statusData->status === 'in_progress' ? 'bg-blue-500' : 'bg-gray-500')) }}"></div>
                                            <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $statusData->status)) }}</span>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ number_format($statusData->count) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500">No data for this period</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Beneficiaries by Status Over Time -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Beneficiaries by Status ({{ ucfirst($timeFilter) }})</h3>
            <div class="space-y-4">
                @foreach($statusCharts['periods'] as $period)
                    <div class="border-l-4 border-purple-400 pl-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">{{ $period }}</span>
                            <span class="text-xs text-gray-500">{{ $timeFilter === 'monthly' ? 'Month' : 'Year' }}</span>
                        </div>
                        <div class="space-y-2">
                            @if(isset($statusCharts['beneficiaries'][$period]))
                                @foreach($statusCharts['beneficiaries'][$period] as $statusData)
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 rounded-full mr-2 {{ $statusData->status === 'fulfilled' ? 'bg-green-500' : ($statusData->status === 'pending' ? 'bg-yellow-500' : ($statusData->status === 'approved' ? 'bg-blue-500' : 'bg-gray-500')) }}"></div>
                                            <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $statusData->status)) }}</span>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ number_format($statusData->count) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500">No data for this period</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Localization Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Donations by Country -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Donations by Country</h3>
            <div class="space-y-3">
                @forelse($localizationCharts['donationCountries'] as $country => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 bg-green-500"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $country }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($count) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No donation data available</p>
                @endforelse
            </div>
        </div>

        <!-- Beneficiaries by Country -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Beneficiaries by Country</h3>
            <div class="space-y-3">
                @forelse($localizationCharts['beneficiaryCountries'] as $country => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 bg-purple-500"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $country }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($count) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No beneficiary data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- State-level Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top States - Donations -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top States - Donations</h3>
            <div class="space-y-3">
                @forelse($localizationCharts['donationStates'] as $state => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 bg-blue-500"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $state }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($count) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No state data available</p>
                @endforelse
            </div>
        </div>

        <!-- Top States - Beneficiaries -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top States - Beneficiaries</h3>
            <div class="space-y-3">
                @forelse($localizationCharts['beneficiaryStates'] as $state => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 bg-orange-500"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $state }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($count) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No state data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Basic Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Donations by Type Chart -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Donations by Type</h3>
            <div class="space-y-3">
                @foreach($donationStats['byType'] as $type => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 {{ $type === 'monetary' ? 'bg-green-500' : ($type === 'materialistic' ? 'bg-blue-500' : 'bg-purple-500') }}"></div>
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ $type }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($count) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Users by Role Chart -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Users by Role</h3>
            <div class="space-y-3">
                @foreach($userStats['byRole'] as $role => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 {{ $role === 'SUPER_ADMIN' ? 'bg-red-500' : ($role === 'VOLUNTEER' ? 'bg-blue-500' : 'bg-gray-500') }}"></div>
                            <span class="text-sm font-medium text-gray-700">{{ str_replace('_', ' ', $role) }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($count) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($recentActivities as $index => $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-4 w-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity['icon'] }}"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $activity['message'] }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $activity['time']->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-center py-8">
                            <p class="text-sm text-gray-500">No recent activity</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
