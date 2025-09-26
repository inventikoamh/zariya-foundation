@extends('layouts.system')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Database Configuration</h1>
        <p class="mt-2 text-gray-600">Manage database connection settings and environment configuration.</p>
    </div>

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

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Database Settings</h3>
        </div>
        <form method="POST" action="{{ route('system.database.config.update') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="environment" class="block text-sm font-medium text-gray-700">Environment</label>
                    <select id="environment" name="environment" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @foreach($environments as $env)
                            <option value="{{ $env }}" {{ $dbConfig['environment'] === $env ? 'selected' : '' }}>
                                {{ ucfirst($env) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="db_connection" class="block text-sm font-medium text-gray-700">Connection Type</label>
                    <select id="db_connection" name="db_connection" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="mysql" {{ $dbConfig['connection'] === 'mysql' ? 'selected' : '' }}>MySQL</option>
                        <option value="pgsql" {{ $dbConfig['connection'] === 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
                        <option value="sqlite" {{ $dbConfig['connection'] === 'sqlite' ? 'selected' : '' }}>SQLite</option>
                        <option value="sqlsrv" {{ $dbConfig['connection'] === 'sqlsrv' ? 'selected' : '' }}>SQL Server</option>
                    </select>
                </div>

                <div>
                    <label for="db_host" class="block text-sm font-medium text-gray-700">Host</label>
                    <input type="text" id="db_host" name="db_host" value="{{ $dbConfig['host'] }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="db_port" class="block text-sm font-medium text-gray-700">Port</label>
                    <input type="text" id="db_port" name="db_port" value="{{ $dbConfig['port'] }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="db_database" class="block text-sm font-medium text-gray-700">Database Name</label>
                    <input type="text" id="db_database" name="db_database" value="{{ $dbConfig['database'] }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="db_username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="db_username" name="db_username" value="{{ $dbConfig['username'] }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label for="db_password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="db_password" name="db_password" placeholder="Enter new password (leave blank to keep current)" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('system.database.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Update Configuration
                </button>
            </div>
        </form>
    </div>

    <!-- Environment Information -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Changing the environment will affect which database configuration is used</li>
                        <li>Make sure to backup your database before making changes</li>
                        <li>Test the connection after updating configuration</li>
                        <li>You may need to run migrations after changing database settings</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
