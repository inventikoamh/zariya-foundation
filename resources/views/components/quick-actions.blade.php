@props(['donation', 'canModify' => true])

<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <h2 class="text-lg font-medium text-gray-900">Quick Actions</h2>
        </div>
    </div>
    <div class="px-6 py-4 space-y-3">
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Current Status</span>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $donation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $donation->status === 'assigned' ? 'bg-blue-100 text-blue-800' : '' }} {{ $donation->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : '' }} {{ $donation->status === 'completed' ? 'bg-green-100 text-green-800' : '' }} {{ $donation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }} {{ $donation->status === 'rejected' ? 'bg-gray-100 text-gray-800' : '' }}">
                    {{ is_string($donation->status_label) ? $donation->status_label : ucfirst(str_replace('_', ' ', $donation->status ?? 'Unknown')) }}
                </span>
            </div>
        </div>

        @if($donation->status !== 'cancelled' && $canModify)
            <button wire:click="cancelDonation"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel Donation
            </button>
        @endif
    </div>
</div>
