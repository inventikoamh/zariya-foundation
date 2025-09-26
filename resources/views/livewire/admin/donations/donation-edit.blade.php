<div>
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Edit Donation #{{ $donation->id }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('admin.donations.show', $donation) }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    View
                </a>
                <a href="{{ route('admin.donations.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Back to Donations
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Donation Details</h2>
        </div>

        <form wire:submit.prevent="save" class="p-6 space-y-6">
            <!-- Donation Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Donation Type *</label>
                <select wire:model="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="monetary">Monetary</option>
                    <option value="materialistic">Materialistic</option>
                    <option value="service">Service</option>
                </select>
                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Donor Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Donor *</label>
                <select wire:model="donor_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($donors as $donor)
                        <option value="{{ $donor->id }}">{{ $donor->first_name }} {{ $donor->last_name }}</option>
                    @endforeach
                </select>
                @error('donor_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select wire:model="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending">Pending</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="rejected">Rejected</option>
                </select>
                @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Assigned To (Read Only) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
                    @if($donation->assignedTo)
                        {{ $donation->assignedTo->first_name }} {{ $donation->assignedTo->last_name ?? '' }}
                        @if($donation->assignedTo->email)
                            ({{ $donation->assignedTo->email }})
                        @endif
                    @else
                        Not assigned
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-500">Use the donation view page to assign volunteers.</p>
            </div>

            <!-- Location Information -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <select wire:model.live="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <select wire:model.live="state_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select state</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('state_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <select wire:model="city_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select city</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('city_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select wire:model="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1">Low</option>
                            <option value="2">Medium</option>
                            <option value="3">High</option>
                            <option value="4">Critical</option>
                        </select>
                        @error('priority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="is_urgent" id="is_urgent" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="is_urgent" class="ml-2 block text-sm text-gray-900">
                            This is an urgent donation
                        </label>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="notes" rows="3"
                              placeholder="Any additional information..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t pt-6">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.donations.show', $donation) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Update Donation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
