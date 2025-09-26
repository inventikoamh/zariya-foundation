<div>
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Your Donation Impact</h1>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Beneficiaries Helped</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $totalBeneficiaries }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Amount Provided</h3>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($totalAmountProvided, 2) }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Items Provided</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $totalQuantityProvided }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Services Provided</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $totalServiceInstances }}</p>
            </div>
        </div>

        <!-- Donation Details -->
        @if($donorImpact->count() > 0)
            <div class="space-y-6">
                @foreach($donorImpact as $impact)
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $impact['donation']->type_label }} Donation
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ $impact['total_beneficiaries'] }} {{ Str::plural('beneficiary', $impact['total_beneficiaries']) }} helped
                            </p>
                        </div>

                        <!-- Impact Summary -->
                        <div class="mb-6">
                            @if($impact['donation']->type === 'monetary')
                                <p class="text-2xl font-bold text-green-600">${{ number_format($impact['total_amount_provided'], 2) }} provided</p>
                            @elseif($impact['donation']->type === 'materialistic')
                                <p class="text-2xl font-bold text-purple-600">{{ $impact['total_quantity_provided'] }} items provided</p>
                            @elseif($impact['donation']->type === 'service')
                                <p class="text-2xl font-bold text-orange-600">{{ $impact['total_service_instances'] }} service instances</p>
                            @endif
                        </div>

                        <!-- Beneficiaries Helped -->
                        @if($impact['beneficiaries_helped']->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Beneficiaries Helped</h4>
                                <div class="space-y-2">
                                    @foreach($impact['beneficiaries_helped'] as $beneficiary)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    Beneficiary #{{ $beneficiary['beneficiary_id'] }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ ucfirst($beneficiary['donation_type']) }} provided
                                                    @if($beneficiary['amount'])
                                                        - ${{ number_format($beneficiary['amount'], 2) }}
                                                    @elseif($beneficiary['quantity'])
                                                        - {{ $beneficiary['quantity'] }} items
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($beneficiary['status']) }}">
                                                    {{ ucfirst($beneficiary['status']) }}
                                                </span>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($beneficiary['provided_at'])->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <h3 class="text-lg font-medium text-gray-900">No donations provided yet</h3>
                <p class="text-gray-500">Your donations haven't been provided to beneficiaries yet.</p>
            </div>
        @endif
    </div>
</div>
