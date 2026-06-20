@extends('layouts.app')

@section('title', 'Payment Status Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Payment Status Report</h1>
            <p class="mt-2 text-gray-600">View fee payment performance for the whole school or a selected class.</p>
        </div>

        <form method="GET" action="{{ route('fees.reports.payment-status') }}" class="grid grid-cols-1 gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-3">
            <div>
                <label for="class_id" class="mb-2 block text-sm font-medium text-gray-900">Class</label>
                <select id="class_id" name="class_id" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Classes</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ (string) $classId === (string) $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-gray-900">Status</label>
                <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Students</option>
                    <option value="fully_paid" {{ $status === 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                    <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="defaulted" {{ $status === 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                    <option value="no_fees" {{ $status === 'no_fees' ? 'selected' : '' }}>No Fees</option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Generate
                </button>
                <a href="{{ route('fees.reports.payment-status') }}" class="inline-flex items-center rounded-lg bg-gray-200 px-4 py-2 font-medium text-gray-900 transition hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Students</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['student_count'] }}</p>
            <p class="mt-1 text-sm text-gray-500">{{ $summary['fully_paid_count'] }} fully paid, {{ $summary['defaulted_count'] }} defaulted</p>
        </div>
        <div class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Total Due</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($summary['total_due'], 2) }}</p>
            <p class="mt-1 text-sm text-gray-500">Expected from selected students</p>
        </div>
        <div class="rounded-xl border border-green-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Amount Paid</p>
            <p class="mt-2 text-3xl font-bold text-green-700">{{ number_format($summary['amount_paid'], 2) }}</p>
            <p class="mt-1 text-sm text-gray-500">Collected so far</p>
        </div>
        <div class="rounded-xl border border-red-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Remaining Balance</p>
            <p class="mt-2 text-3xl font-bold text-red-700">{{ number_format($summary['balance'], 2) }}</p>
            <p class="mt-1 text-sm text-gray-500">Outstanding across this report</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">Students Payment Summary</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Class</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Amount Due</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($reportRows as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $row['student']->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $row['student']->student_id ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $row['class'] }}</td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">{{ number_format($row['total_due'], 2) }}</td>
                            <td class="px-6 py-4 text-right font-medium text-green-700">{{ number_format($row['amount_paid'], 2) }}</td>
                            <td class="px-6 py-4 text-right font-medium {{ $row['balance'] > 0 ? 'text-red-700' : 'text-gray-900' }}">{{ number_format($row['balance'], 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $badgeClasses = match ($row['status']) {
                                        'fully_paid' => 'bg-green-100 text-green-800',
                                        'partial' => 'bg-yellow-100 text-yellow-800',
                                        'defaulted' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">{{ $row['status_label'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">No students matched the selected report filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection