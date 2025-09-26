<div>
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Donation History for {{ $beneficiary->name }}
            </h3>

            @if($donationHistories->count() > 0)
                <div class="space-y-4">
                    @foreach($donationHistories as $history)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->getTypeIcon($history->donation_type) }}"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ ucfirst($history->donation_type) }} Donation
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            From: {{ $history->donation->donor->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Provided by: {{ $history->providedBy->name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($history->status) }}">
                                        {{ ucfirst($history->status) }}
                                    </span>
                                    @if($history->status === 'pending')
                                        <button wire:click="approveDonation({{ $history->id }})"
                                                class="text-green-600 hover:text-green-900 text-sm font-medium">
                                            Approve
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                                @if($history->donation_type === 'monetary')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                        <dd class="text-sm text-gray-900">{{ $history->formatted_amount }}</dd>
                                    </div>
                                    @if($history->converted_amount)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Converted Amount</dt>
                                            <dd class="text-sm text-gray-900">{{ $history->formatted_converted_amount }}</dd>
                                        </div>
                                    @endif
                                    @if($history->account)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Account</dt>
                                            <dd class="text-sm text-gray-900">{{ $history->account->name }}</dd>
                                        </div>
                                    @endif
                                @elseif($history->donation_type === 'materialistic')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                                        <dd class="text-sm text-gray-900">{{ $history->formatted_quantity }}</dd>
                                    </div>
                                    @if($history->description)
                                        <div class="md:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                                            <dd class="text-sm text-gray-900">{{ $history->description }}</dd>
                                        </div>
                                    @endif
                                @elseif($history->donation_type === 'service')
                                    <div class="md:col-span-3">
                                        <dt class="text-sm font-medium text-gray-500">Service Description</dt>
                                        <dd class="text-sm text-gray-900">{{ $history->description }}</dd>
                                    </div>
                                @endif
                            </div>

                            @if($history->notes)
                                <div class="mt-3">
                                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                    <dd class="text-sm text-gray-900">{{ $history->notes }}</dd>
                                </div>
                            @endif

                            <div class="mt-3 text-xs text-gray-500">
                                Provided: {{ $history->provided_at ? $history->provided_at->format('M d, Y H:i') : 'N/A' }}
                                @if($history->approved_at)
                                    | Approved: {{ $history->approved_at->format('M d, Y H:i') }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No donations provided yet</h3>
                    <p class="mt-1 text-sm text-gray-500">No donations have been provided to this beneficiary yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Approval Confirmation Modal -->
    @if($showApprovalModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Approve Donation</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to approve this donation? This action will deduct the amount from the selected account and create a transaction record.</p>

                <div class="flex justify-end space-x-3">
                    <button wire:click="cancelApproval" class="px-4 py-2 border rounded">
                        Cancel
                    </button>
                    <button wire:click="confirmApproval" class="px-4 py-2 bg-green-600 text-white rounded">
                        Approve
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
