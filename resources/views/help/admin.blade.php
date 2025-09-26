@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">Admin Guide</h1>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Dashboard & Access</h2>
    <p>Admins access the <a href="{{ route('admin.dashboard') }}" class="text-blue-600 underline">Admin Dashboard</a> and all admin tools. Admin routes are protected by the <code>role:SUPER_ADMIN</code> middleware.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">User Management</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Manage users: <a href="{{ route('admin.users.index') }}" class="text-blue-600 underline">Users</a></li>
        <li>Create, view, edit users; assign roles SUPER_ADMIN / VOLUNTEER.</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Localization</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Manage countries, states, cities: <a href="{{ route('admin.localization.index') }}" class="text-blue-600 underline">Localization</a></li>
        <li>Assign volunteers to localities.</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Status & Achievements</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Configure statuses: <a href="{{ route('admin.status-management') }}" class="text-blue-600 underline">Status Management</a></li>
        <li>Manage achievements: <a href="{{ route('admin.achievements.index') }}" class="text-blue-600 underline">Achievements</a></li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Donations</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Oversee donations: <a href="{{ route('admin.donations.index') }}" class="text-blue-600 underline">Donations</a></li>
        <li>Create and edit donations; view history and service donations.</li>
        <li>Provide donations to beneficiaries from their profile.</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Finance</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Access finance suite under <a href="{{ route('finance.dashboard') }}" class="text-blue-600 underline">Finance</a> (also SUPER_ADMIN only).</li>
        <li>Manage accounts, expenses, transfers, and reports.</li>
        <li>Materialistic donations management is under finance.</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Certificates</h2>
    <p>Issue and manage donor certificates at <a href="{{ route('admin.certificates.index') }}" class="text-blue-600 underline">Certificates</a>.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Best Practices</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Keep roles minimal: SUPER_ADMIN and VOLUNTEER only; normal users have no role.</li>
        <li>Use localization to route assignments effectively.</li>
        <li>Review finance reports regularly.</li>
    </ul>
</div>
@endsection


