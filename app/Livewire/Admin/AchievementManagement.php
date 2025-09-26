<?php

namespace App\Livewire\Admin;

use App\Models\Achievement;
use App\Models\User;
use App\Services\EmailNotificationService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.admin')]
class AchievementManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Form properties
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingAchievement = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|max:1000')]
    public $description = '';

    #[Validate('required|in:donation,volunteer,general')]
    public $type = '';

    #[Validate('required|in:monetary,materialistic,service,completion,milestone,streak,special')]
    public $category = '';

    #[Validate('required|image|mimes:jpeg,png,jpg|max:2048')]
    public $icon_image;

    // Criteria properties
    public $criteria_type = '';
    public $criteria_data = [];

    #[Validate('required|integer|min:0')]
    public $points = 0;

    #[Validate('required|in:common,uncommon,rare,epic,legendary')]
    public $rarity = 'common';

    public $is_active = true;
    public $is_repeatable = false;
    public $max_earnings = null;

    public $available_from = null;
    public $available_until = null;

    // Filters
    public $typeFilter = '';
    public $categoryFilter = '';
    public $rarityFilter = '';
    public $statusFilter = '';

    public function mount()
    {
        $this->resetForm();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($achievementId)
    {
        $achievement = Achievement::findOrFail($achievementId);

        $this->editingAchievement = $achievement;
        $this->name = $achievement->name;
        $this->description = $achievement->description;
        $this->type = $achievement->type;
        $this->category = $achievement->category;
        $this->criteria_type = $achievement->criteria['type'] ?? '';
        $this->criteria_data = $achievement->criteria;
        $this->points = $achievement->points;
        $this->rarity = $achievement->rarity;
        $this->is_active = $achievement->is_active;
        $this->is_repeatable = $achievement->is_repeatable;
        $this->max_earnings = $achievement->max_earnings;
        $this->available_from = $achievement->available_from?->format('Y-m-d\TH:i');
        $this->available_until = $achievement->available_until?->format('Y-m-d\TH:i');

        $this->showEditModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editingAchievement = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->type = '';
        $this->category = '';
        $this->icon_image = null;
        $this->criteria_type = '';
        $this->criteria_data = [];
        $this->points = 0;
        $this->rarity = 'common';
        $this->is_active = true;
        $this->is_repeatable = false;
        $this->max_earnings = null;
        $this->available_from = null;
        $this->available_until = null;
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->validate();

        // Handle file upload
        $imagePath = $this->icon_image->store('achievements/icons', 'public');

        // Build criteria
        $criteria = $this->buildCriteria();

        $achievement = Achievement::create([
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'category' => $this->category,
            'icon_image' => $imagePath,
            'criteria' => $criteria,
            'points' => $this->points,
            'rarity' => $this->rarity,
            'is_active' => $this->is_active,
            'is_repeatable' => $this->is_repeatable,
            'max_earnings' => $this->max_earnings,
            'available_from' => $this->available_from ? now()->parse($this->available_from) : null,
            'available_until' => $this->available_until ? now()->parse($this->available_until) : null,
        ]);

        // Send new achievement notification to all users
        try {
            $emailService = app(EmailNotificationService::class);
            $users = User::where('email', '!=', null)->get();

            foreach ($users as $user) {
                $emailService->sendNewAchievementNotification($user, $achievement);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send new achievement notifications: ' . $e->getMessage());
        }

        session()->flash('success', 'Achievement created successfully!');
        $this->closeModals();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'type' => 'required|in:donation,volunteer,general',
            'category' => 'required|in:monetary,materialistic,service,completion,milestone,streak,special',
            'points' => 'required|integer|min:0',
            'rarity' => 'required|in:common,uncommon,rare,epic,legendary',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'category' => $this->category,
            'criteria' => $this->buildCriteria(),
            'points' => $this->points,
            'rarity' => $this->rarity,
            'is_active' => $this->is_active,
            'is_repeatable' => $this->is_repeatable,
            'max_earnings' => $this->max_earnings,
            'available_from' => $this->available_from ? now()->parse($this->available_from) : null,
            'available_until' => $this->available_until ? now()->parse($this->available_until) : null,
        ];

        // Handle new image upload if provided
        if ($this->icon_image) {
            // Delete old image
            if ($this->editingAchievement->icon_image) {
                \Storage::disk('public')->delete($this->editingAchievement->icon_image);
            }
            $data['icon_image'] = $this->icon_image->store('achievements/icons', 'public');
        }

        $this->editingAchievement->update($data);

        session()->flash('success', 'Achievement updated successfully!');
        $this->closeModals();
    }

    public function delete($achievementId)
    {
        $achievement = Achievement::findOrFail($achievementId);

        // Delete the image file
        if ($achievement->icon_image) {
            \Storage::disk('public')->delete($achievement->icon_image);
        }

        $achievement->delete();

        session()->flash('success', 'Achievement deleted successfully!');
    }

    public function toggleStatus($achievementId)
    {
        $achievement = Achievement::findOrFail($achievementId);
        $achievement->update(['is_active' => !$achievement->is_active]);

        $status = $achievement->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Achievement {$status} successfully!");
    }

    private function buildCriteria()
    {
        $criteria = ['type' => $this->criteria_type];

        switch ($this->criteria_type) {
            case 'donation_amount':
                $criteria['min_amount'] = $this->criteria_data['min_amount'] ?? 0;
                $criteria['currency'] = $this->criteria_data['currency'] ?? 'USD';
                $criteria['donation_type'] = $this->criteria_data['donation_type'] ?? null;
                break;

            case 'donation_count':
                $criteria['min_count'] = $this->criteria_data['min_count'] ?? 1;
                $criteria['donation_type'] = $this->criteria_data['donation_type'] ?? null;
                $criteria['status'] = $this->criteria_data['status'] ?? 'completed';
                break;

            case 'donation_type_count':
                $criteria['type_counts'] = $this->criteria_data['type_counts'] ?? [];
                $criteria['status'] = $this->criteria_data['status'] ?? 'completed';
                break;

            case 'volunteer_completion':
                $criteria['min_completions'] = $this->criteria_data['min_completions'] ?? 1;
                $criteria['assignment_type'] = $this->criteria_data['assignment_type'] ?? null;
                break;

            case 'beneficiary_help':
                $criteria['min_helped'] = $this->criteria_data['min_helped'] ?? 1;
                $criteria['status'] = $this->criteria_data['status'] ?? 'completed';
                break;

            case 'streak':
                $criteria['min_streak'] = $this->criteria_data['min_streak'] ?? 1;
                $criteria['streak_type'] = $this->criteria_data['streak_type'] ?? 'donation';
                break;

            case 'milestone':
                $criteria['milestone'] = $this->criteria_data['milestone'] ?? null;
                break;

            case 'special':
                $criteria['special_type'] = $this->criteria_data['special_type'] ?? null;
                break;
        }

        return $criteria;
    }

    public function getTypeOptionsProperty()
    {
        return [
            'donation' => 'Donation',
            'volunteer' => 'Volunteer',
            'general' => 'General',
        ];
    }

    public function getCategoryOptionsProperty()
    {
        return [
            'monetary' => 'Monetary',
            'materialistic' => 'Materialistic',
            'service' => 'Service',
            'completion' => 'Completion',
            'milestone' => 'Milestone',
            'streak' => 'Streak',
            'special' => 'Special',
        ];
    }

    public function getRarityOptionsProperty()
    {
        return [
            'common' => 'Common',
            'uncommon' => 'Uncommon',
            'rare' => 'Rare',
            'epic' => 'Epic',
            'legendary' => 'Legendary',
        ];
    }

    public function getCriteriaTypeOptionsProperty()
    {
        return [
            'donation_amount' => 'Donation Amount',
            'donation_count' => 'Donation Count',
            'donation_type_count' => 'Donation Type Count',
            'volunteer_completion' => 'Volunteer Completion',
            'beneficiary_help' => 'Beneficiary Help',
            'streak' => 'Streak',
            'milestone' => 'Milestone',
            'special' => 'Special',
        ];
    }

    public function render()
    {
        $query = Achievement::query();

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->rarityFilter) {
            $query->where('rarity', $this->rarityFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $achievements = $query->orderBy('type')->orderBy('category')->orderBy('points', 'desc')->paginate(10);

        return view('livewire.admin.achievement-management', [
            'achievements' => $achievements,
            'typeOptions' => $this->typeOptions,
            'categoryOptions' => $this->categoryOptions,
            'rarityOptions' => $this->rarityOptions,
            'criteriaTypeOptions' => $this->criteriaTypeOptions,
        ]);
    }
}
