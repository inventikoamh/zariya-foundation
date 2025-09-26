<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]

class AccountTransfer extends Component
{
    use WithPagination;

    public $showForm = false;
    public $form = [
        'from_account_id' => '',
        'to_account_id' => '',
        'amount' => '',
        'description' => '',
        'notes' => '',
        'exchange_rate' => null,
        'converted_amount' => null,
        'converted_currency' => null,
    ];

    public function updatedFormExchangeRate()
    {
        if ($this->form['exchange_rate'] && $this->form['amount']) {
            $this->form['converted_amount'] = number_format($this->form['amount'] * $this->form['exchange_rate'], 2, '.', '');
        }
    }

    public function updatedFormAmount()
    {
        if ($this->form['exchange_rate'] && $this->form['amount']) {
            $this->form['converted_amount'] = number_format($this->form['amount'] * $this->form['exchange_rate'], 2, '.', '');
        }
    }

    protected $rules = [
        'form.from_account_id' => 'required|exists:accounts,id',
        'form.to_account_id' => 'required|exists:accounts,id|different:form.from_account_id',
        'form.amount' => 'required|numeric|min:0.01',
        'form.description' => 'required|string|max:255',
        'form.notes' => 'nullable|string',
        'form.exchange_rate' => 'nullable|numeric|min:0.000001',
        'form.converted_amount' => 'nullable|numeric|min:0.01',
        'form.converted_currency' => 'nullable|string|size:3',
    ];

    protected $messages = [
        'form.to_account_id.different' => 'The destination account must be different from the source account.',
    ];

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function save()
    {
        // Get validation rules
        $rules = [
            'form.from_account_id' => 'required|exists:accounts,id',
            'form.to_account_id' => 'required|exists:accounts,id|different:form.from_account_id',
            'form.amount' => 'required|numeric|min:0.01',
            'form.description' => 'required|string|max:255',
            'form.notes' => 'nullable|string',
            'form.exchange_rate' => 'nullable|numeric|min:0.000001',
            'form.converted_amount' => 'nullable|numeric|min:0.01',
            'form.converted_currency' => 'nullable|string|size:3',
        ];

        $messages = [
            'form.from_account_id.required' => 'Source account is required.',
            'form.to_account_id.required' => 'Destination account is required.',
            'form.to_account_id.different' => 'The destination account must be different from the source account.',
            'form.amount.required' => 'Amount is required.',
            'form.description.required' => 'Description is required.',
        ];

        // Check if currency conversion is needed
        $fromAccount = Account::find($this->form['from_account_id']);
        $toAccount = Account::find($this->form['to_account_id']);

        if ($fromAccount && $toAccount && $fromAccount->currency !== $toAccount->currency) {
            $rules['form.exchange_rate'] = 'required|numeric|min:0.000001';
            $rules['form.converted_amount'] = 'required|numeric|min:0.01';

            $messages['form.exchange_rate.required'] = 'Exchange rate is required when currencies differ.';
            $messages['form.converted_amount.required'] = 'Converted amount is required when currencies differ.';

            // Auto-set the converted currency to the destination account currency
            $this->form['converted_currency'] = $toAccount->currency;
        }

        $this->validate($rules, $messages);

        // Check for insufficient funds
        if (!$fromAccount->hasSufficientBalance($this->form['amount'])) {
            $this->addError('form.amount', 'Insufficient balance in source account. Available: ' . $fromAccount->formatted_balance);
            return;
        }

        // Create transaction
        $transactionData = [
            'transaction_number' => 'TXN' . date('Ymd') . rand(1000, 9999),
            'type' => Transaction::TYPE_TRANSFER,
            'amount' => $this->form['amount'],
            'currency' => $fromAccount->currency, // Use source account currency
            'description' => $this->form['description'],
            'from_account_id' => $this->form['from_account_id'],
            'to_account_id' => $this->form['to_account_id'],
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => Transaction::STATUS_PENDING,
            'notes' => $this->form['notes'],
        ];

        // Add exchange rate data if available (convert empty strings to null)
        if (!empty($this->form['exchange_rate']) && !empty($this->form['converted_amount']) && !empty($this->form['converted_currency'])) {
            $transactionData['exchange_rate'] = $this->form['exchange_rate'];
            $transactionData['converted_amount'] = $this->form['converted_amount'];
            $transactionData['converted_currency'] = $this->form['converted_currency'];
        } else {
            // Ensure null values for decimal columns
            $transactionData['exchange_rate'] = null;
            $transactionData['converted_amount'] = null;
            $transactionData['converted_currency'] = null;
        }

        $transaction = Transaction::create($transactionData);

        // Process the transaction
        if ($transaction->process()) {
            session()->flash('message', 'Transfer completed successfully!');
            $this->resetForm();
            $this->showForm = false;
        } else {
            session()->flash('error', 'Transfer failed. Please try again.');
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->form = [
            'from_account_id' => '',
            'to_account_id' => '',
            'amount' => '',
            'description' => '',
            'notes' => '',
            'exchange_rate' => null,
            'converted_amount' => null,
            'converted_currency' => null,
        ];
    }

    public function render()
    {
        $transfers = Transaction::with(['fromAccount', 'toAccount', 'createdBy'])
            ->where('type', Transaction::TYPE_TRANSFER)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $accounts = Account::active()->get();

        return view('livewire.finance.account-transfer', [
            'transfers' => $transfers,
            'accounts' => $accounts,
        ]);
    }
}
