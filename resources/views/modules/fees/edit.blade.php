@extends('layouts.app')

@section('title', 'Edit Fee')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Fee</h1>
        <p class="text-gray-600 mt-2">{{ $fee->name }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
        <form action="{{ route('fees.update', $fee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Fee Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $fee->name) }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                    required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $fee->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-900 mb-2">Category *</label>
                    <select id="category" name="category" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category') border-red-500 @enderror"
                        required>
                        <option value="">Select Category</option>
                        <option value="tuition" {{ old('category', $fee->category) === 'tuition' ? 'selected' : '' }}>Tuition</option>
                        <option value="registration" {{ old('category', $fee->category) === 'registration' ? 'selected' : '' }}>Registration</option>
                        <option value="activities" {{ old('category', $fee->category) === 'activities' ? 'selected' : '' }}>Activities</option>
                        <option value="facilities" {{ old('category', $fee->category) === 'facilities' ? 'selected' : '' }}>Facilities</option>
                        <option value="examination" {{ old('category', $fee->category) === 'examination' ? 'selected' : '' }}>Examination</option>
                        <option value="other" {{ old('category', $fee->category) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-900 mb-2">Fee Type *</label>
                    <select id="type" name="type" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                        required>
                        <option value="">Select Type</option>
                        <option value="fixed" {{ old('type', $fee->type) === 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="variable" {{ old('type', $fee->type) === 'variable' ? 'selected' : '' }}>Variable</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-8">
                <label for="is_active" class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                        {{ old('is_active', $fee->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-3 text-sm text-gray-900">Active (Fee can be assigned to students)</span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Update Fee
                </button>
                <a href="{{ route('fees.index') }}" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
