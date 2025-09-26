<div>
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Request Details</h1>
                    <p class="text-sm text-gray-600">View and track your assistance request</p>
                </div>
                <a href="{{ route('volunteer.my-requests') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Back to Requests
                </a>
            </div>
        </div>

        <!-- Request Information -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Request Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Name</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->name }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Email</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->email }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Phone</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->phone_country_code }}{{ $beneficiary->phone }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Category</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($beneficiary->category) }}
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Status</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $beneficiary->status_badge_class }}">
                            {{ ucfirst(str_replace('_', ' ', $beneficiary->status)) }}
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Priority</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $beneficiary->priority_badge_class }}">
                            {{ ucfirst($beneficiary->priority) }}
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Assigned To</h4>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($beneficiary->assignedTo)
                                {{ $beneficiary->assignedTo->name }}
                            @else
                                Not assigned
                            @endif
                        </p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Request Date</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($beneficiary->description)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500">Description</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->description }}</p>
                    </div>
                @endif

                @if($beneficiary->urgency_notes)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500">Urgency Notes</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->urgency_notes }}</p>
                    </div>
                @endif

                @if($beneficiary->estimated_amount)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500">Estimated Amount</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $beneficiary->currency }} {{ number_format($beneficiary->estimated_amount, 2) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Remarks/Updates -->
        @if($beneficiary->remarks->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Updates & Remarks</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($beneficiary->remarks as $remark)
                            <div class="border-l-4 border-blue-400 pl-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-900">{{ $remark->remarks }}</p>
                                    <span class="text-xs text-gray-500">{{ $remark->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                @if($remark->createdBy)
                                    <p class="text-xs text-gray-500 mt-1">by {{ $remark->createdBy->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
