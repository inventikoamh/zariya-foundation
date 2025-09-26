# 🔗 Achievement System Integration Guide

## 📋 Table of Contents
1. [Quick Start Integration](#quick-start-integration)
2. [Event-Based Integration](#event-based-integration)
3. [Model Integration](#model-integration)
4. [Controller Integration](#controller-integration)
5. [Service Integration](#service-integration)
6. [Database Integration](#database-integration)
7. [Testing Integration](#testing-integration)
8. [Performance Optimization](#performance-optimization)

## 🚀 Quick Start Integration

### Step 1: Install Dependencies
```bash
# Run migrations
php artisan migrate

# Seed default achievements
php artisan db:seed --class=AchievementSeeder

# Create storage link for achievement icons
php artisan storage:link
```

### Step 2: Basic Integration
```php
use App\Services\EnhancedAchievementService;

// In your donation completion logic
public function completeDonation(Donation $donation)
{
    $donation->update(['status' => 'completed']);
    
    $achievementService = new EnhancedAchievementService();
    $achievements = $achievementService->checkAndAwardAchievements(
        $donation->user,
        'donation_completed',
        [
            'amount' => $donation->amount,
            'type' => $donation->type,
            'currency' => $donation->currency
        ]
    );
    
    return $achievements;
}
```

## 🎯 Event-Based Integration

### 1. Create Achievement Events
```php
// app/Events/AchievementEarned.php
class AchievementEarned
{
    public $user;
    public $achievement;
    
    public function __construct(User $user, $achievement)
    {
        $this->user = $user;
        $this->achievement = $achievement;
    }
}
```

### 2. Create Event Listeners
```php
// app/Listeners/DonationCompletedListener.php
class DonationCompletedListener
{
    protected $achievementService;
    
    public function __construct(EnhancedAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }
    
    public function handle(DonationCompleted $event)
    {
        $achievements = $this->achievementService->checkAndAwardAchievements(
            $event->donation->user,
            'donation_completed',
            [
                'amount' => $event->donation->amount,
                'type' => $event->donation->type,
                'currency' => $event->donation->currency
            ]
        );
        
        foreach ($achievements as $achievement) {
            event(new AchievementEarned($event->donation->user, $achievement));
        }
    }
}
```

### 3. Register Event Listeners
```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    DonationCompleted::class => [
        DonationCompletedListener::class,
    ],
    VolunteerAssignmentCompleted::class => [
        VolunteerAssignmentCompletedListener::class,
    ],
    BeneficiaryRequestCompleted::class => [
        BeneficiaryRequestCompletedListener::class,
    ],
];
```

## 🏗️ Model Integration

### 1. Update Donation Model
```php
// app/Models/Donation.php
class Donation extends Model
{
    protected static function booted()
    {
        static::updated(function ($donation) {
            if ($donation->wasChanged('status') && $donation->status === 'completed') {
                event(new DonationCompleted($donation));
            }
        });
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 2. Update VolunteerAssignment Model
```php
// app/Models/VolunteerAssignment.php
class VolunteerAssignment extends Model
{
    protected static function booted()
    {
        static::updated(function ($assignment) {
            if ($assignment->wasChanged('status') && $assignment->status === 'completed') {
                event(new VolunteerAssignmentCompleted($assignment));
            }
        });
    }
    
    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
}
```

### 3. Update User Model
```php
// app/Models/User.php
class User extends Model
{
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
                    ->withPivot('earned_at', 'metadata', 'is_notified')
                    ->withTimestamps();
    }
    
    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }
    
    public function getTotalAchievementPointsAttribute()
    {
        return $this->achievements()->sum('points');
    }
    
    public function recentAchievements($days = 30)
    {
        return $this->userAchievements()
            ->with('achievement')
            ->where('earned_at', '>=', now()->subDays($days))
            ->orderBy('earned_at', 'desc')
            ->get();
    }
}
```

## 🎮 Controller Integration

### 1. Update Donation Controller
```php
// app/Http/Controllers/DonationController.php
class DonationController extends Controller
{
    protected $achievementService;
    
    public function __construct(EnhancedAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }
    
    public function complete(Donation $donation)
    {
        $donation->update(['status' => 'completed']);
        
        $achievements = $this->achievementService->checkAndAwardAchievements(
            $donation->user,
            'donation_completed',
            [
                'amount' => $donation->amount,
                'type' => $donation->type,
                'currency' => $donation->currency
            ]
        );
        
        if (count($achievements) > 0) {
            session()->flash('achievements', $achievements);
        }
        
        return redirect()->back()->with('success', 'Donation completed successfully!');
    }
}
```

### 2. Update Volunteer Controller
```php
// app/Http/Controllers/VolunteerController.php
class VolunteerController extends Controller
{
    protected $achievementService;
    
    public function __construct(EnhancedAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }
    
    public function completeAssignment(VolunteerAssignment $assignment)
    {
        $assignment->update(['status' => 'completed']);
        
        $achievements = $this->achievementService->checkAndAwardAchievements(
            $assignment->volunteer,
            'volunteer_assignment_completed',
            [
                'assignment_id' => $assignment->id,
                'beneficiary_id' => $assignment->beneficiary_id,
                'hours_worked' => $assignment->hours_worked
            ]
        );
        
        if (count($achievements) > 0) {
            session()->flash('achievements', $achievements);
        }
        
        return redirect()->back()->with('success', 'Assignment completed successfully!');
    }
}
```

## 🔧 Service Integration

### 1. Create Achievement Service Provider
```php
// app/Providers/AchievementServiceProvider.php
class AchievementServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(EnhancedAchievementService::class, function ($app) {
            return new EnhancedAchievementService();
        });
    }
    
    public function boot()
    {
        // Register achievement event listeners
        Event::listen(DonationCompleted::class, DonationCompletedListener::class);
        Event::listen(VolunteerAssignmentCompleted::class, VolunteerAssignmentCompletedListener::class);
        Event::listen(BeneficiaryRequestCompleted::class, BeneficiaryRequestCompletedListener::class);
    }
}
```

### 2. Update App Service Provider
```php
// app/Providers/AppServiceProvider.php
public function register()
{
    $this->app->register(AchievementServiceProvider::class);
}
```

## 🗄️ Database Integration

### 1. Create Achievement Tables
```bash
php artisan make:migration create_achievements_table
php artisan make:migration create_user_achievements_table
```

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Seed Default Achievements
```bash
php artisan db:seed --class=AchievementSeeder
```

## 🧪 Testing Integration

### 1. Create Achievement Tests
```php
// tests/Feature/AchievementTest.php
class AchievementTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_donation_completion_awards_achievement()
    {
        $user = User::factory()->create();
        $donation = Donation::factory()->create([
            'user_id' => $user->id,
            'amount' => 10000,
            'type' => 'monetary',
            'status' => 'pending'
        ]);
        
        $achievement = Achievement::create([
            'name' => 'Generous Donor',
            'description' => 'Donated ₹10,000 or more',
            'type' => 'donation',
            'category' => 'monetary',
            'rarity' => 'uncommon',
            'points' => 50,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'donation_amount',
                'min_amount' => 10000
            ],
            'is_active' => true
        ]);
        
        $achievementService = new EnhancedAchievementService();
        $achievements = $achievementService->checkAndAwardAchievements(
            $user,
            'donation_completed',
            [
                'amount' => 10000,
                'type' => 'monetary'
            ]
        );
        
        $this->assertCount(1, $achievements);
        $this->assertEquals($achievement->id, $achievements[0]->achievement_id);
    }
}
```

### 2. Create Unit Tests
```php
// tests/Unit/AchievementServiceTest.php
class AchievementServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_achievement_progress_calculation()
    {
        $user = User::factory()->create();
        $achievement = Achievement::create([
            'name' => 'Regular Donor',
            'description' => 'Complete 5 donations',
            'type' => 'donation',
            'category' => 'milestone',
            'rarity' => 'uncommon',
            'points' => 40,
            'criteria' => [
                'event_type' => 'donation_completed',
                'type' => 'donation_count',
                'count' => 5
            ],
            'is_active' => true
        ]);
        
        // Create 3 donations
        Donation::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'completed'
        ]);
        
        $achievementService = new EnhancedAchievementService();
        $progress = $achievementService->getAchievementProgress($user, $achievement);
        
        $this->assertEquals(3, $progress['current']);
        $this->assertEquals(5, $progress['target']);
        $this->assertEquals(60, $progress['percentage']);
    }
}
```

## ⚡ Performance Optimization

### 1. Database Indexing
```php
// Add indexes to achievement tables
Schema::table('user_achievements', function (Blueprint $table) {
    $table->index(['user_id', 'achievement_id']);
    $table->index('earned_at');
});

Schema::table('achievements', function (Blueprint $table) {
    $table->index(['type', 'is_active']);
    $table->index('rarity');
});
```

### 2. Caching
```php
// Cache frequently accessed achievement data
public function getAchievementProgress(User $user, Achievement $achievement)
{
    $cacheKey = "achievement_progress_{$user->id}_{$achievement->id}";
    
    return Cache::remember($cacheKey, 300, function () use ($user, $achievement) {
        return $this->calculateAchievementProgress($user, $achievement);
    });
}
```

### 3. Batch Processing
```php
// Process achievements in batches for better performance
public function batchCheckAchievements($eventType, $eventData = [])
{
    $users = User::chunk(100, function ($users) use ($eventType, $eventData) {
        foreach ($users as $user) {
            $this->checkAndAwardAchievements($user, $eventType, $eventData);
        }
    });
}
```

## 🔄 Real-Time Integration

### 1. WebSocket Integration
```php
// Broadcast achievement earned events
class AchievementEarned implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }
    
    public function broadcastWith()
    {
        return [
            'achievement' => $this->achievement,
            'user' => $this->user,
            'earned_at' => now()
        ];
    }
}
```

### 2. Livewire Integration
```php
// Update Livewire components when achievements are earned
class UserDashboard extends Component
{
    protected $listeners = ['achievementEarned' => 'refreshAchievements'];
    
    public function refreshAchievements()
    {
        $this->emit('achievementsUpdated');
    }
}
```

## 📱 Mobile Integration

### 1. API Endpoints
```php
// app/Http/Controllers/Api/AchievementController.php
class AchievementController extends Controller
{
    public function getUserAchievements(Request $request)
    {
        $user = $request->user();
        
        $achievements = $user->achievements()->with('achievement')->get();
        $availableAchievements = Achievement::active()
            ->available()
            ->whereNotIn('id', $achievements->pluck('achievement_id'))
            ->get();
        
        return response()->json([
            'earned_achievements' => $achievements,
            'available_achievements' => $availableAchievements,
            'stats' => [
                'total_earned' => $achievements->count(),
                'total_points' => $user->total_achievement_points
            ]
        ]);
    }
}
```

### 2. Push Notifications
```php
// Send push notifications for achievements
public function sendAchievementNotification(User $user, $achievement)
{
    $user->notify(new AchievementEarnedNotification($achievement));
}
```

## 🎨 Frontend Integration

### 1. JavaScript Integration
```javascript
// Listen for achievement events
window.Echo.private(`user.${userId}`)
    .listen('AchievementEarned', (e) => {
        showAchievementNotification(e.achievement);
    });

function showAchievementNotification(achievement) {
    // Show achievement notification
    const notification = document.createElement('div');
    notification.className = 'achievement-notification';
    notification.innerHTML = `
        <div class="achievement-icon">
            <img src="${achievement.icon}" alt="${achievement.name}">
        </div>
        <div class="achievement-content">
            <h3>${achievement.name}</h3>
            <p>${achievement.description}</p>
            <span class="points">+${achievement.points} points</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
```

### 2. CSS Styling
```css
.achievement-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 1000;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
```

## 🔍 Monitoring & Analytics

### 1. Achievement Analytics
```php
// Track achievement statistics
public function getAchievementAnalytics()
{
    return [
        'total_achievements' => Achievement::count(),
        'total_awarded' => UserAchievement::count(),
        'most_earned' => UserAchievement::with('achievement')
            ->selectRaw('achievement_id, count(*) as count')
            ->groupBy('achievement_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get(),
        'user_leaderboard' => User::withCount('achievements')
            ->orderBy('achievements_count', 'desc')
            ->limit(10)
            ->get()
    ];
}
```

### 2. Performance Monitoring
```php
// Monitor achievement system performance
public function checkAndAwardAchievements(User $user, string $eventType, array $eventData = []): array
{
    $startTime = microtime(true);
    
    $awardedAchievements = [];
    $activeAchievements = Achievement::where('is_active', true)->get();
    
    foreach ($activeAchievements as $achievement) {
        if ($this->evaluateAchievementCriteria($user, $achievement, $eventType, $eventData)) {
            $awarded = $this->awardAchievement($user, $achievement, $eventData);
            if ($awarded) {
                $awardedAchievements[] = $awarded;
            }
        }
    }
    
    $executionTime = microtime(true) - $startTime;
    
    // Log performance metrics
    Log::info('Achievement check completed', [
        'user_id' => $user->id,
        'event_type' => $eventType,
        'achievements_checked' => $activeAchievements->count(),
        'achievements_awarded' => count($awardedAchievements),
        'execution_time' => $executionTime
    ]);
    
    return $awardedAchievements;
}
```

This comprehensive integration guide provides everything you need to implement the achievement system in your Laravel application. Follow the steps in order for a smooth integration process.
