<?php

namespace App\Livewire\Finance;

use App\Models\Donation;
use App\Models\User;
use App\Services\StatusHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

#[Layout('layouts.admin')]

class MaterialisticDonationsManagement extends Component
{
    use WithPagination;

    public $filters = [
        'status' => '',
        'volunteer_id' => '',
        'date_from' => '',
        'date_to' => '',
    ];


    public function mount()
    {
        $this->filters['date_from'] = now()->startOfMonth()->format('Y-m-d');
        $this->filters['date_to'] = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }


    public function render()
    {
        $query = Donation::with(['assignedTo', 'donor'])
            ->where('type', 'materialistic');

        // Apply filters
        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['volunteer_id']) {
            $query->where('assigned_to', $this->filters['volunteer_id']);
        }

        if ($this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if ($this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $donations = $query->orderBy('created_at', 'desc')->paginate(12);

        $volunteers = User::whereHas('roles', function ($query) {
            $query->where('name', 'VOLUNTEER');
        })->get();

        // Calculate summary statistics dynamically
        $summary = [
            'total' => Donation::where('type', 'materialistic')->count(),
        ];

        // Add counts for each materialistic status
        $materialisticStatuses = StatusHelper::getStatuses('materialistic');
        foreach ($materialisticStatuses as $status) {
            $summary[$status->name] = Donation::where('type', 'materialistic')
                ->where('status', $status->name)
                ->count();
        }

        return view('livewire.finance.materialistic-donations-management', [
            'donations' => $donations,
            'volunteers' => $volunteers,
            'summary' => $summary,
            'statusOptions' => StatusHelper::getStatusOptions('materialistic'),
        ]);
    }
}
