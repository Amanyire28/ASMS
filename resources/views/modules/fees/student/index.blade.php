@extends('layouts.app')

@section('title', 'Student Fees - ' . $student->first_name . ' ' . $student->last_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $student->first_name }} {{ $student->last_name }}'s Fees
        </h1>
        <p class="text-gray-600 mt-2">Admission: {{ $student->admission_number }} | Class: {{ $student->class?->name }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Billed</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($studentFees->sum('amount'), 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Paid</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Outstanding</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($totalOutstanding, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Fees Count</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $studentFees->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Student Fees Details</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse ($studentFees as $studentFee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $studentFee->fee->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ number_format($studentFee->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                                Term {{ $studentFee->term }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">
                                {{ $studentFee->due_date ? $studentFee->due_date->format('M d, Y') : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-green-600">{{ number_format($studentFee->amount_paid, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-red-600">{{ number_format($studentFee->outstanding, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($studentFee->waived)
                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">Waived</span>
                            @elseif ($studentFee->outstanding == 0)
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Paid</span>
                            @elseif ($studentFee->amount_paid > 0)
                                <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Partial</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">Outstanding</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            @if ($studentFee->outstanding > 0 && !$studentFee->waived)
                                <a href="{{ route('fees.payment.record', $studentFee) }}" class="text-green-600 hover:text-green-900 font-medium inline-block">
                                    <i class="fas fa-plus-circle"></i> Payment
                                </a>
                            @endif
                            @if ($studentFee->payments->count() > 0)
                                <button type="button" class="text-blue-600 hover:text-blue-900 font-medium" onclick="showPaymentHistory({{ $studentFee->id }})">
                                    <i class="fas fa-history"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No fees assigned to this student yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex gap-4">
        <a href="{{ route('fees.statement', $student) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="fas fa-file-pdf mr-2"></i> View Statement
        </a>
        <a href="{{ route('students.show', $student) }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
            Back to Student
        </a>
    </div>
</div>
@endsection
