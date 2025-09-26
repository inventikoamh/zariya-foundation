<?php

namespace App\Livewire\Admin;

use App\Models\Donation;
use App\Services\StatusHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class ServiceDonationsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function getServiceDonationsProperty()
    {
        $query = Donation::where('type', 'service')
            ->with(['donor', 'assignedTo']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('donor', function ($donorQuery) {
                      $donorQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    public function getStatusOptionsProperty()
    {
        return StatusHelper::getStatusOptions('service');
    }

    public function getSummaryProperty()
    {
        $total = Donation::where('type', 'service')->count();
        $pending = Donation::where('type', 'service')->where('status', 'pending')->count();
        $assigned = Donation::where('type', 'service')->where('status', 'assigned')->count();
        $completed = Donation::where('type', 'service')->where('status', 'completed')->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'assigned' => $assigned,
            'completed' => $completed,
        ];
    }

    public function render()
    {
        return view('livewire.admin.service-donations-index', [
            'serviceDonations' => $this->serviceDonations,
            'statusOptions' => $this->statusOptions,
            'summary' => $this->summary,
        ]);
    }
}
