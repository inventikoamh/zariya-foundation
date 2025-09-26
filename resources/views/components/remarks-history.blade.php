@props(['donation', 'canViewRemarks' => true])

@if($canViewRemarks && $donation->remarks->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h2 class="text-lg font-medium text-gray-900">Remarks History</h2>
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="flow-root">
                <ul class="-mb-8">
                    @foreach($donation->remarks->sortByDesc('created_at') as $index => $remark)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <span class="text-white text-xs font-medium">
                                                {{ substr($remark->user->first_name ?? 'U', 0, 1) }}{{ substr($remark->user->last_name ?? 'U', 0, 1) }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $remark->user->first_name }} {{ $remark->user->last_name }}
                                                </p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($remark->type === 'status_update') bg-blue-100 text-blue-800
                                                    @elseif($remark->type === 'progress') bg-yellow-100 text-yellow-800
                                                    @elseif($remark->type === 'completion') bg-green-100 text-green-800
                                                    @elseif($remark->type === 'cancellation') bg-red-100 text-red-800
                                                    @elseif($remark->type === 'assignment') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $remark->type)) }}
                                                </span>
                                                @if($remark->is_internal)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Internal
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm text-gray-700">{{ $remark->remark }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <time datetime="{{ $remark->created_at->toISOString() }}">
                                                {{ $remark->created_at->format('M j, Y g:i A') }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@elseif($canViewRemarks)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h2 class="text-lg font-medium text-gray-900">Remarks History</h2>
            </div>
        </div>
        <div class="px-6 py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No remarks yet</h3>
            <p class="mt-1 text-sm text-gray-500">No remarks have been added to this donation.</p>
        </div>
    </div>
@else
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <h3 class="text-sm font-medium text-gray-700">Restricted Access</h3>
        </div>
        <p class="mt-2 text-sm text-gray-500">You can only view remarks for donations assigned to you.</p>
    </div>
@endif
