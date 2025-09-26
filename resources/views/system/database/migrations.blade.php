@extends('layouts.system')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Migration Management</h1>
                <p class="mt-2 text-gray-600">Select and manage database migrations with granular control</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    <span class="font-medium">{{ count($migrations) }}</span> total migrations
                </div>
                <a href="{{ route('system.database.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Back to Database
                </a>
            </div>
        </div>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    @if(session('output'))
                        <div class="mt-2 text-sm text-green-700">
                            <pre class="bg-green-100 p-2 rounded text-xs overflow-x-auto">{{ session('output') }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions Bar -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="selectAllCheckbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="selectAllCheckbox" class="text-sm font-medium text-gray-700">Select All</label>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span id="selectedCount">0</span> of <span id="totalCount">{{ count($migrations) }}</span> selected
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Selected Actions -->
                    <div class="flex items-center space-x-2" id="selectedActions" style="display: none;">
                        <form method="POST" action="{{ route('system.database.migrate.selected') }}" class="inline" id="selectedMigrateForm">
                            @csrf
                            <div class="flex items-center space-x-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="with_seeding" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-600">With seeding</span>
                                </label>
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Run Selected
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="flex items-center space-x-2">
                        <form method="POST" action="{{ route('system.database.migrate') }}" class="inline">
                            @csrf
                            <input type="hidden" name="steps" value="all">
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Run All Pending
                            </button>
                        </form>

                        <form method="POST" action="{{ route('system.database.rollback') }}" class="inline">
                            @csrf
                            <input type="hidden" name="steps" value="1">
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Rollback Last
                            </button>
                        </form>

                        <form method="POST" action="{{ route('system.database.fresh') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('This will drop all tables and re-run all migrations. Are you sure?')">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Fresh Start
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Migrations List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Migration Files</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($migrations as $migration)
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Checkbox -->
                            <div class="flex-shrink-0">
                                <input type="checkbox"
                                       class="migration-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       value="{{ $migration['filename'] }}"
                                       data-status="{{ $migration['status'] }}"
                                       {{ $migration['status'] === 'run' ? 'disabled' : '' }}>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex-shrink-0">
                                @if($migration['status'] === 'run')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Executed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pending
                                    </span>
                                @endif
                            </div>

                            <!-- Migration Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $migration['filename'] }}
                                        </p>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <p class="text-xs text-gray-500">
                                                {{ number_format($migration['size'] / 1024, 2) }} KB
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $migration['modified'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Actions -->
                        <div class="flex items-center space-x-2">
                            @if($migration['status'] === 'pending')
                                <form method="POST" action="{{ route('system.database.migrate.single') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="migration" value="{{ $migration['filename'] }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Run
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('system.database.rollback.single') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="migration" value="{{ $migration['filename'] }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                        </svg>
                                        Rollback
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No migrations found</h3>
                    <p class="mt-1 text-sm text-gray-500">No migration files were found in the database/migrations directory.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
class MigrationManager {
    constructor() {
        this.selectAllCheckbox = document.getElementById('selectAllCheckbox');
        this.selectedCountElement = document.getElementById('selectedCount');
        this.selectedActionsElement = document.getElementById('selectedActions');
        this.selectedMigrateForm = document.getElementById('selectedMigrateForm');
        this.migrationCheckboxes = document.querySelectorAll('.migration-checkbox');

        this.init();
    }

    init() {
        this.updateSelectedCount();
        this.bindEvents();
    }

    bindEvents() {
        // Select all checkbox
        if (this.selectAllCheckbox) {
            this.selectAllCheckbox.addEventListener('change', () => this.toggleAllMigrations());
        }

        // Individual migration checkboxes
        this.migrationCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => this.updateSelectedCount());
        });
    }

    updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.migration-checkbox:checked');
        const count = checkedBoxes.length;
        const totalCount = this.migrationCheckboxes.length;

        // Update counter
        if (this.selectedCountElement) {
            this.selectedCountElement.textContent = count;
        }

        // Update select all checkbox state
        if (this.selectAllCheckbox) {
            if (count === 0) {
                this.selectAllCheckbox.indeterminate = false;
                this.selectAllCheckbox.checked = false;
            } else if (count === totalCount) {
                this.selectAllCheckbox.indeterminate = false;
                this.selectAllCheckbox.checked = true;
            } else {
                this.selectAllCheckbox.indeterminate = true;
            }
        }

        // Show/hide selected actions
        if (this.selectedActionsElement) {
            if (count > 0) {
                this.selectedActionsElement.style.display = 'flex';
            } else {
                this.selectedActionsElement.style.display = 'none';
            }
        }

        // Update form with selected migrations
        this.updateSelectedMigrationsForm();
    }

    updateSelectedMigrationsForm() {
        if (!this.selectedMigrateForm) return;

        const checkedBoxes = document.querySelectorAll('.migration-checkbox:checked');

        // Remove existing hidden inputs
        const existingInputs = this.selectedMigrateForm.querySelectorAll('input[name="migrations[]"]');
        existingInputs.forEach(input => input.remove());

        // Add new hidden inputs for selected migrations
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'migrations[]';
            input.value = checkbox.value;
            this.selectedMigrateForm.appendChild(input);
        });
    }

    toggleAllMigrations() {
        const isChecked = this.selectAllCheckbox.checked;
        const availableCheckboxes = document.querySelectorAll('.migration-checkbox:not([disabled])');

        availableCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });

        this.updateSelectedCount();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new MigrationManager();
});
</script>
@endsection
