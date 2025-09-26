<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\SystemSetting::get('front_title', 'Zariya Foundation - Making a Difference Together') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gradient-to-br min-h-screen" style="--tw-gradient-from: {{ \App\Models\SystemSetting::get('front_bg_from', '#eff6ff') }}; --tw-gradient-to: {{ \App\Models\SystemSetting::get('front_bg_to', '#e0e7ff') }}; background-image: linear-gradient(to bottom right, var(--tw-gradient-from), var(--tw-gradient-to));">
        <div class="min-h-screen flex items-center justify-center">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <!-- Hero Section -->
                    <div class="mb-12">
                        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                            <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                {{ \App\Models\SystemSetting::get('front_headline', 'Zariya Foundation') }}
                            </span>
                        </h1>
                        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                            {{ \App\Models\SystemSetting::get('front_subheadline', 'Making a difference together. Join us in creating positive change through donations, volunteer work, and community support.') }}
                        </p>
                                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-gray-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-700 transition duration-200 shadow-lg hover:shadow-xl">
                                Dashboard
                            </a>
                            <a href="{{ route('donate') }}" class="bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition duration-200 shadow-lg hover:shadow-xl">
                                Make a Donation
                            </a>
                            <a href="{{ route('beneficiary.submit') }}" class="bg-green-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-700 transition duration-200 shadow-lg hover:shadow-xl">
                                Request Assistance
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-gray-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-700 transition duration-200 shadow-lg hover:shadow-xl">
                                Login
                            </a>
                            <a href="{{ \App\Models\SystemSetting::get('front_cta_primary_link', route('wizard', 'donation')) }}" class="bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition duration-200 shadow-lg hover:shadow-xl">
                                {{ \App\Models\SystemSetting::get('front_cta_primary_text', 'Donate Now') }}
                            </a>
                            <a href="{{ \App\Models\SystemSetting::get('front_cta_secondary_link', route('wizard', 'beneficiary')) }}" class="bg-green-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-700 transition duration-200 shadow-lg hover:shadow-xl">
                                {{ \App\Models\SystemSetting::get('front_cta_secondary_text', 'Request Assistance') }}
                            </a>
                        @endauth
                    </div>

                    <!-- Test User Credentials Section -->
                    <div class="mt-16 bg-white p-8 rounded-lg shadow-lg">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Test User Credentials</h2>
                        <p class="text-gray-600 mb-6">Use these credentials to test different user roles in the CRM system:</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Super Admin User -->
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <h3 class="font-semibold text-red-800 mb-2">Super Admin User</h3>
                                <div class="text-sm text-gray-700">
                                    <p><strong>Email:</strong> david@example.com</p>
                                    <p><strong>Phone:</strong> +91-9876543214</p>
                                    <p><strong>Password:</strong> password</p>
                                    <p class="text-red-600 font-medium">Full system access (Admin panel, Finance, etc.)</p>
                                </div>
                            </div>

                            <!-- Normal Users (Donors) -->
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h3 class="font-semibold text-blue-800 mb-2">Normal Users</h3>
                                <div class="text-sm text-gray-700">
                                    <p><strong>Email:</strong> john@example.com</p>
                                    <p><strong>Phone:</strong> +91-9876543210</p>
                                    <p><strong>Password:</strong> password</p>
                                    <p class="text-blue-600 font-medium">Can donate and request assistance</p>
                                </div>
                            </div>

                            <!-- Volunteer Users -->
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <h3 class="font-semibold text-green-800 mb-2">Volunteer Users</h3>
                                <div class="text-sm text-gray-700">
                                    <p><strong>Email:</strong> mike@example.com</p>
                                    <p><strong>Phone:</strong> +91-9876543212</p>
                                    <p><strong>Password:</strong> password</p>
                                    <p class="text-green-600 font-medium">Can manage assignments</p>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Features Section -->
                    <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Monetary Donations</h3>
                            <p class="text-gray-600">Support our cause with financial contributions that help us reach more people in need.</p>
                                </div>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Material Donations</h3>
                            <p class="text-gray-600">Donate physical items like clothes, food, books, and other essentials to help those in need.</p>
                                </div>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Service Donations</h3>
                            <p class="text-gray-600">Offer your time and skills to volunteer and make a direct impact in your community.</p>
                        </div>
                    </div>
                </div>
        </div>
    </body>
</html>
