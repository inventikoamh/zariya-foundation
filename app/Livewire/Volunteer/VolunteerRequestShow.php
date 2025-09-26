<?php

namespace App\Livewire\Volunteer;

use App\Models\Beneficiary;
use App\Services\StatusHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.volunteer')]
class VolunteerRequestShow extends Component
{
    public Beneficiary $request;
    public $newRemark = '';
    public $remarkType = 'general';
    public $isInternal = false;
    public $newStatus = '';
    public $statusRemark = '';

    public function mount(Beneficiary $request)
    {
        // Allow volunteers to view any request, but restrict modifications to assigned requests only
        $this->request = $request->load([
            'requestedBy.country',
            'requestedBy.state',
            'requestedBy.city',
            'assignedTo',
            'reviewedBy',
            'remarks.user'
        ]);
    }

    public function getCanModifyProperty()
    {
        return $this->request->assigned_to === auth()->id();
    }

    public function getCanViewRemarksProperty()
    {
        return $this->request->assigned_to === auth()->id();
    }

    public function addRemark()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only add remarks to requests assigned to you.');
            return;
        }

        $this->validate([
            'newRemark' => 'required|string|max:1000',
            'remarkType' => 'required|in:status_update,assignment,progress,completion,cancellation,general',
        ]);

        $this->request->remarks()->create([
            'user_id' => auth()->id(),
            'type' => $this->remarkType,
            'remark' => $this->newRemark,
            'is_internal' => $this->isInternal,
        ]);

        $this->newRemark = '';
        $this->remarkType = 'general';
        $this->isInternal = false;

        session()->flash('success', 'Remark added successfully.');
    }

    public function updateStatus($status)
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only update status for requests assigned to you.');
            return;
        }

        $oldStatus = $this->request->status;

        $this->request->update([
            'status' => $status,
            'reviewed_at' => in_array($status, ['approved', 'rejected', 'fulfilled']) ? now() : null,
            'reviewed_by' => in_array($status, ['approved', 'rejected', 'fulfilled']) ? auth()->id() : null,
        ]);

        // Add status update remark
        $this->request->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'status_update',
            'remark' => "Status changed from {$oldStatus} to {$status}",
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $status,
            ],
        ]);

        session()->flash('success', 'Status updated successfully.');
    }

    public function approveRequest()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only approve requests assigned to you.');
            return;
        }

        $this->request->update([
            'status' => Beneficiary::STATUS_APPROVED,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Add approval remark
        $this->request->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'completion',
            'remark' => 'Request approved for assistance',
        ]);

        session()->flash('success', 'Request approved successfully.');
    }

    public function rejectRequest()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only reject requests assigned to you.');
            return;
        }

        $this->request->update([
            'status' => Beneficiary::STATUS_REJECTED,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Add rejection remark
        $this->request->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'cancellation',
            'remark' => 'Request rejected',
        ]);

        session()->flash('success', 'Request rejected.');
    }

    public function fulfillRequest()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only fulfill requests assigned to you.');
            return;
        }

        $this->request->update([
            'status' => Beneficiary::STATUS_FULFILLED,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Add fulfillment remark
        $this->request->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'completion',
            'remark' => 'Request fulfilled - assistance provided',
        ]);

        session()->flash('success', 'Request marked as fulfilled.');
    }

    public function updateRequestStatus()
    {
        if (!$this->canModify) {
            session()->flash('error', 'You can only update status for requests assigned to you.');
            return;
        }

        $this->validate([
            'newStatus' => 'required|in:pending,under_review,approved,rejected,fulfilled',
            'statusRemark' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $this->request->status;
        $status = $this->newStatus;

        $this->request->update([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Add status update remark
        $remarkContent = $this->statusRemark ?: "Status changed from {$oldStatus} to {$status}";
        $this->request->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'status_update',
            'remark' => $remarkContent,
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $status,
            ]
        ]);

        $this->newStatus = '';
        $this->statusRemark = '';

        session()->flash('success', 'Request status updated successfully.');
    }

    public function render()
    {
        $this->request->load(['requestedBy', 'assignedTo', 'reviewedBy', 'remarks.user']);

        return view('livewire.volunteer.volunteer-request-show', [
            'canModify' => $this->canModify,
            'canViewRemarks' => $this->canViewRemarks,
            'statusOptions' => StatusHelper::getStatusOptions('beneficiary'),
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
