<div>
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Create Donation</h1>
            <a href="{{ route('admin.donations.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Back to Donations
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Donation Details</h2>
        </div>

        <form wire:submit.prevent="save" class="p-6 space-y-6">
            <!-- Donation Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Donation Type *</label>
                <select wire:model.live="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select type</option>
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
                    <option value="">Select donor</option>
                    @foreach($donors as $donor)
                        <option value="{{ $donor->id }}">
                            {{ $donor->first_name ? $donor->first_name . ' ' . $donor->last_name : 'User #' . $donor->id . ' (' . $donor->phone . ')' }}
                        </option>
                    @endforeach
                </select>
                @error('donor_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Type-specific fields -->
            @if($type === 'monetary')
                <div class="border-t pt-6" wire:key="monetary-fields">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Monetary Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                            <input type="number" wire:model="amount" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                            @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                            <select wire:model="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="INR">INR</option>
                                <option value="CAD">CAD</option>
                                <option value="AUD">AUD</option>
                            </select>
                            @error('currency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                            <select wire:model="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="stripe">Stripe</option>
                                <option value="other">Other</option>
                            </select>
                            @error('payment_method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @elseif($type === 'materialistic')
                <div class="border-t pt-6" wire:key="materialistic-fields">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Item Details</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                            <input type="text" wire:model="item_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter item name">
                            @error('item_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                            <textarea wire:model="item_description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Describe the item"></textarea>
                            @error('item_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Phone *</label>
                            <input type="text" wire:model="alternate_phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter alternate contact number">
                            @error('alternate_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Images *</label>
                            <p class="text-sm text-gray-500 mb-2">Upload 1-5 images (max 5MB each)</p>

                            @if(count($item_images) > 0)
                                @foreach($item_images as $index => $image)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="file" wire:model="item_images.{{ $index }}"
                                               accept="image/*"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <button type="button" wire:click="removeImage({{ $index }})"
                                                class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                            Remove
                                        </button>
                                    </div>
                                @endforeach
                            @endif

                            @if(count($item_images) < 5)
                                <button type="button" wire:click="addImage"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Add Image
                                </button>
                            @endif

                            @error('item_images') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            @error('item_images.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @elseif($type === 'service')
                <div class="border-t pt-6" wire:key="service-fields">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Service Details</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Service Type *</label>
                            <input type="text" wire:model="service_type"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., Teaching, Medical, Construction">
                            @error('service_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                            <textarea wire:model="service_description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Describe the service you can provide"></textarea>
                            @error('service_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Availability *</label>
                            <input type="text" wire:model="availability"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., Weekends, 9 AM - 5 PM">
                            @error('availability') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

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
                        <input type="checkbox" wire:model="is_urgent" id="is_urgent"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_urgent" class="ml-2 block text-sm text-gray-900">
                            Mark as urgent
                        </label>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Additional notes or instructions"></textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Location Information -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <select wire:model.live="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select country</option>
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

            <!-- Submit Button -->
            <div class="border-t pt-6">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.donations.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Create Donation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
