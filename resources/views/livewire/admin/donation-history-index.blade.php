<div>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Donation History</h1>
            <p class="mt-2 text-sm text-gray-700">View and manage all donation history records.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="mt-6 bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text"
                       wire:model.live="search"
                       id="search"
                       placeholder="Search by beneficiary or donor name..."
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model.live="statusFilter"
                        id="statusFilter"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label for="typeFilter" class="block text-sm font-medium text-gray-700">Type</label>
                <select wire:model.live="typeFilter"
                        id="typeFilter"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Types</option>
                    <option value="monetary">Monetary</option>
                    <option value="materialistic">Materialistic</option>
                    <option value="service">Service</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($donationHistories as $history)
                <li class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->getTypeIcon($history->type) }}"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $history->beneficiary->name ?? 'Unknown Beneficiary' }}
                                    </p>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($history->status) }}">
                                        {{ ucfirst($history->status) }}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    <p>Donor: {{ $history->donation->donor->name ?? 'Unknown' }}</p>
                                    <p>Type: {{ ucfirst($history->type) }}</p>
                                    @if($history->amount)
                                        <p>Amount: ${{ number_format($history->amount, 2) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <time datetime="{{ $history->created_at->toISOString() }}">
                                {{ $history->created_at->format('M j, Y g:i A') }}
                            </time>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No donation history found</h3>
                    <p class="mt-1 text-sm text-gray-500">No donation history records match your current filters.</p>
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Pagination -->
    @if($donationHistories->hasPages())
        <div class="mt-6">
            {{ $donationHistories->links() }}
        </div>
    @endif
</div>
