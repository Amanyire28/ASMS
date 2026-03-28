@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Class Levels')
    @section('page-title', 'Class Levels')
    @section('page-description', 'Manage school class levels and their supervisors')
    @section('content')
@endif
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Class Levels Management</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Define the class levels available in your school</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('class-levels.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Class Level
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        @if($classLevels->count() > 0)
            <!-- Class Levels Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Level Teacher</th>
                            <th class="px-6 py-3">Classes</th>
                            <th class="px-6 py-3">Sort Order</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classLevels as $level)
                        <tr class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                {{ $level->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                @if($level->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ $level->category->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">No category</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                @if(isset($level->levelTeacher) && $level->levelTeacher)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-r from-maroon to-maroon-dark rounded-full flex items-center justify-center mr-2">
                                            <span class="text-white font-bold text-xs">
                                                {{ substr($level->levelTeacher->first_name, 0, 1) }}{{ substr($level->levelTeacher->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ $level->levelTeacher->first_name }} {{ $level->levelTeacher->last_name }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    {{ $level->classes->count() }} classes
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $level->sort_order }}
                            </td>
                            <td class="px-6 py-4">
                                @if($level->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('class-levels.edit', $level) }}" 
                                       class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('class-levels.destroy', $level) }}" 
                                          class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this class level?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $classLevels->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i class="fas fa-layer-group text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Class Levels Found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Get started by creating your first class level.</p>
                <a href="{{ route('class-levels.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add First Class Level
                </a>
            </div>
        @endif
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif