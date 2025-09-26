<div>
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-900">My Assigned Donations</h1>
                <div class="text-sm text-gray-600">
                    Manage donations assigned to you
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

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" wire:model.live.debounce.300ms="search"
                               placeholder="Search donations, donors..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="statusFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select wire:model.live="typeFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select wire:model.live="priorityFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Priorities</option>
                            @foreach($priorityOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donations List -->
        <div class="bg-white shadow rounded-lg">
            @if($donations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Donation Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Donor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($donations as $donation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($donation->type === 'monetary')
                                                ${{ number_format($donation->details['amount'] ?? 0, 2) }} {{ $donation->details['payment_method'] ?? '' }}
                                            @elseif($donation->type === 'materialistic')
                                                {{ $donation->details['item_name'] ?? '' }}
                                            @elseif($donation->type === 'service')
                                                {{ $donation->details['service_type'] ?? '' }}
                                            @endif
                                        </div>
                                        @if($donation->notes)
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ Str::limit($donation->notes, 50) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $donation->donor->first_name ?? '' }} {{ $donation->donor->last_name ?? '' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $donation->donor->phone ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($donation->type === 'monetary') bg-green-100 text-green-800
                                            @elseif($donation->type === 'materialistic') bg-blue-100 text-blue-800
                                            @elseif($donation->type === 'service') bg-purple-100 text-purple-800
                                            @endif">
                                            {{ ucfirst($donation->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \App\Services\StatusHelper::getStatusBadgeClass($donation->status, $donation->type) }}">
                                            {{ $donation->status_label }}
                                        </span>
                                        @if($donation->is_urgent)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Urgent
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($donation->priority == 1) bg-gray-100 text-gray-800
                                            @elseif($donation->priority == 2) bg-yellow-100 text-yellow-800
                                            @elseif($donation->priority == 3) bg-orange-100 text-orange-800
                                            @elseif($donation->priority == 4) bg-red-100 text-red-800
                                            @endif">
                                            {{ $priorityOptions[$donation->priority] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($donation->city)
                                            {{ $donation->city->name }}, {{ $donation->state->name }}
                                        @elseif($donation->state)
                                            {{ $donation->state->name }}, {{ $donation->country->name }}
                                        @elseif($donation->country)
                                            {{ $donation->country->name }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $donation->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('volunteer.donations.show', $donation->id) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                View
                                            </a>
                                            <button wire:click="showAddRemarkModal({{ $donation->id }})"
                                                    class="text-purple-600 hover:text-purple-900">
                                                Add Note
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $donations->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No donations assigned</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any donations assigned to you yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Status Update Modal -->
    @if($showStatusModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeStatusModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
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
                                Update the status of this donation and provide a remark.
                            </p>
                        </div>

                        <form wire:submit.prevent="updateDonationStatus" class="mt-4">
                            <div class="mb-4">
                                <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Status *
                                </label>
                                <select wire:model="newStatus" id="newStatus"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Status</option>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('newStatus')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Account Selection for Monetary Donations -->
                            @if($donationToUpdate && $donationToUpdate->type === 'monetary' && $newStatus === 'completed')
                                <div class="mb-4">
                                    <label for="selectedAccountId" class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Account for Donation *
                                    </label>
                                    <select wire:model.live="selectedAccountId" id="selectedAccountId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->formatted_balance }})</option>
                                        @endforeach
                                    </select>
                                    @error('selectedAccountId')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Currency Conversion for Monetary Donations -->
                                @if($selectedAccountId && $donationToUpdate)
                                    @php
                                        $selectedAccount = $accounts->firstWhere('id', $selectedAccountId);
                                    @endphp
                                    @if($selectedAccount && $selectedAccount->currency !== $donationToUpdate->currency)
                                        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                            <h4 class="text-sm font-medium text-yellow-800 mb-3">Currency Conversion Required</h4>
                                            <p class="text-sm text-yellow-700 mb-3">
                                                The donation currency ({{ $donationToUpdate->currency }}) differs from the account currency ({{ $selectedAccount->currency }}).
                                                Please provide exchange rate information.
                                            </p>

                                            <div class="grid grid-cols-1 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Exchange Rate *</label>
                                                    <input type="number" step="0.000001" wire:model="exchangeRate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 0.85">
                                                    @error('exchangeRate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                    <p class="text-xs text-gray-500 mt-1">{{ $donationToUpdate->currency }} to {{ $selectedAccount->currency }} - Required when currencies differ</p>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Converted Amount</label>
                                                    <input type="number" step="0.01" wire:model="convertedAmount" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600" placeholder="Auto-calculated">
                                                    @error('convertedAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                    <p class="text-xs text-gray-500 mt-1">Automatically calculated from donation amount × exchange rate</p>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Converted Currency</label>
                                                    <input type="text" value="{{ $selectedAccount->currency }}" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600">
                                                    <p class="text-xs text-gray-500 mt-1">Account currency: {{ $selectedAccount->currency }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif

                            <div class="mb-4">
                                <label for="statusRemark" class="block text-sm font-medium text-gray-700 mb-2">
                                    Remark *
                                </label>
                                <textarea wire:model="statusRemark" id="statusRemark" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Provide details about this status change..."
                                          required></textarea>
                                @error('statusRemark')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="button" wire:click="closeStatusModal"
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Remark Modal -->
    @if($showRemarkModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeRemarkModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                    </div>
                    <div class="mt-2 px-7 py-3">
                        <h3 class="text-lg font-medium text-gray-900 text-center">Add Remark</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500 text-center">
                                Add a note or remark for this donation.
                            </p>
                        </div>

                        <form wire:submit.prevent="addRemark" class="mt-4">
                            <div class="mb-4">
                                <label for="remarkType" class="block text-sm font-medium text-gray-700 mb-2">
                                    Remark Type *
                                </label>
                                <select wire:model="remarkType" id="remarkType"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    @foreach($remarkTypeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('remarkType')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="remarkContent" class="block text-sm font-medium text-gray-700 mb-2">
                                    Remark Content *
                                </label>
                                <textarea wire:model="remarkContent" id="remarkContent" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                          placeholder="Enter your remark..."
                                          required></textarea>
                                @error('remarkContent')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="isInternal" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Internal note (not visible to donor)</span>
                                </label>
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="button" wire:click="closeRemarkModal"
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    Add Remark
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
