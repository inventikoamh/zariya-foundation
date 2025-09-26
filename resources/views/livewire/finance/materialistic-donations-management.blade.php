<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Materialistic Donations Management</h2>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        <!-- Total Donations Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Donations</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $summary['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Status Cards -->
        @foreach(\App\Services\StatusHelper::getStatuses('materialistic') as $status)
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center" style="background-color: {{ $status->color }}">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ $status->display_name }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $summary[$status->name] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model="filters.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Volunteer</label>
                <select wire:model="filters.volunteer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Volunteers</option>
                    @foreach($volunteers as $volunteer)
                        <option value="{{ $volunteer->id }}">{{ $volunteer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" wire:model="filters.date_from" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Date To</label>
                <input type="date" wire:model="filters.date_to" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Donations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($donations as $donation)
            <a href="{{ route('admin.donations.show', $donation->id) }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 cursor-pointer overflow-hidden">
                <!-- Item Image -->
                @if(isset($donation->details['images']) && is_array($donation->details['images']) && count($donation->details['images']) > 0)
                    <div class="h-48 bg-gray-200 relative overflow-hidden">
                        <img src="{{ asset('storage/' . $donation->details['images'][0]) }}"
                             alt="{{ $donation->details['item_name'] ?? 'Donation Item' }}"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-200">
                        <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                            {{ count($donation->details['images']) }} image{{ count($donation->details['images']) > 1 ? 's' : '' }}
                        </div>
                    </div>
                @else
                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                        <div class="text-center text-gray-400">
                            <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">No Image</p>
                        </div>
                    </div>
                @endif

                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="flex justify-between items-start mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \App\Services\StatusHelper::getStatusBadgeClass($donation->status, 'materialistic') }}">
                            {{ \App\Services\StatusHelper::getStatusDisplayName($donation->status, 'materialistic') }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $donation->created_at->format('M d, Y') }}</span>
                    </div>

                    <!-- Item Details -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $donation->details['item_name'] ?? 'Unknown Item' }}
                        </h3>
                        @if(isset($donation->details['estimated_value']))
                            <p class="text-sm text-gray-600">
                                <strong>Estimated Value:</strong> ${{ number_format($donation->details['estimated_value'], 2) }}
                            </p>
                        @endif
                    </div>

                    <!-- Donor Info -->
                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-600 mb-1">
                            <strong>Donor:</strong> {{ $donation->donor->name ?? 'Unknown' }}
                        </p>
                        @if($donation->assignedTo)
                            <p class="text-sm text-gray-600">
                                <strong>Volunteer:</strong> {{ $donation->assignedTo->name }}
                            </p>
                        @endif
                    </div>

                    <!-- Notes Preview -->
                    @if($donation->notes)
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-xs text-gray-500">
                                {{ Str::limit($donation->notes, 80) }}
                            </p>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No materialistic donations found</h3>
                <p class="mt-1 text-sm text-gray-500">No donations match your current filters.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($donations->hasPages())
        <div class="mt-6">
            {{ $donations->links() }}
        </div>
    @endif

</div>
