<!-- ============================================ -->
<!-- FILE: resources/views/partials/mobile-nav.blade.php -->
<!-- ============================================ -->

@php
    // Get user data
    $user = auth()->user();
    $isMobileRequest = preg_match('/Android|iPhone|iPad|iPod|Mobile/i', request()->userAgent() ?? '') === 1;
    $userName = $user->name ?? 'User';
    $userRole = $user->getRoleNames()->first() ?? 'Administrator';
    $userInitials = strtoupper(substr($userName, 0, 2));
    $unreadNotifications = $isMobileRequest ? $user->unreadNotifications()->count() : 0;

    // Get school data
    $schoolLogo = school_setting('school_logo');
    $schoolName = school_setting('school_name') ?? config('app.name', 'ASMS');
@endphp

<!-- Mobile Header -->
<div class="bg-white dark:bg-gray-800 shadow-sm fixed top-0 left-0 right-0 z-40 safe-top lg:hidden">
    <div class="px-4 py-3 flex justify-between items-center">
        <!-- Menu Button -->
        <button onclick="openMobileSidebar()" class="text-gray-600 dark:text-gray-300 hover:text-maroon p-2">
            <i class="fas fa-bars text-xl"></i>
        </button>

        <!-- Logo/Brand -->
        <div class="flex items-center">
            <div class="w-8 h-8 bg-maroon rounded-full flex items-center justify-center mr-2 overflow-hidden">
                @if($schoolLogo && Storage::exists('public/' . $schoolLogo))
                    <img src="{{ asset('storage/' . $schoolLogo) }}"
                         alt="{{ $schoolName }}"
                         class="w-full h-full object-cover">
                @else
                    <span class="text-white font-bold text-xs">
                        {{ strtoupper(substr($schoolName, 0, 2)) }}
                    </span>
                @endif
            </div>
            <span class="font-bold text-maroon dark:text-maroon-light text-lg">{{ $schoolName }}</span>
        </div>

        <!-- Right side buttons -->
        <div class="flex items-center space-x-2">
            <!-- Theme Toggle for Mobile -->
            <button onclick="window.toggleTheme()" class="text-gray-600 dark:text-gray-300 hover:text-maroon p-2">
                <i id="mobileThemeIcon" class="fas fa-moon text-lg"></i>
            </button>

            <!-- Search Button -->
            <button onclick="toggleMobileSearch()" class="text-gray-600 dark:text-gray-300 hover:text-maroon p-2">
                <i class="fas fa-search text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Search Bar (Hidden by default) -->
    <div id="mobileSearchBar" class="px-4 py-3 bg-gray-50 dark:bg-gray-700 hidden">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text"
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-maroon/50 focus:border-maroon focus:outline-none"
                   placeholder="Search students, teachers, classes...">
            <button onclick="toggleMobileSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i class="fas fa-times text-gray-400"></i>
            </button>
        </div>
    </div>
</div>

<!-- Mobile Notifications Panel -->
<div id="mobileNotifications" class="fixed inset-0 bg-white dark:bg-gray-800 z-50 transform translate-x-full transition-transform duration-300 lg:hidden">
    <div class="h-full flex flex-col">
        <!-- Notifications Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Notifications</h3>
            <button onclick="closeMobileNotifications()" class="text-gray-600 dark:text-gray-300 hover:text-maroon p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Notifications Content -->
        <div class="flex-1 overflow-y-auto p-4">
            @php
                // Avoid running mobile-only notification queries on desktop requests.
                $notifications = $isMobileRequest ? $user->notifications()->latest()->take(20)->get() : collect();
                $groupedNotifications = $notifications->groupBy(function ($notification) {
                    return $notification->created_at->isToday() ? 'Today' :
                           ($notification->created_at->isYesterday() ? 'Yesterday' :
                           $notification->created_at->format('F d, Y'));
                });
            @endphp

            @if($notifications->count() > 0)
                @foreach($groupedNotifications as $date => $dayNotifications)
                <div class="mb-6">
                    <h4 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-3">{{ $date }}</h4>
                    <div class="space-y-3">
                        @foreach($dayNotifications as $notification)
                            @php
                                $notificationData = $notification->data;
                                $type = $notificationData['type'] ?? 'default';

                                // Determine colors based on type
                                $colors = [
                                    'student_registered' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'icon' => 'fa-user-plus text-blue-600 dark:text-blue-400'],
                                    'teacher_assigned' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'icon' => 'fa-chalkboard-teacher text-green-600 dark:text-green-400'],
                                    'mark_entered' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'icon' => 'fa-edit text-yellow-600 dark:text-yellow-400'],
                                    'system_alert' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'icon' => 'fa-exclamation-triangle text-red-600 dark:text-red-400'],
                                    'default' => ['bg' => 'bg-gray-50 dark:bg-gray-900/20', 'icon' => 'fa-bell text-gray-600 dark:text-gray-400'],
                                ];
                                $color = $colors[$type] ?? $colors['default'];
                            @endphp

                            <div class="{{ $color['bg'] }} rounded-lg p-3 {{ !$notification->read_at ? 'border-l-4 border-maroon' : '' }}">
                                <div class="flex items-start">
                                    <div class="p-2 {{ str_replace('text-', 'bg-', $color['icon']) }} {{ str_replace('text-', 'dark:bg-', $color['icon']) }} bg-opacity-20 dark:bg-opacity-20 rounded-lg mr-3">
                                        <i class="fas {{ $color['icon'] }}"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800 dark:text-white">
                                            {{ $notificationData['message'] ?? 'Notification' }}
                                        </p>
                                        @if(isset($notificationData['details']))
                                            <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">
                                                {{ $notificationData['details'] }}
                                            </p>
                                        @endif
                                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(!$notification->read_at)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="ml-2">
                                        @csrf
                                        @method('POST')
                                        <button type="submit"
                                                class="p-1 text-gray-400 hover:text-green-600 dark:hover:text-green-400"
                                                title="Mark as read">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @else
            <!-- Empty state -->
            <div class="flex flex-col items-center justify-center h-64">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bell-slash text-gray-400 dark:text-gray-500 text-2xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-lg">No notifications yet</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">You're all caught up!</p>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4 space-y-3">
            @if($notifications->count() > 0)
            <div class="flex space-x-2">
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="flex-1">
                    @csrf
                    @method('POST')
                    <button type="submit" class="w-full bg-maroon text-white py-3 rounded-lg font-medium hover:bg-maroon-dark transition">
                        Mark All as Read
                    </button>
                </form>
                <form action="{{ route('notifications.clearAll') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all notifications?')" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 py-3 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Clear All
                    </button>
                </form>
            </div>
            @endif
            <a href="{{ route('notifications.index') }}"
               class="block text-center text-maroon dark:text-maroon-light font-medium hover:underline">
                View All Notifications
            </a>
        </div>
    </div>
