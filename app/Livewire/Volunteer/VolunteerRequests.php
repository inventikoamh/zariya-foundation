<?php

namespace App\Livewire\Volunteer;

use App\Models\Beneficiary;
use App\Models\Remark;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.volunteer')]
class VolunteerRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $priorityFilter = '';

    // Remark modal properties
    public $showRemarkModal = false;
    public $requestForRemark = null;
    public $remarkContent = '';
    public $remarkType = 'general';
    public $isInternal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }


    public function showAddRemarkModal($requestId)
    {
        $request = Beneficiary::where('id', $requestId)
            ->where('assigned_to', auth()->id())
            ->first();

        if (!$request) {
            session()->flash('error', 'Request not found or you are not assigned to it.');
            return;
        }

        $this->requestForRemark = $request;
        $this->remarkContent = '';
        $this->remarkType = 'general';
        $this->isInternal = false;
        $this->showRemarkModal = true;
    }

    public function addRemark()
    {
        $this->validate([
            'remarkContent' => 'required|string|min:10|max:1000',
            'remarkType' => 'required|in:status_update,assignment,progress,completion,cancellation,general'
        ], [
            'remarkContent.required' => 'Please provide remark content.',
            'remarkContent.min' => 'Remark must be at least 10 characters.',
            'remarkContent.max' => 'Remark cannot exceed 1000 characters.',
            'remarkType.required' => 'Please select a remark type.'
        ]);

        if (!$this->requestForRemark) {
            session()->flash('error', 'Request not found.');
            return;
        }

        // Add remark
        $this->requestForRemark->remarks()->create([
            'user_id' => auth()->id(),
            'type' => $this->remarkType,
            'remark' => $this->remarkContent,
            'is_internal' => $this->isInternal
        ]);

        $this->showRemarkModal = false;
        $this->requestForRemark = null;
        $this->remarkContent = '';
        $this->remarkType = 'general';
        $this->isInternal = false;

        session()->flash('success', 'Remark added successfully.');
    }


    public function closeRemarkModal()
    {
        $this->showRemarkModal = false;
        $this->requestForRemark = null;
        $this->remarkContent = '';
        $this->remarkType = 'general';
        $this->isInternal = false;
    }

    public function render()
    {
        $query = Beneficiary::where('assigned_to', auth()->id())
            ->with(['requestedBy', 'assignedTo', 'reviewedBy', 'remarks' => function($q) {
                $q->orderBy('created_at', 'desc');
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('urgency_notes', 'like', '%' . $this->search . '%')
                  ->orWhere('additional_info', 'like', '%' . $this->search . '%')
                  ->orWhereHas('requestedBy', function($userQuery) {
                      $userQuery->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply category filter
        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        // Apply priority filter
        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.volunteer.volunteer-requests', [
            'requests' => $requests,
            'statusOptions' => [
                'pending' => 'Pending',
                'under_review' => 'Under Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'fulfilled' => 'Fulfilled',
            ],
            'categoryOptions' => [
                'medical' => 'Medical Assistance',
                'education' => 'Education Support',
                'food' => 'Food & Nutrition',
                'shelter' => 'Shelter & Housing',
                'emergency' => 'Emergency Relief',
                'other' => 'Other',
            ],
            'priorityOptions' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'urgent' => 'Urgent',
            ],
            'remarkTypeOptions' => [
                'general' => 'General',
                'progress' => 'Progress Update',
                'completion' => 'Completion',
                'cancellation' => 'Cancellation',
            ]
        ]);
    }
}
