@extends('layouts.app')

@section('title', 'Payment Status Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Payment Status Report</h1>
            <p class="text-gray-600 mt-2">Review fee payment performance across the school or by class.</p>
        </div>
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="fas fa-print mr-2"></i> Print Report
        </button>
    </div>

    <div class="mb-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Generate Report</h2>
        <form method="GET" action="{{ route('fees.reports.payment-status') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                <select id="class_id" name="class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Classes</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ (string) $selectedClassId === (string) $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All Students</option>
                    <option value="fully-paid" {{ $selectedStatus === 'fully-paid' ? 'selected' : '' }}>Fully Paid</option>
                    <option value="partial" {{ $selectedStatus === 'partial' ? 'selected' : '' }}>Partial Payment</option>
                    <option value="unpaid" {{ $selectedStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="defaulted" {{ $selectedStatus === 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                    <option value="no-fees" {{ $selectedStatus === 'no-fees' ? 'selected' : '' }}>No Fees Assigned</option>
                </select>
            </div>
            <div class="md:col-span-2 flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-search mr-2"></i> View Report
                </button>
                <a href="{{ route('fees.reports.payment-status') }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Students in Report</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $summary['students_count'] }}</p>
        </div>
        <div class="bg-indigo-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Amount Payable</p>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ number_format($summary['total_payable'], 2) }}</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Paid</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($summary['total_paid'], 2) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Remaining Balance</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($summary['remaining_balance'], 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 uppercase tracking-wide">Fully Paid Students</p>
            <p class="text-2xl font-bold text-green-600 mt-2">{{ $summary['fully_paid_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <p class="text-sm text-gray-600 uppercase tracking-wide">Defaulted Students</p>
            <p class="text-2xl font-bold text-red-600 mt-2">{{ $summary['defaulted_count'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Student Payment Breakdown</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount to Pay</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($paginatedRows as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $row['student']->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $row['student']->admission_number ?? $row['student']->student_id }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $row['class_name'] }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ number_format($row['total_payable'], 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-green-600">{{ number_format($row['total_paid'], 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-red-600">{{ number_format($row['remaining_balance'], 2) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'fully-paid' => 'bg-green-100 text-green-800',
                                        'partial' => 'bg-yellow-100 text-yellow-800',
                                        'unpaid' => 'bg-orange-100 text-orange-800',
                                        'defaulted' => 'bg-red-100 text-red-800',
                                        'no-fees' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusLabels = [
                                        'fully-paid' => 'Fully Paid',
                                        'partial' => 'Partial',
                                        'unpaid' => 'Unpaid',
                                        'defaulted' => 'Defaulted',
                                        'no-fees' => 'No Fees',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusClasses[$row['status']] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$row['status']] ?? ucfirst($row['status']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('fees.student', $row['student']) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    <i class="fas fa-eye mr-1"></i> View Fees
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No students match the selected report filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 flex justify-center">
        {{ $paginatedRows->links() }}
    </div>

    <div class="mt-8 flex gap-4">
        <a href="{{ route('fees.reports.collection') }}" class="px-6 py-2 bg-purple-100 text-purple-900 rounded-lg hover:bg-purple-200 transition font-medium">
            View Collection Report
        </a>
        <a href="{{ route('fees.reports.overdue') }}" class="px-6 py-2 bg-yellow-100 text-yellow-900 rounded-lg hover:bg-yellow-200 transition font-medium">
            View Overdue Report
        </a>
        <a href="{{ route('fees.index') }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
            Back to Fees
        </a>
    </div>
</div>
@endsection