<?php

namespace App\Livewire\Beneficiaries;

use App\Models\Beneficiary;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.user')]
class RequestDetails extends Component
{
    public Beneficiary $beneficiary;

    public function mount(Beneficiary $beneficiary)
    {
        $user = auth()->user();

        // Check if user is the requester of this beneficiary request
        $isRequester = $beneficiary->requested_by === $user->id;

        if (!$isRequester) {
            abort(403, 'You are not authorized to view this request.');
        }

        $this->beneficiary = $beneficiary->load(['requestedBy', 'assignedTo', 'reviewedBy', 'remarks.user']);
    }

    public function render()
    {
        return view('livewire.beneficiaries.request-details');
    }
}
