<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Donation;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.admin')]

class FinanceDashboard extends Component
{
    public function render()
    {
        $totalAccounts = Account::active()->count();
        $totalBalance = Account::active()->sum('current_balance');
        
        $thisMonth = Carbon::now()->startOfMonth();
        $thisMonthTransactions = Transaction::where('created_at', '>=', $thisMonth)->count();
        $thisMonthIncome = Transaction::where('created_at', '>=', $thisMonth)
            ->where('type', Transaction::TYPE_INCOME)
            ->sum('amount');
        $thisMonthExpenses = Transaction::where('created_at', '>=', $thisMonth)
            ->where('type', Transaction::TYPE_EXPENSE)
            ->sum('amount');
        $thisMonthDonations = Transaction::where('created_at', '>=', $thisMonth)
            ->where('type', Transaction::TYPE_DONATION)
            ->sum('amount');

        $pendingExpenses = Expense::where('status', Expense::STATUS_PENDING)->count();
        $pendingTransactions = Transaction::where('status', Transaction::STATUS_PENDING)->count();

        $recentTransactions = Transaction::with(['fromAccount', 'toAccount', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentExpenses = Expense::with(['account', 'requestedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $accounts = Account::active()->get();

        return view('livewire.finance.finance-dashboard', [
            'totalAccounts' => $totalAccounts,
            'totalBalance' => $totalBalance,
            'thisMonthTransactions' => $thisMonthTransactions,
            'thisMonthIncome' => $thisMonthIncome,
            'thisMonthExpenses' => $thisMonthExpenses,
            'thisMonthDonations' => $thisMonthDonations,
            'pendingExpenses' => $pendingExpenses,
            'pendingTransactions' => $pendingTransactions,
            'recentTransactions' => $recentTransactions,
            'recentExpenses' => $recentExpenses,
            'accounts' => $accounts,
        ]);
    }
}
