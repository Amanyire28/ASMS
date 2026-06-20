@extends('layouts.app')

@section('title', 'Student Fee Ledger - ' . $student->full_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Payment Ledger</h1>
            <p class="text-gray-600 mt-1">{{ $student->full_name }} ({{ $student->student_id ?? 'N/A' }})</p>
            <p class="text-sm text-gray-500">Class: {{ $student->class->name ?? 'N/A' }}</p>
        </div>
        <a href="{{ route('fees.allocate-fees') }}" class="text-blue-600 hover:text-blue-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Students
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Balance Summary -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
                    <div class="text-sm text-gray-600 uppercase tracking-wide">Total Amount Due</div>
                    <div class="text-2xl font-bold text-blue-600 mt-2">
                        {{ number_format($totalDue, 2) }}
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
                    <div class="text-sm text-gray-600 uppercase tracking-wide">Amount Paid</div>
                    <div class="text-2xl font-bold text-green-600 mt-2">
                        {{ number_format($totalPaid, 2) }}
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 {{ $currentBalance > 0 ? 'border-red-600' : 'border-green-600' }}">
                    <div class="text-sm text-gray-600 uppercase tracking-wide">Outstanding Balance</div>
                    <div class="text-2xl font-bold {{ $currentBalance > 0 ? 'text-red-600' : 'text-green-600' }} mt-2">
                        {{ number_format($currentBalance, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Record Payment Button -->
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Payment</h3>
                <p class="text-sm text-gray-600 mb-4">Use the form below to record a payment from this student</p>
            </div>
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium w-full" 
                    onclick="document.getElementById('paymentForm').scrollIntoView({behavior: 'smooth'})">
                <i class="fas fa-plus mr-2"></i> Add Payment
            </button>
        </div>
    </div>

    <!-- Payment History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Payment History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Credit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($ledger as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $entry['date']->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $entry['description'] }}</div>
                                <span class="text-xs {{ $entry['type'] === 'fee' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }} px-2 py-1 rounded">
                                    {{ ucfirst($entry['type']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($entry['debit'] > 0)
                                    <span class="text-sm font-semibold text-red-600">{{ number_format($entry['debit'], 2) }}</span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($entry['credit'] > 0)
                                    <span class="text-sm font-semibold text-green-600">{{ number_format($entry['credit'], 2) }}</span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-semibold {{ $entry['running_balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($entry['running_balance'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No transactions recorded for this student
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Record Payment Form -->
    <div id="paymentForm" class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Record Payments - Select Fees to Pay</h2>

        <form action="{{ route('fees.record-allocation') }}" method="POST" id="paymentFormEl">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">

            <!-- Outstanding Fees Table -->
            <div class="mb-8">
                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="w-4 h-4 cursor-pointer">
                                </th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Fee Type</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Amount Due</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Already Paid</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Outstanding</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Pay Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" id="feesTable">
                            @forelse ($studentFees as $sf)
                                @php
                                    $feePaid = $sf->payments()->sum('amount') ?? 0;
                                    $outstanding = $sf->amount - $feePaid;
                                @endphp
                                @if ($outstanding > 0)
                                    <tr class="hover:bg-gray-50 fee-row">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" class="fee-checkbox w-4 h-4 cursor-pointer" 
                                                data-fee-id="{{ $sf->id }}" 
                                                data-fee-name="{{ $sf->fee->name }}"
                                                data-outstanding="{{ $outstanding }}">
                                        </td>
                                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $sf->fee->name }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($sf->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-green-600 font-medium">{{ number_format($feePaid, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-red-600 font-semibold">{{ number_format($outstanding, 2) }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <input type="number" step="0.01" min="0" 
                                                class="pay-amount w-24 px-3 py-2 border border-gray-300 rounded text-right text-sm"
                                                data-fee-id="{{ $sf->id }}"
                                                max="{{ $outstanding }}"
                                                placeholder="0.00"
                                                disabled>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-check-circle text-2xl text-green-500 mb-2"></i>
                                        <p class="mt-2">No outstanding fees - student balance is fully paid!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Details Section -->
            <div class="bg-gray-50 border rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method_id" class="block text-sm font-medium text-gray-900 mb-2">Payment Method *</label>
                        <select id="payment_method_id" name="payment_method_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_method_id') border-red-500 @enderror"
                            required>
                            <option value="">Select payment method</option>
                            <option value="1">Cash</option>
                            <option value="2">Check</option>
                            <option value="3">Bank Transfer</option>
                            <option value="4">Mobile Money</option>
                            <option value="5">Online Payment</option>
                        </select>
                        @error('payment_method_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-900 mb-2">Payment Date *</label>
                        <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_date') border-red-500 @enderror"
                            required>
                        @error('payment_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference -->
                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-900 mb-2">Reference (Optional)</label>
                        <input type="text" id="reference" name="reference" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Cheque #12345">
                    </div>
                </div>

                <!-- Summary -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white border rounded-lg p-4">
                        <p class="text-xs text-gray-600 uppercase font-semibold">Selected Fees</p>
                        <p class="text-2xl font-bold text-blue-600" id="selectedCount">0</p>
                    </div>
                    <div class="bg-white border rounded-lg p-4">
                        <p class="text-xs text-gray-600 uppercase font-semibold">Total to Pay</p>
                        <p class="text-2xl font-bold text-green-600" id="totalToPay">0.00</p>
                    </div>
                    <div class="bg-white border rounded-lg p-4">
                        <p class="text-xs text-gray-600 uppercase font-semibold">Remaining Balance</p>
                        <p class="text-2xl font-bold text-red-600" id="remainingBalance">{{ number_format($currentBalance, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>How to use:</strong> Check the boxes for the fees you want to record payments for, enter the amount for each fee (up to the outstanding amount), then click "Record Payment".
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium" id="submitBtn">
                    <i class="fas fa-check mr-2"></i> Record Payment
                </button>
                <a href="{{ route('fees.allocate-fees') }}" class="px-6 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 transition font-medium">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
