@extends('layouts.app')

@section('title', 'Fee Collection Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Fee Collection Report</h1>
        <p class="text-gray-600 mt-2">Fee payment analytics and collection summary</p>
    </div>

    <div class="mb-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter by Date Range</h2>
        <form method="GET" action="{{ route('fees.reports.collection') }}" class="flex gap-4 flex-wrap items-end">
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" id="from" name="from" value="{{ $dateFrom }}" class="px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" id="to" name="to" value="{{ $dateTo }}" class="px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-search mr-2"></i> Filter
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-green-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Collected</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($totalCollected, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $dateFrom }} to {{ $dateTo }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Transactions</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $payments->total() }}</p>
        </div>
        <div class="bg-purple-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Average per Transaction</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">
                {{ $payments->total() > 0 ? number_format($totalCollected / $payments->total(), 2) : '0.00' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Collection by Payment Method</h2>
            <div class="space-y-4">
                @forelse ($collectionByMethod as $method)
                    <div class="flex items-center justify-between pb-4 border-b">
                        <span class="text-gray-900 font-medium">{{ $method->name }}</span>
                        <span class="text-lg font-bold text-green-600">{{ number_format($method->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500">No payment data available</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Collection Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Amount Collected:</span>
                    <span class="font-bold text-gray-900">{{ number_format($totalCollected, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Number of Transactions:</span>
                    <span class="font-bold text-gray-900">{{ $payments->total() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Period:</span>
                    <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($dateFrom)->diffForHumans(\Carbon\Carbon::parse($dateTo), true) }}</span>
                </div>
                <div class="flex justify-between pt-4 border-t">
                    <span class="text-gray-600 font-medium">Avg per Transaction:</span>
                    <span class="font-bold text-green-600 text-lg">
                        {{ $payments->total() > 0 ? number_format($totalCollected / $payments->total(), 2) : '0.00' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Payment Transactions</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $payment->receipt_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $payment->studentFee->student->admission_number }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $payment->studentFee->fee->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-green-600">{{ number_format($payment->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                                {{ $payment->paymentMethod->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ $payment->payment_date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('fees.payment.view', $payment) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No payments found for the selected date range.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center">
        {{ $payments->links() }}
    </div>

    <div class="mt-8 flex gap-4">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="fas fa-print mr-2"></i> Print Report
        </button>
        <a href="{{ route('fees.index') }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
            Back to Fees
        </a>
    </div>
</div>
@endsection
