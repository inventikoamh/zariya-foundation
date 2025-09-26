@props(['donation', 'canModify' => true, 'volunteers' => [], 'selectedVolunteerId' => '', 'assignmentNote' => ''])

<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h2 class="text-lg font-medium text-gray-900">Assignment Information</h2>
        </div>
    </div>
    <div class="px-6 py-4">
        @if($donation->assignedTo)
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $donation->assignedTo->first_name }} {{ $donation->assignedTo->last_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Assigned At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $donation->assigned_at?->format('M j, Y g:i A') }}</dd>
                </div>
            </dl>
        @else
            <div class="text-center py-4">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No volunteer assigned</h3>
                <p class="mt-1 text-sm text-gray-500">This donation has not been assigned to a volunteer yet.</p>
            </div>
        @endif

        @if($canModify && count($volunteers) > 0)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Assign to Volunteer</h3>
                <form wire:submit.prevent="assignToVolunteer" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Volunteer ({{ count($volunteers) }} available)</label>
                        <select wire:model="selectedVolunteerId" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Choose a volunteer...</option>
                            @forelse($volunteers as $volunteer)
                                <option value="{{ $volunteer->id }}">
                                    {{ $volunteer->first_name }} {{ $volunteer->last_name ?? '' }}
                                    @if($volunteer->email)
                                        ({{ $volunteer->email }})
                                    @endif
                                </option>
                            @empty
                                <option value="" disabled>No volunteers available</option>
                            @endforelse
                        </select>
                        @error('selectedVolunteerId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assignment Note (Optional)</label>
                        <textarea wire:model="assignmentNote" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Add any specific instructions or notes for the volunteer..."></textarea>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Assign Donation
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
