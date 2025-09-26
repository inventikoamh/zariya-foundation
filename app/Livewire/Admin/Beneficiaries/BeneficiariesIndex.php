<?php

namespace App\Livewire\Admin\Beneficiaries;

use App\Models\Beneficiary;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class BeneficiariesIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $categoryFilter = '';
    public $assignedToFilter = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'assignedToFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingAssignedToFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->priorityFilter = '';
        $this->categoryFilter = '';
        $this->assignedToFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $beneficiaries = Beneficiary::query()
            ->with(['requestedBy', 'assignedTo', 'reviewedBy'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter, function ($query) {
                $query->where('priority', $this->priorityFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->when($this->assignedToFilter, function ($query) {
                $query->where('assigned_to', $this->assignedToFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $volunteers = User::whereHas('roles', function ($query) {
            $query->where('name', 'VOLUNTEER');
        })->orderBy('name')->get();

        $stats = [
            'total' => Beneficiary::count(),
            'pending' => Beneficiary::where('status', Beneficiary::STATUS_PENDING)->count(),
            'under_review' => Beneficiary::where('status', Beneficiary::STATUS_UNDER_REVIEW)->count(),
            'approved' => Beneficiary::where('status', Beneficiary::STATUS_APPROVED)->count(),
            'rejected' => Beneficiary::where('status', Beneficiary::STATUS_REJECTED)->count(),
            'fulfilled' => Beneficiary::where('status', Beneficiary::STATUS_FULFILLED)->count(),
            'urgent' => Beneficiary::where('priority', Beneficiary::PRIORITY_URGENT)->count(),
        ];

        return view('livewire.admin.beneficiaries.beneficiaries-index', [
            'beneficiaries' => $beneficiaries,
            'volunteers' => $volunteers,
            'stats' => $stats,
        ]);
    }
}
