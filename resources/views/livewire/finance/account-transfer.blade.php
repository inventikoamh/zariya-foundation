<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Account Transfers</h2>
        <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            New Transfer
        </button>
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

    <!-- Transfer Form Modal -->
    @if($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Transfer</h3>

                    <form wire:submit.prevent="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">From Account</label>
                                <select wire:model="form.from_account_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Source Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->formatted_balance }})</option>
                                    @endforeach
                                </select>
                                @error('form.from_account_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">To Account</label>
                                <select wire:model="form.to_account_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Destination Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->formatted_balance }})</option>
                                    @endforeach
                                </select>
                                @error('form.to_account_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount</label>
                                <input type="number" step="0.01" wire:model="form.amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" wire:model="form.description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea wire:model="form.notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                @error('form.notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            @if($form['from_account_id'] && $form['to_account_id'])
                                @php
                                    $fromAccount = $accounts->firstWhere('id', $form['from_account_id']);
                                    $toAccount = $accounts->firstWhere('id', $form['to_account_id']);
                                @endphp
                                @if($fromAccount && $toAccount && $fromAccount->currency !== $toAccount->currency)
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                        <h4 class="text-sm font-medium text-yellow-800 mb-3">Currency Conversion Required</h4>
                                        <p class="text-sm text-yellow-700 mb-3">
                                            Transferring from {{ $fromAccount->currency }} to {{ $toAccount->currency }}.
                                            Please provide exchange rate information.
                                        </p>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Exchange Rate *</label>
                                                <input type="number" step="0.000001" wire:model="form.exchange_rate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 0.85">
                                                @error('form.exchange_rate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                <p class="text-xs text-gray-500 mt-1">{{ $fromAccount->currency }} to {{ $toAccount->currency }} - Required when currencies differ</p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Converted Amount</label>
                                                <input type="number" step="0.01" wire:model="form.converted_amount" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600" placeholder="Auto-calculated">
                                                @error('form.converted_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                <p class="text-xs text-gray-500 mt-1">Automatically calculated from amount × exchange rate</p>
                                            </div>

<div>
                                                <label class="block text-sm font-medium text-gray-700">Converted Currency</label>
                                                <input type="text" value="{{ $toAccount->currency }}" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600">
                                                <p class="text-xs text-gray-500 mt-1">Destination account currency</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="cancel" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Transfer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Transfers Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transfers as $transfer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $transfer->transaction_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transfer->fromAccount->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transfer->toAccount->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($transfer->hasCurrencyConversion())
                                        <div class="space-y-1">
                                            <div class="font-medium">{{ $transfer->formatted_amount }}</div>
                                            <div class="text-xs text-gray-500 bg-blue-50 px-2 py-1 rounded">
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                    </svg>
                                                    <span>Rate: {{ number_format($transfer->exchange_rate, 6) }}</span>
                                                </div>
                                                <div class="text-blue-600 font-medium">
                                                    {{ $transfer->converted_currency }} {{ number_format($transfer->converted_amount, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{ $transfer->formatted_amount }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transfer->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'cancelled' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$transfer->status] }}">
                                        {{ $transfer->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transfer->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No transfers found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $transfers->links() }}
            </div>
        </div>
    </div>
</div>
