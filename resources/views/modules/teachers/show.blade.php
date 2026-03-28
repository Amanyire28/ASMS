@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', $teacher->full_name . ' - Teacher Profile')
    @section('page-title', 'Teacher Profile')
    @section('page-description', 'View teacher details and manage assignments')
    @section('content')
@endif
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/10 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/10 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        @if($teacher->photo)
                        <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->full_name }}"
                             class="w-20 h-20 rounded-2xl object-cover shadow-lg border-4 border-white dark:border-gray-800">
                        @else
                        <div class="w-20 h-20 bg-gradient-to-r from-maroon to-maroon-dark rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                            {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                        </div>
                        @endif
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 {{ $teacher->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full border-4 border-white dark:border-gray-800"></div>
                    </div>
                    <div>
                        <div class="flex items-center space-x-3">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $teacher->full_name }}</h1>
                            <span class="px-3 py-1 bg-gradient-to-r {{ $teacher->is_active ? 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/10 text-green-700 dark:text-green-400' : 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/10 text-red-700 dark:text-red-400' }} text-sm font-semibold rounded-full">
                                {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-4 mt-2">
                            <p class="text-gray-600 dark:text-gray-300">{{ $teacher->designation ?? 'Teacher' }}</p>
                            <span class="text-gray-500 dark:text-gray-400">•</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $teacher->employee_id }}</span>
                            <span class="text-gray-500 dark:text-gray-400">•</span>
                            <span class="text-gray-600 dark:text-gray-300">
                                <i class="fas fa-user-shield mr-1"></i>
                                {{ $teacher->user && $teacher->user->role ? $teacher->user->role : 'Teacher' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
                @can('teachers.edit')
                <a href="{{ route('teachers.edit', $teacher) }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Profile
                </a>
                @endcan

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fas fa-ellipsis-v mr-2"></i>
                        Actions
                    </button>

                    <div x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 py-2 z-50"
                        style="display: none;">

                        @can('teachers.reset_password')
                        <button onclick="resetPassword({{ $teacher->id }})" class="flex items-center w-full px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 text-left">
                            <i class="fas fa-key text-yellow-600 dark:text-yellow-400 w-5 mr-3"></i>
                            <span class="text-gray-900 dark:text-gray-100">Reset Password</span>
                        </button>
                        @endcan

                        @can('teachers.edit')
                        <button onclick="toggleStatus({{ $teacher->id }}, {{ $teacher->is_active ? 'false' : 'true' }})" class="flex items-center w-full px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 text-left">
                            <i class="fas {{ $teacher->is_active ? 'fa-user-slash text-red-600' : 'fa-user-check text-green-600' }} dark:{{ $teacher->is_active ? 'text-red-400' : 'text-green-400' }} w-5 mr-3"></i>
                            <span class="text-gray-900 dark:text-gray-100">{{ $teacher->is_active ? 'Deactivate' : 'Activate' }}</span>
                        </button>
                        @endcan

                        <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>

                        @can('teachers.delete')
                        <button onclick="deleteTeacher({{ $teacher->id }})" class="flex items-center w-full px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 text-left text-red-600 dark:text-red-400">
                            <i class="fas fa-trash w-5 mr-3"></i>
                            <span>Delete Teacher</span>
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Left Column: Personal Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h3>
                        @can('teachers.edit')
                        <button onclick="editPersonalInfo()" class="text-sm text-maroon dark:text-maroon-light hover:text-maroon-dark dark:hover:text-maroon-lighter font-medium">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Full Name</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $teacher->full_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Employee ID</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $teacher->employee_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Gender</label>
                                <p class="text-gray-900 dark:text-white font-medium capitalize">{{ $teacher->gender }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Date of Birth</label>
                                <p class="text-gray-900 dark:text-white font-medium">
                                    {{ $teacher->date_of_birth ? $teacher->date_of_birth->format('F d, Y') : 'Not set' }}
                                    @if($teacher->date_of_birth)
                                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                        ({{ $teacher->date_of_birth->age }} years old)
                                    </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email Address</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $teacher->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Phone Number</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $teacher->phone }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">National ID</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $teacher->national_id ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Employment Date</label>
                                <p class="text-gray-900 dark:text-white font-medium">
                                    {{ $teacher->employment_date ? $teacher->employment_date->format('F d, Y') : 'Not set' }}
                                    @if($teacher->employment_date)
                                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                        ({{ $teacher->employment_date->diffInYears() }} years ago)
                                    </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Address</label>
                        <p class="text-gray-900 dark:text-white">{{ $teacher->address ?? 'Not provided' }}</p>
                    </div>

                    <!-- Qualifications -->
                    @if($teacher->qualifications)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Qualifications</label>
                        <p class="text-gray-900 dark:text-white">{{ $teacher->qualifications }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Class Assignments Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Class Assignments</h3>
                        @can('teachers.edit')
                        <button onclick="manageClasses()" class="text-sm text-maroon dark:text-maroon-light hover:text-maroon-dark dark:hover:text-maroon-lighter font-medium">
                            <i class="fas fa-plus mr-1"></i> Manage Classes
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="p-6">
                    @if($teacher->classes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Class</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Level</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Students</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($teacher->classes as $class)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">
                                                {{ substr($class->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $class->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $class->classLevel->name ?? 'No Level' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full">
                                            {{ $class->classLevel->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-gray-900 dark:text-white">{{ $class->students_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($class->pivot->is_class_teacher)
                                        <span class="px-2 py-1 text-xs font-semibold bg-gradient-to-r from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/20 text-green-800 dark:text-green-400 rounded-full">
                                            Class Teacher
                                        </span>
                                        @else
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full">
                                            Subject Teacher
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @can('teachers.edit')
                                        <button onclick="removeClassAssignment({{ $teacher->id }}, {{ $class->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chalkboard-teacher text-gray-400 dark:text-gray-500 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Class Assignments</h4>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">This teacher hasn't been assigned to any classes yet.</p>
                        @can('teachers.edit')
                        <button onclick="manageClasses()" class="px-4 py-2 bg-gradient-to-r from-maroon to-maroon-dark text-white text-sm font-semibold rounded-lg transition-all duration-300">
                            <i class="fas fa-plus mr-2"></i> Assign Classes
                        </button>
                        @endcan
                    </div>
                    @endif
                </div>
            </div>

            <!-- Subject Assignments Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subject Assignments</h3>
                        @can('teachers.edit')
                        <button onclick="manageSubjects()" class="text-sm text-maroon dark:text-maroon-light hover:text-maroon-dark dark:hover:text-maroon-lighter font-medium">
                            <i class="fas fa-plus mr-1"></i> Manage Subjects
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="p-6">
                    @if($teacher->subjects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($teacher->subjects as $subject)
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">
                                        {{ substr($subject->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $subject->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $subject->code ?? 'No Code' }}</div>
                                    </div>
                                </div>
                                @can('teachers.edit')
                                <button onclick="removeSubjectAssignment({{ $teacher->id }}, {{ $subject->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endcan
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-clock text-xs mr-1"></i>
                                    {{ $subject->weekly_hours ?? 'N/A' }} hrs/week
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-book text-gray-400 dark:text-gray-500 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Subject Assignments</h4>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">This teacher hasn't been assigned any subjects yet.</p>
                        @can('teachers.edit')
                        <button onclick="manageSubjects()" class="px-4 py-2 bg-gradient-to-r from-maroon to-maroon-dark text-white text-sm font-semibold rounded-lg transition-all duration-300">
                            <i class="fas fa-plus mr-2"></i> Assign Subjects
                        </button>
                        @endcan
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Statistics & Quick Info -->
        <div class="space-y-6">
            <!-- Statistics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-maroon to-maroon-dark">
                    <h3 class="text-lg font-semibold text-white">Teaching Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/20 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-chalkboard-teacher text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Classes Assigned</div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $teacher->classes_count }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Class Teacher</div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $teacher->classTeacherOf->count() }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/20 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-book text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Subjects Assigned</div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $teacher->subjects_count }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/20 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Students</div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">
                                        @php
                                            $totalStudents = 0;
                                            foreach($teacher->classes as $class) {
                                                $totalStudents += $class->students_count ?? 0;
                                            }
                                            echo $totalStudents;
                                        @endphp
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/20 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-alt text-yellow-600 dark:text-yellow-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Experience</div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">
                                        @if($teacher->employment_date)
                                            {{ $teacher->employment_date->diffInYears() }} years
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @can('teachers.view')
                        <a href="#" class="flex items-center p-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 rounded-xl transition-all duration-300">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white mr-3">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">View Timetable</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Weekly schedule</div>
                            </div>
                        </a>
                        @endcan

                        @can('teachers.edit')
                        <button onclick="viewAttendance()" class="flex items-center w-full p-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 rounded-xl transition-all duration-300 text-left">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white mr-3">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Attendance Record</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">View & manage</div>
                            </div>
                        </button>
                        @endcan

                        @can('teachers.view')
                        <a href="#" class="flex items-center p-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 rounded-xl transition-all duration-300">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white mr-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Performance Reports</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">View analytics</div>
                            </div>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Account Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Account Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Username</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $teacher->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Account Status</label>
                            <p class="flex items-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $teacher->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400' }}">
                                    {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if($teacher->user && $teacher->user->must_change_password)
                                <span class="ml-2 px-2 py-1 text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 rounded-full">
                                    Must Reset Password
                                </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Last Login</label>
                            <p class="text-gray-900 dark:text-white">
                                @if($teacher->user && $teacher->user->last_login_at)
                                    {{ $teacher->user->last_login_at->diffForHumans() }}
                                @else
                                    Never logged in
                                @endif
                            </p>
                        </div>
                    </div>

                    @can('teachers.reset_password')
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <button onclick="resetPassword({{ $teacher->id }})" class="w-full px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-semibold rounded-lg transition-all duration-300">
                            <i class="fas fa-key mr-2"></i> Reset Password
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals will be implemented in scripts -->
@push('scripts')
<script>
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Load any modals or initial data if needed
    });

    // Edit Personal Info
    function editPersonalInfo() {
        window.location.href = "{{ route('teachers.edit', $teacher) }}";
    }

    // Manage Classes
    function manageClasses() {
        window.location.href = "{{ route('teachers.edit', $teacher) }}#class-assignments";
    }

    // Manage Subjects
    function manageSubjects() {
        window.location.href = "{{ route('teachers.edit', $teacher) }}#subject-assignments";
    }

    // Remove Class Assignment
    function removeClassAssignment(teacherId, classId) {
        if (confirm('Are you sure you want to remove this class assignment?')) {
            fetch(`/admin/teachers/${teacherId}/remove-class-assignment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ class_id: classId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Error removing assignment', 'error');
                }
            })
            .catch(error => {
                showNotification('Error removing assignment', 'error');
                console.error('Error:', error);
            });
        }
    }

    // Remove Subject Assignment
    function removeSubjectAssignment(teacherId, subjectId) {
        if (confirm('Are you sure you want to remove this subject assignment?')) {
            fetch(`/admin/teachers/${teacherId}/remove-subject-assignment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ subject_id: subjectId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Error removing assignment', 'error');
                }
            })
            .catch(error => {
                showNotification('Error removing assignment', 'error');
                console.error('Error:', error);
            });
        }
    }

    // Toggle Teacher Status
    function toggleStatus(teacherId, newStatus) {
        const action = newStatus ? 'activate' : 'deactivate';
        if (confirm(`Are you sure you want to ${action} this teacher?`)) {
            fetch(`/admin/teachers/${teacherId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ is_active: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Error updating status', 'error');
                }
            })
            .catch(error => {
                showNotification('Error updating status', 'error');
                console.error('Error:', error);
            });
        }
    }

    // Reset Password
    function resetPassword(teacherId) {
        if (confirm('Reset password to default? The teacher will need to change it on next login.')) {
            fetch(`/admin/teachers/${teacherId}/reset-password`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Error resetting password', 'error');
                }
            })
            .catch(error => {
                showNotification('Error resetting password', 'error');
                console.error('Error:', error);
            });
        }
    }

    // Delete Teacher
    function deleteTeacher(teacherId) {
        if (confirm('Are you sure you want to delete this teacher? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/teachers/${teacherId}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // View Attendance
    function viewAttendance() {
        showNotification('Attendance feature coming soon!', 'info');
    }

    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const colors = {
            'success': 'from-green-500 to-green-600',
            'error': 'from-red-500 to-red-600',
            'info': 'from-blue-500 to-blue-600',
            'warning': 'from-yellow-500 to-yellow-600'
        };

        notification.className = `fixed top-6 right-6 bg-gradient-to-r ${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl z-50 animate-slideIn`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} text-xl mr-3"></i>
                <div>
                    <p class="font-semibold">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-6 text-white/80 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
</script>
@endpush

<style>
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animate-slideIn {
        animation: slideIn 0.3s ease-out;
    }

    /* Smooth transitions for cards */
    .bg-gradient-to-br {
        transition: all 0.3s ease;
    }

    .bg-gradient-to-br:hover {
        transform: translateY(-2px);
    }
</style>

@if(!request()->header('HX-Request'))
    @endsection
@endif
