<?php

namespace App\Livewire\Beneficiaries;

use App\Models\Beneficiary;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.user')]
class MyRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
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

    public function render()
    {
        $query = Beneficiary::where('requested_by', auth()->id())
            ->with(['assignedTo', 'remarks' => function($q) {
                $q->orderBy('created_at', 'desc');
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
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

        $beneficiaries = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.beneficiaries.my-requests', [
            'beneficiaries' => $beneficiaries,
            'statusOptions' => [
                'pending' => 'Pending',
                'under_review' => 'Under Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'fulfilled' => 'Fulfilled',
            ],
            'categoryOptions' => [
                'medical' => 'Medical',
                'education' => 'Education',
                'food' => 'Food',
                'shelter' => 'Shelter',
                'emergency' => 'Emergency',
                'other' => 'Other',
            ]
        ]);
    }
}
