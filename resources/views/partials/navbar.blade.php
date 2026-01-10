<nav class="navbar bg-gradient-to-r from-white via-white to-gray-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-900 shadow-lg border-b border-gray-100 dark:border-gray-700 px-4 sm:px-6 py-4">
    <div class="flex items-center justify-between">
        <!-- Left Section: Brand & Navigation -->
        <div class="flex items-center space-x-4 lg:space-x-6">
            <!-- Sidebar Toggle Button -->
            <button id="sidebarToggle"
                    type="button"
                    onclick="document.dispatchEvent(new CustomEvent('toggle-sidebar'))"
                    class="sidebar-toggle-btn p-3 bg-gradient-to-r from-maroon/10 to-maroon/5 dark:from-gray-700 dark:to-gray-600 hover:from-maroon hover:to-maroon-dark dark:hover:from-gray-600 dark:hover:to-gray-500 rounded-2xl transition-all duration-300 group shadow-sm hover:shadow-md">
                <i class="fas fa-chevron-left text-maroon dark:text-gray-300 group-hover:text-white text-lg transition-colors duration-300"></i>
            </button>

            <!-- Brand & Page Title -->
            <div class="flex items-center space-x-4">
                <!-- Logo -->

                <div class="hidden lg:flex items-center justify-center w-12 h-12 bg-gradient-to-br from-maroon to-maroon-dark rounded-full shadow-lg">
                    <img
                src="{{ asset('storage/' . school_setting('school_logo')) }}"
                alt="{{ school_setting('school_name') }}"
                class="w-full h-full rounded-full"
            >


                </div>

                <!-- Page Information -->
                <div class="min-w-0">
                    <div class="flex items-center space-x-3 mb-1">
                        <h1 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white tracking-tight truncate">
                            @yield('page-title', 'Dashboard')
                        </h1>

                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 truncate max-w-md">
                        @yield('page-description', 'Academic School Management System ')
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Section: Actions & User Menu -->
        <div class="flex items-center space-x-2 sm:space-x-3">

            <button id="mobileSearchToggle"
                    type="button"
                    class="lg:hidden p-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-maroon/10 hover:to-maroon/5 dark:hover:from-gray-600 rounded-2xl transition-all duration-300 shadow-sm">
                <i class="fas fa-search text-gray-700 dark:text-gray-300 text-lg"></i>
            </button>

            <!-- Search (Desktop) -->
            <div class="hidden lg:block relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 dark:text-gray-500 text-sm"></i>
                </div>
                <input type="text"
                    class="pl-12 pr-4 py-3 w-64 xl:w-80 border-2 border-gray-200 dark:border-gray-600 rounded-2xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white text-sm transition-all duration-300 placeholder-gray-500 dark:placeholder-gray-400 shadow-sm"
                    placeholder="Search students, teachers, classes..."
                    data-search-input>
            </div>


            <div class="flex items-center space-x-2">

                <div class="relative" x-data="{ theme: localStorage.getItem('theme') || 'light' }"
                     x-init="$watch('theme', val => {
                        localStorage.setItem('theme', val);
                        document.documentElement.classList.toggle('dark', val === 'dark');
                     })">
                    <button @click="theme = theme === 'light' ? 'dark' : 'light'"
                        class="p-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-maroon/10 hover:to-maroon/5 dark:hover:from-gray-600 rounded-2xl transition-all duration-300 group shadow-sm"
                        :title="theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'">
                        <i class="fas text-lg transition-all duration-300 group-hover:scale-110"
                           :class="theme === 'light' ? 'fa-moon text-gray-700' : 'fa-sun text-amber-400'"></i>
                    </button>
                </div>

                <!-- Notifications - ALWAYS VISIBLE -->
               <div class="relative" x-data="notificationSystem()" x-init="init()">
    <button @click="toggleDropdown()"
        class="p-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-maroon/10 hover:to-maroon/5 dark:hover:from-gray-600 rounded-2xl transition-all duration-300 group shadow-sm relative">
        <i class="fas fa-bell text-gray-700 dark:text-gray-300 text-lg group-hover:animate-pulse"></i>
        <span x-show="unreadCount > 0"
              x-text="unreadCount"
              class="absolute -top-1 -right-1 w-6 h-6 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-md"
              :class="unreadCount > 0 ? 'animate-bounce' : ''"></span>
    </button>

    <!-- Notifications Dropdown -->
    <div x-show="isOpen"
        @click.away="isOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="absolute right-0 mt-3 w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 py-3 z-50 overflow-hidden"
        style="display: none;">
        <!-- Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">Notifications</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        <span x-text="unreadCount > 0 ? 'You have ' + unreadCount + ' new notifications' : 'No new notifications'"></span>
                    </p>
                </div>
                <span x-show="unreadCount > 0"
                      class="px-3 py-1 bg-gradient-to-r from-maroon to-maroon-dark text-white text-xs font-bold rounded-full">NEW</span>
            </div>

            <!-- Mark All as Read Button -->
            <div x-show="unreadCount > 0" class="mt-3">
                <button @click="markAllAsRead()"
                        :disabled="isMarkingAll"
                        class="w-full text-center text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-check-double mr-1"></i>
                    <span x-text="isMarkingAll ? 'Processing...' : 'Mark all as read'"></span>
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <!-- Loading State -->
            <div x-show="isLoading" class="px-5 py-8">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-12 h-12 border-4 border-maroon/30 border-t-maroon rounded-full animate-spin"></div>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Loading notifications...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="!isLoading && notifications.length === 0" class="px-5 py-8">
                <div class="text-center">
                    <div class="inline-block p-4 bg-gray-100 dark:bg-gray-900 rounded-full mb-3">
                        <i class="fas fa-bell-slash text-gray-300 dark:text-gray-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">No notifications yet</p>
                    <p class="text-gray-500 dark:text-gray-500 text-xs mt-1">You're all caught up!</p>
                </div>
            </div>

            <!-- Notifications -->
            <template x-for="notification in notifications" :key="notification.id">
                <div class="flex items-center px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-50 dark:border-gray-700/50 transition-all duration-200 group"
                     :class="!notification.read_at ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''">
                    <!-- Notification Icon -->
                    <div class="flex-shrink-0 relative">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm"
                             :class="getNotificationColor(notification.type)">
                            <i :class="getNotificationIcon(notification.type)" class="text-lg"></i>
                        </div>
                        <div x-show="!notification.read_at"
                             class="absolute -bottom-1 -right-1 w-5 h-5 bg-red-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
                            <i class="fas fa-circle text-white text-[8px]"></i>
                        </div>
                    </div>

                    <!-- Notification Content -->
                    <div class="ml-4 flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-white text-sm"
                           x-text="notification.data.message || notification.data.title || 'Notification'"></p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-clock mr-1 text-[10px]"></i>
                                <span x-text="formatTime(notification.created_at)"></span>
                            </span>
                            <template x-if="notification.read_at">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 text-xs rounded-full">Read</span>
                            </template>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col items-center space-y-2 ml-2">
                        <template x-if="!notification.read_at">
                            <button @click="markAsRead(notification.id)"
                                    :disabled="notification.markingAsRead"
                                    class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors disabled:opacity-50"
                                    title="Mark as read">
                                <i class="fas fa-check-circle text-sm"></i>
                            </button>
                        </template>
                        <button @click="deleteNotification(notification.id)"
                                :disabled="notification.deleting"
                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors disabled:opacity-50"
                                title="Delete notification">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('notifications.index') }}"
               class="block text-center text-sm font-semibold text-maroon dark:text-maroon-light hover:text-maroon-dark dark:hover:text-maroon-lighter transition-colors duration-200">
                <i class="fas fa-eye mr-2"></i>
                View all notifications
            </a>
        </div>
    </div>
