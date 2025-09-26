<?php

namespace App\Livewire\Admin;

use App\Models\Certificate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.admin')]
class CertificateManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Form properties
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingCertificate = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|string|max:1000')]
    public $description = '';

    #[Validate('required|in:monetary,materialistic,service,general')]
    public $type = '';

    #[Validate('required|image|mimes:jpeg,png,jpg|max:10240')]
    public $template_image;

    // Name positioning and styling
    #[Validate('required|array')]
    public $name_position = ['x' => 0, 'y' => 0];

    #[Validate('required|string|max:100')]
    public $name_font_family = 'Arial';

    #[Validate('required|integer|min:8|max:72')]
    public $name_font_size = 24;

    #[Validate('required|string|regex:/^#[0-9A-Fa-f]{6}$/')]
    public $name_font_color = '#000000';

    public $name_bold = false;
    public $name_italic = false;

    // Date positioning and styling (optional)
    public $date_position = null;
    public $date_font_family = 'Arial';
    public $date_font_size = 16;
    public $date_font_color = '#666666';

    // Amount positioning and styling (optional)
    public $amount_position = null;
    public $amount_font_family = 'Arial';
    public $amount_font_size = 18;
    public $amount_font_color = '#000000';

    public $is_active = true;
    public $is_default = false;

    // Filters
    public $typeFilter = '';
    public $statusFilter = '';

    public function mount()
    {
        $this->resetForm();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);

        $this->editingCertificate = $certificate;
        $this->name = $certificate->name;
        $this->description = $certificate->description;
        $this->type = $certificate->type;
        $this->name_position = $certificate->name_position;
        $this->name_font_family = $certificate->name_font_family;
        $this->name_font_size = $certificate->name_font_size;
        $this->name_font_color = $certificate->name_font_color;
        $this->name_bold = $certificate->name_bold;
        $this->name_italic = $certificate->name_italic;
        $this->date_position = $certificate->date_position;
        $this->date_font_family = $certificate->date_font_family;
        $this->date_font_size = $certificate->date_font_size;
        $this->date_font_color = $certificate->date_font_color;
        $this->amount_position = $certificate->amount_position;
        $this->amount_font_family = $certificate->amount_font_family;
        $this->amount_font_size = $certificate->amount_font_size;
        $this->amount_font_color = $certificate->amount_font_color;
        $this->is_active = $certificate->is_active;
        $this->is_default = $certificate->is_default;

        $this->showEditModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editingCertificate = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->type = '';
        $this->template_image = null;
        $this->name_position = ['x' => 0, 'y' => 0];
        $this->name_font_family = 'Arial';
        $this->name_font_size = 24;
        $this->name_font_color = '#000000';
        $this->name_bold = false;
        $this->name_italic = false;
        $this->date_position = null;
        $this->date_font_family = 'Arial';
        $this->date_font_size = 16;
        $this->date_font_color = '#666666';
        $this->amount_position = null;
        $this->amount_font_family = 'Arial';
        $this->amount_font_size = 18;
        $this->amount_font_color = '#000000';
        $this->is_active = true;
        $this->is_default = false;
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->validate();

        // Handle file upload
        $imagePath = $this->template_image->store('certificates/templates', 'public');

        // If this is set as default, unset other defaults for this type
        if ($this->is_default) {
            Certificate::where('type', $this->type)->update(['is_default' => false]);
        }

        Certificate::create([
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'template_image' => $imagePath,
            'name_position' => $this->name_position,
            'name_font_family' => $this->name_font_family,
            'name_font_size' => $this->name_font_size,
            'name_font_color' => $this->name_font_color,
            'name_bold' => $this->name_bold,
            'name_italic' => $this->name_italic,
            'date_position' => $this->date_position,
            'date_font_family' => $this->date_font_family,
            'date_font_size' => $this->date_font_size,
            'date_font_color' => $this->date_font_color,
            'amount_position' => $this->amount_position,
            'amount_font_family' => $this->amount_font_family,
            'amount_font_size' => $this->amount_font_size,
            'amount_font_color' => $this->amount_font_color,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
        ]);

        session()->flash('success', 'Certificate template created successfully!');
        $this->closeModals();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:monetary,materialistic,service,general',
            'name_position' => 'required|array',
            'name_font_family' => 'required|string|max:100',
            'name_font_size' => 'required|integer|min:8|max:72',
            'name_font_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'name_position' => $this->name_position,
            'name_font_family' => $this->name_font_family,
            'name_font_size' => $this->name_font_size,
            'name_font_color' => $this->name_font_color,
            'name_bold' => $this->name_bold,
            'name_italic' => $this->name_italic,
            'date_position' => $this->date_position,
            'date_font_family' => $this->date_font_family,
            'date_font_size' => $this->date_font_size,
            'date_font_color' => $this->date_font_color,
            'amount_position' => $this->amount_position,
            'amount_font_family' => $this->amount_font_family,
            'amount_font_size' => $this->amount_font_size,
            'amount_font_color' => $this->amount_font_color,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
        ];

        // Handle new image upload if provided
        if ($this->template_image) {
            // Delete old image
            if ($this->editingCertificate->template_image) {
                \Storage::disk('public')->delete($this->editingCertificate->template_image);
            }
            $data['template_image'] = $this->template_image->store('certificates/templates', 'public');
        }

        // If this is set as default, unset other defaults for this type
        if ($this->is_default) {
            Certificate::where('type', $this->type)
                ->where('id', '!=', $this->editingCertificate->id)
                ->update(['is_default' => false]);
        }

        $this->editingCertificate->update($data);

        session()->flash('success', 'Certificate template updated successfully!');
        $this->closeModals();
    }

    public function delete($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);

        // Delete the image file
        if ($certificate->template_image) {
            \Storage::disk('public')->delete($certificate->template_image);
        }

        $certificate->delete();

        session()->flash('success', 'Certificate template deleted successfully!');
    }

    public function toggleStatus($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);
        $certificate->update(['is_active' => !$certificate->is_active]);

        $status = $certificate->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Certificate template {$status} successfully!");
    }

    public function setDefault($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);

        // Unset other defaults for this type
        Certificate::where('type', $certificate->type)
            ->where('id', '!=', $certificateId)
            ->update(['is_default' => false]);

        // Set this as default
        $certificate->update(['is_default' => true]);

        session()->flash('success', 'Certificate template set as default successfully!');
    }

    public function getTypeOptionsProperty()
    {
        return [
            'monetary' => 'Monetary Donation',
            'materialistic' => 'Materialistic Donation',
            'service' => 'Service Donation',
            'general' => 'General',
        ];
    }

    public function render()
    {
        $query = Certificate::query();

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $certificates = $query->orderBy('type')->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.certificate-management', [
            'certificates' => $certificates,
            'typeOptions' => $this->typeOptions,
        ]);
    }
}
