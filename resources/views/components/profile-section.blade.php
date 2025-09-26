<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Account Settings</h2>
        <p class="mt-1 text-sm text-gray-600">Manage your account settings and profile information.</p>
    </div>

    <div class="space-y-6">
        <!-- Update Profile Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
                <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address.</p>
            </div>
            <div class="px-6 py-4">
                <livewire:profile.update-profile-form />
            </div>
        </div>

        <!-- Update Password Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
                <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>
            </div>
            <div class="px-6 py-4">
                <livewire:profile.update-password-form />
            </div>
        </div>
    </div>
</div>