</div>

                <!-- Quick Add -->

                    @canany(['students.create', 'teachers.create', 'marks.entry', 'classes.create'])
                    <div class="relative hidden md:block" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="p-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-maroon hover:to-maroon-dark dark:hover:from-gray-600 rounded-2xl transition-all duration-300 group shadow-sm">
                            <i class="fas fa-plus text-gray-700 dark:text-gray-300 text-lg group-hover:text-white group-hover:rotate-90 transition-all duration-300"></i>
                        </button>

                        <!-- Quick Add Dropdown -->
                        <div x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="absolute right-0 mt-3 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 py-3 z-50 overflow-hidden"
                            style="display: none;">
                            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-bold text-gray-900 dark:text-white text-sm">Quick Actions</h3>
                            </div>

                            <div class="py-2">
                                @can('students.create')
                                <a href="/admin/students/create"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 group">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200">
                                        <i class="fas fa-user-plus text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white text-sm">New Student</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Add student record</p>
                                    </div>
                                </a>
                                @endcan

                                @can('teachers.create')
                                <a href="/admin/teachers/create"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 group">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/10 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200">
                                        <i class="fas fa-chalkboard-teacher text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white text-sm">New Teacher</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Add teacher record</p>
                                    </div>
                                </a>
                                @endcan

                                @can('marks.entry')
                                <a href="/admin/marks-entry"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 group">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/10 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200">
                                        <i class="fas fa-edit text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white text-sm">Enter Marks</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Record student marks</p>
                                    </div>
                                </a>
                                @endcan

                                @can('classes.create')
                                <a href="/admin/classes/create"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 group">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/10 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200">
                                        <i class="fas fa-chalkboard text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white text-sm">New Class</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Create class section</p>
                                    </div>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endcanany

            </div>

           <!-- User Profile -->
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open"
            class="flex items-center space-x-2 sm:space-x-3 p-2 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 group">
            <!-- profile_photo_path -->
            <div class="relative">
                <div class="w-10 h-10 sm:w-11 sm:h-11 bg-gradient-to-r from-maroon to-maroon-dark rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                            alt="{{ auth()->user()->name }}"
                            class="w-full h-full rounded-2xl object-cover">
                    @else
                        <span class="text-white font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                    @endif
                </div>
                <div class="absolute -bottom-1 -right-1 w-3 h-3 sm:w-4 sm:h-4 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
            </div>

            <!-- User Info (Visible on medium screens and up) -->
            <div class="hidden md:block text-left">
                <p class="font-bold text-gray-900 dark:text-white text-sm tracking-tight truncate max-w-[120px]">
                   @php
    $parts = preg_split('/\s+/', trim(auth()->user()->name));
    $initials = collect($parts)
        ->take(2)
        ->map(fn($p) => strtoupper(substr($p, 0, 1)))
        ->implode('.');
