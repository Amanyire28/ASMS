@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', $classCategory->name)
    @section('page-title', $classCategory->name)
    @section('page-description', 'Class category details and information')
    @section('content')
@endif
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('class-categories.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Class Categories</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $classCategory->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $classCategory->name }}</h1>
            <p class="text-gray-600 mt-1">Class category details and management</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('class-categories.edit', $classCategory) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Edit Category
            </a>
            <a href="{{ route('class-categories.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Categories
            </a>
        </div>
    </div>

    <!-- Category Information -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Category Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Name -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Name</h3>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $classCategory->name }}</p>
                </div>
                
                <!-- Sort Order -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Sort Order</h3>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $classCategory->sort_order }}</p>
                </div>
                
                <!-- Status -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status</h3>
                    <p class="mt-2">
                        @if($classCategory->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </p>
                </div>
                
                <!-- Class Levels Count -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Class Levels</h3>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $classCategory->classLevels->count() }}</p>
                </div>
            </div>

            @if($classCategory->description)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Description</h3>
                    <p class="text-gray-700">{{ $classCategory->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Class Levels -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Class Levels in this Category</h2>
                <a href="{{ route('class-levels.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Class Level
                </a>
            </div>
        </div>

        <div class="p-6">
            @if($classCategory->classLevels->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Level Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sort Order
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($classCategory->classLevels as $classLevel)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $classLevel->name }}</div>
                                        @if($classLevel->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($classLevel->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $classLevel->sort_order }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($classLevel->is_active)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('class-levels.show', $classLevel) }}" 
                                               class="text-blue-600 hover:text-blue-800" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('class-levels.edit', $classLevel) }}" 
                                               class="text-yellow-600 hover:text-yellow-800" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-layer-group text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Class Levels</h3>
                    <p class="text-gray-600 mb-4">This category doesn't have any class levels yet.</p>
                    <a href="{{ route('class-levels.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Class Level
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif