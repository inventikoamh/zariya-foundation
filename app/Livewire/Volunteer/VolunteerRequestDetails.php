<?php

namespace App\Livewire\Volunteer;

use App\Models\Beneficiary;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.volunteer')]
class VolunteerRequestDetails extends Component
{
    public Beneficiary $beneficiary;

    public function mount($beneficiary)
    {
        $this->beneficiary = Beneficiary::where('id', $beneficiary)
            ->where('requested_by', auth()->id())
            ->with(['assignedTo', 'remarks' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.volunteer.volunteer-request-details');
    }
}
