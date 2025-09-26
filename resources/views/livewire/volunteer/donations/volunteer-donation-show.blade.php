<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

        <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @if($donation->type === 'monetary')
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    @elseif($donation->type === 'materialistic')
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Donation #{{ $donation->id }}</h1>
                    <p class="text-sm text-gray-500">{{ ucfirst($donation->type) }} • {{ $donation->created_at->format('M j, Y') }}</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('volunteer.donations.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Donations
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
            <!-- Donation Details -->
            <x-donation-details :donation="$donation" />

                <!-- Materialistic Donation Images -->
                @if($donation->type === 'materialistic' && isset($donation->details['images']) && is_array($donation->details['images']) && count($donation->details['images']) > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Item Images</h2>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($donation->details['images'] as $index => $image)
                                <div class="relative group cursor-pointer">
                                    <a href="{{ asset('storage/' . $image) }}"
                                       data-fancybox="donation-gallery"
                                       data-caption="{{ is_array($donation->details['item_name'] ?? null) ? implode(', ', $donation->details['item_name']) : ($donation->details['item_name'] ?? 'Donation Item') }} - Image {{ $index + 1 }}">
                                        <img src="{{ asset('storage/' . $image) }}"
                                             alt="{{ is_array($donation->details['item_name'] ?? null) ? implode(', ', $donation->details['item_name']) : ($donation->details['item_name'] ?? 'Donation Item') }} - Image {{ $index + 1 }}"
                                             class="w-full h-48 object-cover rounded-lg border hover:shadow-lg transition-shadow duration-200">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                            <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center">
                                                <svg class="w-8 h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span class="text-sm font-medium">Click to enlarge</span>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            <!-- Donation Type Specific Details -->
            @if($donation->type === 'materialistic')
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Item Details</h2>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Item Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($donation->details['item_name']))
                                        @if(is_array($donation->details['item_name']))
                                            {{ implode(', ', $donation->details['item_name']) }}
                                        @else
                                            {{ $donation->details['item_name'] }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Alternate Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($donation->details['alternate_phone']))
                                        @if(is_array($donation->details['alternate_phone']))
                                            {{ implode(', ', $donation->details['alternate_phone']) }}
                                        @else
                                            {{ $donation->details['alternate_phone'] }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($donation->details['item_description']))
                                        @if(is_array($donation->details['item_description']))
                                            {{ implode(', ', $donation->details['item_description']) }}
                                        @else
                                            {{ $donation->details['item_description'] }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                            </div>
            @elseif($donation->type === 'monetary')
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Monetary Details</h2>
                        </div>
                                </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($donation->details['amount']))
                                        @if(is_array($donation->details['amount']))
                                            ${{ number_format(array_sum($donation->details['amount']), 2) }}
                                        @else
                                            ${{ number_format($donation->details['amount'], 2) }}
                                        @endif
                                    @else
                                        $0.00
                                    @endif
                                </dd>
                            </div>
                                <div>
                                <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($donation->details['payment_method']))
                                        @if(is_array($donation->details['payment_method']))
                                            {{ ucfirst(str_replace('_', ' ', implode(', ', $donation->details['payment_method']))) }}
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $donation->details['payment_method'])) }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @elseif($donation->type === 'service')
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Service Details</h2>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                <dt class="text-sm font-medium text-gray-500">Service Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($donation->details['service_type']))
                                        @if(is_array($donation->details['service_type']))
                                            {{ implode(', ', $donation->details['service_type']) }}
                                        @else
                                            {{ $donation->details['service_type'] }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </dd>
                                </div>
                                <div>
                                <dt class="text-sm font-medium text-gray-500">Availability</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($donation->details['availability']))
                                        @if(is_array($donation->details['availability']))
                                            {{ implode(', ', $donation->details['availability']) }}
                                        @else
                                            {{ $donation->details['availability'] }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </dd>
                                </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($donation->details['service_description']))
                                        @if(is_array($donation->details['service_description']))
                                            {{ implode(', ', $donation->details['service_description']) }}
                                        @else
                                            {{ $donation->details['service_description'] }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if($donation->notes)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <h2 class="text-lg font-medium text-gray-900">Notes</h2>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-900">{{ $donation->notes }}</p>
                    </div>
                </div>
            @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
            <!-- Quick Actions -->
            <x-quick-actions :donation="$donation" :canModify="$this->canModify" />

            <!-- Add Remark -->
            <x-add-remark :canModify="$this->canModify" />

            <!-- Status Update Card -->
            <x-status-update :donation="$donation" :accounts="$accounts" :canModify="$this->canModify" :newStatus="$newStatus" :statusOptions="$statusOptions" />

            <!-- Assignment Card -->
            <x-assignment-info :donation="$donation" :canModify="false" />


        </div>
    </div>

    <!-- Remarks History - Full Width -->
    <div class="mt-8">
        <x-remarks-history :donation="$donation" :canViewRemarks="$this->canViewRemarks" />
    </div>
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
