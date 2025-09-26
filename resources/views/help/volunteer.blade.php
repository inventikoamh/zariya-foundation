@extends('layouts.volunteer')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">Volunteer Guide</h1>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Dashboard & Navigation</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Access <a href="{{ route('volunteer.dashboard') }}" class="text-blue-600 underline">Volunteer Dashboard</a> after login.</li>
        <li>Key sections: Assigned Donations, Requests, Materialistic & Service donations, Personal donations.</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Manage Assigned Donations</h2>
    <ol class="list-decimal pl-5 space-y-2">
        <li>Open <a href="{{ route('volunteer.donations.index') }}" class="text-blue-600 underline">Donations</a> to view assignments.</li>
        <li>Click a donation to update status, add remarks, or complete monetary donations selecting accounts.</li>
        <li>Use currency conversion when applicable.</li>
    </ol>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Requests & Materialistic/Service Donations</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Review beneficiary requests at <a href="{{ route('volunteer.requests.index') }}" class="text-blue-600 underline">Requests</a>.</li>
        <li>View materialistic donations at <a href="{{ route('volunteer.materialistic-donations.index') }}" class="text-blue-600 underline">Materialistic</a>.</li>
        <li>Track service donations at <a href="{{ route('volunteer.service-donations.index') }}" class="text-blue-600 underline">Service</a>.</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Personal Donations</h2>
    <p>Volunteers can donate via <a href="{{ route('volunteer.donate') }}" class="text-blue-600 underline">Donate</a> and track at <a href="{{ route('volunteer.my-donations') }}" class="text-blue-600 underline">My Donations</a>.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Best Practices</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Keep clear remarks for each status change.</li>
        <li>Coordinate with admins for localization and assignments.</li>
        <li>Earn achievements by completing assignments and helping beneficiaries.</li>
    </ul>
</div>
@endsection


