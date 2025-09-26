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
                    <!-- Donation Type -->
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
                                        <span class="text-purple-600 text-sm font-medium">🛠️</span>
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

                    @if($type)
                        <!-- Type-specific fields -->
                        @if($type === 'monetary')
                            <div class="border-t pt-6" wire:key="monetary-fields">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Monetary Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                                        <input type="number" wire:model="amount" step="0.01" min="0"
                                               placeholder="0.00"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                                        <select wire:model="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                            @foreach($this->getCurrencyOptions() as $code => $name)
                                                <option value="{{ $code }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('currency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                                        <select wire:model="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                            <option value="">Select method</option>
                                            @foreach($this->getPaymentMethodOptions() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
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

                                        <div class="space-y-3">
                                            @if(count($item_images) > 0)
                                                @foreach($item_images as $index => $image)
                                                    @php
                                                        $imageProperty = 'image_' . ($index + 1);
                                                    @endphp
                                                    <div class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg">
                                                        <div class="flex-1">
                                                            <input type="file" wire:model="{{ $imageProperty }}"
                                                                   accept="image/*"
                                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                                   wire:loading.attr="disabled">
                                                            @if($this->$imageProperty)
                                                                <p class="text-xs text-green-600 mt-1">✓ Image selected</p>
                                                            @else
                                                                <p class="text-xs text-gray-500 mt-1">No image selected</p>
                                                            @endif
                                                            @error($imageProperty)
                                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        <button type="button" wire:click="removeImage({{ $index }})"
                                                                class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                                            Remove
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @endif

                                            @if(count($item_images) < 5)
                                                <button type="button" wire:click="addImage"
                                                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                        <span>Add Image {{ count($item_images) + 1 }}</span>
                                                    </div>
                                                </button>
                                            @endif
                                        </div>

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
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                               placeholder="e.g., Teaching, Medical, Construction">
                                        @error('service_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                        <textarea wire:model="service_description" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                                  placeholder="Describe the service you can provide"></textarea>
                                        @error('service_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability *</label>
                                        <input type="text" wire:model="availability"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                               placeholder="e.g., Weekends, 9 AM - 5 PM">
                                        @error('availability') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

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