@endphp

{{ $initials }}

                </p>
                <p class="text-xs text-maroon dark:text-maroon-light font-medium flex items-center">
                    <i class="fas fa-shield-alt mr-1 text-[10px]"></i>
                    {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                </p>
            </div>

            <!-- Dropdown Icon -->
            <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 text-xs hidden md:block transition-transform duration-300"
            :class="open ? 'rotate-180' : ''"></i>
        </button>

        <!-- User Dropdown Menu -->
        <div x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="absolute right-0 mt-3 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50"
            style="display: none;">
            <!-- Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 px-6 py-5">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-r from-maroon to-maroon-dark rounded-2xl flex items-center justify-center shadow-lg overflow-hidden">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                                alt="{{ auth()->user()->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <span class="text-white font-bold text-lg">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg truncate">{{ auth()->user()->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 truncate">{{ auth()->user()->email }}</p>
                        <div class="flex items-center mt-2 flex-wrap gap-2">
                            @foreach(auth()->user()->getRoleNames() as $role)
                                <span class="px-3 py-1 bg-gradient-to-r from-maroon/10 to-maroon/5 text-maroon dark:text-maroon-light text-xs font-bold rounded-full">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    {{ $role }}
                                </span>
                            @endforeach
                            @if(auth()->user()->getRoleNames()->isEmpty())
                                <span class="px-3 py-1 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-600 dark:text-gray-400 text-xs font-bold rounded-full">
                                    <i class="fas fa-user mr-1"></i>
                                    User
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="py-3">
                <!-- My Profile - Always visible -->
                <a href="/profile/edit"
                class="flex items-center px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mr-3">
                        <i class="fas fa-user text-gray-700 dark:text-gray-300"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 dark:text-white">My Profile</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View & edit profile</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                </a>

                <!-- Settings - Only for users with system.settings permission -->
                @can('system.settings')
                <a href="/admin/system"
                class="flex items-center px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mr-3">
                        <i class="fas fa-cog text-gray-700 dark:text-gray-300"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 dark:text-white">System Settings</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Configure system preferences</p>
                    </div>

                </a>
                @endcan



                <!-- Notifications - Always visible but with dynamic count -->
    <a href="/notifications"
    class="flex items-center px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 group">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mr-3">
            <i class="fas fa-bell text-gray-700 dark:text-gray-300"></i>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-gray-900 dark:text-white">Notifications</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">View system notifications</p>
        </div>
        @php
            $unreadNotifications = auth()->user()->unreadNotifications()->count();
        @endphp
        @if($unreadNotifications > 0)
            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold rounded-full notification-badge">
                {{ $unreadNotifications }}
            </span>
        @endif
    </a>

                <!-- Reports - Only for users with reports.view permission -->
                @can('reports.view')
                <a href=""
                class="flex items-center px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mr-3">
                        <i class="fas fa-chart-bar text-gray-700 dark:text-gray-300"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 dark:text-white">Reports & Analytics</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View system reports</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                </a>
                @endcan


            </div>

            <!-- Logout -->
            <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/10 hover:from-red-100 hover:to-red-200 dark:hover:from-red-800/30 dark:hover:to-red-700/20 text-red-600 dark:text-red-400 font-semibold rounded-xl transition-all duration-300 group">
                        <i class="fas fa-sign-out-alt mr-3 group-hover:animate-pulse"></i>
                        Sign Out
                        <i class="fas fa-arrow-right ml-auto text-sm opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Mobile Search Bar -->
    <div id="mobile-search" class="mt-4 hidden lg:hidden">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
            </div>
            <input type="text"
                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-2xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 shadow-sm transition-all duration-300"
                placeholder="Search students, teachers, classes..."
                data-search-input>
        </div>
    </div>
</nav>

<style>
    /* Smooth transitions */
    .navbar {
        transition: all 0.3s ease-in-out;
    }

    /* Ensure buttons are visible */
    @media (max-width: 640px) {
        .flex.items-center.space-x-2 {
            gap: 0.25rem;
        }
    }

    @media (min-width: 641px) and (max-width: 768px) {
        .flex.items-center.space-x-2 {
            gap: 0.5rem;
        }
    }

    /* Scrollbar styling for dropdowns */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-track {
        background: #374151;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #6b7280;
    }

    /* Animation for dropdowns */
    [x-show] {
        animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Glow effect for active items */
    .sidebar-toggle-btn:hover {
        box-shadow: 0 4px 20px rgba(128, 0, 0, 0.2);
    }

    .dark .sidebar-toggle-btn:hover {
        box-shadow: 0 4px 20px rgba(128, 0, 0, 0.3);
    }
</style>

<style>
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>

<script>
    // Toggle mobile search
    document.addEventListener('DOMContentLoaded', function() {
        const mobileSearchToggle = document.getElementById('mobileSearchToggle');
        const mobileSearch = document.getElementById('mobile-search');

        if (mobileSearchToggle && mobileSearch) {
            mobileSearchToggle.addEventListener('click', function() {
                mobileSearch.classList.toggle('hidden');
                mobileSearch.classList.toggle('animate-slideDown');

                // Focus on input when shown
                if (!mobileSearch.classList.contains('hidden')) {
                    setTimeout(() => {
                        const input = mobileSearch.querySelector('input');
                        if (input) input.focus();
                    }, 100);
                }
            });
        }

        // Search functionality
        const searchInputs = document.querySelectorAll('[data-search-input]');
        searchInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                console.log('Searching for:', searchTerm);
                // Add your search logic here
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            // Close all dropdowns except the one being clicked
            if (!e.target.closest('[x-data]')) {
                const dropdowns = document.querySelectorAll('[x-data]');
                dropdowns.forEach(dropdown => {
                    if (dropdown.__x) {
                        dropdown.__x.$data.open = false;
                    }
                });
            }
        });
    });
</script>

<script>
function notificationSystem() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        isLoading: false,
        isMarkingAll: false,
        refreshInterval: null,

        init() {
            this.loadNotifications();
            this.loadUnreadCount();

            // Set up real-time updates every 10 seconds
            this.refreshInterval = setInterval(() => {
                if (!this.isOpen) {
                    this.loadUnreadCount();
                }
            }, 10000);

            // Listen for new notifications via Laravel Echo (if you have it setup)
            this.setupRealtime();
        },

        setupRealtime() {
            // If you have Laravel Echo/Pusher setup
            if (typeof Echo !== 'undefined') {
                Echo.private(`App.Models.User.{{ auth()->id() }}`)
                    .notification((notification) => {
                        this.handleNewNotification(notification);
                    });
            }

            // Also listen for browser events
            document.addEventListener('new-notification', (e) => {
                this.handleNewNotification(e.detail);
            });
        },

        handleNewNotification(notification) {
            // Add new notification to the top
            this.notifications.unshift({
                ...notification,
                created_at: new Date().toISOString(),
                read_at: null
            });

            // Update unread count
            this.unreadCount++;

            // Show a toast notification
            this.showToast(notification);
        },

        showToast(notification) {
            // Create and show a toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-white dark:bg-gray-800 rounded-xl shadow-xl p-4 max-w-sm z-50 animate-slide-in';
            toast.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-lg ${this.getNotificationColor(notification.type)} flex items-center justify-center">
                            <i class="${this.getNotificationIcon(notification.type)}"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="font-semibold text-gray-900 dark:text-white text-sm">
                            ${notification.data.message || notification.data.title || 'New Notification'}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Just now
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },

        async loadNotifications() {
            this.isLoading = true;
            try {
                const response = await fetch('{{ route("notifications.latest") }}');
                const data = await response.json();
                this.notifications = data.notifications.map(notification => ({
                    ...notification,
                    markingAsRead: false,
                    deleting: false
                }));
            } catch (error) {
                console.error('Error loading notifications:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async loadUnreadCount() {
            try {
                const response = await fetch('{{ route("notifications.unreadCount") }}');
                const data = await response.json();
                this.unreadCount = data.count;
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        },

        async markAsRead(id) {
            const notification = this.notifications.find(n => n.id === id);
            if (!notification) return;

            notification.markingAsRead = true;

            try {
                const response = await fetch('{{ route("notifications.markAsRead", '') }}/' + id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    // Update notification locally without removing it
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Error marking as read:', error);
            } finally {
                notification.markingAsRead = false;
            }
        },

        async markAllAsRead() {
            if (this.unreadCount === 0) return;

            this.isMarkingAll = true;

            try {
                const response = await fetch('{{ route("notifications.markAllAsRead") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    // Mark all as read locally
                    this.notifications.forEach(notification => {
                        if (!notification.read_at) {
                            notification.read_at = new Date().toISOString();
                        }
                    });
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            } finally {
                this.isMarkingAll = false;
            }
        },

        async deleteNotification(id) {
            const notification = this.notifications.find(n => n.id === id);
            if (!notification) return;

            if (!confirm('Are you sure you want to delete this notification?')) {
                return;
            }

            notification.deleting = true;

            try {
                const response = await fetch('{{ route("notifications.destroy", '') }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    // Remove from local array
                    const index = this.notifications.findIndex(n => n.id === id);
                    if (index > -1) {
                        this.notifications.splice(index, 1);
                    }

                    // Update unread count if notification was unread
                    if (!notification.read_at) {
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        },

        getNotificationIcon(type) {
            const icons = {
                'App\\Notifications\\StudentRegistered': 'fas fa-user-plus',
                'App\\Notifications\\TeacherAssigned': 'fas fa-chalkboard-teacher',
                'App\\Notifications\\MarkEntered': 'fas fa-edit',
                'App\\Notifications\\ReportGenerated': 'fas fa-chart-bar',
                'App\\Notifications\\SystemAlert': 'fas fa-exclamation-triangle',
                'App\\Notifications\\PasswordChanged': 'fas fa-key',
                'App\\Notifications\\UserCreated': 'fas fa-user-plus',
                'App\\Notifications\\PaymentReceived': 'fas fa-money-check-alt',
                'App\\Notifications\\Announcement': 'fas fa-bullhorn',
            };
            return icons[type] || 'fas fa-bell';
        },

        getNotificationColor(type) {
            const colors = {
                'App\\Notifications\\StudentRegistered': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                'App\\Notifications\\TeacherAssigned': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                'App\\Notifications\\MarkEntered': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
                'App\\Notifications\\ReportGenerated': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                'App\\Notifications\\SystemAlert': 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                'App\\Notifications\\PasswordChanged': 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
                'App\\Notifications\\UserCreated': 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
                'App\\Notifications\\PaymentReceived': 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
                'App\\Notifications\\Announcement': 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
            };
            return colors[type] || 'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400';
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;

            return date.toLocaleDateString();
        }
    };
}
</script>



<script>
    // Auto-refresh notification count every 30 seconds
    document.addEventListener('alpine:initialized', () => {
        setInterval(() => {
            const notificationComponent = document.querySelector('[x-data*="notifications"]');
            if (notificationComponent && notificationComponent.__x) {
                const alpine = notificationComponent.__x;
                alpine.$data.loadUnreadCount();
            }
        }, 30000);
    });
</script>

