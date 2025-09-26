@props(['donation', 'accounts', 'canModify' => true, 'newStatus' => '', 'statusOptions' => []])

@if($canModify)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <h2 class="text-lg font-medium text-gray-900">Update Status</h2>
            </div>
        </div>
        <div class="px-6 py-4">
            <form wire:submit.prevent="updateDonationStatus" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
                    <select wire:model.live="newStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Status</option>
                        @if(is_array($statusOptions))
                            @foreach($statusOptions as $value => $label)
                                @if(is_string($value) && is_string($label))
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @elseif(is_numeric($value) && is_string($label))
                                    <option value="{{ $label }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="pending">Pending</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        @endif
                    </select>
                    @error('newStatus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Account Selection for Monetary Donations when completing -->
                @if($donation->type === 'monetary' && $newStatus === 'completed')
                    <x-account-selection
                        :donation="$donation"
                        :accounts="$accounts"
                        :selectedAccountId="$wire->selectedAccountId ?? ''"
                        :exchangeRate="$wire->exchangeRate ?? ''"
                        :convertedAmount="$wire->convertedAmount ?? ''"
                    />
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remark (Optional)</label>
                    <textarea wire:model="statusRemark" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Add a remark about this status change..."></textarea>
                </div>

                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="updateDonationStatus"
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <div wire:loading.remove wire:target="updateDonationStatus">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Status
                    </div>
                    <div wire:loading wire:target="updateDonationStatus" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating...
                    </div>
                </button>
            </form>
        </div>
    </div>
@else
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <h3 class="text-sm font-medium text-gray-700">Restricted Access</h3>
        </div>
        <p class="mt-2 text-sm text-gray-500">You can only update status for donations assigned to you.</p>
    </div>
@endif
