@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Edit Subject - ' . $subject->name)
    @section('page-title', 'Edit Subject')
    @section('page-description', 'Update subject information and details')
    @section('content')
@endif
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-maroon dark:text-gray-400 dark:hover:text-white">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('subjects.index') }}" class="text-sm font-medium text-gray-700 hover:text-maroon dark:text-gray-400 dark:hover:text-white">Subjects</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('subjects.show', $subject) }}" class="text-sm font-medium text-gray-700 hover:text-maroon dark:text-gray-400 dark:hover:text-white">{{ $subject->name }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit Subject</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update {{ $subject->name }} information</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('subjects.show', $subject) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        View Subject
                    </a>
                    <a href="{{ route('subjects.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Subjects
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('subjects.update', $subject) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subject Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Subject Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $subject->name) }}"
                           placeholder="e.g., Mathematics, Biology, English Literature"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-maroon focus:border-transparent @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Subject Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code', $subject->code) }}"
                           placeholder="e.g., MATH101, BIO201, ENG301"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-maroon focus:border-transparent @error('code') border-red-500 @enderror"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Subject code must be unique</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              placeholder="Brief description of the subject, its objectives, and what students will learn..."
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-maroon focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $subject->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $subject->is_active) == '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-maroon bg-gray-100 border-gray-300 focus:ring-maroon dark:focus:ring-maroon dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="is_active" 
                                   value="0" 
                                   {{ old('is_active', $subject->is_active) == '0' ? 'checked' : '' }}
                                   class="w-4 h-4 text-maroon bg-gray-100 border-gray-300 focus:ring-maroon dark:focus:ring-maroon dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inactive</span>
                        </label>
                    </div>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Inactive subjects won't appear in class scheduling but remain in the system for records
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('subjects.show', $subject) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-maroon hover:bg-maroon-dark text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Subject
                </button>
            </div>
        </form>
    </div>

    <!-- Change History -->
    <div class="mt-6 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-history text-gray-600 dark:text-gray-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">Subject Information</h3>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p><strong>Created:</strong> {{ $subject->created_at->format('M d, Y \a\t g:i A') }}</p>
                    <p><strong>Last Updated:</strong> {{ $subject->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    @if($subject->created_at != $subject->updated_at)
                        <p><strong>Last Modified:</strong> {{ $subject->updated_at->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Section -->
    @if(isset($hasClasses) && $hasClasses)
    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">Important Notice</h3>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    This subject is currently being taught in one or more classes. Changes to the subject code or status may affect class schedules and student records. Please review the impact before making changes.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif