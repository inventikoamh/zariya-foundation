<?php

namespace App\Livewire\Volunteer;

use App\Models\Donation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

#[Layout('layouts.volunteer')]
class ServiceDonations extends Component
{
    use WithPagination;

    public $filters = [
        'status' => '',
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
        $query = Donation::with(['donor', 'assignedTo'])
            ->where('type', 'service');

        // Apply filters
        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if ($this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $donations = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate summary statistics for all service donations
        $summary = [
            'total' => Donation::where('type', 'service')->count(),
            'assigned_to_me' => Donation::where('type', 'service')
                ->where('assigned_to', auth()->id())
                ->count(),
            'completed' => Donation::where('type', 'service')
                ->where('status', 'completed')
                ->count(),
            'this_month' => Donation::where('type', 'service')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('livewire.volunteer.service-donations', [
            'donations' => $donations,
            'summary' => $summary,
        ]);
    }
}
