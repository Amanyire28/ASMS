@extends('layouts.app')

@section('title', 'Edit Fee Schedule')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Fee Schedule</h1>
        <p class="text-gray-600 mt-2">
            {{ $schedule->class->name }} - Term {{ $schedule->term }} - {{ $schedule->academic_year }}
        </p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-4xl">
        <form action="{{ route('fees.schedules.update', $schedule) }}" method="POST" id="editScheduleForm">
            @csrf
            @method('PUT')

            <!-- Fee Amounts Table -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-900 mb-4">Edit Fee Amounts *</label>
                <div class="border border-gray-300 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Fee Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Type</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @if (!empty($schedule->fee_amounts))
                                    @foreach ($schedule->fee_amounts as $feeId => $amount)
                                        @php
                                            $fee = App\Models\Fee::find($feeId);
                                        @endphp
                                        @if ($fee)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $fee->name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($fee->category) }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($fee->type) }}</td>
                                                <td class="px-4 py-3 text-right">
                                                    <input type="number" name="fee_amounts[{{ $feeId }}]" step="0.01" min="0"
                                                        class="fee-amount w-32 px-3 py-2 border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                        value="{{ number_format($amount, 2, '.', '') }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @error('fee_amounts')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Amount Display -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="text-sm text-gray-700">
                    <span class="font-medium">Total Amount:</span>
                    <span class="text-lg font-bold text-blue-600 ml-2" id="totalAmount">
                        {{ number_format($schedule->total_amount, 2) }}
                    </span>
                </div>
            </div>

            <!-- Schedule Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="term" class="block text-sm font-medium text-gray-900 mb-2">Term *</label>
                    <select id="term" name="term" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('term') border-red-500 @enderror"
                        required>
                        <option value="1" {{ old('term', $schedule->term) === '1' ? 'selected' : '' }}>Term 1</option>
                        <option value="2" {{ old('term', $schedule->term) === '2' ? 'selected' : '' }}>Term 2</option>
                        <option value="3" {{ old('term', $schedule->term) === '3' ? 'selected' : '' }}>Term 3</option>
                    </select>
                    @error('term')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-900 mb-2">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $schedule->due_date->format('Y-m-d')) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('due_date') border-red-500 @enderror"
                        required>
                    @error('due_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-900 mb-2">Academic Year *</label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', $schedule->academic_year) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('academic_year') border-red-500 @enderror"
                        placeholder="e.g., 2024/2025" required>
                    @error('academic_year')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Editing these amounts will update the schedule. Student fees already assigned will not be automatically updated - only new assignments will use these amounts.
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Update Schedule
                </button>
                <a href="{{ route('fees.schedules') }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInputs = document.querySelectorAll('.fee-amount');

    function calculateTotal() {
        let total = 0;
        amountInputs.forEach(input => {
            if (input.value) {
                total += parseFloat(input.value) || 0;
            }
        });
        document.getElementById('totalAmount').textContent = total.toFixed(2);
    }

    amountInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Initial calculation
    calculateTotal();
});
</script>
@endsection
