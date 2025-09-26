<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_country_code',
        'phone',
        'phone_verified_at',
        'is_disabled',
        'avatar_url',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'address_line',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_disabled' => 'boolean',
            'dob' => 'date',
        ];
    }

    /**
     * Get the volunteer assignments for the user.
     */
    public function volunteerAssignments()
    {
        return $this->hasMany(VolunteerAssignment::class);
    }

    /**
     * Check if user is a head volunteer for a specific region.
     */
    public function isHeadVolunteerFor($assignmentType, $countryId = null, $stateId = null, $cityId = null): bool
    {
        return $this->volunteerAssignments()
            ->where('assignment_type', $assignmentType)
            ->where('role', 'head_volunteer')
            ->where('is_active', true)
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->when($stateId, fn($q) => $q->where('state_id', $stateId))
            ->when($cityId, fn($q) => $q->where('city_id', $cityId))
            ->exists();
    }

    /**
     * Get donations made by this user (as donor).
     */
    public function donations()
    {
        return $this->hasMany(Donation::class, 'donor_id');
    }

    /**
     * Get donations assigned to this user (as volunteer).
     */
    public function assignedDonations()
    {
        return $this->hasMany(Donation::class, 'assigned_to');
    }

    /**
     * Get remarks made by this user.
     */
    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

    /**
     * Get the country for this user.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state for this user.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city for this user.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get certificates for this user (as donor).
     */
    public function certificates()
    {
        return $this->hasMany(DonorCertificate::class, 'donor_id');
    }

    /**
     * Get achievements for this user.
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
                    ->withPivot('earned_at', 'metadata', 'is_notified')
                    ->withTimestamps();
    }

    /**
     * Get user achievements (pivot records).
     */
    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * Get total achievement points for this user.
     */
    public function getTotalAchievementPointsAttribute()
    {
        return $this->achievements()->sum('points');
    }

    /**
     * Get recent achievements for this user.
     */
    public function recentAchievements($days = 30)
    {
        return $this->userAchievements()
            ->with('achievement')
            ->where('earned_at', '>=', now()->subDays($days))
            ->orderBy('earned_at', 'desc')
            ->get();
    }

    /**
     * Check if user has a specific achievement.
     */
    public function hasAchievement($achievementId)
    {
        return $this->achievements()->where('achievement_id', $achievementId)->exists();
    }
}
