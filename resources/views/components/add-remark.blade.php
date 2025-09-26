@props(['canModify' => true])

@if($canModify)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <h2 class="text-lg font-medium text-gray-900">Add Remark</h2>
            </div>
        </div>
        <div class="px-6 py-4">
            <form wire:submit.prevent="addRemark" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select wire:model="remarkType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="general">General</option>
                        <option value="status_update">Status Update</option>
                        <option value="progress">Progress Update</option>
                        <option value="assignment">Assignment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea wire:model="newRemark" rows="3"
                              placeholder="Add a remark..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" wire:model="isInternal" id="isInternal" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="isInternal" class="ml-2 block text-sm text-gray-900">
                        Internal note (not visible to donor)
                    </label>
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Remark
                </button>
            </form>
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
        <p class="mt-2 text-sm text-gray-500">You can only add remarks to donations assigned to you.</p>
    </div>
@endif
