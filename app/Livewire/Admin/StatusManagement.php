<?php

namespace App\Livewire\Admin;

use App\Models\Status;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class StatusManagement extends Component
{
    use WithPagination;

    public $selectedType = 'monetary';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingStatus = null;

    // Form properties
    public $name = '';
    public $display_name = '';
    public $type = 'monetary';
    public $color = '#6B7280';
    public $description = '';
    public $sort_order = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'display_name' => 'required|string|max:255',
        'type' => 'required|in:monetary,beneficiary,materialistic,service',
        'color' => 'required|string|max:7',
        'description' => 'nullable|string|max:1000',
        'sort_order' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->type = $this->selectedType;
    }

    public function updatedSelectedType()
    {
        $this->type = $this->selectedType;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->type = $this->selectedType;
        $this->showCreateModal = true;
    }

    public function openEditModal(Status $status)
    {
        $this->editingStatus = $status;
        $this->name = $status->name;
        $this->display_name = $status->display_name;
        $this->type = $status->type;
        $this->color = $status->color;
        $this->description = $status->description;
        $this->sort_order = $status->sort_order;
        $this->showEditModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editingStatus = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->display_name = '';
        $this->type = $this->selectedType;
        $this->color = '#6B7280';
        $this->description = '';
        $this->sort_order = 0;
    }

    public function createStatus()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:statuses,name,NULL,id,type,' . $this->type,
            'display_name' => 'required|string|max:255',
            'type' => 'required|in:monetary,beneficiary,materialistic,service',
            'color' => 'required|string|max:7',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer|min:0',
        ]);

        Status::create([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'type' => $this->type,
            'color' => $this->color,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'is_fixed' => false,
            'is_active' => true,
        ]);

        session()->flash('success', 'Status created successfully!');
        $this->closeModals();
    }

    public function updateStatus()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:statuses,name,' . $this->editingStatus->id . ',id,type,' . $this->type,
            'display_name' => 'required|string|max:255',
            'type' => 'required|in:monetary,beneficiary,materialistic,service',
            'color' => 'required|string|max:7',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer|min:0',
        ]);

        $this->editingStatus->update([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'type' => $this->type,
            'color' => $this->color,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
        ]);

        session()->flash('success', 'Status updated successfully!');
        $this->closeModals();
    }

    public function deleteStatus(Status $status)
    {
        if (!$status->canBeDeleted()) {
            session()->flash('error', 'Cannot delete this status. It is either fixed or in use.');
            return;
        }

        $status->delete();
        session()->flash('success', 'Status deleted successfully!');
    }

    public function toggleStatus(Status $status)
    {
        $status->update(['is_active' => !$status->is_active]);
        $message = $status->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Status {$message} successfully!");
    }

    public function getStatusesProperty()
    {
        return Status::where('type', $this->selectedType)
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->paginate(20);
    }

    public function getTypeOptionsProperty()
    {
        return [
            'monetary' => 'Monetary Donation Statuses',
            'beneficiary' => 'Beneficiary Statuses',
            'materialistic' => 'Materialistic Donation Statuses',
            'service' => 'Service Donation Statuses',
        ];
    }

    public function getColorOptionsProperty()
    {
        return [
            '#EF4444' => 'Red',
            '#F59E0B' => 'Yellow',
            '#10B981' => 'Green',
            '#3B82F6' => 'Blue',
            '#8B5CF6' => 'Purple',
            '#F97316' => 'Orange',
            '#6B7280' => 'Gray',
            '#7C2D12' => 'Brown',
        ];
    }

    public function render()
    {
        return view('livewire.admin.status-management', [
            'statuses' => $this->statuses,
            'typeOptions' => $this->typeOptions,
            'colorOptions' => $this->colorOptions,
        ]);
    }
}
