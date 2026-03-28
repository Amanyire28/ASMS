@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Edit Class Level')
    @section('page-title', 'Edit Class Level')
    @section('page-description', 'Update class level information')
    @section('content')
@endif
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit Class Level: {{ $classLevel->name }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update the class level information</p>
            </div>
            <a href="{{ route('class-levels.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Class Levels
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="p-6">
        <form data-spa-form method="POST" action="{{ route('class-levels.update', $classLevel) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Class Level Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $classLevel->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon/50 focus:border-maroon bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                           placeholder="e.g., Primary 1, Nursery, Grade 10"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" 
                            name="category_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon/50 focus:border-maroon bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $classLevel->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <a href="{{ route('class-categories.index') }}" class="text-maroon hover:underline">Manage categories</a>
                    </p>
                </div>

                <!-- Level Teacher -->
                <div>
                    <label for="level_teacher_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Level Teacher
                    </label>
                    <select id="level_teacher_id" 
                            name="level_teacher_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon/50 focus:border-maroon bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">Select Level Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('level_teacher_id', $classLevel->level_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('level_teacher_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        The teacher who oversees all classes in this level
                    </p>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sort Order <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           value="{{ old('sort_order', $classLevel->sort_order) }}"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon/50 focus:border-maroon bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                           placeholder="1"
                           required>
                    @error('sort_order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Lower numbers appear first in lists
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select id="is_active" 
                            name="is_active"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon/50 focus:border-maroon bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="1" {{ old('is_active', $classLevel->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $classLevel->is_active) ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('class-levels.index') }}" 
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-maroon hover:bg-maroon-dark text-white rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Class Level
                </button>
            </div>
        </form>
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif