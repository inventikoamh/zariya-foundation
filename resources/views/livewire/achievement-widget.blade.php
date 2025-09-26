<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            Achievements
        </h3>
        <a href="{{ route('achievements') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_earned'] }}</div>
            <div class="text-xs text-gray-500">Earned</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['total_points'] }}</div>
            <div class="text-xs text-gray-500">Points</div>
        </div>
    </div>

    <!-- Recent Achievements -->
    @if($recentAchievements->count() > 0)
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Achievements</h4>
            <div class="space-y-2">
                @foreach($recentAchievements as $userAchievement)
                    @php
                        $achievement = $userAchievement->achievement;
                        $rarityColors = [
                            'common' => 'bg-gray-100',
                            'uncommon' => 'bg-green-100',
                            'rare' => 'bg-blue-100',
                            'epic' => 'bg-purple-100',
                            'legendary' => 'bg-yellow-100',
                        ];
                    @endphp
                    <div class="flex items-center space-x-3 p-2 {{ $rarityColors[$achievement->rarity] ?? 'bg-gray-100' }} rounded-lg">
                        <img src="{{ $achievement->icon_image_url }}"
                             alt="{{ $achievement->name }}"
                             class="w-8 h-8 rounded">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $achievement->name }}</p>
                            <p class="text-xs text-gray-600">{{ $achievement->points }} pts</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $userAchievement->time_ago }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Next Achievements -->
    @if($nextAchievements->count() > 0)
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Next to Earn</h4>
            <div class="space-y-2">
                @foreach($nextAchievements as $achievement)
                    <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded-lg opacity-75">
                        <img src="{{ $achievement->icon_image_url }}"
                             alt="{{ $achievement->name }}"
                             class="w-8 h-8 rounded grayscale">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $achievement->name }}</p>
                            <p class="text-xs text-gray-600">{{ $achievement->points }} pts</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if($recentAchievements->count() === 0 && $nextAchievements->count() === 0)
        <div class="text-center py-4">
            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <p class="text-sm text-gray-500 mt-2">Start earning achievements!</p>
        </div>
    @endif
</div>
