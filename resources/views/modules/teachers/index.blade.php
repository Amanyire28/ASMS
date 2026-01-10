@extends('layouts.app')

@section('title', 'Teachers Management')
@section('page-title', 'Teachers Management')
@section('page-description', 'View and manage all teachers')

@section('content')
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

    <!-- Main Content Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <form method="GET" action="/admin/teachers/" id="searchForm">
                        <div class="relative max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <input type="text"
                                   name="search"
                                   id="teacherSearch"
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                                   placeholder="Search teachers by name, ID, or email..."
                                   onkeyup="debouncedSearch(this.value)">
                            @if(request('search'))
                            <a href="{{ route('teachers.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-times text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer"></i>
                            </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Filter Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-all duration-300 shadow-sm">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>

                        <div x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 py-3 z-50"
                            style="display: none;">
                            <form method="GET" action="{{ route('teachers.index') }}" id="filterForm">
                                <div class="px-4 py-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm mb-3">Filter by Status</h4>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="status" value="all" {{ request('status') == 'all' || !request('status') ? 'checked' : '' }} class="rounded border-gray-300 text-maroon focus:ring-maroon/50">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm">All Teachers</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="status" value="active" {{ request('status') == 'active' ? 'checked' : '' }} class="rounded border-gray-300 text-maroon focus:ring-maroon/50">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm">Active Only</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="status" value="inactive" {{ request('status') == 'inactive' ? 'checked' : '' }} class="rounded border-gray-300 text-maroon focus:ring-maroon/50">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm">Inactive Only</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-3">
                                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-maroon to-maroon-dark text-white font-medium rounded-lg transition-all duration-300">
                                        Apply Filters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex items-center bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-xl p-1">
                        <button id="gridView" onclick="toggleView('grid')"
                                class="p-2 rounded-lg bg-white dark:bg-gray-600 shadow-sm text-maroon dark:text-white transition-all duration-300">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button id="listView" onclick="toggleView('list')"
                                class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-maroon dark:hover:text-white transition-all duration-300 ml-1">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teachers Grid View (Default) -->
        <div id="teachersGridView" class="p-6">
            @if($teachers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($teachers as $teacher)
                <div class="teacher-card bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden group hover:scale-[1.02]">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->full_name }}"
                                         class="w-14 h-14 rounded-xl object-cover shadow-lg">
                                    @else
                                    <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                        {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 {{ $teacher->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full border-2 border-white dark:border-gray-800"></div>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $teacher->full_name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $teacher->designation ?? 'Teacher' }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 text-blue-700 dark:text-blue-400 text-xs font-semibold rounded-full">
                                {{ $teacher->employee_id }}
                            </span>
                        </div>

                        <!-- Details -->
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-envelope text-gray-400 dark:text-gray-500 w-5"></i>
                                <span class="text-gray-600 dark:text-gray-300 ml-2 truncate">{{ $teacher->email }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-phone text-gray-400 dark:text-gray-500 w-5"></i>
                                <span class="text-gray-600 dark:text-gray-300 ml-2">{{ $teacher->phone }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-chalkboard text-gray-400 dark:text-gray-500 w-5"></i>
                                <span class="text-gray-600 dark:text-gray-300 ml-2">
                                    {{ $teacher->classes_count }} {{ Str::plural('Class', $teacher->classes_count) }}
                                </span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-book text-gray-400 dark:text-gray-500 w-5"></i>
                                <span class="text-gray-600 dark:text-gray-300 ml-2">
                                    {{ $teacher->subjects_count }} {{ Str::plural('Subject', $teacher->subjects_count) }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 bg-gradient-to-r {{ $teacher->is_active ? 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/10 text-green-700 dark:text-green-400' : 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/10 text-red-700 dark:text-red-400' }} text-xs font-semibold rounded-full">
                                    <i class="fas fa-circle text-[8px] mr-1"></i>
                                    {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if($teacher->employment_date)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($teacher->employment_date)->diffInYears() }} yrs
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <a href="{{ route('teachers.show', $teacher) }}" class="p-2 text-gray-500 hover:text-maroon dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('teachers.edit', $teacher) }}" class="p-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteTeacher({{ $teacher->id }})" class="p-2 text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Add New Teacher Card -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-2xl shadow-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-maroon dark:hover:border-maroon transition-all duration-300 group">
                    <a href="{{ route('teachers.create') }}" class="h-full flex flex-col items-center justify-center p-8 text-center hover:scale-[1.02] transition-transform duration-300">
                        <div class="w-20 h-20 bg-gradient-to-r from-maroon/10 to-maroon/5 rounded-2xl flex items-center justify-center mb-4 group-hover:from-maroon/20 group-hover:to-maroon/10 transition-all duration-300">
                            <i class="fas fa-user-plus text-maroon text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Add New Teacher</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Click here to add a new teacher to the system</p>
                        <span class="px-4 py-2 bg-gradient-to-r from-maroon to-maroon-dark text-white text-sm font-semibold rounded-lg transition-all duration-300">
                            Create New
                        </span>
                    </a>
                </div>
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-users text-gray-400 dark:text-gray-500 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No Teachers Found</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-8">
                    @if(request()->has('search') || request()->has('status'))
                    No teachers match your search criteria. Try adjusting your filters.
                    @else
                    You haven't added any teachers yet. Get started by adding your first teacher.
                    @endif
                </p>
                <a href="{{ route('teachers.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-maroon to-maroon-dark hover:from-maroon-dark hover:to-maroon text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add Your First Teacher
                </a>
            </div>
            @endif
        </div>

        <!-- Teachers List View (Hidden by default) -->
        <div id="teachersListView" class="hidden p-6">
            @if($teachers->count() > 0)
            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                        <tr>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Teacher
                            </th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                ID & Contact
                            </th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Assignments
                            </th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($teachers as $teacher)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="px-3 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->full_name }}"
                                         class="w-10 h-10 rounded-lg object-cover mr-3">
                                    @else
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">
                                        {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $teacher->full_name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $teacher->designation ?? 'Teacher' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $teacher->employee_id }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $teacher->email }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $teacher->phone }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-chalkboard mr-1"></i> {{ $teacher->classes_count }} {{ Str::plural('Class', $teacher->classes_count) }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-book mr-1"></i> {{ $teacher->subjects_count }} {{ Str::plural('Subject', $teacher->subjects_count) }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $teacher->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400' }}">
                                    {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="text-maroon dark:text-maroon-light hover:text-maroon-dark dark:hover:text-maroon-lighter">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteTeacher({{ $teacher->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($teachers->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300 mb-4 sm:mb-0">
                    Showing <span class="font-semibold">{{ $teachers->firstItem() }}</span> to <span class="font-semibold">{{ $teachers->lastItem() }}</span> of <span class="font-semibold">{{ $teachers->total() }}</span> teachers
                </div>
                <div class="flex items-center space-x-2">
                    {{ $teachers->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all duration-300 scale-95">
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-12 h-12 bg-gradient-to-r from-red-100 to-red-200 dark:from-red-900/30 dark:to-red-800/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Delete Teacher</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">This action cannot be undone.</p>
            </div>
        </div>

        <p class="text-gray-700 dark:text-gray-300 mb-6">Are you sure you want to delete this teacher? All their records, assignments, and user account will be permanently removed.</p>

        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                Cancel
            </button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-lg transition-all duration-300">
                    Delete Teacher
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // View toggle functionality
    function toggleView(viewType) {
        const gridView = document.getElementById('teachersGridView');
        const listView = document.getElementById('teachersListView');
        const gridBtn = document.getElementById('gridView');
        const listBtn = document.getElementById('listView');

        if (viewType === 'grid') {
            gridView.classList.remove('hidden');
            listView.classList.add('hidden');
            gridBtn.classList.add('bg-white', 'dark:bg-gray-600', 'shadow-sm', 'text-maroon', 'dark:text-white');
            gridBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
            listBtn.classList.remove('bg-white', 'dark:bg-gray-600', 'shadow-sm', 'text-maroon', 'dark:text-white');
            listBtn.classList.add('text-gray-500', 'dark:text-gray-400');

            // Save preference to localStorage
            localStorage.setItem('teacherView', 'grid');
        } else {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
            listBtn.classList.add('bg-white', 'dark:bg-gray-600', 'shadow-sm', 'text-maroon', 'dark:text-white');
            listBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
            gridBtn.classList.remove('bg-white', 'dark:bg-gray-600', 'shadow-sm', 'text-maroon', 'dark:text-white');
            gridBtn.classList.add('text-gray-500', 'dark:text-gray-400');

            // Save preference to localStorage
            localStorage.setItem('teacherView', 'list');
        }
    }

    // Debounced search function
    let searchTimeout;
    function debouncedSearch(value) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 500);
    }

    // Delete teacher function
    let currentTeacherId = null;

    function deleteTeacher(teacherId) {
        currentTeacherId = teacherId;
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/admin/teachers/${teacherId}`;

        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            currentTeacherId = null;
        }, 300);
    }

    // Export to Excel
    document.getElementById('exportExcel')?.addEventListener('click', function(e) {
        e.preventDefault();
        showNotification('Exporting teachers to Excel...', 'info');

        // Create export URL with current filters
        const url = new URL('{{ route("teachers.index") }}');
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'excel');
        window.location.href = `${url.pathname}?${params.toString()}`;
    });

    // Print list
    document.getElementById('printList')?.addEventListener('click', function(e) {
        e.preventDefault();
        showNotification('Preparing print view...', 'info');
        window.print();
    });

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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Restore view preference from localStorage
        const savedView = localStorage.getItem('teacherView') || 'grid';
        toggleView(savedView);

        // Add click handlers for teacher cards
        document.querySelectorAll('.teacher-card').forEach((card) => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('a')) {
                    const viewBtn = this.querySelector('a[href*="/show"]');
                    if (viewBtn) viewBtn.click();
                }
            });
        });

        // Close modal on background click
        document.getElementById('deleteModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('deleteModal')?.classList.contains('hidden') === false) {
                closeDeleteModal();
            }
        });
    });
</script>
@endpush

<style>
    /* Animation for notifications */
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

    /* Smooth transitions */
    .teacher-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .teacher-card:hover {
        transform: translateY(-4px);
    }

    /* Modal animation */
    #deleteModal > div {
        transition: transform 0.3s ease-out;
    }

    /* Scrollbar styling */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .dark .overflow-x-auto::-webkit-scrollbar-track {
        background: #374151;
    }

    .dark .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #6b7280;
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: white !important;
            color: black !important;
        }

        #teachersListView {
            display: block !important;
        }

        #teachersGridView {
            display: none !important;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection
