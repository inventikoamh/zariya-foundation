@props(['donation', 'accounts', 'selectedAccountId' => '', 'exchangeRate' => '', 'convertedAmount' => ''])

@if($donation->type === 'monetary')
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Select Account</label>
        <select wire:model="selectedAccountId" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Choose an account...</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}">
                    {{ $account->name }} ({{ $account->currency }})
                </option>
            @endforeach
        </select>
        @error('selectedAccountId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    @if($selectedAccountId)
        @php
            $selectedAccount = $accounts->firstWhere('id', $selectedAccountId);
            $donationCurrency = $donation->details['currency'] ?? 'USD';
            $accountCurrency = $selectedAccount ? $selectedAccount->currency : 'USD';
        @endphp

        @if($donationCurrency !== $accountCurrency)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Exchange Rate</label>
                <p class="text-sm text-gray-600 mb-2">
                    Converting from {{ $donationCurrency }} to {{ $accountCurrency }}
                </p>
                <input type="number" wire:model="exchangeRate" step="0.0001" min="0.0001" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter exchange rate">
                @error('exchangeRate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif
    @endif
@endif
