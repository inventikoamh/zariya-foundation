<div>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Request #{{ $request->id }}</h1>
                        <p class="text-sm text-gray-500">{{ ucfirst($request->category) }} •
                            {{ $request->created_at->format('M j, Y') }}</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('volunteer.requests.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Requests
                    </a>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Request Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Request Details</h2>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                    Request Name
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $request->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                    Category
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                        @if ($request->category === 'medical') bg-red-100 text-red-800
                                        @elseif($request->category === 'education') bg-blue-100 text-blue-800
                                        @elseif($request->category === 'food') bg-green-100 text-green-800
                                        @elseif($request->category === 'shelter') bg-yellow-100 text-yellow-800
                                        @elseif($request->category === 'emergency') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $categoryOptions[$request->category] ?? ucfirst($request->category) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                        @if ($request->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($request->status === 'under_review') bg-blue-100 text-blue-800
                                        @elseif($request->status === 'approved') bg-green-100 text-green-800
                                        @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                        @elseif($request->status === 'fulfilled') bg-purple-100 text-purple-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                        </path>
                                    </svg>
                                    Priority
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                        @if ($request->priority === 'low') bg-gray-100 text-gray-800
                                        @elseif($request->priority === 'medium') bg-blue-100 text-blue-800
                                        @elseif($request->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($request->priority === 'urgent') bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                </dd>
                            </div>
                            @if ($request->estimated_amount)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                            </path>
                                        </svg>
                                        Estimated Amount
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $request->currency }}
                                        {{ number_format($request->estimated_amount, 2) }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Created
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $request->created_at->format('M j, Y g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Description</h2>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $request->description }}</p>
                    </div>
                </div>

                <!-- Urgency Notes -->
                @if ($request->urgency_notes)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-orange-400 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <h2 class="text-lg font-medium text-gray-900">Urgency Notes</h2>
                            </div>
                        </div>
                        <div class="px-6 py-4">
                            <div class="bg-orange-50 border border-orange-200 rounded-md p-4">
                                <p class="text-sm text-orange-800 whitespace-pre-wrap">{{ $request->urgency_notes }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Additional Information -->
                @if ($request->additional_info)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h2 class="text-lg font-medium text-gray-900">Additional Information</h2>
                            </div>
                        </div>
                        <div class="px-6 py-4">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $request->additional_info }}</p>
                        </div>
                    </div>
                @endif

                <!-- Location Information -->
                @if ($request->location)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <h2 class="text-lg font-medium text-gray-900">Location</h2>
                            </div>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                @if (isset($request->location['country_id']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Country</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @php
                                                $country = \App\Models\Country::find($request->location['country_id']);
                                            @endphp
                                            {{ $country->name ?? 'Unknown' }}
                                        </dd>
                                    </div>
                                @endif
                                @if (isset($request->location['state_id']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">State</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @php
                                                $state = \App\Models\State::find($request->location['state_id']);
                                            @endphp
                                            {{ $state->name ?? 'Unknown' }}
                                        </dd>
                                    </div>
                                @endif
                                @if (isset($request->location['city_id']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">City</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @php
                                                $city = \App\Models\City::find($request->location['city_id']);
                                            @endphp
                                            {{ $city->name ?? 'Unknown' }}
                                        </dd>
                                    </div>
                                @endif
                                @if (isset($request->location['pincode']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Pincode/ZIP</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $request->location['pincode'] }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Quick Actions</h2>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Current Status</span>
                                <span
                                    class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                    @if ($request->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'under_review') bg-blue-100 text-blue-800
                                    @elseif($request->status === 'approved') bg-green-100 text-green-800
                                    @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                    @elseif($request->status === 'fulfilled') bg-purple-100 text-purple-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </div>

                            @if($canModify)
                                <!-- Provide Donation Button -->
                                <div class="pt-3">
                                    <livewire:volunteer.provide-donation :beneficiary="$request" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Assignment Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Assignment Information</h2>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        @if ($request->assignedTo)
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 text-sm font-medium">
                                                {{ substr($request->assignedTo->first_name ?? 'U', 0, 1) }}{{ substr($request->assignedTo->last_name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $request->assignedTo->first_name }}
                                            {{ $request->assignedTo->last_name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $request->assignedTo->phone_country_code ?? '' }}{{ $request->assignedTo->phone }}
                                        </p>
                                    </div>
                                </div>
                                @if ($request->assigned_at)
                                    <div class="text-sm text-gray-500">
                                        Assigned on {{ $request->assigned_at->format('M j, Y g:i A') }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No volunteer assigned</h3>
                                <p class="mt-1 text-sm text-gray-500">This request has not been assigned to a volunteer
                                    yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Applicant Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Applicant Information</h2>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-green-600 text-sm font-medium">
                                            @if($request->requestedBy)
                                                {{ substr($request->requestedBy->first_name ?? 'U', 0, 1) }}{{ substr($request->requestedBy->last_name ?? 'U', 0, 1) }}
                                            @else
                                                UU
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($request->requestedBy)
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $request->requestedBy->first_name ?? '' }}
                                            {{ $request->requestedBy->last_name ?? '' }}
                                        </p>
                                        @if ($request->requestedBy->email)
                                            <p class="text-sm text-gray-500">{{ $request->requestedBy->email }}</p>
                                        @endif
                                        @if ($request->requestedBy->phone)
                                            <p class="text-sm text-gray-500">
                                                {{ $request->requestedBy->phone_country_code ?? '' }}{{ $request->requestedBy->phone }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-sm font-medium text-gray-500">User not found</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Remark -->
                @if ($this->canModify)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <h2 class="text-lg font-medium text-gray-900">Add Remark</h2>
                            </div>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Remark Type</label>
                                <select wire:model="remarkType"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach ($remarkTypeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Remark</label>
                                <textarea wire:model="newRemark" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Add a remark..."></textarea>
                                @error('newRemark')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" wire:model="isInternal" id="isInternal"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="isInternal" class="ml-2 block text-sm text-gray-900">
                                    Internal remark (only visible to staff)
                                </label>
                            </div>

                            <button wire:click="addRemark"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Remark
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <h3 class="text-sm font-medium text-gray-700">Restricted Access</h3>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">You can only add remarks to requests assigned to you.</p>
                    </div>
                @endif

                <!-- Status Update Card -->
                @if ($this->canModify)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h2 class="text-lg font-medium text-gray-900">Update Status</h2>
                            </div>
                        </div>
                        <div class="px-6 py-4">
                            <form wire:submit.prevent="updateRequestStatus" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">New Status</label>
                                    <select wire:model="newStatus"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="pending">Pending</option>
                                        <option value="under_review">Under Review</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="fulfilled">Fulfilled</option>
                                    </select>
                                    @error('newStatus')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status Remark</label>
                                    <textarea wire:model="statusRemark" rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Add a remark for this status change..."></textarea>
                                    @error('statusRemark')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <h3 class="text-sm font-medium text-gray-700">Restricted Access</h3>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">You can only update status for requests assigned to you.
                        </p>
                    </div>
                @endif


            </div>
        </div>
    </div>

    <!-- Remarks History - Full Width -->
    <div class="mt-8">
        @if ($this->canViewRemarks && $request->remarks->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <h2 class="text-lg font-medium text-gray-900">Remarks History</h2>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach ($request->remarks->sortByDesc('created_at') as $index => $remark)
                                <li>
                                    <div class="relative pb-8">
                                        @if (!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <span class="text-white text-xs font-medium">
                                                        {{ substr($remark->user->first_name ?? 'U', 0, 1) }}{{ substr($remark->user->last_name ?? 'U', 0, 1) }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <div class="flex items-center space-x-2">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $remark->user->first_name }}
                                                            {{ $remark->user->last_name }}
                                                        </p>
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if ($remark->type === 'status_update') bg-blue-100 text-blue-800
                                                    @elseif($remark->type === 'progress') bg-yellow-100 text-yellow-800
                                                    @elseif($remark->type === 'completion') bg-green-100 text-green-800
                                                    @elseif($remark->type === 'cancellation') bg-red-100 text-red-800
                                                    @elseif($remark->type === 'assignment') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                            {{ ucfirst(str_replace('_', ' ', $remark->type)) }}
                                                        </span>
                                                        @if ($remark->is_internal)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                                        clip-rule="evenodd"></path>
                                                                </svg>
                                                                Internal
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-700">
                                                        {{ $remark->remark }}</p>
                                                </div>
                                                <div
                                                    class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $remark->created_at->toISOString() }}">
                                                        {{ $remark->created_at->format('M j, Y g:i A') }}
                                                    </time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @elseif($this->canViewRemarks)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <h2 class="text-lg font-medium text-gray-900">Remarks History</h2>
                    </div>
                </div>
                <div class="px-6 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No remarks yet</h3>
                    <p class="mt-1 text-sm text-gray-500">No remarks have been added to this request.</p>
                </div>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <h3 class="text-sm font-medium text-gray-700">Restricted Access</h3>
                </div>
                <p class="mt-2 text-sm text-gray-500">You can only view remarks for requests assigned to you.
                </p>
            </div>
        @endif
    </div>
</div>
