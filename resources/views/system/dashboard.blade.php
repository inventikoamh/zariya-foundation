@extends('layouts.system')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">System Dashboard</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('system.settings.general') }}" class="block bg-white p-5 rounded shadow hover:shadow-md">
                <h2 class="font-semibold mb-2">General Settings</h2>
                <p class="text-sm text-gray-600">CRM name, logo, favicon</p>
            </a>
            <a href="{{ route('system.settings.smtp') }}" class="block bg-white p-5 rounded shadow hover:shadow-md">
                <h2 class="font-semibold mb-2">SMTP Settings</h2>
                <p class="text-sm text-gray-600">Mail configuration</p>
            </a>
            <a href="#" class="block bg-white p-5 rounded shadow hover:shadow-md">
                <h2 class="font-semibold mb-2">Database Explorer</h2>
                <p class="text-sm text-gray-600">View tables and data</p>
            </a>
            <a href="#" class="block bg-white p-5 rounded shadow hover:shadow-md">
                <h2 class="font-semibold mb-2">Front Page Editor</h2>
                <p class="text-sm text-gray-600">Edit landing page content</p>
            </a>
            <a href="#" class="block bg-white p-5 rounded shadow hover:shadow-md">
                <h2 class="font-semibold mb-2">Cron Jobs</h2>
                <p class="text-sm text-gray-600">Schedule and status</p>
            </a>
    </div>
@endsection


