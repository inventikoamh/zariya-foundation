<?php

namespace App\Livewire\Admin\Beneficiaries;

use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Remark;
use App\Services\VolunteerRoutingService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class BeneficiaryShow extends Component
{
    public Beneficiary $beneficiary;
    public $newRemark = '';
    public $status = '';
    public $priority = '';
    public $assignedTo = '';
    public $remarkType = 'general';
    public $isInternal = false;
    public $statusRemark = '';
    public $assignmentNote = '';

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;
        $this->status = $beneficiary->status;
        $this->priority = $beneficiary->priority;
        $this->assignedTo = $beneficiary->assigned_to;
    }

    public function updateStatus()
    {
        $this->validate([
            'status' => 'required|in:' . implode(',', array_keys($this->beneficiary->getStatusOptions())),
            'statusRemark' => 'nullable|string|max:1000',
        ]);

        $this->beneficiary->update([
            'status' => $this->status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Add remark about status change
        $remarkText = "Status changed to: " . $this->beneficiary->getStatusOptions()[$this->status];
        if ($this->statusRemark) {
            $remarkText .= "\n\nNote: " . $this->statusRemark;
        }

        Remark::create([
            'remarkable_type' => Beneficiary::class,
            'remarkable_id' => $this->beneficiary->id,
            'user_id' => auth()->id(),
            'remark' => $remarkText,
            'type' => 'status_update',
        ]);

        $this->statusRemark = '';
        session()->flash('success', 'Status updated successfully.');
    }

    public function updatePriority()
    {
        $this->validate([
            'priority' => 'required|in:' . implode(',', array_keys($this->beneficiary->getPriorityOptions())),
        ]);

        $this->beneficiary->update([
            'priority' => $this->priority,
        ]);

        // Add remark about priority change
        Remark::create([
            'remarkable_type' => Beneficiary::class,
            'remarkable_id' => $this->beneficiary->id,
            'user_id' => auth()->id(),
            'remark' => "Priority changed to: " . $this->beneficiary->getPriorityOptions()[$this->priority],
            'type' => 'priority_change',
        ]);

        session()->flash('success', 'Priority updated successfully.');
    }

    public function assignToVolunteer()
    {
        $this->validate([
            'assignedTo' => 'nullable|exists:users,id',
            'assignmentNote' => 'nullable|string|max:1000',
        ]);

        $this->beneficiary->update([
            'assigned_to' => $this->assignedTo ?: null,
        ]);

        $volunteerName = $this->assignedTo ? User::find($this->assignedTo)->name : 'Unassigned';

        // Add remark about assignment
        $remarkText = "Assigned to: " . $volunteerName;
        if ($this->assignmentNote) {
            $remarkText .= "\n\nNote: " . $this->assignmentNote;
        }

        Remark::create([
            'remarkable_type' => Beneficiary::class,
            'remarkable_id' => $this->beneficiary->id,
            'user_id' => auth()->id(),
            'remark' => $remarkText,
            'type' => 'assignment',
        ]);

        $this->assignmentNote = '';
        session()->flash('success', 'Assignment updated successfully.');
    }

    public function autoAssignVolunteer()
    {
        $location = $this->beneficiary->location;

        if (!$location || (!isset($location['city_id']) && !isset($location['state_id']) && !isset($location['country_id']))) {
            session()->flash('error', 'Cannot auto-assign: Location information is missing.');
            return;
        }

        $routingService = app(VolunteerRoutingService::class);
        $assignedVolunteer = $routingService->findNearestHeadVolunteer(
            $location['city_id'] ?? null,
            $location['state_id'] ?? null,
            $location['country_id'] ?? null,
            $this->beneficiary->requested_by // Exclude the creator from being assigned
        );

        if (!$assignedVolunteer) {
            session()->flash('error', 'No volunteer found for this location. Please assign manually.');
            return;
        }

        $this->beneficiary->update([
            'assigned_to' => $assignedVolunteer->id,
        ]);

        $this->assignedTo = $assignedVolunteer->id;

        // Add remark about auto-assignment
        Remark::create([
            'remarkable_type' => Beneficiary::class,
            'remarkable_id' => $this->beneficiary->id,
            'user_id' => auth()->id(),
            'remark' => "Auto-assigned to {$assignedVolunteer->first_name} {$assignedVolunteer->last_name} based on location",
            'type' => 'assignment',
        ]);

        session()->flash('success', "Auto-assigned to {$assignedVolunteer->first_name} {$assignedVolunteer->last_name}.");
    }


    public function addRemark()
    {
        $this->validate([
            'newRemark' => 'required|string|max:1000',
            'remarkType' => 'required|in:general,status_update,progress,assignment',
        ]);

        Remark::create([
            'remarkable_type' => Beneficiary::class,
            'remarkable_id' => $this->beneficiary->id,
            'user_id' => auth()->id(),
            'remark' => $this->newRemark,
            'type' => $this->remarkType,
            'is_internal' => $this->isInternal,
        ]);

        $this->newRemark = '';
        $this->remarkType = 'general';
        $this->isInternal = false;
        session()->flash('success', 'Remark added successfully.');
    }

    public function render()
    {
        $beneficiary = $this->beneficiary->load(['requestedBy', 'assignedTo', 'reviewedBy', 'remarks.user']);

        $volunteers = User::whereHas('roles', function ($query) {
            $query->where('name', 'VOLUNTEER');
        })->orderBy('name')->get();

        return view('livewire.admin.beneficiaries.beneficiary-show', [
            'beneficiary' => $beneficiary,
            'volunteers' => $volunteers,
        ]);
    }
}
