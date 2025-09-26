<x-guest-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-6">Help Center</h1>
        <p class="mb-6">Choose a guide tailored to your role.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('help.general') }}" class="block p-5 border rounded hover:bg-gray-50">
                <h2 class="text-xl font-semibold">General User Guide</h2>
                <p class="text-sm text-gray-600 mt-1">Donations, requests, profile, achievements.</p>
            </a>
            <a href="{{ route('volunteer.help') }}" class="block p-5 border rounded hover:bg-gray-50">
                <h2 class="text-xl font-semibold">Volunteer Guide</h2>
                <p class="text-sm text-gray-600 mt-1">Assignments, donation handling, remarks, personal donations.</p>
            </a>
            <a href="{{ route('admin.help') }}" class="block p-5 border rounded hover:bg-gray-50">
                <h2 class="text-xl font-semibold">Admin Guide</h2>
                <p class="text-sm text-gray-600 mt-1">Users, localization, status, finance, donations, achievements.</p>
            </a>
            <a href="{{ route('system.help') }}" class="block p-5 border rounded hover:bg-gray-50">
                <h2 class="text-xl font-semibold">System Guide</h2>
                <p class="text-sm text-gray-600 mt-1">System login, settings, SMTP, frontpage, env, cron.</p>
            </a>
        </div>
    </div>
</x-guest-layout>