</div>

<!-- Mobile Profile Menu -->
<div id="mobileProfileMenu" class="fixed inset-0 bg-white dark:bg-gray-800 z-50 transform translate-x-full transition-transform duration-300 lg:hidden">
    <div class="h-full flex flex-col">
        <!-- Profile Header -->
        <div class="bg-maroon text-white p-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-r from-maroon-light to-maroon rounded-full flex items-center justify-center shadow-lg overflow-hidden">
                    @if($user->profile_photo_path && Storage::exists('public/' . $user->profile_photo_path))
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                             alt="{{ $userName }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-white font-bold text-2xl">
                            {{ $userInitials }}
                        </span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-bold truncate">{{ $userName }}</h3>
                    <p class="text-white/80 truncate">{{ $userRole }}</p>
                    <p class="text-white/60 text-sm truncate">{{ $user->email }}</p>
                </div>
            </div>
            <button onclick="closeMobileProfileMenu()"
                    class="absolute top-4 right-4 text-white hover:bg-white/20 p-2 rounded-full">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Profile Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-4">
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}"
                       onclick="closeMobileProfileMenu()"
                       class="flex items-center p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-user text-gray-600 dark:text-gray-300 w-6 mr-3"></i>
                        <span class="text-gray-800 dark:text-white">My Profile</span>
                        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                    </a>

                    @can('system.settings')
                    <a href="{{ route('settings.school-profile') }}"
                       onclick="closeMobileProfileMenu()"
                       class="flex items-center p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-cog text-gray-600 dark:text-gray-300 w-6 mr-3"></i>
                        <span class="text-gray-800 dark:text-white">System Settings</span>
                        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                    </a>
                    @endcan

                    <button onclick="openMobileNotifications(); closeMobileProfileMenu();"
                            class="flex items-center w-full p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-bell text-gray-600 dark:text-gray-300 w-6 mr-3"></i>
                        <span class="text-gray-800 dark:text-white">Notifications</span>
                        @if($unreadNotifications > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">
                                {{ $unreadNotifications }}
                            </span>
                        @endif
                    </button>

                    <!-- Theme Toggle in Mobile Menu -->
                    <button onclick="window.toggleTheme()"
                            class="flex items-center w-full p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i id="mobileMenuThemeIcon" class="fas fa-moon text-gray-600 dark:text-gray-300 w-6 mr-3"></i>
                        <span class="text-gray-800 dark:text-white">Dark Mode</span>
                        <span id="themeStatus" class="ml-auto text-xs text-gray-500">Off</span>
                    </button>

                    <a href="#" class="flex items-center p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-shield-alt text-gray-600 dark:text-gray-300 w-6 mr-3"></i>
                        <span class="text-gray-800 dark:text-white">Privacy & Security</span>
                        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                    </a>

                    <a href="#" class="flex items-center p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-question-circle text-gray-600 dark:text-gray-300 w-6 mr-3"></i>
                        <span class="text-gray-800 dark:text-white">Help & Support</span>
                        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                    </a>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                onclick="closeMobileProfileMenu()"
                                class="flex items-center w-full p-4 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt w-6 mr-3"></i>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Action Buttons (Floating) -->
<div class="lg:hidden fixed bottom-20 right-4 z-30 safe-bottom">
    <div class="flex flex-col items-center space-y-3">
        <!-- Quick Add Button -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="w-14 h-14 bg-maroon rounded-full shadow-lg flex items-center justify-center text-white hover:bg-maroon-dark transition">
                <i class="fas fa-plus text-xl transition-transform duration-200" :class="{'rotate-45': open}"></i>
            </button>

            <!-- Quick Add Menu -->
            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 style="display: none;"
                 class="absolute bottom-full right-0 mb-3 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2">
                @can('students.create')
                <a href="{{ route('students.create') }}"
                   @click="open = false"
                   class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-user-plus text-blue-600 dark:text-blue-400 w-5 mr-3"></i>
                    <span class="text-gray-800 dark:text-white">New Student</span>
                </a>
                @endcan

                @can('teachers.create')
                <a href="{{ route('teachers.create') }}"
                   @click="open = false"
                   class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-chalkboard-teacher text-green-600 dark:text-green-400 w-5 mr-3"></i>
                    <span class="text-gray-800 dark:text-white">New Teacher</span>
                </a>
                @endcan

                @can('marks.entry')
                <a href="{{ route('marks.entry.form') }}"
                   @click="open = false"
                   class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-edit text-purple-600 dark:text-purple-400 w-5 mr-3"></i>
                    <span class="text-gray-800 dark:text-white">Enter Marks</span>
                </a>
                @endcan

                @can('classes.create')
                <a href="{{ route('classes.create') }}"
                   @click="open = false"
                   class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-chalkboard text-orange-600 dark:text-orange-400 w-5 mr-3"></i>
                    <span class="text-gray-800 dark:text-white">New Class</span>
                </a>
                @endcan
            </div>
        </div>

        <!-- Notifications Button -->
        <button onclick="openMobileNotifications()"
                class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-maroon relative transition-colors">
            <i class="fas fa-bell text-lg"></i>
            @if($unreadNotifications > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center animate-pulse">
                    {{ $unreadNotifications }}
                </span>
            @endif
        </button>

        <!-- Profile Button -->
        <button onclick="openMobileProfileMenu()"
                class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-maroon transition-colors">
            <i class="fas fa-user text-lg"></i>
        </button>
    </div>
</div>

<script>
    // Mobile navigation functions
    function toggleMobileSearch() {
        const searchBar = document.getElementById('mobileSearchBar');
        searchBar.classList.toggle('hidden');
    }

    function openMobileNotifications() {
        document.getElementById('mobileNotifications').classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileNotifications() {
        document.getElementById('mobileNotifications').classList.add('translate-x-full');
        document.body.style.overflow = 'auto';
    }

    function openMobileProfileMenu() {
        document.getElementById('mobileProfileMenu').classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileProfileMenu() {
        document.getElementById('mobileProfileMenu').classList.add('translate-x-full');
        document.body.style.overflow = 'auto';
    }

    // Update mobile theme icons
    function updateMobileThemeIcons() {
        const currentTheme = window.appTheme?.current || localStorage.getItem('theme') || 'light';
        const mobileThemeIcon = document.getElementById('mobileThemeIcon');
        const mobileMenuThemeIcon = document.getElementById('mobileMenuThemeIcon');
        const themeStatus = document.getElementById('themeStatus');

        if (mobileThemeIcon) {
            mobileThemeIcon.className = currentTheme === 'light' ? 'fas fa-moon text-lg' : 'fas fa-sun text-lg';
            mobileThemeIcon.style.color = currentTheme === 'light' ? '' : '#fbbf24';
        }

        if (mobileMenuThemeIcon) {
            mobileMenuThemeIcon.className = currentTheme === 'light' ?
                'fas fa-moon text-gray-600 dark:text-gray-300 w-6 mr-3' :
                'fas fa-sun text-yellow-500 w-6 mr-3';
        }

        if (themeStatus) {
            themeStatus.textContent = currentTheme === 'light' ? 'Off' : 'On';
            themeStatus.className = currentTheme === 'light' ?
                'ml-auto text-xs text-gray-500' :
                'ml-auto text-xs text-yellow-500 font-medium';
        }
    }

    // Initialize mobile theme on load
    document.addEventListener('DOMContentLoaded', function() {
        updateMobileThemeIcons();
        updateSafeAreas();
    });

    // Listen for theme changes
    document.addEventListener('themeChanged', updateMobileThemeIcons);

    // Update safe areas for modern phones
    function updateSafeAreas() {
        const elements = document.querySelectorAll('.safe-top, .safe-bottom');
        elements.forEach(el => {
            if (el.classList.contains('safe-top')) {
                el.style.paddingTop = 'calc(env(safe-area-inset-top) + 0.75rem)';
            }
            if (el.classList.contains('safe-bottom')) {
                el.style.paddingBottom = 'calc(env(safe-area-inset-bottom) + 0.75rem)';
            }
        });
    }

    // Update on resize
    window.addEventListener('resize', updateSafeAreas);
</script>
