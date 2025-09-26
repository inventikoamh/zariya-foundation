<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

#[Layout('layouts.admin')]

class AccountManagement extends Component
{
    use WithPagination;

    public $showForm = false;
    public $editingAccount = null;
    public $form = [
        'name' => '',
        'account_number' => '',
        'type' => 'bank',
        'bank_name' => '',
        'branch_name' => '',
        'ifsc_code' => '',
        'currency' => 'USD',
        'opening_balance' => 0,
        'description' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.account_number' => 'required|string|unique:accounts,account_number',
        'form.type' => 'required|in:bank,cash',
        'form.bank_name' => 'nullable|string|max:255',
        'form.branch_name' => 'nullable|string|max:255',
        'form.ifsc_code' => 'nullable|string|max:11',
        'form.currency' => 'required|string|size:3',
        'form.opening_balance' => 'required|numeric|min:0',
        'form.description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->form['account_number'] = 'ACC' . date('Ymd') . strtoupper(Str::random(6));
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingAccount = null;
    }

    public function edit(Account $account)
    {
        $this->editingAccount = $account;
        $this->form = [
            'name' => $account->name,
            'account_number' => $account->account_number,
            'type' => $account->type,
            'bank_name' => $account->bank_name,
            'branch_name' => $account->branch_name,
            'currency' => $account->currency,
            'opening_balance' => $account->opening_balance,
            'description' => $account->description,
        ];
        $this->showForm = true;
    }

    public function save()
    {
        if ($this->editingAccount) {
            $this->rules['form.account_number'] = 'required|string|unique:accounts,account_number,' . $this->editingAccount->id;
        }

        $this->validate();

        $data = $this->form;
        $data['current_balance'] = $data['opening_balance'];
        $data['created_by'] = auth()->id();

        if ($this->editingAccount) {
            // Check if opening balance is being changed
            if ($this->editingAccount->opening_balance != $data['opening_balance']) {
                $this->editingAccount->updateOpeningBalance($data['opening_balance']);
                session()->flash('message', 'Account updated successfully! Opening balance changed and current balance adjusted accordingly.');
            } else {
                $this->editingAccount->update($data);
                session()->flash('message', 'Account updated successfully!');
            }
        } else {
            Account::create($data);
            session()->flash('message', 'Account created successfully!');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(Account $account)
    {
        $account->delete();
        session()->flash('message', 'Account deleted successfully!');
    }

    public function toggleStatus(Account $account)
    {
        $account->update(['is_active' => !$account->is_active]);
        session()->flash('message', 'Account status updated successfully!');
    }

    public function recalculateBalance(Account $account)
    {
        $account->recalculateBalance();
        session()->flash('message', 'Account balance recalculated successfully!');
    }


    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->form = [
            'name' => '',
            'account_number' => 'ACC' . date('Ymd') . strtoupper(Str::random(6)),
            'type' => 'bank',
            'bank_name' => '',
            'branch_name' => '',
            'ifsc_code' => '',
            'currency' => 'USD',
            'opening_balance' => 0,
            'description' => '',
        ];
        $this->editingAccount = null;
    }

    public function render()
    {
        $accounts = Account::with('createdBy')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.finance.account-management', [
            'accounts' => $accounts,
        ]);
    }
}
