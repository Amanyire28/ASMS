@extends('layouts.app')

@section('title', 'Fees Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fees Management</h1>
            <p class="text-gray-600 mt-2">Manage school fees and fee types</p>
        </div>
        <a href="{{ route('fees.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> Add New Fee
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($fees as $fee)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden border-l-4 border-blue-500">
                <div class="p-6">
                    <!-- Fee Name -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $fee->name }}</h3>
                    
                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($fee->description, 60) ?? 'No description' }}</p>
                    
                    <!-- Details Section -->
                    <div class="space-y-3 mb-5 pb-4 border-b border-gray-200">
                        <!-- Category -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-600 uppercase">Category</span>
                            <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                                {{ ucfirst($fee->category) }}
                            </span>
                        </div>
                        
                        <!-- Type -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-600 uppercase">Type</span>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($fee->type) }}</span>
                        </div>
                        
                        <!-- Status -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-600 uppercase">Status</span>
                            @if ($fee->is_active)
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-3">
                        <a href="{{ route('fees.edit', $fee) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition font-medium text-sm">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                        <form action="{{ route('fees.destroy', $fee) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this fee?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded transition font-medium text-sm">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-folder-open text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">No fees found.</p>
                <a href="{{ route('fees.create') }}" class="text-blue-600 hover:underline font-medium mt-2 inline-block">Create one now</a>
            </div>
        @endforelse
    </div>

    <div class="flex justify-center mt-8">
        {{ $fees->links() }}
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('fees.schedules') }}" class="px-6 py-4 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-indigo-600 text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-gray-900">Fee Schedules</h3>
                    <p class="text-sm text-gray-600">Set fee amounts by class and term</p>
                </div>
            </div>
        </a>
        <a href="{{ route('fees.allocate-fees') }}" class="px-6 py-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition">
            <div class="flex items-center">
                <i class="fas fa-user-check text-green-600 text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-gray-900">Allocate Fees</h3>
                    <p class="text-sm text-gray-600">Record payments and manage student balances</p>
                </div>
            </div>
        </a>
        <a href="{{ route('fees.reports.payment-status') }}" class="px-6 py-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
            <div class="flex items-center">
                <i class="fas fa-file-invoice-dollar text-blue-600 text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-gray-900">Payment Status Report</h3>
                    <p class="text-sm text-gray-600">View fully paid, partial, unpaid, and defaulted students</p>
                </div>
            </div>
        </a>
        <a href="{{ route('fees.reports.overdue') }}" class="px-6 py-4 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-gray-900">Overdue Fees</h3>
                    <p class="text-sm text-gray-600">View overdue student fees</p>
                </div>
            </div>
        </a>
        <a href="{{ route('fees.reports.collection') }}" class="px-6 py-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition">
            <div class="flex items-center">
                <i class="fas fa-chart-bar text-purple-600 text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-gray-900">Collection Report</h3>
                    <p class="text-sm text-gray-600">Fee collection analytics</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
