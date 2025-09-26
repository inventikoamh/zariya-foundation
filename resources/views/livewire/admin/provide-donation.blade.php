<div>
    <button wire:click="openModal" class="bg-green-600 text-white px-4 py-2 rounded">
        Provide Donation
    </button>

    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Provide Donation to {{ $beneficiary->name }}</h3>

                <form wire:submit.prevent="provideDonation">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Select Donation</label>
                        <select wire:model.live="selectedDonationId" class="w-full border rounded px-3 py-2">
                            <option value="">Choose a donation...</option>
                            @foreach($donations as $donation)
                                <option value="{{ $donation->id }}">
                                    {{ $donation->type_label }} - {{ $donation->donor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('selectedDonationId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if($donationType === 'monetary')
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Amount</label>
                            <input type="number" step="0.01" wire:model="amount" class="w-full border rounded px-3 py-2">
                            @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Account</label>
                            <select wire:model="selectedAccountId" class="w-full border rounded px-3 py-2">
                                <option value="">Select account...</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->name }} ({{ $account->balance }} {{ $account->currency }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedAccountId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($donationType === 'materialistic')
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Quantity</label>
                                <input type="number" wire:model="quantity" class="w-full border rounded px-3 py-2">
                                @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Unit</label>
                                <input type="text" wire:model="unit" placeholder="kg, pieces" class="w-full border rounded px-3 py-2">
                                @error('unit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    @if($donationType === 'service')
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Service Description</label>
                            <textarea wire:model="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Notes (Optional)</label>
                        <textarea wire:model="notes" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
                            Provide Donation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
