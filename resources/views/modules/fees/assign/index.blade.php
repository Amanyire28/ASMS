@extends('layouts.app')

@section('title', 'Assign Fees to Students')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Assign Fees to Students</h1>
        <p class="text-gray-600 mt-2">Bulk assign fees to all students in a class</p>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
        <form action="{{ route('fees.assign.bulk') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="class_id" class="block text-sm font-medium text-gray-900 mb-2">Select Class *</label>
                <select id="class_id" name="class_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('class_id') border-red-500 @enderror"
                    required>
                    <option value="">Choose a class</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') === (string)$class->id ? 'selected' : '' }}>
                            {{ $class->name }} @if($class->stream)({{ $class->stream->name }})@endif
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-900 mb-4">Select Fees *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 @error('fee_ids') border-2 border-red-500 rounded-lg p-4 @enderror">
                    @forelse ($fees as $fee)
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="fee_ids[]" value="{{ $fee->id }}" 
                                {{ in_array($fee->id, old('fee_ids', [])) ? 'checked' : '' }}
                                class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">{{ $fee->name }}</span>
                                <span class="block text-xs text-gray-600 mt-1">{{ ucfirst($fee->category) }} - {{ ucfirst($fee->type) }}</span>
                            </span>
                        </label>
                    @empty
                        <p class="text-gray-500 text-sm">No active fees available</p>
                    @endforelse
                </div>
                @error('fee_ids')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
                    <label for="academic_year" class="block text-sm font-medium text-gray-900 mb-2">Academic Year *</label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('academic_year') border-red-500 @enderror"
                        placeholder="e.g., 2024/2025" required>
                    @error('academic_year')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    These fees will be assigned to all active students in the selected class. Fees already assigned to students will be skipped.
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Assign Fees
                </button>
                <a href="{{ route('fees.index') }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
