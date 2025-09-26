@extends('layouts.user')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">General User Guide</h1>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Getting Started</h2>
    <ol class="list-decimal pl-5 space-y-2">
        <li>Register or log in from the top-right navigation.</li>
        <li>Complete your <a href="{{ route('profile') }}" class="text-blue-600 underline">profile</a> for a better experience.</li>
        <li>Use the dashboard to access donations, requests, and achievements.</li>
    </ol>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Make a Donation</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Go to <a href="{{ route('donate') }}" class="text-blue-600 underline">Donate</a> to submit Monetary, Materialistic, or Service donations.</li>
        <li>Track donations at <a href="{{ route('my-donations') }}" class="text-blue-600 underline">My Donations</a>.</li>
        <li>Update status or cancel pending/assigned donations from the details page.</li>
    </ul>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Request Assistance</h2>
    <p>Navigate to <a href="{{ route('beneficiary.submit') }}" class="text-blue-600 underline">Request Assistance</a> to submit a request, then monitor at <a href="{{ route('my-requests') }}" class="text-blue-600 underline">My Requests</a>.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Achievements</h2>
    <p>View your achievements and progress at <a href="{{ route('achievements') }}" class="text-blue-600 underline">Achievements</a>. Earn badges for donations, requests, and profile completion.</p>

    <h2 class="text-2xl font-semibold mt-8 mb-3">Tips</h2>
    <ul class="list-disc pl-5 space-y-1">
        <li>Keep your profile complete to unlock achievements.</li>
        <li>Use the wizard <code>/wizard/donation</code> or <code>/wizard/beneficiary</code> for guided flows.</li>
        <li>Check <a href="{{ route('donor.impact') }}" class="text-blue-600 underline">My Impact</a> for contribution insights.</li>
    </ul>
</div>
@endsection


