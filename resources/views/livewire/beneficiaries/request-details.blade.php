<div>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Request Details</h1>
                <a href="{{ route('my-requests') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Back to My Requests
                </a>
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

        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">
                            Request #{{ $beneficiary->id }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            Submitted on {{ $beneficiary->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $beneficiary->status_badge_class }}">
                            {{ ucfirst(str_replace('_', ' ', $beneficiary->status)) }}
                        </span>
                        @if($beneficiary->priority === 'urgent')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Urgent
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 space-y-6">
                <!-- Request Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Request Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($beneficiary->category) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $beneficiary->priority_badge_class }}">
                                    {{ ucfirst($beneficiary->priority) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($beneficiary->estimated_amount)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Estimated Amount</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->currency }} {{ number_format($beneficiary->estimated_amount, 2) }}</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->description }}</p>
                    </div>

                    @if($beneficiary->urgency_notes)
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Urgency Notes</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>{{ $beneficiary->urgency_notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($beneficiary->additional_info)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Additional Information</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->additional_info }}</p>
                        </div>
                    @endif
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->email ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->phone ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Requested By</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->requestedBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                @if($beneficiary->location)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Location Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Country</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->location['country_id'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">State</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->location['state_id'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">City</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->location['city_id'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($beneficiary->location['pincode'])
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Pincode</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->location['pincode'] }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Assignment Information -->
                @if($beneficiary->assignedTo)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Assigned Volunteer</h3>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-green-600 text-sm font-medium">
                                            {{ substr($beneficiary->assignedTo->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-900">
                                        {{ $beneficiary->assignedTo->name }}
                                    </p>
                                    @if($beneficiary->assignedTo->email)
                                        <p class="text-sm text-green-700">{{ $beneficiary->assignedTo->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status History -->
                @if($beneficiary->remarks && $beneficiary->remarks->where('is_internal', false)->count() > 0)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status History</h3>
                        <div class="space-y-4">
                            @foreach($beneficiary->remarks->where('is_internal', false)->sortBy('created_at') as $remark)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <span class="text-green-600 text-sm font-medium">
                                                    {{ substr($remark->user->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $remark->user->name }}
                                                    </p>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($remark->type === 'general') bg-gray-100 text-gray-800
                                                        @elseif($remark->type === 'status_update') bg-blue-100 text-blue-800
                                                        @elseif($remark->type === 'assignment') bg-purple-100 text-purple-800
                                                        @elseif($remark->type === 'progress') bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $remark->type)) }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    {{ $remark->created_at->format('M d, Y g:i A') }}
                                                </p>
                                            </div>
                                            <p class="text-sm text-gray-700 leading-relaxed">{{ $remark->remark }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
