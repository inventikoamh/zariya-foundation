<div>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Donation Details</h1>
                @if(auth()->user()->hasRole('SUPER_ADMIN'))
                    <a href="{{ route('admin.donations.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Back to All Donations
                    </a>
                @elseif(auth()->user()->hasRole('VOLUNTEER'))
                    <a href="{{ route('volunteer.donations.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Back to Assigned Donations
                    </a>
                @else
                    <a href="{{ route('my-donations') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Back to My Donations
                    </a>
                @endif
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
                            Donation #{{ $donation->id }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            Submitted on {{ $donation->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($donation->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($donation->status === 'assigned') bg-blue-100 text-blue-800
                            @elseif($donation->status === 'in_progress') bg-indigo-100 text-indigo-800
                            @elseif($donation->status === 'completed') bg-green-100 text-green-800
                            @elseif($donation->status === 'cancelled') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $donation->status)) }}
                        </span>
                        @if($donation->is_urgent)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Urgent
                            </span>
                        @endif
                        @if(in_array($donation->status, ['pending', 'assigned']) && $donation->donor_id === auth()->id())
                            <button wire:click="openCancelModal"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Cancel Donation
                            </button>
                        @endif
                        @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasRole('VOLUNTEER'))
                            <button wire:click="showStatusUpdateModal"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Status
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 space-y-6">
                <!-- Donation Type and Details -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Donation Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($donation->type === 'monetary') bg-green-100 text-green-800
                                    @elseif($donation->type === 'materialistic') bg-blue-100 text-blue-800
                                    @elseif($donation->type === 'service') bg-purple-100 text-purple-800
                                    @endif">
                                    {{ ucfirst($donation->type) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($donation->priority == 1) Low
                                @elseif($donation->priority == 2) Medium
                                @elseif($donation->priority == 3) High
                                @elseif($donation->priority == 4) Critical
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Type-specific details -->
                    @if($donation->type === 'monetary')
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount</label>
                                @php
                                    $currencySymbol = [
                                        'INR' => '₹',
                                        'USD' => '$',
                                        'EUR' => '€',
                                        'GBP' => '£',
                                    ][$donation->currency ?? ''] ?? ($donation->currency ?? '')
                                @endphp
                                <p class="mt-1 text-sm text-gray-900">{{ $currencySymbol }} {{ number_format($donation->amount ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $donation->details['payment_method'] ?? '')) }}</p>
                            </div>
                        </div>
                    @elseif($donation->type === 'materialistic')
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Item Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->details['item_name'] ?? '' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->details['item_description'] ?? '' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alternate Phone</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->details['alternate_phone'] ?? '' }}</p>
                            </div>
                            @if(isset($donation->details['images']) && count($donation->details['images']) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Images</label>
                                    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                                        @foreach($donation->details['images'] as $index => $image)
                                            <div class="relative group cursor-pointer">
                                                <a href="{{ Storage::url($image) }}"
                                                   data-fancybox="donation-gallery"
                                                   data-caption="{{ $donation->details['item_name'] ?? 'Donation Item' }} - Image {{ $index + 1 }}">
                                                    <img src="{{ Storage::url($image) }}"
                                                         alt="{{ $donation->details['item_name'] ?? 'Donation Item' }} - Image {{ $index + 1 }}"
                                                         class="w-full h-24 object-cover rounded-lg border hover:shadow-lg transition-shadow duration-200">
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                        <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center">
                                                            <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                            <span class="text-xs font-medium">Click to enlarge</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($donation->type === 'service')
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service Type</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->details['service_type'] ?? '' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->details['service_description'] ?? '' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Availability</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->details['availability'] ?? '' }}</p>
                            </div>
                        </div>
                    @endif

                    @if($donation->notes)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $donation->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Location Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Location Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($donation->country)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Country</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->country->name }}</p>
                            </div>
                        @endif
                        @if($donation->state)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">State</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->state->name }}</p>
                            </div>
                        @endif
                        @if($donation->city)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">City</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $donation->city->name }}</p>
                            </div>
                        @endif
                    </div>
                    @if($donation->pincode)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Pincode</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $donation->pincode }}</p>
                        </div>
                    @endif
                    @if($donation->address)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $donation->address }}</p>
                        </div>
                    @endif
                </div>

                <!-- Assignment Information -->
                @if($donation->assignedTo)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Assigned Volunteer</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 text-sm font-medium">
                                            {{ substr($donation->assignedTo->first_name ?? 'U', 0, 1) }}{{ substr($donation->assignedTo->last_name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-900">
                                        {{ $donation->assignedTo->first_name }} {{ $donation->assignedTo->last_name }}
                                    </p>
                                    <p class="text-sm text-blue-700">{{ $donation->assignedTo->phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status History -->
                @if($donation->remarks && $donation->remarks->where('is_internal', false)->count() > 0)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status History</h3>
                        <div class="space-y-4">
                            @foreach($donation->remarks->where('is_internal', false)->sortBy('created_at') as $remark)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 text-sm font-medium">
                                                    {{ substr($remark->user->first_name ?? 'U', 0, 1) }}{{ substr($remark->user->last_name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $remark->user->first_name }} {{ $remark->user->last_name }}
                                                    </p>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($remark->type === 'general') bg-gray-100 text-gray-800
                                                        @elseif($remark->type === 'status_update') bg-blue-100 text-blue-800
                                                        @elseif($remark->type === 'assignment') bg-purple-100 text-purple-800
                                                        @elseif($remark->type === 'cancellation') bg-red-100 text-red-800
                                                        @elseif($remark->type === 'completion') bg-green-100 text-green-800
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

    <!-- Status Update Modal -->
    @if($showStatusModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeStatusModal">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div class="mt-2 px-7 py-3">
                        <h3 class="text-lg font-medium text-gray-900 text-center">Update Donation Status</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500 text-center">
                                Update the status of this donation and provide a remark for the change.
                            </p>
                        </div>

                        <form wire:submit.prevent="updateDonationStatus" class="mt-4">
                            <div class="space-y-4">
                                <!-- Status Selection -->
                                <div>
                                    <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">
                                        New Status *
                                    </label>
                                    <select
                                        wire:model="newStatus"
                                        id="newStatus"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    >
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('newStatus')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Account Selection (for completed monetary donations) -->
                                @if($newStatus === 'completed' && $donation->type === 'monetary')
                                    <div>
                                        <label for="selectedAccountId" class="block text-sm font-medium text-gray-700 mb-2">
                                            Select Account *
                                        </label>
                                        <select
                                            wire:model="selectedAccountId"
                                            id="selectedAccountId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">Select an account</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->name }} ({{ $account->currency }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedAccountId')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Currency Conversion (if currencies differ) -->
                                    @if($selectedAccountId)
                                        @php
                                            $selectedAccount = $accounts->firstWhere('id', $selectedAccountId);
                                        @endphp
                                        @if($selectedAccount && $selectedAccount->currency !== $donation->currency)
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                <h4 class="text-sm font-medium text-blue-900 mb-3">Currency Conversion Required</h4>
                                                <p class="text-sm text-blue-700 mb-3">
                                                    Converting from {{ $donation->currency }} to {{ $selectedAccount->currency }}
                                                </p>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="exchangeRate" class="block text-sm font-medium text-gray-700 mb-1">
                                                            Exchange Rate *
                                                        </label>
                                                        <input
                                                            type="number"
                                                            wire:model.live="exchangeRate"
                                                            id="exchangeRate"
                                                            step="0.000001"
                                                            min="0.000001"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                            placeholder="Enter exchange rate"
                                                        >
                                                        @error('exchangeRate')
                                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label for="convertedAmount" class="block text-sm font-medium text-gray-700 mb-1">
                                                            Converted Amount ({{ $selectedAccount->currency }})
                                                        </label>
                                                        <input
                                                            type="number"
                                                            wire:model="convertedAmount"
                                                            id="convertedAmount"
                                                            step="0.01"
                                                            min="0.01"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                                                            placeholder="Auto-calculated"
                                                            readonly
                                                        >
                                                        @error('convertedAmount')
                                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mt-2">
                                                    <input
                                                        type="hidden"
                                                        wire:model="convertedCurrency"
                                                        value="{{ $selectedAccount->currency }}"
                                                    >
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endif

                                <!-- Status Remark -->
                                <div>
                                    <label for="statusRemark" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status Remark *
                                    </label>
                                    <textarea
                                        wire:model="statusRemark"
                                        id="statusRemark"
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Please provide details about this status change..."
                                        required
                                    ></textarea>
                                    @error('statusRemark')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button
                                    type="button"
                                    wire:click="closeStatusModal"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Cancellation Modal -->
    @if($showCancelModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeCancelModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="mt-2 px-7 py-3">
                        <h3 class="text-lg font-medium text-gray-900 text-center">Cancel Donation</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500 text-center">
                                Please provide a reason for cancelling this donation. This information will be recorded for our records.
                            </p>
                        </div>

                        <form wire:submit.prevent="cancelDonation" class="mt-4">
                            <div>
                                <label for="cancellationReason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cancellation Reason *
                                </label>
                                <textarea
                                    wire:model="cancellationReason"
                                    id="cancellationReason"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="Please explain why you want to cancel this donation..."
                                    required
                                ></textarea>
                                @error('cancellationReason')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button
                                    type="button"
                                    wire:click="closeCancelModal"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                                >
                                    Keep Donation
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                >
                                    Cancel Donation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Fancybox.bind("[data-fancybox]", {
            // Options
            Toolbar: {
                display: {
                    left: ["infobar"],
                    middle: [
                        "zoomIn",
                        "zoomOut",
                        "toggle1to1",
                        "rotateCCW",
                        "rotateCW",
                        "flipX",
                        "flipY",
                    ],
                    right: ["slideshow", "thumbs", "close"],
                },
            },
            Thumbs: {
                autoStart: false,
            },
        });
    });
</script>
