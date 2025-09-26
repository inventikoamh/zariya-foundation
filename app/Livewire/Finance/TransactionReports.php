<?php

namespace App\Livewire\Finance;

use App\Models\Transaction;
use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.admin')]

class TransactionReports extends Component
{
    use WithPagination;

    public $filters = [
        'type' => '',
        'status' => '',
        'account_id' => '',
        'date_from' => '',
        'date_to' => '',
    ];

    public $summary = [];

    public function mount()
    {
        $this->filters['date_from'] = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filters['date_to'] = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->loadSummary();
    }

    public function updatedFilters()
    {
        $this->resetPage();
        $this->loadSummary();
    }

    public function loadSummary()
    {
        $query = Transaction::query();

        // Apply filters
        if ($this->filters['type']) {
            $query->where('type', $this->filters['type']);
        }

        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['account_id']) {
            $query->byAccount($this->filters['account_id']);
        }

        if ($this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if ($this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        // Calculate summary
        $this->summary = [
            'total_transactions' => $query->count(),
            'total_income' => $query->clone()->where('type', Transaction::TYPE_INCOME)->sum('amount'),
            'total_expenses' => $query->clone()->where('type', Transaction::TYPE_EXPENSE)->sum('amount'),
            'total_transfers' => $query->clone()->where('type', Transaction::TYPE_TRANSFER)->sum('amount'),
            'total_donations' => $query->clone()->where('type', Transaction::TYPE_DONATION)->sum('amount'),
            'completed_transactions' => $query->clone()->where('status', Transaction::STATUS_COMPLETED)->count(),
            'pending_transactions' => $query->clone()->where('status', Transaction::STATUS_PENDING)->count(),
        ];
    }

    public function exportReport()
    {
        try {
            // Get all transactions with the same filters
            $query = Transaction::with(['fromAccount', 'toAccount', 'createdBy', 'approvedBy', 'donation', 'expense']);

            // Apply filters
            if ($this->filters['type']) {
                $query->where('type', $this->filters['type']);
            }

            if ($this->filters['status']) {
                $query->where('status', $this->filters['status']);
            }

            if ($this->filters['account_id']) {
                $query->byAccount($this->filters['account_id']);
            }

            if ($this->filters['date_from']) {
                $query->whereDate('created_at', '>=', $this->filters['date_from']);
            }

            if ($this->filters['date_to']) {
                $query->whereDate('created_at', '<=', $this->filters['date_to']);
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();
            $accounts = Account::active()->get();

            // Generate CSV content
            $csvContent = $this->generateCsvContent($transactions, $accounts);

            // Generate filename with date range
            $dateFrom = $this->filters['date_from'] ?: 'all';
            $dateTo = $this->filters['date_to'] ?: 'all';
            $filename = "transaction_report_{$dateFrom}_to_{$dateTo}_" . now()->format('Y-m-d_H-i-s') . '.csv';

            // Return download response
            return response()->streamDownload(function () use ($csvContent) {
                echo $csvContent;
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export report: ' . $e->getMessage());
            return;
        }
    }

    private function generateCsvContent($transactions, $accounts)
    {
        $output = fopen('php://temp', 'r+');

        // CSV Headers
        $headers = [
            'Transaction #',
            'Type',
            'Status',
            'Amount',
            'Currency',
            'Exchange Rate',
            'Converted Amount',
            'Converted Currency',
            'From Account',
            'To Account',
            'Description',
            'Notes',
            'Created By',
            'Created At',
            'Approved By',
            'Approved At',
            'Processed At'
        ];
        fputcsv($output, $headers);

        // Transaction data
        foreach ($transactions as $transaction) {
            $row = [
                $transaction->transaction_number,
                $transaction->type_label,
                $transaction->status_label,
                $transaction->amount,
                $transaction->currency,
                $transaction->exchange_rate ?: '',
                $transaction->converted_amount ?: '',
                $transaction->converted_currency ?: '',
                $transaction->fromAccount->name ?? '',
                $transaction->toAccount->name ?? '',
                $transaction->description,
                $transaction->notes,
                $transaction->createdBy->name ?? '',
                $transaction->created_at->format('Y-m-d H:i:s'),
                $transaction->approvedBy->name ?? '',
                $transaction->approved_at ? $transaction->approved_at->format('Y-m-d H:i:s') : '',
                $transaction->processed_at ? $transaction->processed_at->format('Y-m-d H:i:s') : ''
            ];
            fputcsv($output, $row);
        }

        // Add summary section
        fputcsv($output, []); // Empty row
        fputcsv($output, ['SUMMARY']);
        fputcsv($output, ['Total Transactions', $this->summary['total_transactions']]);
        fputcsv($output, ['Total Income', $this->summary['total_income']]);
        fputcsv($output, ['Total Expenses', $this->summary['total_expenses']]);
        fputcsv($output, ['Total Transfers', $this->summary['total_transfers']]);
        fputcsv($output, ['Total Donations', $this->summary['total_donations']]);
        fputcsv($output, ['Completed Transactions', $this->summary['completed_transactions']]);
        fputcsv($output, ['Pending Transactions', $this->summary['pending_transactions']]);

        // Add account balances section
        fputcsv($output, []); // Empty row
        fputcsv($output, ['ACCOUNT BALANCES']);
        fputcsv($output, ['Account Name', 'Account Number', 'Type', 'Currency', 'Current Balance', 'Opening Balance', 'Status']);

        foreach ($accounts as $account) {
            fputcsv($output, [
                $account->name,
                $account->account_number,
                $account->type_label,
                $account->currency,
                $account->current_balance,
                $account->opening_balance,
                $account->is_active ? 'Active' : 'Inactive'
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    public function render()
    {
        $query = Transaction::with(['fromAccount', 'toAccount', 'createdBy', 'approvedBy', 'donation', 'expense']);

        // Apply filters
        if ($this->filters['type']) {
            $query->where('type', $this->filters['type']);
        }

        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['account_id']) {
            $query->byAccount($this->filters['account_id']);
        }

        if ($this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if ($this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        $accounts = Account::active()->get();

        return view('livewire.finance.transaction-reports', [
            'transactions' => $transactions,
            'accounts' => $accounts,
        ]);
    }
}
