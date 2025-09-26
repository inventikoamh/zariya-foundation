@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Login Method Settings</h2>
                    <div class="text-sm text-gray-500">
                        Manage how users can log in to the system
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="space-y-6">
                    @foreach($loginMethods->where('method', '!=', 'both') as $method)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            id="method-{{ $method->id }}"
                                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded toggle-method"
                                            data-method-id="{{ $method->id }}"
                                            {{ $method->is_enabled ? 'checked' : '' }}
                                        >
                                        <label for="method-{{ $method->id }}" class="ml-3 text-base font-medium text-gray-900">
                                            {{ $method->display_name }}
                                        </label>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $method->is_enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $method->is_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Method: <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $method->method }}</code>
                                </div>
                            </div>

                            @if($method->description)
                                <p class="text-sm text-gray-600 mb-4">{{ $method->description }}</p>
                            @endif

                            @if($method->settings)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-900">Configuration Settings</h4>
                                        <button
                                            type="button"
                                            onclick="editSettings({{ $method->id }})"
                                            class="text-xs text-indigo-600 hover:text-indigo-500 font-medium"
                                        >
                                            Edit Settings
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                        @foreach($method->settings as $key => $value)
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="font-medium text-gray-900">
                                                    @if(is_bool($value))
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $value ? 'Yes' : 'No' }}
                                                        </span>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 flex justify-end space-x-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                                    onclick="editMethod({{ $method->id }})"
                                >
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Details
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-900 mb-3">How it works:</h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">•</span>
                            <div>
                                <strong>Password Login:</strong> Users enter phone number + password
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">•</span>
                            <div>
                                <strong>SMS Login:</strong> Users enter phone number + receive OTP
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">•</span>
                            <div>
                                <strong>Both Checked:</strong> Users see method switcher with both options
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">•</span>
                            <div>
                                <strong>Single Method:</strong> Only the enabled method is shown
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">•</span>
                            <div>
                                <strong>Phone Numbers:</strong> Unique identifiers, emails can be duplicate
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Method Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Login Method</h3>
            <form id="editForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                    <input type="text" id="edit_display_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="edit_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Settings Modal -->
<div id="settingsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Configuration Settings</h3>
            <form id="settingsForm">
                <div id="settingsContainer" class="space-y-4">
                    <!-- Settings will be dynamically populated here -->
                </div>
                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeSettingsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle toggle switches
    document.querySelectorAll('.toggle-method').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const methodId = this.dataset.methodId;
            const isEnabled = this.checked;

            fetch(`/admin/login-methods/${methodId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    is_enabled: isEnabled
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the status badge
                    const badge = this.closest('.border').querySelector('.inline-flex');
                    if (isEnabled) {
                        badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                        badge.textContent = 'Enabled';
                    } else {
                        badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
                        badge.textContent = 'Disabled';
                    }
                } else {
                    // Revert checkbox state
                    this.checked = !isEnabled;
                    alert('Failed to update login method status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !isEnabled;
                alert('An error occurred while updating the login method');
            });
        });
    });
});

function editMethod(methodId) {
    // Fetch method data and populate form
    fetch(`/admin/login-methods/${methodId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_display_name').value = data.display_name;
            document.getElementById('edit_description').value = data.description || '';
            document.getElementById('editForm').dataset.methodId = methodId;
            document.getElementById('editModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load method data');
        });
}

function editSettings(methodId) {
    // Fetch method data and populate settings form
    fetch(`/admin/login-methods/${methodId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('settingsContainer');
            container.innerHTML = '';

            if (data.settings) {
                Object.entries(data.settings).forEach(([key, value]) => {
                    const settingDiv = document.createElement('div');
                    settingDiv.className = 'grid grid-cols-1 md:grid-cols-2 gap-4 items-center';

                    const label = document.createElement('label');
                    label.className = 'block text-sm font-medium text-gray-700';
                    label.textContent = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                    let input;
                    if (typeof value === 'boolean') {
                        input = document.createElement('select');
                        input.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
                        input.innerHTML = `
                            <option value="true" ${value ? 'selected' : ''}>Yes</option>
                            <option value="false" ${!value ? 'selected' : ''}>No</option>
                        `;
                    } else if (typeof value === 'number') {
                        input = document.createElement('input');
                        input.type = 'number';
                        input.value = value;
                        input.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
                    } else {
                        input = document.createElement('input');
                        input.type = 'text';
                        input.value = value;
                        input.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
                    }

                    input.name = `settings[${key}]`;

                    settingDiv.appendChild(label);
                    settingDiv.appendChild(input);
                    container.appendChild(settingDiv);
                });
            }

            document.getElementById('settingsForm').dataset.methodId = methodId;
            document.getElementById('settingsModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load settings data');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function closeSettingsModal() {
    document.getElementById('settingsModal').classList.add('hidden');
}

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const methodId = this.dataset.methodId;
    const formData = {
        display_name: document.getElementById('edit_display_name').value,
        description: document.getElementById('edit_description').value
    };

    fetch(`/admin/login-methods/${methodId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update login method');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the login method');
    });
});

document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const methodId = this.dataset.methodId;
    const formData = new FormData(this);
    const settings = {};

    // Convert form data to settings object
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('settings[')) {
            const settingKey = key.replace('settings[', '').replace(']', '');
            settings[settingKey] = value === 'true' ? true : value === 'false' ? false : isNaN(value) ? value : Number(value);
        }
    }

    fetch(`/admin/login-methods/${methodId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            settings: settings
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update settings');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the settings');
    });
});
</script>
@endsection
