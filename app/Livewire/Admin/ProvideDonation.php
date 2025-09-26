<?php

namespace App\Livewire\Admin;

use App\Models\Beneficiary;
use App\Models\Donation;
use App\Models\Account;
use App\Services\DonationHistoryService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.admin')]
class ProvideDonation extends Component
{
    public Beneficiary $beneficiary;
    public $donations = [];
    public $accounts = [];

    // Form properties
    public $selectedDonationId = '';
    public $donationType = '';
    public $amount = '';
    public $currency = 'USD';
    public $exchangeRate = '';
    public $convertedAmount = '';
    public $convertedCurrency = 'USD';
    public $selectedAccountId = '';
    public $quantity = '';
    public $unit = '';
    public $description = '';
    public $notes = '';

    // Modal properties
    public $showModal = false;

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;
        $this->loadDonations();
        $this->loadAccounts();
    }

    public function loadDonations()
    {
        $this->donations = Donation::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->with(['donor', 'assignedTo'])
            ->get();
    }

    public function loadAccounts()
    {
        $this->accounts = Account::where('is_active', true)->get();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function updatedSelectedDonationId()
    {
        if ($this->selectedDonationId) {
            $donation = Donation::find($this->selectedDonationId);
            $this->donationType = $donation->type;

            if ($donation->type === 'monetary') {
                $this->amount = $donation->amount;
                $this->currency = $donation->currency;
            }
        }
    }

    public function updatedExchangeRate()
    {
        if ($this->exchangeRate && $this->amount) {
            $this->convertedAmount = number_format($this->amount * $this->exchangeRate, 2, '.', '');
        }
    }

    public function resetForm()
    {
        $this->selectedDonationId = '';
        $this->donationType = '';
        $this->amount = '';
        $this->currency = 'USD';
        $this->exchangeRate = '';
        $this->convertedAmount = '';
        $this->convertedCurrency = 'USD';
        $this->selectedAccountId = '';
        $this->quantity = '';
        $this->unit = '';
        $this->description = '';
        $this->notes = '';
    }

    public function provideDonation()
    {
        $this->validate([
            'selectedDonationId' => 'required|exists:donations,id',
            'donationType' => 'required|in:monetary,materialistic,service',
        ]);

        $donationHistoryService = new DonationHistoryService();

        try {
            if ($this->donationType === 'monetary') {
                $this->validate([
                    'amount' => 'required|numeric|min:0.01',
                    'currency' => 'required|string|max:3',
                    'selectedAccountId' => 'required|exists:accounts,id',
                ]);

                $donationHistoryService->provideMonetaryDonation(
                    $this->beneficiary->id,
                    $this->selectedDonationId,
                    $this->amount,
                    $this->currency,
                    $this->exchangeRate ?: null,
                    $this->convertedAmount ?: null,
                    $this->convertedCurrency,
                    $this->selectedAccountId,
                    $this->notes
                );

            } elseif ($this->donationType === 'materialistic') {
                $this->validate([
                    'quantity' => 'required|integer|min:1',
                    'unit' => 'required|string|max:50',
                ]);

                $donationHistoryService->provideMaterialisticDonation(
                    $this->beneficiary->id,
                    $this->selectedDonationId,
                    $this->quantity,
                    $this->unit,
                    $this->description,
                    $this->notes
                );

            } elseif ($this->donationType === 'service') {
                $this->validate([
                    'description' => 'required|string|max:1000',
                ]);

                $donationHistoryService->provideServiceDonation(
                    $this->beneficiary->id,
                    $this->selectedDonationId,
                    $this->description,
                    $this->notes
                );
            }

            session()->flash('success', 'Donation provided successfully!');
            $this->closeModal();
            $this->dispatch('donation-provided');

        } catch (\Exception $e) {
            session()->flash('error', 'Error providing donation: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.provide-donation');
    }
}
