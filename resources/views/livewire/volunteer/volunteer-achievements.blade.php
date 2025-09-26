<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Achievements</h1>
        <p class="mt-1 text-sm text-gray-600">Track your progress and celebrate your contributions to the foundation.</p>
    </div>

    <!-- Stats Overview -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Achievement Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $totalAchievements }}</div>
                <div class="text-sm text-gray-500">Total Earned</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $userAchievements->sum('achievement.points') }}</div>
                <div class="text-sm text-gray-500">Total Points</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $recentAchievements->count() }}</div>
                <div class="text-sm text-gray-500">Recent</div>
            </div>
        </div>
    </div>

    <!-- Recent Achievements -->
    @if($recentAchievements->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Recent Achievements
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentAchievements as $userAchievement)
                    @php
                        $achievement = $userAchievement->achievement;
                        $rarityColors = [
                            'common' => 'border-gray-300 bg-gray-50',
                            'uncommon' => 'border-green-300 bg-green-50',
                            'rare' => 'border-blue-300 bg-blue-50',
                            'epic' => 'border-purple-300 bg-purple-50',
                            'legendary' => 'border-yellow-300 bg-yellow-50',
                        ];
                    @endphp
                    <div class="border-2 {{ $rarityColors[$achievement->rarity] ?? 'border-gray-300 bg-gray-50' }} rounded-lg p-4 relative">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $achievement->icon_image_url }}"
                                     alt="{{ $achievement->name }}"
                                     class="w-12 h-12 rounded-lg">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900">{{ $achievement->name }}</h4>
                                <p class="text-xs text-gray-600 mt-1">{{ $achievement->description }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $rarityColors[$achievement->rarity] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $achievement->rarity_label }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-900">{{ $achievement->points }} pts</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Earned {{ $userAchievement->earned_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        <!-- Earned Badge -->
                        <div class="absolute top-2 right-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- All Achievements -->
    @if($userAchievements->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                All Achievements
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($userAchievements as $userAchievement)
                    @php
                        $achievement = $userAchievement->achievement;
                        $rarityColors = [
                            'common' => 'border-gray-300 bg-gray-50',
                            'uncommon' => 'border-green-300 bg-green-50',
                            'rare' => 'border-blue-300 bg-blue-50',
                            'epic' => 'border-purple-300 bg-purple-50',
                            'legendary' => 'border-yellow-300 bg-yellow-50',
                        ];
                    @endphp
                    <div class="border-2 {{ $rarityColors[$achievement->rarity] ?? 'border-gray-300 bg-gray-50' }} rounded-lg p-4 relative">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $achievement->icon_image_url }}"
                                     alt="{{ $achievement->name }}"
                                     class="w-12 h-12 rounded-lg">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900">{{ $achievement->name }}</h4>
                                <p class="text-xs text-gray-600 mt-1">{{ $achievement->description }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $rarityColors[$achievement->rarity] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $achievement->rarity_label }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-900">{{ $achievement->points }} pts</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Earned {{ $userAchievement->earned_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                        <!-- Earned Badge -->
                        <div class="absolute top-2 right-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $userAchievements->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No achievements yet</h3>
            <p class="mt-1 text-sm text-gray-500">Start contributing to earn your first achievement!</p>
        </div>
    @endif
</div>
