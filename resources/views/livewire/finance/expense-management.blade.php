<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Expense Management</h2>
        <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Request New Expense
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    <!-- Information Notice -->
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Expense Approval Policy
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Expenses can be approved even if they result in negative account balances. This allows for emergency expenses and overdraft situations. Transfers, however, require sufficient funds.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Form Modal -->
    @if($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $editingExpense ? 'Edit Expense' : 'Request New Expense' }}
                    </h3>

                    @if($editingExpense && in_array($editingExpense->status, ['approved', 'paid']))
                        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-md p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Approved/Paid Expense
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>This expense has been approved and processed. Updating it will:</p>
                                        <ul class="list-disc list-inside mt-1">
                                            <li>Restore the original amount to the current account</li>
                                            <li>Delete the associated transaction</li>
                                            <li>Reset the expense to pending status</li>
                                            <li>Require re-approval before processing</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" wire:model="form.title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea wire:model="form.description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                @error('form.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount</label>
                                <input type="number" step="0.01" wire:model="form.amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Currency</label>
                                <select wire:model="form.currency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="INR">INR</option>
                                </select>
                                @error('form.currency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <select wire:model="form.category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="office_supplies">Office Supplies</option>
                                    <option value="utilities">Utilities</option>
                                    <option value="transportation">Transportation</option>
                                    <option value="communication">Communication</option>
                                    <option value="equipment">Equipment</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="training">Training</option>
                                    <option value="events">Events</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="legal">Legal</option>
                                    <option value="insurance">Insurance</option>
                                    <option value="rent">Rent</option>
                                    <option value="salaries">Salaries</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('form.category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Account</label>
                                <select wire:model.live="form.account_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->formatted_balance }})</option>
                                    @endforeach
                                </select>
                                @error('form.account_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            @if($form['account_id'] && $form['currency'])
                                @php
                                    $selectedAccount = $accounts->firstWhere('id', $form['account_id']);
                                @endphp
                                @if($selectedAccount && $selectedAccount->currency !== $form['currency'])
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                        <h4 class="text-sm font-medium text-yellow-800 mb-3">Currency Conversion Required</h4>
                                        <p class="text-sm text-yellow-700 mb-3">
                                            The expense currency ({{ $form['currency'] }}) differs from the account currency ({{ $selectedAccount->currency }}).
                                            Please provide exchange rate information.
                                        </p>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Exchange Rate *</label>
                                                <input type="number" step="0.000001" wire:model="form.exchange_rate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 0.85">
                                                @error('form.exchange_rate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                <p class="text-xs text-gray-500 mt-1">Required when currencies differ</p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Converted Amount</label>
                                                <input type="number" step="0.01" wire:model="form.converted_amount" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600" placeholder="Auto-calculated">
                                                @error('form.converted_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                <p class="text-xs text-gray-500 mt-1">Automatically calculated from amount × exchange rate</p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Converted Currency</label>
                                                <input type="text" wire:model="form.converted_currency" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600">
                                                <p class="text-xs text-gray-500 mt-1">Account currency: {{ $selectedAccount->currency }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="cancel" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                {{ $editingExpense ? 'Update' : 'Request' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Expenses Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expense</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($expenses as $expense)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
<div>
                                        <div class="text-sm font-medium text-gray-900">{{ $expense->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($expense->description, 50) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($expense->hasCurrencyConversion())
                                        <div class="space-y-1">
                                            <div class="font-medium">{{ $expense->formatted_amount }}</div>
                                            <div class="text-xs text-gray-500 bg-blue-50 px-2 py-1 rounded">
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                    </svg>
                                                    <span>Rate: {{ number_format($expense->exchange_rate, 6) }}</span>
                                                </div>
                                                <div class="text-blue-600 font-medium">
                                                    {{ $expense->converted_currency }} {{ number_format($expense->converted_amount, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{ $expense->formatted_amount }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $expense->category_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $expense->account->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'paid' => 'bg-blue-100 text-blue-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$expense->status] }}">
                                        {{ $expense->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $expense->requestedBy->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if($expense->status === 'pending' && auth()->user()->hasRole('SUPER_ADMIN'))
                                        <button wire:click="approve({{ $expense->id }})" class="text-green-600 hover:text-green-900">Approve</button>
                                        <button wire:click="reject({{ $expense->id }})" class="text-red-600 hover:text-red-900">Reject</button>
                                    @endif
                                    <button wire:click="edit({{ $expense->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                                    @if(auth()->user()->hasRole('SUPER_ADMIN'))
                                        <button wire:click="confirmDelete({{ $expense->id }})"
                                                class="text-red-600 hover:text-red-900">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No expenses found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($expenseToDelete)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="cancelDelete">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Delete Expense</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete the expense "{{ $expenseToDelete->title }}"?
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            This will restore the balance to the account and delete the associated transaction.
                        </p>
                        @if($expenseToDelete->status === 'approved')
                            <p class="text-sm text-yellow-600 mt-2 font-medium">
                                Amount to restore: {{ $expenseToDelete->formatted_amount }}
                            </p>
                        @endif
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="delete" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Delete
                        </button>
                        <button wire:click="cancelDelete" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
