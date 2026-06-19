@extends('layouts.app')

@section('title', 'Account Statement - ' . $student->first_name . ' ' . $student->last_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Account Statement</h1>
        <p class="text-gray-600 mt-2">
            {{ $student->first_name }} {{ $student->last_name }} | 
            {{ $student->admission_number }} | 
            {{ $student->class?->name }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Billed</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($totalBilled, 2) }}</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Amount Paid</p>
            <p class="text-2xl font-bold text-green-600 mt-2">{{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Outstanding</p>
            <p class="text-2xl font-bold text-red-600 mt-2">{{ number_format($totalOutstanding, 2) }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Balance</p>
            <p class="text-2xl font-bold text-blue-600 mt-2">
                {{ $totalOutstanding > 0 ? '(' . number_format($totalOutstanding, 2) . ')' : number_format(0, 2) }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Statement Summary</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-gray-600 text-sm">School Name</p>
                <p class="font-medium text-gray-900">{{ config('school.school_name', 'ASMS School') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Statement Date</p>
                <p class="font-medium text-gray-900">{{ now()->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Academic Year</p>
                <p class="font-medium text-gray-900">{{ config('school.academic_year', '2024/2025') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Status</p>
                <p class="font-medium">
                    @if ($totalOutstanding == 0)
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Settled</span>
                    @elseif ($totalOutstanding < 0)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">Credit Balance</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">Outstanding</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Detailed Fees</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($studentFees as $studentFee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $studentFee->fee->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($studentFee->fee->category) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm">Term {{ $studentFee->term }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-medium">{{ number_format($studentFee->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-green-600">{{ number_format($studentFee->amount_paid, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-red-600">{{ number_format($studentFee->outstanding, 2) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                <tr>
                    <td colspan="2" class="px-6 py-4 font-bold text-gray-900">TOTALS</td>
                    <td class="px-6 py-4 text-right font-bold">{{ number_format($totalBilled, 2) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">{{ number_format($totalPaid, 2) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-red-600">{{ number_format($totalOutstanding, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h3 class="font-semibold text-blue-900 mb-2">Notes:</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• This statement reflects the account status as of {{ now()->format('M d, Y') }}</li>
            <li>• Payments received after the statement date may not be reflected</li>
            <li>• Please contact the finance office for payment arrangement options</li>
            <li>• Keep this statement for your records</li>
        </ul>
    </div>

    <div class="flex gap-4">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="fas fa-print mr-2"></i> Print Statement
        </button>
        <a href="{{ route('fees.student', $student) }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
            Back to Student Fees
        </a>
    </div>

    <div class="mt-6 text-center text-xs text-gray-500">
        <p>Generated on {{ now()->format('M d, Y H:i:s') }}</p>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
@endsection
