<div>
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Status Management</h1>

        <!-- Type Selector -->
        <div class="mb-6">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                @foreach($typeOptions as $type => $label)
                    <button wire:click="$set('selectedType', '{{ $type }}')"
                            class="flex-1 px-4 py-2 text-sm font-medium rounded-md {{ $selectedType === $type ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Add Status Button -->
        <div class="mb-6">
            <button wire:click="openCreateModal" class="bg-indigo-600 text-white px-4 py-2 rounded">
                Add Status
            </button>
        </div>

        <!-- Statuses Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @if($statuses->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($statuses as $status)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $status->color }}"></div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status->badge_class }}">
                                            {{ $status->display_name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $status->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $status->description ?: 'No description' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="openEditModal({{ $status->id }})" class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </button>
                                    @if($status->canBeDeleted())
                                        <button wire:click="deleteStatus({{ $status->id }})"
                                                wire:confirm="Are you sure you want to delete this status?"
                                                class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $statuses->links() }}
            @else
                <div class="text-center py-12">
                    <h3 class="text-lg font-medium text-gray-900">No statuses found</h3>
                    <p class="text-gray-500">Get started by creating a new status.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Create New Status</h3>

                <form wire:submit.prevent="createStatus">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Name</label>
                            <input type="text" wire:model="name" class="w-full border rounded px-3 py-2">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Display Name</label>
                            <input type="text" wire:model="display_name" class="w-full border rounded px-3 py-2">
                            @error('display_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Type</label>
                            <select wire:model="type" class="w-full border rounded px-3 py-2">
                                @foreach($typeOptions as $type => $label)
                                    <option value="{{ $type }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Color</label>
                            <select wire:model="color" class="w-full border rounded px-3 py-2">
                                @foreach($colorOptions as $color => $label)
                                    <option value="{{ $color }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Description</label>
                            <textarea wire:model="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="closeModals" class="px-4 py-2 border rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                            Create Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Edit Status</h3>

                <form wire:submit.prevent="updateStatus">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Name</label>
                            <input type="text" wire:model="name" class="w-full border rounded px-3 py-2">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Display Name</label>
                            <input type="text" wire:model="display_name" class="w-full border rounded px-3 py-2">
                            @error('display_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Type</label>
                            <select wire:model="type" class="w-full border rounded px-3 py-2">
                                @foreach($typeOptions as $type => $label)
                                    <option value="{{ $type }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Color</label>
                            <select wire:model="color" class="w-full border rounded px-3 py-2">
                                @foreach($colorOptions as $color => $label)
                                    <option value="{{ $color }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Description</label>
                            <textarea wire:model="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="closeModals" class="px-4 py-2 border rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
