<div>
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Achievement Management</h1>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.achievements.guide') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Creation Guide
                </a>
                <button wire:click="openCreateModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Achievement
                </button>
            </div>
        </div>

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

        <!-- Help Section -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Need help creating achievements?</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Check out our comprehensive <a href="{{ route('admin.achievements.guide') }}" class="font-medium underline hover:text-blue-600">Achievement Creation Guide</a> for step-by-step instructions, examples, and best practices.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select wire:model.live="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($typeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select wire:model.live="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categoryOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rarity</label>
                    <select wire:model.live="rarityFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Rarities</option>
                        @foreach($rarityOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Achievements Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Achievement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rarity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($achievements as $achievement)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <img class="h-12 w-12 object-cover rounded-lg border"
                                             src="{{ $achievement->icon_image_url }}"
                                             alt="{{ $achievement->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $achievement->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($achievement->description, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $achievement->type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $achievement->category_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $rarityColors = [
                                        'common' => 'bg-gray-100 text-gray-800',
                                        'uncommon' => 'bg-green-100 text-green-800',
                                        'rare' => 'bg-blue-100 text-blue-800',
                                        'epic' => 'bg-purple-100 text-purple-800',
                                        'legendary' => 'bg-yellow-100 text-yellow-800',
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $rarityColors[$achievement->rarity] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $achievement->rarity_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $achievement->points }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $achievement->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $achievement->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="openEditModal({{ $achievement->id }})"
                                            class="text-blue-600 hover:text-blue-900">Edit</button>

                                    <button wire:click="toggleStatus({{ $achievement->id }})"
                                            class="{{ $achievement->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                        {{ $achievement->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>

                                    <button wire:click="delete({{ $achievement->id }})"
                                            wire:confirm="Are you sure you want to delete this achievement?"
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No achievements found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $achievements->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Create Achievement</h3>
                        <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="create" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h4 class="text-md font-medium text-gray-900">Basic Information</h4>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input type="text" wire:model="name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea wire:model="description" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select wire:model="type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Type</option>
                                        @foreach($typeOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select wire:model="category"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Category</option>
                                        @foreach($categoryOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon Image</label>
                                    <input type="file" wire:model="icon_image" accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('icon_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 mt-1">Upload a square icon image (recommended: 64x64px)</p>
                                </div>
                            </div>

                            <!-- Criteria and Settings -->
                            <div class="space-y-4">
                                <h4 class="text-md font-medium text-gray-900">Criteria & Settings</h4>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Criteria Type</label>
                                    <select wire:model="criteria_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Criteria Type</option>
                                        @foreach($criteriaTypeOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Dynamic Criteria Fields -->
                                @if($criteria_type)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-900 mb-3">Criteria Configuration</h5>

                                        @if($criteria_type === 'donation_amount')
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Amount</label>
                                                    <input type="number" wire:model="criteria_data.min_amount" min="0" step="0.01"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                           placeholder="0.00">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                                    <select wire:model="criteria_data.currency"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="USD">USD</option>
                                                        <option value="EUR">EUR</option>
                                                        <option value="GBP">GBP</option>
                                                        <option value="INR">INR</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Donation Type (Optional)</label>
                                                <select wire:model="criteria_data.donation_type"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="">Any Type</option>
                                                    <option value="monetary">Monetary</option>
                                                    <option value="materialistic">Materialistic</option>
                                                    <option value="service">Service</option>
                                                </select>
                                            </div>
                                        @endif

                                        @if($criteria_type === 'donation_count')
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Count</label>
                                                    <input type="number" wire:model="criteria_data.min_count" min="1"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                           placeholder="1">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                    <select wire:model="criteria_data.status"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="completed">Completed</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="any">Any Status</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Donation Type (Optional)</label>
                                                <select wire:model="criteria_data.donation_type"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="">Any Type</option>
                                                    <option value="monetary">Monetary</option>
                                                    <option value="materialistic">Materialistic</option>
                                                    <option value="service">Service</option>
                                                </select>
                                            </div>
                                        @endif

                                        @if($criteria_type === 'volunteer_completion')
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Completions</label>
                                                    <input type="number" wire:model="criteria_data.min_completions" min="1"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                           placeholder="1">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assignment Type (Optional)</label>
                                                    <select wire:model="criteria_data.assignment_type"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="">Any Type</option>
                                                        <option value="delivery">Delivery</option>
                                                        <option value="collection">Collection</option>
                                                        <option value="support">Support</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        @if($criteria_type === 'streak')
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Streak</label>
                                                    <input type="number" wire:model="criteria_data.min_streak" min="1"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                           placeholder="1">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Streak Type</label>
                                                    <select wire:model="criteria_data.streak_type"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="donation">Donation</option>
                                                        <option value="volunteer">Volunteer</option>
                                                        <option value="login">Login</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        @if($criteria_type === 'milestone')
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Milestone Type</label>
                                                <select wire:model="criteria_data.milestone"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="">Select Milestone</option>
                                                    <option value="first_donation">First Donation</option>
                                                    <option value="first_volunteer">First Volunteer</option>
                                                    <option value="profile_complete">Profile Complete</option>
                                                    <option value="account_verified">Account Verified</option>
                                                </select>
                                            </div>
                                        @endif

                                        @if($criteria_type === 'special')
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Special Type</label>
                                                <select wire:model="criteria_data.special_type"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="">Select Special Type</option>
                                                    <option value="birthday_donation">Birthday Donation</option>
                                                    <option value="holiday_donation">Holiday Donation</option>
                                                    <option value="emergency_response">Emergency Response</option>
                                                    <option value="anniversary">Anniversary</option>
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                                        <input type="number" wire:model="points" min="0"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('points') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rarity</label>
                                        <select wire:model="rarity"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            @foreach($rarityOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('rarity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="is_active" class="mr-2">
                                        <span class="text-sm text-gray-700">Active</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="is_repeatable" class="mr-2">
                                        <span class="text-sm text-gray-700">Repeatable</span>
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Earnings (optional)</label>
                                    <input type="number" wire:model="max_earnings" min="1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited (if repeatable)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Achievement Explanation -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Understanding Your Achievement</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p class="mb-2">You're creating an achievement that will automatically be awarded to users when they meet specific criteria:</p>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li><strong>Donation Count:</strong> Awards when users complete a specific number of donations</li>
                                            <li><strong>Donation Amount:</strong> Awards when users make donations above a certain amount</li>
                                            <li><strong>Volunteer Completion:</strong> Awards when users complete volunteer assignments</li>
                                            <li><strong>Streak:</strong> Awards for consecutive days of activity</li>
                                            <li><strong>Milestone:</strong> Awards for special first-time achievements</li>
                                            <li><strong>Special:</strong> Awards for unique events like birthdays or holidays</li>
                                        </ul>
                                        <p class="mt-2">The system will automatically check these criteria and award achievements to eligible users.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <button type="button" wire:click="closeModals"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Create Achievement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Modal (similar structure to create modal) -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Edit Achievement</h3>
                        <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="update" class="space-y-6">
                        <!-- Similar form structure as create modal with dynamic criteria fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h4 class="text-md font-medium text-gray-900">Basic Information</h4>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input type="text" wire:model="name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea wire:model="description" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select wire:model="type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Type</option>
                                        @foreach($typeOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select wire:model="category"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Category</option>
                                        @foreach($categoryOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon Image</label>
                                    <input type="file" wire:model="icon_image" accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('icon_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
                                </div>
                            </div>

                            <!-- Criteria and Settings -->
                            <div class="space-y-4">
                                <h4 class="text-md font-medium text-gray-900">Criteria & Settings</h4>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Criteria Type</label>
                                    <select wire:model="criteria_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Criteria Type</option>
                                        @foreach($criteriaTypeOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Dynamic Criteria Fields (same as create modal) -->
                                @if($criteria_type)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-900 mb-3">Criteria Configuration</h5>

                                        @if($criteria_type === 'donation_count')
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Count</label>
                                                    <input type="number" wire:model="criteria_data.min_count" min="1"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                           placeholder="1">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                    <select wire:model="criteria_data.status"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="completed">Completed</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="any">Any Status</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Donation Type (Optional)</label>
                                                <select wire:model="criteria_data.donation_type"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="">Any Type</option>
                                                    <option value="monetary">Monetary</option>
                                                    <option value="materialistic">Materialistic</option>
                                                    <option value="service">Service</option>
                                                </select>
                                            </div>
                                        @endif

                                        <!-- Add other criteria types here as needed -->
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                                        <input type="number" wire:model="points" min="0"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('points') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rarity</label>
                                        <select wire:model="rarity"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            @foreach($rarityOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('rarity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="is_active" class="mr-2">
                                        <span class="text-sm text-gray-700">Active</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="is_repeatable" class="mr-2">
                                        <span class="text-sm text-gray-700">Repeatable</span>
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Earnings (optional)</label>
                                    <input type="number" wire:model="max_earnings" min="1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited (if repeatable)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Achievement Explanation -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Understanding Your Achievement</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p class="mb-2">You're editing an achievement that will automatically be awarded to users when they meet specific criteria:</p>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li><strong>Donation Count:</strong> Awards when users complete a specific number of donations</li>
                                            <li><strong>Donation Amount:</strong> Awards when users make donations above a certain amount</li>
                                            <li><strong>Volunteer Completion:</strong> Awards when users complete volunteer assignments</li>
                                            <li><strong>Streak:</strong> Awards for consecutive days of activity</li>
                                            <li><strong>Milestone:</strong> Awards for special first-time achievements</li>
                                            <li><strong>Special:</strong> Awards for unique events like birthdays or holidays</li>
                                        </ul>
                                        <p class="mt-2">The system will automatically check these criteria and award achievements to eligible users.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <button type="button" wire:click="closeModals"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Update Achievement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
