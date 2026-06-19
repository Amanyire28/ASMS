@extends('layouts.app')

@section('title', 'Overdue Fees Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Overdue Fees Report</h1>
        <p class="text-gray-600 mt-2">Students with outstanding fees past due date</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-red-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Overdue Records</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $countOverdue }}</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Overdue Amount</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($totalOverdue, 2) }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Report Generated</p>
            <p class="text-lg font-medium text-gray-900 mt-2">{{ now()->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse ($overdueRecords as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $record->student->first_name }} {{ $record->student->last_name }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $record->student->admission_number }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $record->student->class?->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $record->fee->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ number_format($record->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-red-600">{{ number_format($record->outstanding, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ $record->due_date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">
                                {{ $record->daysOverdue() }} days
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <a href="{{ route('fees.student', $record->student) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('fees.payment.record', $record) }}" class="text-green-600 hover:text-green-900 font-medium">
                                <i class="fas fa-plus"></i> Payment
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No overdue fees found. Great!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center">
        {{ $overdueRecords->links() }}
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
