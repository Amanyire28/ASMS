@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', $subject->name)
    @section('page-title', $subject->name)
    @section('page-description', 'Subject details and information')
    @section('content')
@endif
<div class="max-w-6xl mx-auto">
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
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $subject->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Subject Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-book text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $subject->name }}</h1>
                                <div class="flex items-center mt-1 space-x-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        {{ $subject->code }}
                                    </span>
                                    @if($subject->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                            <i class="fas fa-pause-circle mr-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('subjects.edit', $subject) }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Edit
                            </a>
                            <a href="{{ route('subjects.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Created</h3>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $subject->created_at->format('M d, Y') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $subject->created_at->diffForHumans() }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Last Updated</h3>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $subject->updated_at->format('M d, Y') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $subject->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($subject->description)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Description</h3>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $subject->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Classes Teaching This Subject -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Classes Teaching This Subject</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Classes where this subject is being taught</p>
                </div>
                <div class="p-6">
                    @if(isset($classes) && $classes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($classes as $class)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $class->name }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $class->students_count ?? 0 }} students</p>
                                        </div>
                                        <a href="{{ route('classes.show', $class) }}" 
                                           class="text-maroon hover:text-maroon-dark">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-chalkboard text-4xl text-gray-400 dark:text-gray-600 mb-3"></i>
                            <p class="text-gray-600 dark:text-gray-400">No classes are currently teaching this subject</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Teachers Teaching This Subject -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Teachers</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Teachers qualified to teach this subject</p>
                </div>
                <div class="p-6">
                    @if(isset($teachers) && $teachers->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($teachers as $teacher)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">
                                                    {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $teacher->email }}</p>
                                        </div>
                                        <a href="{{ route('teachers.show', $teacher) }}" 
                                           class="text-maroon hover:text-maroon-dark">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-chalkboard-teacher text-4xl text-gray-400 dark:text-gray-600 mb-3"></i>
                            <p class="text-gray-600 dark:text-gray-400">No teachers are currently assigned to this subject</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('subjects.edit', $subject) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Subject
                    </a>
                    
                    <form method="POST" action="{{ route('subjects.destroy', $subject) }}" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors"
                                onclick="return confirm('Are you sure you want to delete this subject? This action cannot be undone.')">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Subject
                        </button>
                    </form>
                </div>
            </div>

            <!-- Subject Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Statistics</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Classes Teaching</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $classes->count() ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Qualified Teachers</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $teachers->count() ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Students</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $totalStudents ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Related Links -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Related</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('subjects.index') }}" 
                       class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-maroon dark:hover:text-maroon">
                        <i class="fas fa-book mr-3"></i>
                        All Subjects
                    </a>
                    <a href="{{ route('classes.index') }}" 
                       class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-maroon dark:hover:text-maroon">
                        <i class="fas fa-chalkboard mr-3"></i>
                        All Classes
                    </a>
                    <a href="{{ route('teachers.index') }}" 
                       class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-maroon dark:hover:text-maroon">
                        <i class="fas fa-chalkboard-teacher mr-3"></i>
                        All Teachers
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif