@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🏆 Achievement Creation Guide</h1>
                <p class="mt-2 text-gray-600">Complete guide to creating and managing achievements in the Foundation CRM</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.achievements.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Achievements
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Navigation -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Quick Navigation</h2>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#overview" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-900">System Overview</span>
                </a>
                <a href="#types" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-900">Achievement Types</span>
                </a>
                <a href="#creation" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <svg class="h-5 w-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="text-sm font-medium text-purple-900">Step-by-Step Creation</span>
                </a>
            </div>
        </div>
    </div>

    <!-- System Overview -->
    <div id="overview" class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">🎯 System Overview</h2>
        </div>
        <div class="px-6 py-4">
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">
                    The Foundation CRM Achievement System is designed to gamify user engagement and recognize contributions across all aspects of the foundation's operations. The system automatically detects user actions and awards achievements based on predefined criteria.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Core Components</h3>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• <strong>Achievement Model:</strong> Defines criteria and metadata</li>
                            <li>• <strong>UserAchievement Model:</strong> Tracks earned achievements</li>
                            <li>• <strong>AchievementService:</strong> Handles logic detection and awarding</li>
                            <li>• <strong>Admin Management:</strong> Interface for creating/managing</li>
                            <li>• <strong>User Display:</strong> Dashboard widgets and pages</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Key Features</h3>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• <strong>Automatic Detection:</strong> Real-time achievement checking</li>
                            <li>• <strong>Progress Tracking:</strong> Visual progress indicators</li>
                            <li>• <strong>Multiple Types:</strong> Donation, volunteer, general</li>
                            <li>• <strong>Rarity System:</strong> Common to legendary levels</li>
                            <li>• <strong>Points System:</strong> Gamified point accumulation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievement Types -->
    <div id="types" class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">🏷️ Achievement Types & Categories</h2>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-6">
                <!-- Achievement Types -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Achievement Types</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                <h4 class="font-medium text-blue-900">Donation</h4>
                            </div>
                            <p class="text-sm text-gray-600">For donors making contributions (monetary, materialistic, service)</p>
                        </div>
                        <div class="border border-green-200 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <h4 class="font-medium text-green-900">Volunteer</h4>
                            </div>
                            <p class="text-sm text-gray-600">For volunteers completing tasks and assignments</p>
                        </div>
                        <div class="border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                <h4 class="font-medium text-purple-900">General</h4>
                            </div>
                            <p class="text-sm text-gray-600">For general platform engagement and milestones</p>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Achievement Categories</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h4 class="font-medium text-gray-900 text-sm">Monetary</h4>
                            <p class="text-xs text-gray-600">Cash donations</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h4 class="font-medium text-gray-900 text-sm">Materialistic</h4>
                            <p class="text-xs text-gray-600">Physical item donations</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h4 class="font-medium text-gray-900 text-sm">Service</h4>
                            <p class="text-xs text-gray-600">Service/volunteer work</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h4 class="font-medium text-gray-900 text-sm">Completion</h4>
                            <p class="text-xs text-gray-600">Task/request completion</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h4 class="font-medium text-gray-900 text-sm">Milestone</h4>
                            <p class="text-xs text-gray-600">Significant progress markers</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h4 class="font-medium text-gray-900 text-sm">Streak</h4>
                            <p class="text-xs text-gray-600">Consecutive actions</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h4 class="font-medium text-gray-900 text-sm">Special</h4>
                            <p class="text-xs text-gray-600">Special events/occasions</p>
                        </div>
                    </div>
                </div>

                <!-- Rarity Levels -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Rarity Levels</h3>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div class="bg-gray-100 p-3 rounded-lg text-center">
                            <div class="w-4 h-4 bg-gray-500 rounded-full mx-auto mb-2"></div>
                            <h4 class="font-medium text-gray-900 text-sm">Common</h4>
                            <p class="text-xs text-gray-600">10-50 points</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg text-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mx-auto mb-2"></div>
                            <h4 class="font-medium text-green-900 text-sm">Uncommon</h4>
                            <p class="text-xs text-green-600">50-100 points</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg text-center">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mx-auto mb-2"></div>
                            <h4 class="font-medium text-blue-900 text-sm">Rare</h4>
                            <p class="text-xs text-blue-600">100-200 points</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-lg text-center">
                            <div class="w-4 h-4 bg-purple-500 rounded-full mx-auto mb-2"></div>
                            <h4 class="font-medium text-purple-900 text-sm">Epic</h4>
                            <p class="text-xs text-purple-600">200-500 points</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-lg text-center">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full mx-auto mb-2"></div>
                            <h4 class="font-medium text-yellow-900 text-sm">Legendary</h4>
                            <p class="text-xs text-yellow-600">500+ points</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step-by-Step Creation Guide -->
    <div id="creation" class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">📝 Step-by-Step Achievement Creation</h2>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-6">
                <!-- Step 1 -->
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Basic Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <ul class="text-sm text-gray-700 space-y-2">
                                <li><strong>Name:</strong> Choose a clear, descriptive name (e.g., "First Donation", "Volunteer Hero")</li>
                                <li><strong>Description:</strong> Explain what the achievement represents and how to earn it</li>
                                <li><strong>Type:</strong> Select from donation, volunteer, or general</li>
                                <li><strong>Category:</strong> Choose the appropriate category based on the achievement type</li>
                                <li><strong>Icon Image:</strong> Upload a square icon (recommended: 64x64px)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">2</div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Criteria Configuration</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-700 mb-3">Select the criteria type that determines when this achievement is awarded:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm mb-2">Donation Criteria</h4>
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li>• <strong>Donation Amount:</strong> Minimum donation amount</li>
                                        <li>• <strong>Donation Count:</strong> Number of donations made</li>
                                        <li>• <strong>Donation Type Count:</strong> Count by donation type</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm mb-2">Other Criteria</h4>
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li>• <strong>Volunteer Completion:</strong> Assignment completions</li>
                                        <li>• <strong>Beneficiary Help:</strong> Number of beneficiaries helped</li>
                                        <li>• <strong>Streak:</strong> Consecutive actions</li>
                                        <li>• <strong>Milestone:</strong> Special milestones</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">3</div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Points & Rarity</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <ul class="text-sm text-gray-700 space-y-2">
                                <li><strong>Points:</strong> Set the point value (higher for more difficult achievements)</li>
                                <li><strong>Rarity:</strong> Choose rarity level based on difficulty and point value</li>
                                <li><strong>Active:</strong> Enable/disable the achievement</li>
                                <li><strong>Repeatable:</strong> Allow users to earn it multiple times</li>
                                <li><strong>Max Earnings:</strong> Limit how many times it can be earned (if repeatable)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">4</div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Availability & Testing</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <ul class="text-sm text-gray-700 space-y-2">
                                <li><strong>Available From:</strong> Set start date (optional)</li>
                                <li><strong>Available Until:</strong> Set end date (optional)</li>
                                <li><strong>Test:</strong> Create test scenarios to verify the achievement works</li>
                                <li><strong>Monitor:</strong> Check achievement statistics after deployment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Criteria Examples -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">💡 Criteria Examples</h2>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-6">
                <!-- Donation Examples -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Donation Achievement Examples</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-green-200 rounded-lg p-4">
                            <h4 class="font-medium text-green-900 mb-2">First Donation</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Type:</strong> Donation</p>
                                <p><strong>Category:</strong> Milestone</p>
                                <p><strong>Criteria:</strong> Donation Count = 1</p>
                                <p><strong>Points:</strong> 25</p>
                                <p><strong>Rarity:</strong> Common</p>
                            </div>
                        </div>
                        <div class="border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Big Donor</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Type:</strong> Donation</p>
                                <p><strong>Category:</strong> Monetary</p>
                                <p><strong>Criteria:</strong> Donation Amount ≥ $1000</p>
                                <p><strong>Points:</strong> 200</p>
                                <p><strong>Rarity:</strong> Epic</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volunteer Examples -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Volunteer Achievement Examples</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-purple-200 rounded-lg p-4">
                            <h4 class="font-medium text-purple-900 mb-2">Volunteer Hero</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Type:</strong> Volunteer</p>
                                <p><strong>Category:</strong> Completion</p>
                                <p><strong>Criteria:</strong> Volunteer Completion ≥ 10</p>
                                <p><strong>Points:</strong> 150</p>
                                <p><strong>Rarity:</strong> Rare</p>
                            </div>
                        </div>
                        <div class="border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-medium text-yellow-900 mb-2">Consistent Helper</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Type:</strong> Volunteer</p>
                                <p><strong>Category:</strong> Streak</p>
                                <p><strong>Criteria:</strong> Streak ≥ 7 days</p>
                                <p><strong>Points:</strong> 100</p>
                                <p><strong>Rarity:</strong> Uncommon</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Practices -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">⭐ Best Practices</h2>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">✅ Do's</h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>• Use clear, descriptive names and descriptions</li>
                        <li>• Set appropriate point values based on difficulty</li>
                        <li>• Test achievements before making them active</li>
                        <li>• Use high-quality, consistent icon images</li>
                        <li>• Consider the user journey when setting criteria</li>
                        <li>• Monitor achievement statistics regularly</li>
                        <li>• Balance difficulty to maintain engagement</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">❌ Don'ts</h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>• Don't make achievements too easy or too hard</li>
                        <li>• Don't use vague or confusing descriptions</li>
                        <li>• Don't set unrealistic criteria</li>
                        <li>• Don't forget to test before activation</li>
                        <li>• Don't use low-quality or inconsistent icons</li>
                        <li>• Don't create too many similar achievements</li>
                        <li>• Don't ignore user feedback on achievements</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Troubleshooting -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">🔧 Troubleshooting</h2>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-4">
                <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Achievement Not Triggering</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Check if the achievement is active</li>
                                    <li>Verify the criteria configuration is correct</li>
                                    <li>Ensure the user meets all requirements</li>
                                    <li>Check if the achievement has already been earned (if not repeatable)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-l-4 border-blue-400 bg-blue-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Icon Not Displaying</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Ensure the image file is uploaded successfully</li>
                                    <li>Check that the image format is supported (JPEG, PNG, JPG)</li>
                                    <li>Verify the file size is under 2MB</li>
                                    <li>Make sure the image is square (recommended: 64x64px)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-l-4 border-green-400 bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Performance Optimization</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Limit the number of active achievements</li>
                                    <li>Use efficient criteria that don't require complex queries</li>
                                    <li>Monitor database performance with large user bases</li>
                                    <li>Consider caching for frequently accessed achievements</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">🚀 Quick Actions</h2>
        </div>
        <div class="px-6 py-4">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.achievements.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New Achievement
                </a>
                <a href="{{ route('admin.achievements.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Manage Existing Achievements
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Guide
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
