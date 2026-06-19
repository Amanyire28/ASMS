@extends('layouts.app')

@section('title', 'Create Fee Schedule')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create Fee Schedule</h1>
        <p class="text-gray-600 mt-2">Set individual amounts for each fee type and automatically assign to students</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-5xl">
        <form action="{{ route('fees.schedules.store') }}" method="POST" id="feeScheduleForm">
            @csrf

            <!-- Classes Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-900 mb-2">Select Classes *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 border border-gray-300 rounded-lg p-4 @error('class_ids') border-red-500 @enderror">
                    @forelse ($classes as $class)
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" 
                                {{ in_array($class->id, old('class_ids', [])) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-900">
                                {{ $class->name }}
                                @if($class->stream)
                                    <span class="text-gray-600">({{ $class->stream->name }})</span>
                                @endif
                            </span>
                        </label>
                    @empty
                        <p class="col-span-full text-gray-500">No classes available</p>
                    @endforelse
                </div>
                @error('class_ids')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fees Selection with Amounts -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-900 mb-2">Select Fees & Set Amounts *</label>
                <div class="border border-gray-300 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Select</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Fee Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Type</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse ($fees as $fee)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="fee_ids[]" value="{{ $fee->id }}" 
                                                class="fee-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                data-fee-id="{{ $fee->id }}"
                                                {{ in_array($fee->id, old('fee_ids', [])) ? 'checked' : '' }}>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $fee->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($fee->category) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($fee->type) }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <input type="number" name="fee_amounts[{{ $fee->id }}]" step="0.01" min="0"
                                                class="fee-amount w-24 px-3 py-2 border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="0.00"
                                                value="{{ old('fee_amounts.' . $fee->id) }}"
                                                {{ in_array($fee->id, old('fee_ids', [])) ? '' : 'disabled' }}>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No fees available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @error('fee_ids')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('fee_amounts')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Amount Display -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="text-sm text-gray-700">
                    <span class="font-medium">Total Fee Amount:</span>
                    <span class="text-lg font-bold text-blue-600 ml-2" id="totalAmount">0.00</span>
                </div>
            </div>

            <!-- Term, Due Date, and Year -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="term" class="block text-sm font-medium text-gray-900 mb-2">Term *</label>
                    <select id="term" name="term" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('term') border-red-500 @enderror"
                        required>
                        <option value="">Select Term</option>
                        <option value="1" {{ old('term') === '1' ? 'selected' : '' }}>Term 1</option>
                        <option value="2" {{ old('term') === '2' ? 'selected' : '' }}>Term 2</option>
                        <option value="3" {{ old('term') === '3' ? 'selected' : '' }}>Term 3</option>
                    </select>
                    @error('term')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-900 mb-2">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('due_date') border-red-500 @enderror"
                        required>
                    @error('due_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-900 mb-2">Academic Year *</label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year') }}" 
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
                    Prices set here are fixed for all students in the selected classes. The total amount is the sum of all individual fees. Once created, you can edit individual fee amounts later.
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Create Schedule & Assign
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
    const checkboxes = document.querySelectorAll('.fee-checkbox');
    const amountInputs = document.querySelectorAll('.fee-amount');

    function updateAmountField(feeId, isChecked) {
        const input = document.querySelector(`input[name="fee_amounts[${feeId}]"]`);
        if (input) {
            input.disabled = !isChecked;
            if (!isChecked) {
                input.value = '';
            }
            input.focus();
        }
    }

    function calculateTotal() {
        let total = 0;
        const checkedCheckboxes = document.querySelectorAll('.fee-checkbox:checked');
        
        checkedCheckboxes.forEach(checkbox => {
            const feeId = checkbox.dataset.feeId;
            const amountInput = document.querySelector(`input[name="fee_amounts[${feeId}]"]`);
            if (amountInput && amountInput.value) {
                total += parseFloat(amountInput.value) || 0;
            }
        });

        document.getElementById('totalAmount').textContent = total.toFixed(2);
    }

    // Handle checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateAmountField(this.dataset.feeId, this.checked);
            calculateTotal();
        });
    });

    // Handle amount input changes
    amountInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Initial calculation
    calculateTotal();

    // Form validation
    document.getElementById('feeScheduleForm').addEventListener('submit', function(e) {
        const checkedFees = document.querySelectorAll('.fee-checkbox:checked');
        if (checkedFees.length === 0) {
            e.preventDefault();
            alert('Please select at least one fee');
            return false;
        }

        let hasEmptyAmount = false;
        checkedFees.forEach(checkbox => {
            const feeId = checkbox.dataset.feeId;
            const amountInput = document.querySelector(`input[name="fee_amounts[${feeId}]"]`);
            if (!amountInput.value || parseFloat(amountInput.value) <= 0) {
                hasEmptyAmount = true;
                amountInput.classList.add('border-red-500');
            }
        });

        if (hasEmptyAmount) {
            e.preventDefault();
            alert('Please enter valid amounts for all selected fees');
            return false;
        }
    });
});
</script>
@endsection
