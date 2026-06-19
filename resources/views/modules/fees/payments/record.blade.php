@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Record Payment</h1>
        <p class="text-gray-600 mt-2">
            {{ $studentFee->student->first_name }} {{ $studentFee->student->last_name }} - 
            {{ $studentFee->fee->name }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Fee Amount</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($studentFee->amount, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Already Paid</p>
            <p class="text-2xl font-bold text-green-600 mt-2">{{ number_format($studentFee->amount_paid, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Outstanding</p>
            <p class="text-2xl font-bold text-red-600 mt-2">{{ number_format($studentFee->outstanding, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
        <form action="{{ route('fees.payment.store', $studentFee) }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-900 mb-2">Payment Amount *</label>
                <div class="relative">
                    <span class="absolute left-4 top-2 text-gray-500">Shs</span>
                    <input type="number" id="amount" name="amount" step="0.01" value="{{ old('amount') }}" 
                        max="{{ $studentFee->outstanding }}"
                        class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                        placeholder="0.00" required>
                </div>
                <p class="text-xs text-gray-500 mt-2">Maximum: {{ number_format($studentFee->outstanding, 2) }}</p>
                @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="payment_method_id" class="block text-sm font-medium text-gray-900 mb-2">Payment Method *</label>
                <select id="payment_method_id" name="payment_method_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_method_id') border-red-500 @enderror"
                    required>
                    <option value="">Select Payment Method</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}" {{ old('payment_method_id') === (string)$method->id ? 'selected' : '' }}>
                            {{ $method->name }}
                        </option>
                    @endforeach
                </select>
                @error('payment_method_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="payment_date" class="block text-sm font-medium text-gray-900 mb-2">Payment Date *</label>
                <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_date') border-red-500 @enderror"
                    required>
                @error('payment_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="transaction_reference" class="block text-sm font-medium text-gray-900 mb-2">Transaction Reference</label>
                <input type="text" id="transaction_reference" name="transaction_reference" value="{{ old('transaction_reference') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('transaction_reference') border-red-500 @enderror"
                    placeholder="e.g., Check #, Mobile Money reference, etc.">
                @error('transaction_reference')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label for="notes" class="block text-sm font-medium text-gray-900 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                    placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    A receipt will be automatically generated with receipt number and timestamp after payment is recorded.
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    <i class="fas fa-check mr-2"></i> Record Payment
                </button>
                <a href="{{ route('fees.student', $studentFee->student) }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
