@extends('layouts.app')

@section('title', 'Fee Schedules')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fee Schedules</h1>
            <p class="text-gray-600 mt-2">Manage fee amounts by class and term</p>
        </div>
        <a href="{{ route('fees.schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> New Schedule
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

    @forelse ($schedules as $schedule)
        <div class="mb-6 bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-blue-600">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-gray-50 border-b">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $schedule->class->name ?? 'N/A' }}
                            @if ($schedule->class?->stream)
                                <span class="text-gray-600 font-normal text-sm">({{ $schedule->class->stream->name }})</span>
                            @endif
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Term {{ $schedule->term }} • {{ $schedule->academic_year }} • Due: {{ $schedule->due_date->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-600 uppercase tracking-wide mb-1">Total Amount</div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ number_format($schedule->total_amount, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fees Details -->
            <div class="px-6 py-4">
                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Included Fees:</label>
                    <div class="mt-3 space-y-2">
                        @if (!empty($schedule->fee_amounts))
                            @foreach ($schedule->fee_amounts as $feeId => $amount)
                                @php
                                    $fee = App\Models\Fee::find($feeId);
                                @endphp
                                @if ($fee)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex-1">
                                            <span class="text-sm font-medium text-gray-900">{{ $fee->name }}</span>
                                            <span class="text-xs text-gray-500 ml-2">{{ ucfirst($fee->category) }}</span>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($amount, 2) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">No fees assigned</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <a href="{{ route('fees.schedules.edit', $schedule) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    <i class="fas fa-edit mr-2"></i> Edit Amounts
                </a>
                <form action="{{ route('fees.schedules.destroy', $schedule) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this schedule? This will not affect student fees already assigned.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-600 mb-4">No fee schedules found</p>
            <a href="{{ route('fees.schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> Create First Schedule
            </a>
        </div>
    @endforelse

    <div class="mt-8 flex justify-center">
        {{ $schedules->links() }}
    </div>
</div>
@endsection
