@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Payment Details</h1>
        <p class="text-gray-600 mt-2">Receipt: {{ $payment->receipt_number }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Information</h2>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm">Receipt Number</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $payment->receipt_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Payment Date</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $payment->payment_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Amount Paid</p>
                        <p class="text-lg font-medium text-green-600 mt-1">{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Payment Method</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $payment->paymentMethod->name }}</p>
                    </div>
                    @if ($payment->transaction_reference)
                        <div>
                            <p class="text-gray-600 text-sm">Transaction Reference</p>
                            <p class="text-lg font-medium text-gray-900 mt-1">{{ $payment->transaction_reference }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-gray-600 text-sm">Recorded By</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $payment->recordedByUser->name }}</p>
                    </div>
                </div>

                @if ($payment->notes)
                    <div class="mt-6 pt-6 border-t">
                        <p class="text-gray-600 text-sm">Notes</p>
                        <p class="text-gray-900 mt-2">{{ $payment->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Fee Information</h2>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm">Fee Name</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $payment->studentFee->fee->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Term</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">Term {{ $payment->studentFee->term }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Original Amount</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ number_format($payment->studentFee->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Due Date</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">
                            {{ $payment->studentFee->due_date ? $payment->studentFee->due_date->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Student Information</h2>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm">Student Name</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">
                            {{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Admission Number</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $payment->studentFee->student->admission_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Class</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $payment->studentFee->student->class?->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                
                <a href="{{ route('fees.payment.receipt', $payment) }}" class="block w-full mb-3 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-file-pdf mr-2"></i> View Receipt
                </a>

                <a href="{{ route('fees.student', $payment->studentFee->student) }}" class="block w-full px-4 py-2 bg-gray-200 text-gray-900 text-center rounded-lg hover:bg-gray-300 transition font-medium">
                    Back to Fees
                </a>

                <div class="mt-6 pt-6 border-t">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Recorded:</strong> {{ $payment->created_at->format('M d, Y g:i A') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
