@extends('layouts.system')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">System Guide</h1>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Access</h2>
    <p>System users sign in at <a href="{{ route('system.login') }}" class="text-blue-600 underline">System Login</a>. After login, go to <a href="{{ route('system.dashboard') }}" class="text-blue-600 underline">System Dashboard</a>.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">General Settings</h2>
    <p>Configure foundation name, logo, contact info at <a href="{{ route('system.settings.general') }}" class="text-blue-600 underline">Settings › General</a>.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">SMTP & Email</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Set SMTP host, port, encryption, username, and password at <a href="{{ route('system.settings.smtp') }}" class="text-blue-600 underline">Settings › SMTP</a>.</li>
        <li>Use the Test button to verify configuration.</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Frontpage Editor</h2>
    <p>Update home page content at <a href="{{ route('system.frontpage') }}" class="text-blue-600 underline">Frontpage</a>.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">ENV Manager</h2>
    <p>Edit environment variables at <a href="{{ route('system.env') }}" class="text-blue-600 underline">Environment</a>. Be cautious—changes affect the entire system.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Cron</h2>
    <p>Trigger scheduled tasks at <a href="{{ route('system.cron') }}" class="text-blue-600 underline">Cron</a> or run schedules manually using the <em>Run Now</em> action.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Security</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Use strong passwords; limit access to system users.</li>
        <li>Rotate SMTP credentials periodically.</li>
        <li>Restrict ENV edits to maintenance windows.</li>
    </ul>
</div>
@endsection


