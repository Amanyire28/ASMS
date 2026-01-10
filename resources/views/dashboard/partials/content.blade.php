{{-- resources/views/dashboard/partials/content.blade.php --}}
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 dark:from-gray-900 dark:to-gray-800 rounded-xl shadow-lg p-6 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-maroon/20 to-maroon/10 rounded-full -translate-y-12 translate-x-12"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
            <div class="mb-4 md:mb-0">
                <div class="flex items-center mb-2">
                    <div class="w-12 h-12 bg-gradient-to-br from-maroon to-maroon-dark rounded-xl flex items-center justify-center shadow-lg mr-4">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">
                            Welcome, {{ auth()->user()->name }}! 👋
                        </h1>
                    </div>
                </div>
                <p class="text-gray-300 mt-2">
                    Academic School Management System Dashboard
                </p>
            </div>

            <div class="text-center md:text-right">
                <p class="text-sm text-gray-400">Today is</p>
                <p class="text-xl font-semibold text-white">
                    {{ now()->format('F d, Y') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ now()->format('l') }} • {{ now()->format('h:i A') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Students Card - Show only if user can view students -->
        @canany(['students.view', 'students.view-detail'])
        <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group"
             onclick="window.location.href='{{ route('students.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Students</p>
                    <p class="text-4xl font-bold mt-2">
                        {{ \App\Models\Student::count() }}
                    </p>
                    <div class="flex items-center text-blue-100 text-xs mt-2">
                        <i class="fas fa-arrow-up mr-1 animate-pulse"></i>
                        <span>{{ \App\Models\Student::whereDate('created_at', today())->count() }} new today</span>
                    </div>
                </div>
                <div class="bg-white/20 p-4 rounded-full group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                    <i class="fas fa-user-graduate text-3xl"></i>
                </div>
            </div>
        </div>
        @endcanany

        <!-- Teachers Card - Show only if user can view teachers -->
        @canany(['teachers.view', 'teachers.view-detail'])
        <div class="bg-gradient-to-br from-green-500 via-green-600 to-green-700 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group"
             onclick="window.location.href='{{ route('teachers.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Teachers</p>
                    <p class="text-4xl font-bold mt-2">
                        {{ \App\Models\Teacher::count() }}
                    </p>
                    <div class="flex items-center text-green-100 text-xs mt-2">
                        <i class="fas fa-users mr-1"></i>
                        <span>Active staff members</span>
                    </div>
                </div>
                <div class="bg-white/20 p-4 rounded-full group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                    <i class="fas fa-chalkboard-teacher text-3xl"></i>
                </div>
            </div>
        </div>
        @endcanany

        <!-- Classes Card - Show only if user can view classes -->
        @canany(['classes.view', 'classes.view-detail'])
        <div class="bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group"
             onclick="window.location.href='{{ route('classes.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Classes</p>
                    <p class="text-4xl font-bold mt-2">
                        {{ \App\Models\ClassModel::count() }}
                    </p>
                    <div class="flex items-center text-purple-100 text-xs mt-2">
                        <i class="fas fa-layer-group mr-1"></i>
                        <span>Active classes</span>
                    </div>
                </div>
                <div class="bg-white/20 p-4 rounded-full group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                    <i class="fas fa-chalkboard text-3xl"></i>
                </div>
            </div>
        </div>
        @endcanany

        <!-- Subjects Card - Show only if user can view subjects -->
        @canany(['subjects.view', 'subjects.view-detail'])
        <div class="bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group"
             onclick="window.location.href='{{ route('subjects.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Subjects</p>
                    <p class="text-4xl font-bold mt-2">
                        {{ \App\Models\Subject::count() }}
                    </p>
                    <div class="flex items-center text-orange-100 text-xs mt-2">
                        <i class="fas fa-book-open mr-1"></i>
                        <span>Course curriculum</span>
                    </div>
                </div>
                <div class="bg-white/20 p-4 rounded-full group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                    <i class="fas fa-book text-3xl"></i>
                </div>
            </div>
        </div>
        @endcanany
    </div>



    <!-- Quick Actions Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-r from-yellow-100 to-yellow-50 dark:from-yellow-900/30 dark:to-yellow-800/20 rounded-xl mr-4">
                    <i class="fas fa-bolt text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Quick Actions
                </h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Add Student - Show only if user can create students -->
            @can('students.create')
            <a href="{{ route('students.create') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-blue-300 dark:hover:border-blue-700">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-user-plus text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">Add Student</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Register new student</p>
                </div>
                <i class="fas fa-arrow-right text-blue-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcan

            <!-- View Students - Show only if user can view students -->
            @canany(['students.view', 'students.view-detail'])
            <a href="{{ route('students.index') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-green-300 dark:hover:border-green-700">
                <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">View Students</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage all students</p>
                </div>
                <i class="fas fa-arrow-right text-green-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcanany

            <!-- Add Teacher - Show only if user can create teachers -->
            @can('teachers.create')
            <a href="{{ route('teachers.create') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-yellow-300 dark:hover:border-yellow-700">
                <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-user-tie text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">Add Teacher</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Register new teacher</p>
                </div>
                <i class="fas fa-arrow-right text-yellow-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcan

            <!-- View Teachers - Show only if user can view teachers -->
            @canany(['teachers.view', 'teachers.view-detail'])
            <a href="{{ route('teachers.index') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-indigo-300 dark:hover:border-indigo-700">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">View Teachers</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage teaching staff</p>
                </div>
                <i class="fas fa-arrow-right text-indigo-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcanany

            <!-- Manage Subjects - Show only if user can view subjects -->
            @canany(['subjects.view', 'subjects.view-detail'])
            <a href="{{ route('subjects.index') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-purple-300 dark:hover:border-purple-700">
                <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-book text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">Manage Subjects</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Course curriculum</p>
                </div>
                <i class="fas fa-arrow-right text-purple-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcanany

            <!-- Enter Marks - Show only if user can enter marks -->
            @can('marks.entry')
            <a href="{{ route('marks.entry.form') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-red-300 dark:hover:border-red-700">
                <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-edit text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">Enter Marks</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Record student marks</p>
                </div>
                <i class="fas fa-arrow-right text-red-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcan

            <!-- New Announcement - Show only if user can create announcements -->
            @can('announcements.create')
            <a href="{{ route('announcements.create') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-pink-50 to-pink-100 dark:from-pink-900/20 dark:to-pink-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-pink-300 dark:hover:border-pink-700">
                <div class="p-3 bg-gradient-to-br from-pink-500 to-pink-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-bullhorn text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">New Notice</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Create announcement</p>
                </div>
                <i class="fas fa-arrow-right text-pink-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcan

            <!-- Generate Report - Show only if user can generate reports -->
            @can('reports.view')
            <a href="{{ route('reports.create') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-cyan-300 dark:hover:border-cyan-700">
                <div class="p-3 bg-gradient-to-br from-cyan-500 to-cyan-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">Generate Report</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Create reports</p>
                </div>
                <i class="fas fa-arrow-right text-cyan-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcan

            <!-- Add Class - Show only if user can create classes -->
            @can('classes.create')
            <a href="{{ route('classes.create') }}"
               class="group flex items-center p-4 bg-gradient-to-r from-teal-50 to-teal-100 dark:from-teal-900/20 dark:to-teal-800/20 rounded-xl hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-teal-300 dark:hover:border-teal-700">
                <div class="p-3 bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-lg group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-plus-circle text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-bold text-gray-800 dark:text-gray-100">Add Class</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">New class section</p>
                </div>
                <i class="fas fa-arrow-right text-teal-500 group-hover:translate-x-2 transition-transform"></i>
            </a>
            @endcan
        </div>


    </div>

    <!-- Announcements Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-r from-red-100 to-red-50 dark:from-red-900/30 dark:to-red-800/20 rounded-xl mr-4">
                    <i class="fas fa-bullhorn text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Recent Announcements
                </h2>
            </div>
            <a href="{{ route('announcements.index') }}"
               class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition-colors flex items-center">
                View All <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        @php
            $recentAnnouncements = \App\Models\Announcement::latest()
                ->with('creator')
                ->take(5)
                ->get();
        @endphp

        @if($recentAnnouncements->count() > 0)
            <div class="space-y-4">
                @foreach($recentAnnouncements as $announcement)
                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-700 hover:bg-red-50/30 dark:hover:bg-red-900/10 transition-all duration-300 cursor-pointer group"
                         onclick="window.location.href='{{ route('announcements.show', $announcement) }}'">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $announcement->type === 'general' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' :
                                           ($announcement->type === 'academic' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                           ($announcement->type === 'event' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' :
                                           ($announcement->type === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                           'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'))) }}">
                                        {{ ucfirst($announcement->type) }}
                                    </span>

                                    @if($announcement->is_active && !$announcement->isExpired())
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            <i class="fas fa-circle text-[8px] mr-1"></i> Active
                                        </span>
                                    @elseif($announcement->isExpired())
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300">
                                            <i class="fas fa-clock text-[8px] mr-1"></i> Expired
                                        </span>
                                    @endif
                                </div>

                                <h4 class="font-bold text-gray-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                    {{ $announcement->title }}
                                </h4>

                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                    {{ Str::limit($announcement->content, 100) }}
                                </p>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-gray-400 to-gray-500 text-white text-xs flex items-center justify-center">
                                            {{ strtoupper(substr($announcement->creator->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $announcement->creator->name ?? 'System' }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $announcement->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-red-500 dark:group-hover:text-red-400 ml-4 mt-1 transition-colors"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="inline-block p-6 bg-gray-100 dark:bg-gray-900 rounded-full mb-4">
                    <i class="fas fa-bullhorn text-gray-300 dark:text-gray-600 text-4xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-lg">No announcements yet</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Share important updates with your school community</p>
                @can('announcements.create')
                    <a href="{{ route('announcements.create') }}"
                       class="inline-flex items-center mt-4 px-5 py-2.5 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300">
                        <i class="fas fa-plus mr-2"></i>
                        Create Announcement
                    </a>
                @endcan
            </div>
        @endif
    </div>

    <!-- System Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">System Status</p>
                    <div class="flex items-center mt-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-3"></div>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-100">Operational</p>
                    </div>
                </div>
                <i class="fas fa-check-circle text-green-500 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">Database</p>
                    <div class="flex items-center mt-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse mr-3"></div>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-100">Connected</p>
                    </div>
                </div>
                <i class="fas fa-database text-blue-500 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider font-semibold">Server Time</p>
                    <div class="flex items-center mt-2">
                        <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse mr-3"></div>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ now()->format('H:i') }}</p>
                    </div>
                </div>
                <i class="fas fa-clock text-purple-500 text-3xl"></i>
            </div>
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }

    /* Custom scrollbar for announcements */
    .announcements-container {
        max-height: 400px;
        overflow-y: auto;
    }

    .announcements-container::-webkit-scrollbar {
        width: 6px;
    }

    .announcements-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .dark .announcements-container::-webkit-scrollbar-track {
        background: #374151;
    }

    .announcements-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .dark .announcements-container::-webkit-scrollbar-thumb {
        background: #4B5563;
    }

    .announcements-container::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    .dark .announcements-container::-webkit-scrollbar-thumb:hover {
        background: #6B7280;
    }
</style>
