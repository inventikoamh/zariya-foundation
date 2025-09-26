<?php

namespace App\Livewire\Finance;

use App\Models\Expense;
use App\Models\Account;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]

class ExpenseManagement extends Component
{
    use WithPagination;

    public $showForm = false;
    public $editingExpense = null;
    public $expenseToDelete = null;
    public $form = [
        'title' => '',
        'description' => '',
        'amount' => '',
        'currency' => 'USD',
        'category' => 'other',
        'account_id' => '',
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

    public function updatedFormAccountId()
    {
        // Auto-set converted currency when account is selected
        if ($this->form['account_id']) {
            $account = Account::find($this->form['account_id']);
            if ($account && $account->currency !== $this->form['currency']) {
                $this->form['converted_currency'] = $account->currency;
            } else {
                $this->form['converted_currency'] = null;
            }
        } else {
            $this->form['converted_currency'] = null;
        }
    }

    public function updatedFormCurrency()
    {
        // Auto-set converted currency when currency is changed
        if ($this->form['account_id']) {
            $account = Account::find($this->form['account_id']);
            if ($account && $account->currency !== $this->form['currency']) {
                $this->form['converted_currency'] = $account->currency;
            } else {
                $this->form['converted_currency'] = null;
            }
        } else {
            $this->form['converted_currency'] = null;
        }
    }

    protected $rules = [
        'form.title' => 'required|string|max:255',
        'form.description' => 'nullable|string',
        'form.amount' => 'required|numeric|min:0.01',
        'form.currency' => 'required|string|size:3',
        'form.category' => 'required|string',
        'form.account_id' => 'required|exists:accounts,id',
        'form.exchange_rate' => 'nullable|numeric|min:0.000001',
        'form.converted_amount' => 'nullable|numeric|min:0.01',
        'form.converted_currency' => 'nullable|string|size:3',
    ];

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingExpense = null;
    }

    public function edit(Expense $expense)
    {
        $this->editingExpense = $expense;
        $this->form = [
            'title' => $expense->title,
            'description' => $expense->description,
            'amount' => $expense->amount,
            'currency' => $expense->currency,
            'category' => $expense->category,
            'account_id' => $expense->account_id,
            'exchange_rate' => $expense->exchange_rate,
            'converted_amount' => $expense->converted_amount,
            'converted_currency' => $expense->converted_currency,
        ];
        $this->showForm = true;
    }

    public function save()
    {
        // Get validation rules
        $rules = [
            'form.title' => 'required|string|max:255',
            'form.description' => 'nullable|string',
            'form.amount' => 'required|numeric|min:0.01',
            'form.currency' => 'required|string|size:3',
            'form.category' => 'required|string',
            'form.account_id' => 'required|exists:accounts,id',
            'form.exchange_rate' => 'nullable|numeric|min:0.000001',
            'form.converted_amount' => 'nullable|numeric|min:0.01',
            'form.converted_currency' => 'nullable|string|size:3',
        ];

        $messages = [
            'form.title.required' => 'Title is required.',
            'form.amount.required' => 'Amount is required.',
            'form.currency.required' => 'Currency is required.',
            'form.category.required' => 'Category is required.',
            'form.account_id.required' => 'Account is required.',
        ];

        // Check if currency conversion is needed
        $account = Account::find($this->form['account_id']);
        if ($account && $account->currency !== $this->form['currency']) {
            $rules['form.exchange_rate'] = 'required|numeric|min:0.000001';
            $rules['form.converted_amount'] = 'required|numeric|min:0.01';
            $rules['form.converted_currency'] = 'required|string|size:3';

            $messages['form.exchange_rate.required'] = 'Exchange rate is required when currencies differ.';
            $messages['form.converted_amount.required'] = 'Converted amount is required when currencies differ.';
            $messages['form.converted_currency.required'] = 'Converted currency is required when currencies differ.';
        }

        $this->validate($rules, $messages);

        $data = $this->form;
        $data['requested_by'] = auth()->id();
        $data['status'] = Expense::STATUS_PENDING;

        // Convert empty strings to null for decimal columns
        $data['exchange_rate'] = !empty($data['exchange_rate']) ? $data['exchange_rate'] : null;
        $data['converted_amount'] = !empty($data['converted_amount']) ? $data['converted_amount'] : null;
        $data['converted_currency'] = !empty($data['converted_currency']) ? $data['converted_currency'] : null;

        if ($this->editingExpense) {
            $wasApproved = in_array($this->editingExpense->status, [Expense::STATUS_APPROVED, Expense::STATUS_PAID]);
            $this->editingExpense->updateWithApprovalReset($data);

            if ($wasApproved) {
                session()->flash('message', 'Expense updated successfully! The expense has been reset to pending status and requires re-approval.');
            } else {
                session()->flash('message', 'Expense updated successfully!');
            }
        } else {
            Expense::create($data);
            session()->flash('message', 'Expense request created successfully!');
        }

        $this->resetForm();
        $this->showForm = false;
    }


