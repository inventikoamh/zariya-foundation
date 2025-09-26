<div>
    @if($showSuccessMessage)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-green-800">Donation Submitted Successfully!</h3>
            <p class="mt-2 text-sm text-green-700">Your donation has been submitted and will be reviewed by our team.</p>
            <p class="mt-1 text-sm text-green-700"><strong>Donation ID:</strong> #{{ $submittedDonation->id }}</p>
            <button wire:click="submitAnother" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Submit Another Donation
            </button>
        </div>
    @else
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900">Submit a Donation</h1>
                    <p class="mt-1 text-sm text-gray-600">Help make a difference by donating to our cause.</p>
                </div>

                @if (session()->has('error'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <p class="text-red-800">{{ session('error') }}</p>
                    </div>
                @endif

                <form wire:submit.prevent="submitDonation" class="p-6 space-y-6">
                    <!-- Donation Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Donation Type *</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $type === 'monetary' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                                <input type="radio" wire:model.live="type" value="monetary" class="sr-only">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-green-600 text-sm font-medium">$</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Monetary</div>
                                        <div class="text-sm text-gray-500">Cash or digital payment</div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $type === 'materialistic' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                <input type="radio" wire:model.live="type" value="materialistic" class="sr-only">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 text-sm font-medium">📦</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Materialistic</div>
                                        <div class="text-sm text-gray-500">Physical items or goods</div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $type === 'service' ? 'border-purple-500 bg-purple-50' : 'border-gray-300' }}">
                                <input type="radio" wire:model.live="type" value="service" class="sr-only">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                        <span class="text-purple-600 text-sm font-medium">⚙️</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Service</div>
                                        <div class="text-sm text-gray-500">Skills or time</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Type-specific fields -->
                    @if($type === 'monetary')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-green-800 mb-4">Monetary Donation Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                                    <input type="number" wire:model="amount" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="0.00">
                                    @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                    <select wire:model="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                        <option value="GBP">GBP</option>
                                        <option value="INR">INR</option>
                                        <option value="CAD">CAD</option>
                                        <option value="AUD">AUD</option>
                                    </select>
                                    @error('currency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                                    <select wire:model="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="">Select payment method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="credit_card">Credit Card</option>
                                        <option value="debit_card">Debit Card</option>
                                        <option value="digital_wallet">Digital Wallet</option>
                                        <option value="check">Check</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('payment_method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($type === 'materialistic')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-blue-800 mb-4">Materialistic Donation Details</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                                    <input type="text" wire:model="item_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Laptop, Books, Clothes">
                                    @error('item_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Description *</label>
                                    <textarea wire:model="item_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe the item, its condition, and any relevant details..."></textarea>
                                    @error('item_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Phone</label>
                                    <input type="tel" wire:model="alternate_phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Alternative contact number">
                                    @error('alternate_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Image Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Images (Optional)</label>
                                    <div class="space-y-2">
                                        @foreach($item_images as $index => $image)
                                            <div class="flex items-center space-x-2">
                                                <input type="file" wire:model="item_images.{{ $index }}" accept="image/*" class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                @if($index > 0)
                                                    <button type="button" wire:click="removeImage({{ $index }})" class="text-red-600 hover:text-red-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if(count($item_images) < 5)
                                            <button type="button" wire:click="addImage" class="text-blue-600 hover:text-blue-800 text-sm">
                                                + Add another image
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($type === 'service')
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-purple-800 mb-4">Service Donation Details</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Type *</label>
                                    <input type="text" wire:model="service_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g., Tutoring, Medical Consultation, Legal Advice">
                                    @error('service_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Description *</label>
                                    <textarea wire:model="service_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Describe the service you can provide, your qualifications, and any relevant details..."></textarea>
                                    @error('service_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Availability *</label>
                                    <input type="text" wire:model="availability" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g., Weekends, 9 AM - 5 PM, Flexible">
                                    @error('availability') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Location Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                <select wire:model="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                                <select wire:model="state_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" {{ empty($states) ? 'disabled' : '' }}>
                                    <option value="">Select state</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <select wire:model="city_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" {{ empty($cities) ? 'disabled' : '' }}>
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
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" wire:model="pincode" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="12345">
                                @error('pincode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea wire:model="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Street address, building, etc."></textarea>
                                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea wire:model="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Any additional notes or special instructions..."></textarea>
                                @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="is_urgent" id="is_urgent" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_urgent" class="ml-2 block text-sm text-gray-900">
                                    This is an urgent donation
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t pt-6">
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="resetForm"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Reset Form
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium disabled:opacity-50"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>Submit Donation</span>
                                <span wire:loading>Submitting...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
