<div>
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="mt-2 text-sm text-gray-600">Comprehensive overview of your CRM system performance and analytics</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    Last updated: {{ now()->format('M j, Y g:i A') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="bg-white shadow-sm rounded-lg p-6 mb-8 border border-gray-200">
        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Time Period:</label>
                <select wire:model.live="timeFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Date Range:</label>
                <select wire:model.live="dateRange" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="1_month">Last Month</option>
                    <option value="3_months">Last 3 Months</option>
                    <option value="6_months">Last 6 Months</option>
                    <option value="1_year">Last Year</option>
                    <option value="all">All Time</option>
                </select>
            </div>
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Status Filter:</label>
                <select wire:model.live="statusFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold">{{ number_format($kpiStats['totalUsers']) }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Donations -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Donations</p>
                    <p class="text-3xl font-bold">{{ number_format($kpiStats['totalDonations']) }}</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Beneficiaries -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Beneficiaries</p>
                    <p class="text-3xl font-bold">{{ number_format($kpiStats['totalBeneficiaries']) }}</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Total Amount</p>
                    <p class="text-3xl font-bold">${{ number_format($kpiStats['totalAmount'], 2) }}</p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Requests</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($kpiStats['pendingRequests']) }}</p>
                </div>
            </div>
        </div>

        <!-- Active Volunteers -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Volunteers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($kpiStats['activeVolunteers']) }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Donations -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($kpiStats['completedDonations']) }}</p>
                </div>
            </div>
        </div>

        <!-- Urgent Items -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Urgent Items</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($kpiStats['urgentItems']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Time Series Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Trends ({{ ucfirst($timeFilter) }})</h3>
            <div class="space-y-4">
                @foreach($timeSeriesCharts['periods'] as $period)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ $period }}</span>
                        </div>
                        <div class="space-y-2">
                            <!-- Donations -->
                            @php
                                $donationCount = $timeSeriesCharts['donations'][$period] ?? 0;
                                $beneficiaryCount = $timeSeriesCharts['beneficiaries'][$period] ?? 0;
                                $maxCount = max(max(array_values($timeSeriesCharts['donations'])), max(array_values($timeSeriesCharts['beneficiaries'])));
                                $donationPercentage = $maxCount > 0 ? ($donationCount / $maxCount) * 100 : 0;
                                $beneficiaryPercentage = $maxCount > 0 ? ($beneficiaryCount / $maxCount) * 100 : 0;
                            @endphp
                            <div class="flex items-center space-x-3">
                                <div class="w-16 text-xs text-gray-500">Donations</div>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $donationPercentage }}%"></div>
                                </div>
                                <div class="w-12 text-xs font-medium text-gray-900">{{ $donationCount }}</div>
                            </div>
                            <!-- Beneficiaries -->
                            <div class="flex items-center space-x-3">
                                <div class="w-16 text-xs text-gray-500">Requests</div>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: {{ $beneficiaryPercentage }}%"></div>
                                </div>
                                <div class="w-12 text-xs font-medium text-gray-900">{{ $beneficiaryCount }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Status Distribution Pie Charts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Distribution</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Donations Status -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Donations</h4>
                    <div class="space-y-2">
                        @foreach($statusDistributionCharts['donations'] as $status => $count)
                            @php
                                $total = array_sum($statusDistributionCharts['donations']);
                                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                $colors = [
                                    'pending' => 'bg-yellow-500',
                                    'approved' => 'bg-blue-500',
                                    'in_progress' => 'bg-indigo-500',
                                    'completed' => 'bg-green-500',
                                    'cancelled' => 'bg-red-500'
                                ];
                                $color = $colors[$status] ?? 'bg-gray-500';
                            @endphp
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full {{ $color }}"></div>
                                    <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium">{{ number_format($count) }}</span>
                                    <span class="text-gray-400">({{ number_format($percentage, 1) }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Beneficiaries Status -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Beneficiaries</h4>
                    <div class="space-y-2">
                        @foreach($statusDistributionCharts['beneficiaries'] as $status => $count)
                            @php
                                $total = array_sum($statusDistributionCharts['beneficiaries']);
                                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                $colors = [
                                    'pending' => 'bg-yellow-500',
                                    'approved' => 'bg-blue-500',
                                    'in_progress' => 'bg-indigo-500',
                                    'fulfilled' => 'bg-green-500',
                                    'cancelled' => 'bg-red-500'
                                ];
                                $color = $colors[$status] ?? 'bg-gray-500';
                            @endphp
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full {{ $color }}"></div>
                                    <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium">{{ number_format($count) }}</span>
                                    <span class="text-gray-400">({{ number_format($percentage, 1) }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Donation Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Donations by Type (Pie Chart) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Donations by Type</h3>
            <div class="space-y-3">
                @foreach($donationCharts['byType'] as $type => $count)
                    @php
                        $total = array_sum($donationCharts['byType']);
                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                        $colors = [
                            'monetary' => 'bg-green-500',
                            'materialistic' => 'bg-blue-500',
                            'service' => 'bg-purple-500'
                        ];
                        $color = $colors[$type] ?? 'bg-gray-500';
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 rounded-full {{ $color }}"></div>
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ $type }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ number_format($count) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- User Roles Distribution -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Roles Distribution</h3>
            <div class="space-y-3">
                @foreach($userCharts['byRole'] as $role => $count)
                    @php
                        $total = array_sum($userCharts['byRole']);
                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                        $colors = [
                            'SUPER_ADMIN' => 'bg-red-500',
                            'ADMIN' => 'bg-orange-500',
                            'VOLUNTEER' => 'bg-blue-500',
                            'USER' => 'bg-green-500'
                        ];
                        $color = $colors[$role] ?? 'bg-gray-500';
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 rounded-full {{ $color }}"></div>
                            <span class="text-sm font-medium text-gray-700">{{ str_replace('_', ' ', $role) }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ number_format($count) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Location Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Countries - Donations -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Countries - Donations</h3>
            <div class="space-y-3">
                @foreach($locationCharts['donationCountries'] as $country => $count)
                    @php
                        $maxCount = max(array_values($locationCharts['donationCountries']));
                        $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                    @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ $country }}</span>
                            <span class="font-semibold text-gray-900">{{ number_format($count) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Countries - Beneficiaries -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Countries - Beneficiaries</h3>
            <div class="space-y-3">
                @foreach($locationCharts['beneficiaryCountries'] as $country => $count)
                    @php
                        $maxCount = max(array_values($locationCharts['beneficiaryCountries']));
                        $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                    @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ $country }}</span>
                            <span class="font-semibold text-gray-900">{{ number_format($count) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
        <div class="space-y-4">
            @forelse($recentActivities as $activity)
                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            {{ $activity['color'] === 'green' ? 'bg-green-100' : ($activity['color'] === 'blue' ? 'bg-blue-100' : 'bg-yellow-100') }}">
                            <svg class="w-4 h-4 {{ $activity['color'] === 'green' ? 'text-green-600' : ($activity['color'] === 'blue' ? 'text-blue-600' : 'text-yellow-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity['icon'] }}"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                        <p class="text-sm text-gray-500">{{ $activity['description'] }}</p>
                        @if(isset($activity['amount']))
                            <p class="text-sm font-semibold text-green-600">${{ number_format($activity['amount'], 2) }}</p>
                        @endif
                    </div>
                    <div class="flex-shrink-0 text-xs text-gray-400">
                        {{ $activity['time']->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500">No recent activity</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
