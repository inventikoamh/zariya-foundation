<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Achievements</h1>
        <p class="mt-1 text-sm text-gray-600">Track your progress and celebrate your contributions to the foundation.</p>
    </div>

    <!-- Stats Overview -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Achievement Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_earned'] }}</div>
                <div class="text-sm text-gray-500">Earned</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['total_points'] }}</div>
                <div class="text-sm text-gray-500">Total Points</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-600">{{ $stats['total_available'] }}</div>
                <div class="text-sm text-gray-500">Available</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">
                    {{ $stats['total_available'] > 0 ? round(($stats['total_earned'] / $stats['total_available']) * 100) : 0 }}%
                </div>
                <div class="text-sm text-gray-500">Completion</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <button wire:click="toggleEarned"
                        class="px-3 py-1 rounded-full text-sm font-medium {{ $showEarned ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                    Earned ({{ $stats['total_earned'] }})
                </button>
                <button wire:click="toggleAvailable"
                        class="px-3 py-1 rounded-full text-sm font-medium {{ $showAvailable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    Available ({{ $stats['total_available'] }})
                </button>
            </div>

            <div class="flex items-center space-x-2">
                <select wire:model.live="typeFilter" class="text-sm border border-gray-300 rounded-md px-2 py-1">
                    <option value="">All Types</option>
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="categoryFilter" class="text-sm border border-gray-300 rounded-md px-2 py-1">
                    <option value="">All Categories</option>
                    @foreach($categoryOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="rarityFilter" class="text-sm border border-gray-300 rounded-md px-2 py-1">
                    <option value="">All Rarities</option>
                    @foreach($rarityOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Earned Achievements -->
    @if($showEarned && $earnedAchievements->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Earned Achievements
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($earnedAchievements as $userAchievement)
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
                                    Earned {{ $userAchievement->time_ago }}
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

    <!-- Available Achievements -->
    @if($showAvailable && $availableAchievements->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                Available Achievements
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($availableAchievements as $achievement)
                    @php
                        $progress = $achievementProgress[$achievement->id] ?? ['current' => 0, 'target' => 1, 'percentage' => 0];
                        $rarityColors = [
                            'common' => 'border-gray-300 bg-gray-50',
                            'uncommon' => 'border-green-300 bg-green-50',
                            'rare' => 'border-blue-300 bg-blue-50',
                            'epic' => 'border-purple-300 bg-purple-50',
                            'legendary' => 'border-yellow-300 bg-yellow-50',
                        ];
                    @endphp
                    <div class="border-2 {{ $rarityColors[$achievement->rarity] ?? 'border-gray-300 bg-gray-50' }} rounded-lg p-4 relative opacity-75">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $achievement->icon_image_url }}"
                                     alt="{{ $achievement->name }}"
                                     class="w-12 h-12 rounded-lg grayscale">
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

                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ $progress['current'] }}/{{ $progress['target'] }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                             style="width: {{ $progress['percentage'] }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ round($progress['percentage']) }}% complete
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Lock Icon -->
                        <div class="absolute top-2 right-2">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Empty States -->
    @if(!$showEarned && !$showAvailable)
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No achievements selected</h3>
            <p class="mt-1 text-sm text-gray-500">Toggle earned or available achievements to view them.</p>
        </div>
    @elseif($earnedAchievements->count() === 0 && $availableAchievements->count() === 0)
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No achievements found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later for new achievements.</p>
        </div>
    @endif
</div>
