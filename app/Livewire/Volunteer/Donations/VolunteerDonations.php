<?php

namespace App\Livewire\Volunteer\Donations;

use App\Models\Donation;
use App\Models\Remark;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\StatusHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.volunteer')]
class VolunteerDonations extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $priorityFilter = '';
    public $isUrgentFilter = '';

    // Status update modal properties
    public $showStatusModal = false;
    public $donationToUpdate = null;
    public $newStatus = '';
    public $statusRemark = '';
    public $selectedAccountId = '';
    public $exchangeRate = null;
    public $convertedAmount = null;
    public $convertedCurrency = null;

    public function updatedExchangeRate()
    {
        if ($this->exchangeRate && $this->donationToUpdate && $this->donationToUpdate->amount) {
            $this->convertedAmount = number_format($this->donationToUpdate->amount * $this->exchangeRate, 2, '.', '');
        }
    }

    // Remark modal properties
    public $showRemarkModal = false;
    public $donationForRemark = null;
    public $remarkContent = '';
    public $remarkType = 'general';
    public $isInternal = false;

    public function getStatusOptionsProperty()
    {
        if ($this->typeFilter) {
            // If a specific type is selected, show only statuses for that type
            return StatusHelper::getStatusOptions($this->typeFilter);
        } else {
            // If no type is selected, show all unique statuses across all types
            $allStatuses = \App\Models\Status::active()->get();
            $uniqueStatuses = $allStatuses->unique('name');
            return $uniqueStatuses->pluck('display_name', 'name')->toArray();
        }
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function showStatusUpdateModal($donationId)
    {
        $donation = Donation::where('id', $donationId)
            ->where('assigned_to', auth()->id())
            ->first();

        if (!$donation) {
            session()->flash('error', 'Donation not found or you are not assigned to it.');
            return;
        }

        $this->donationToUpdate = $donation;
        $this->newStatus = $donation->status;
        $this->statusRemark = '';
        $this->selectedAccountId = $donation->account_id ?? '';
        $this->exchangeRate = null;
        $this->convertedAmount = null;
        $this->convertedCurrency = null;
        $this->showStatusModal = true;
    }

    public function updateDonationStatus()
    {
        $validationRules = [
            'newStatus' => 'required|in:pending,assigned,in_progress,completed,cancelled,rejected',
            'statusRemark' => 'required|string|min:10|max:500'
        ];

        $validationMessages = [
            'newStatus.required' => 'Please select a status.',
            'statusRemark.required' => 'Please provide a remark for this status change.',
            'statusRemark.min' => 'Remark must be at least 10 characters.',
            'statusRemark.max' => 'Remark cannot exceed 500 characters.'
        ];

        // If completing a monetary donation, require account selection
        if ($this->newStatus === 'completed' && $this->donationToUpdate && $this->donationToUpdate->type === 'monetary') {
            $validationRules['selectedAccountId'] = 'required|exists:accounts,id';
            $validationMessages['selectedAccountId.required'] = 'Please select an account for the monetary donation.';

            // Check if currency conversion is needed
            $account = Account::find($this->selectedAccountId);
            if ($account && $account->currency !== $this->donationToUpdate->currency) {
                $validationRules['exchangeRate'] = 'required|numeric|min:0.000001';
                $validationRules['convertedAmount'] = 'required|numeric|min:0.01';
                $validationMessages['exchangeRate.required'] = 'Exchange rate is required when currencies differ.';
                $validationMessages['convertedAmount.required'] = 'Converted amount is required when currencies differ.';

                // Auto-set the converted currency to the account currency
                $this->convertedCurrency = $account->currency;
            }
        }

        $this->validate($validationRules, $validationMessages);

        if (!$this->donationToUpdate) {
            session()->flash('error', 'Donation not found.');
            return;
        }

        $oldStatus = $this->donationToUpdate->status;

        // Update donation status and account if monetary
        $updateData = [
            'status' => $this->newStatus,
            'completed_at' => $this->newStatus === 'completed' ? now() : null,
        ];

        if ($this->newStatus === 'completed' && $this->donationToUpdate->type === 'monetary' && $this->selectedAccountId) {
            $updateData['account_id'] = $this->selectedAccountId;
        }

        $this->donationToUpdate->update($updateData);

        // Create transaction for completed monetary donations
        if ($this->newStatus === 'completed' && $this->donationToUpdate->type === 'monetary' && $this->selectedAccountId) {
            $account = Account::find($this->selectedAccountId);
            $amount = $this->donationToUpdate->amount;
            $currency = $this->donationToUpdate->currency;

            if ($account && $amount) {
                $transactionData = [
                    'transaction_number' => 'TXN' . date('Ymd') . rand(1000, 9999),
                    'type' => Transaction::TYPE_DONATION,
                    'amount' => $amount,
                    'currency' => $currency,
                    'description' => 'Donation: ' . $this->donationToUpdate->donor->name,
                    'to_account_id' => $this->selectedAccountId,
                    'donation_id' => $this->donationToUpdate->id,
                    'created_by' => auth()->id(),
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'status' => Transaction::STATUS_PENDING,
                ];

                // Add exchange rate data if available (convert empty strings to null)
                if (!empty($this->exchangeRate) && !empty($this->convertedAmount) && !empty($this->convertedCurrency)) {
                    $transactionData['exchange_rate'] = $this->exchangeRate;
                    $transactionData['converted_amount'] = $this->convertedAmount;
                    $transactionData['converted_currency'] = $this->convertedCurrency;
                } else {
                    // Ensure null values for decimal columns
                    $transactionData['exchange_rate'] = null;
                    $transactionData['converted_amount'] = null;
                    $transactionData['converted_currency'] = null;
                }

                $transaction = Transaction::create($transactionData);

                // Process the transaction
                $transaction->process();
            }
        }

        // Add status update remark
        $remarkContent = "Status changed from '{$oldStatus}' to '{$this->newStatus}'. {$this->statusRemark}";
        if ($this->newStatus === 'completed' && $this->donationToUpdate->type === 'monetary' && $this->selectedAccountId) {
            $account = Account::find($this->selectedAccountId);
            $remarkContent .= " Donation transferred to account: {$account->name}.";
        }

        $this->donationToUpdate->remarks()->create([
            'user_id' => auth()->id(),
            'type' => 'status_update',
            'remark' => $remarkContent,
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $this->newStatus,
                'account_id' => $this->selectedAccountId
            ]
        ]);

        $this->showStatusModal = false;
        $this->donationToUpdate = null;
        $this->newStatus = '';
        $this->statusRemark = '';
        $this->selectedAccountId = '';

        session()->flash('success', 'Donation status updated successfully.');
    }

    public function showAddRemarkModal($donationId)
    {
        $donation = Donation::where('id', $donationId)
            ->where('assigned_to', auth()->id())
            ->first();

        if (!$donation) {
            session()->flash('error', 'Donation not found or you are not assigned to it.');
            return;
        }

        $this->donationForRemark = $donation;
        $this->remarkContent = '';
        $this->remarkType = 'general';
        $this->isInternal = false;
        $this->showRemarkModal = true;
    }

    public function addRemark()
    {
        $this->validate([
            'remarkContent' => 'required|string|min:10|max:1000',
            'remarkType' => 'required|in:status_update,assignment,progress,completion,cancellation,general'
        ], [
            'remarkContent.required' => 'Please provide remark content.',
            'remarkContent.min' => 'Remark must be at least 10 characters.',
            'remarkContent.max' => 'Remark cannot exceed 1000 characters.',
            'remarkType.required' => 'Please select a remark type.'
        ]);

        if (!$this->donationForRemark) {
            session()->flash('error', 'Donation not found.');
            return;
        }

        // Add remark
        $this->donationForRemark->remarks()->create([
            'user_id' => auth()->id(),
            'type' => $this->remarkType,
            'remark' => $this->remarkContent,
            'is_internal' => $this->isInternal
        ]);

        $this->showRemarkModal = false;
        $this->donationForRemark = null;
        $this->remarkContent = '';
        $this->remarkType = 'general';
        $this->isInternal = false;

        session()->flash('success', 'Remark added successfully.');
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->donationToUpdate = null;
        $this->newStatus = '';
        $this->statusRemark = '';
        $this->selectedAccountId = '';
        $this->exchangeRate = null;
        $this->convertedAmount = null;
        $this->convertedCurrency = null;
    }

    public function closeRemarkModal()
    {
        $this->showRemarkModal = false;
        $this->donationForRemark = null;
        $this->remarkContent = '';
        $this->remarkType = 'general';
        $this->isInternal = false;
    }

    public function render()
    {
        $query = Donation::where('assigned_to', auth()->id())
            ->with(['donor', 'country', 'state', 'city', 'remarks' => function($q) {
                $q->orderBy('created_at', 'desc');
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('notes', 'like', '%' . $this->search . '%')
                  ->orWhereJsonContains('details->item_name', $this->search)
                  ->orWhereJsonContains('details->service_type', $this->search)
                  ->orWhereJsonContains('details->item_description', $this->search)
                  ->orWhereJsonContains('details->service_description', $this->search)
                  ->orWhereHas('donor', function($donorQuery) {
                      $donorQuery->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply type filter
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        // Apply priority filter
        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        // Apply urgent filter
        if ($this->isUrgentFilter) {
            $query->where('is_urgent', true);
        }

        $donations = $query->orderBy('created_at', 'desc')->paginate(10);

        $accounts = Account::active()->get();

        return view('livewire.volunteer.donations.volunteer-donations', [
            'donations' => $donations,
            'accounts' => $accounts,
            'statusOptions' => $this->statusOptions,
            'typeOptions' => [
                'monetary' => 'Monetary',
                'materialistic' => 'Materialistic',
                'service' => 'Service',
            ],
            'priorityOptions' => [
                '1' => 'Low',
                '2' => 'Medium',
                '3' => 'High',
                '4' => 'Critical',
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