    public function approve(Expense $expense)
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('SUPER_ADMIN')) {
            session()->flash('error', 'Only administrators can approve expenses.');
            return;
        }

        // Note: Expenses can be approved even if they result in negative balances
        // This allows for overdraft situations or emergency expenses

        $expense->update([
            'status' => Expense::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Check if transaction already exists (shouldn't happen with new logic, but safety check)
        $existingTransaction = $expense->transaction;
        if ($existingTransaction) {
            $existingTransaction->forceDelete();
        }

        // Create transaction for approved expense
        $transactionData = [
            'transaction_number' => 'TXN' . date('Ymd') . rand(1000, 9999),
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => $expense->amount,
            'currency' => $expense->currency,
            'description' => 'Expense: ' . $expense->title,
            'from_account_id' => $expense->account_id,
            'expense_id' => $expense->id,
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => Transaction::STATUS_PENDING,
        ];

        // Add exchange rate data if available (convert empty strings to null)
        if (!empty($expense->exchange_rate) && !empty($expense->converted_amount) && !empty($expense->converted_currency)) {
            $transactionData['exchange_rate'] = $expense->exchange_rate;
            $transactionData['converted_amount'] = $expense->converted_amount;
            $transactionData['converted_currency'] = $expense->converted_currency;
        } else {
            // Ensure null values for decimal columns
            $transactionData['exchange_rate'] = null;
            $transactionData['converted_amount'] = null;
            $transactionData['converted_currency'] = null;
        }

        $transaction = Transaction::create($transactionData);

        // Process the transaction
        $transaction->process();

        // Update expense status to paid
        $expense->update([
            'status' => Expense::STATUS_PAID,
            'paid_at' => now(),
        ]);

        session()->flash('message', 'Expense approved and processed successfully!');
    }

    public function reject(Expense $expense)
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('SUPER_ADMIN')) {
            session()->flash('error', 'Only administrators can reject expenses.');
            return;
        }

        $expense->update([
            'status' => Expense::STATUS_REJECTED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('message', 'Expense rejected successfully!');
    }

    public function confirmDelete(Expense $expense)
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('SUPER_ADMIN')) {
            session()->flash('error', 'Only administrators can delete expenses.');
            return;
        }

        $this->expenseToDelete = $expense;
    }

    public function delete()
    {
        if (!$this->expenseToDelete) {
            return;
        }

        try {
            // Use the new deletion method with transaction cleanup
            $this->expenseToDelete->deleteWithTransactionCleanup();
            session()->flash('message', 'Expense deleted successfully! Balance restored to account.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete expense: ' . $e->getMessage());
        } finally {
            $this->expenseToDelete = null;
        }
    }

    public function cancelDelete()
    {
        $this->expenseToDelete = null;
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->form = [
            'title' => '',
            'description' => '',
            'amount' => '',
            'currency' => 'USD',
            'category' => 'other',
            'account_id' => '',
            'exchange_rate' => null,
            'converted_amount' => null,
            'converted_currency' => null,
        ];
        $this->editingExpense = null;
    }

    public function render()
    {
        $expenses = Expense::with(['account', 'requestedBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $accounts = Account::active()->get();

        return view('livewire.finance.expense-management', [
            'expenses' => $expenses,
            'accounts' => $accounts,
        ]);
    }
}
