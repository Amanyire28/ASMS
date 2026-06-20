<!-- Mobile Sidebar Overlay -->
<div id="mobileSidebarOverlay"
     class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"
     @click="$store.mobileSidebar.close()"></div>

<!-- Mobile Sidebar with Alpine.js -->
<div id="mobileSidebar"
     x-data="sidebarData"
     class="fixed top-0 left-0 h-full w-64 bg-maroon dark:bg-gray-800 border-r border-white/10 shadow-lg z-50 transform -translate-x-full transition-transform duration-300">
    <div class="p-4 h-full flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-3">
                @php
                    // Get school logo from database
                    $schoolLogo = school_setting('school_logo');
                    $schoolName = school_setting('school_name') ?? config('app.name', 'ASMS');
                @endphp

                @if($schoolLogo && Storage::exists('public/' . $schoolLogo))
                    <div class="w-10 h-10 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <img src="{{ asset('storage/' . $schoolLogo) }}"
                             alt="{{ $schoolName }}"
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-10 h-10 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-maroon font-bold text-sm">
                            {{ strtoupper(substr($schoolName, 0, 2)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <p class="font-medium text-white">{{ $schoolName }}</p>
                    <p class="text-sm text-white/80">Academic School System</p>
                </div>
            </div>

            <button @click="$store.mobileSidebar.close()" class="text-white/90 hover:text-white p-2">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>



        <!-- Mobile navigation links - WITH PERMISSIONS AND ALPINE.JS -->
        <nav class="space-y-1 flex-1 overflow-y-auto">
            <!-- Dashboard (Everyone) -->
            <a href="{{ route('dashboard') }}"
               @click="handleLinkClick()"
               :class="{
                   'bg-white !text-maroon': isExactActive('dashboard'),
                   'text-white/90 hover:bg-white/20': !isExactActive('dashboard')
               }"
               class="mobile-nav-link flex items-center space-x-3 p-3 rounded-lg transition-all duration-200">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Students Dropdown (Only if has permission) -->
            @can('students.view')
            <div class="relative mobile-dropdown">
                <button type="button"
                        @click="toggleDropdown('students', $event)"
                        :class="{
                            'bg-white !text-maroon': isActive('students.*'),
                            'text-white/90 hover:bg-white/20': !isActive('students.*')
                        }"
                        class="mobile-dropdown-btn nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span class="font-medium">Students</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform"
                       :class="dropdowns.students ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="dropdowns.students"
                     x-transition
                     style="display: none;"
                     class="mobile-dropdown-content ml-8 mt-1 space-y-1 bg-white/10 backdrop-blur-sm rounded-lg p-2">
                    @can('students.view')
                    <a href="{{ route('students.index') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('students.index'),
                           'text-white/90 hover:bg-white/20': !isExactActive('students.index')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-list w-4 text-center"></i>
                        <span>All Students</span>
                    </a>
                    @endcan

                    @can('students.create')
                    <a href="{{ route('students.create') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('students.create'),
                           'text-white/90 hover:bg-white/20': !isExactActive('students.create')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-user-plus w-4 text-center"></i>
                        <span>Add New</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcan

            <!-- Teachers Dropdown (Only if has permission) -->
            @can('teachers.view')
            <div class="relative mobile-dropdown">
                <button type="button"
                        @click="toggleDropdown('teachers', $event)"
                        :class="{
                            'bg-white !text-maroon': isActive('teachers.*'),
                            'text-white/90 hover:bg-white/20': !isActive('teachers.*')
                        }"
                        class="mobile-dropdown-btn nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-chalkboard-teacher w-5 text-center"></i>
                        <span class="font-medium">Teachers</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform"
                       :class="dropdowns.teachers ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="dropdowns.teachers"
                     x-transition
                     style="display: none;"
                     class="mobile-dropdown-content ml-8 mt-1 space-y-1 bg-white/10 backdrop-blur-sm rounded-lg p-2">
                    @can('teachers.view')
                    <a href="{{ route('teachers.index') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('teachers.index'),
                           'text-white/90 hover:bg-white/20': !isExactActive('teachers.index')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-list w-4 text-center"></i>
                        <span>All Teachers</span>
                    </a>
                    @endcan

                    @can('teachers.create')
                    <a href="{{ route('teachers.create') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('teachers.create'),
                           'text-white/90 hover:bg-white/20': !isExactActive('teachers.create')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-user-plus w-4 text-center"></i>
                        <span>Add New</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcan

            <!-- Classes (Only if has permission) -->
            @can('classes.view')
            <a href="{{ route('classes.index') }}"
               @click="handleLinkClick()"
               :class="{
                   'bg-white !text-maroon': isActive('classes.*'),
                   'text-white/90 hover:bg-white/20': !isActive('classes.*')
               }"
               class="mobile-nav-link flex items-center space-x-3 p-3 rounded-lg transition-all duration-200">
                <i class="fas fa-chalkboard w-5 text-center"></i>
                <span class="font-medium">Classes</span>
            </a>
            @endcan

            <!-- Subjects (Only if has permission) -->
            @can('subjects.view')
            <a href="{{ route('subjects.index') }}"
               @click="handleLinkClick()"
               :class="{
                   'bg-white !text-maroon': isActive('subjects.*'),
                   'text-white/90 hover:bg-white/20': !isActive('subjects.*')
               }"
               class="mobile-nav-link flex items-center space-x-3 p-3 rounded-lg transition-all duration-200">
                <i class="fas fa-book w-5 text-center"></i>
                <span class="font-medium">Subjects</span>
            </a>
            @endcan

            <!-- Marks Dropdown (Only if has permission) -->
            @canany(['marks.view', 'marks.entry'])
            <div class="relative mobile-dropdown">
                <button type="button"
                        @click="toggleDropdown('marks', $event)"
                        :class="{
                            'bg-white !text-maroon': isActive('marks.*'),
                            'text-white/90 hover:bg-white/20': !isActive('marks.*')
                        }"
                        class="mobile-dropdown-btn nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-edit w-5 text-center"></i>
                        <span class="font-medium">Marks</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform"
                       :class="dropdowns.marks ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="dropdowns.marks"
                     x-transition
                     style="display: none;"
                     class="mobile-dropdown-content ml-8 mt-1 space-y-1 bg-white/10 backdrop-blur-sm rounded-lg p-2">
                    @can('marks.entry')
                    <a href="{{ route('marks.entry.form') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('marks.entry.form'),
                           'text-white/90 hover:bg-white/20': !isExactActive('marks.entry.form')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-pen w-4 text-center"></i>
                        <span>Enter Marks</span>
                    </a>
                    @endcan

                    @can('marks.view')
                    <a href="{{ route('marks.index') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('marks.index'),
                           'text-white/90 hover:bg-white/20': !isExactActive('marks.index')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-list w-4 text-center"></i>
                        <span>View Marks</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- Reports Dropdown (Only if has permission) -->
            @can('reports.view')
            <div class="relative mobile-dropdown">
                <button type="button"
                        @click="toggleDropdown('reports', $event)"
                        :class="{
                            'bg-white !text-maroon': isActive('report.card.*'),
                            'text-white/90 hover:bg-white/20': !isActive('report.card.*')
                        }"
                        class="mobile-dropdown-btn nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-alt w-5 text-center"></i>
                        <span class="font-medium">Reports</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform"
                       :class="dropdowns.reports ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="dropdowns.reports"
                     x-transition
                     style="display: none;"
                     class="mobile-dropdown-content ml-8 mt-1 space-y-1 bg-white/10 backdrop-blur-sm rounded-lg p-2">
                    @can('reports.view')
                    <a href="{{ route('report.card.form') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('report.card.form'),
                           'text-white/90 hover:bg-white/20': !isExactActive('report.card.form')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-graduation-cap w-4 text-center"></i>
                        <span>Report Cards</span>
                    </a>
                    @endcan

                    @can('reports.view')
                    <a href="/admin/reports/mass"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('/admin/reports/mass'),
                           'text-white/90 hover:bg-white/20': !isExactActive('/admin/reports/mass')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-file-archive w-4 text-center"></i>
                        <span>Mass Download</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcan

            <!-- System Management (Settings) -->
            @canany(['system.users', 'system.roles', 'system.settings'])
            <div class="relative mobile-dropdown">
                <button type="button"
                        @click="toggleDropdown('system', $event)"
                        :class="{
                            'bg-white !text-maroon': isActive('system.*') || isActive('settings.*'),
                            'text-white/90 hover:bg-white/20': !isActive('system.*') && !isActive('settings.*')
                        }"
                        class="mobile-dropdown-btn nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-cog w-5 text-center"></i>
                        <span class="font-medium">System</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform"
                       :class="dropdowns.system ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="dropdowns.system"
                     x-transition
                     style="display: none;"
                     class="mobile-dropdown-content ml-8 mt-1 space-y-1 bg-white/10 backdrop-blur-sm rounded-lg p-2">
                    @can('system.users')
                    <a href="{{ route('system.users') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('system.users'),
                           'text-white/90 hover:bg-white/20': !isExactActive('system.users')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-users w-4 text-center"></i>
                        <span>User Management</span>
                    </a>
                    @endcan

                    @can('system.roles')
                    <a href="{{ route('system.roles') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('system.roles'),
                           'text-white/90 hover:bg-white/20': !isExactActive('system.roles')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-user-shield w-4 text-center"></i>
                        <span>Roles & Permissions</span>
                    </a>
                    @endcan

                    @can('system.settings')
                    <a href="{{ route('settings.school-profile') }}"
                       @click="handleLinkClick()"
                       :class="{
                           'bg-white !text-maroon': isExactActive('settings.school-profile'),
                           'text-white/90 hover:bg-white/20': !isExactActive('settings.school-profile')
                       }"
                       class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                        <i class="fas fa-school w-4 text-center"></i>
                        <span>School Settings</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany



            <!-- My Profile -->
            <a href="{{ route('profile.edit') }}"
               @click="handleLinkClick()"
               :class="{
                   'bg-white !text-maroon': isExactActive('profile.edit'),
                   'text-white/90 hover:bg-white/20': !isExactActive('profile.edit')
               }"
               class="mobile-nav-link flex items-center space-x-3 p-3 rounded-lg transition-all duration-200">
                <i class="fas fa-user w-5 text-center"></i>
                <span class="font-medium">My Profile</span>
            </a>
        </nav>

        <!-- Logout button at bottom -->
        <div class="pt-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center space-x-3 w-full p-3 rounded-lg hover:bg-white/20 text-white/90 transition-colors duration-200">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    <span class="font-medium">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Alpine.js Store for Mobile Sidebar -->
<script>
    document.addEventListener('alpine:init', () => {
        if (!Alpine.store('mobileSidebar')) {
            Alpine.store('mobileSidebar', {
                isOpen: false,

                open() {
                    this.isOpen = true;
                    const overlay = document.getElementById('mobileSidebarOverlay');
                    const sidebar = document.getElementById('mobileSidebar');
                    if (overlay && sidebar) {
                        overlay.classList.remove('hidden');
                        sidebar.classList.remove('-translate-x-full');
                        document.body.style.overflow = 'hidden';
                    }
                },

                close() {
                    this.isOpen = false;
                    const overlay = document.getElementById('mobileSidebarOverlay');
                    const sidebar = document.getElementById('mobileSidebar');
                    if (overlay && sidebar) {
                        overlay.classList.add('hidden');
                        sidebar.classList.add('-translate-x-full');
                        document.body.style.overflow = 'auto';
                    }
                }
            });
        }

        // Mobile sidebar data
        Alpine.data('sidebarData', () => ({
            dropdowns: {
                students: false,
                teachers: false,
                marks: false,
                reports: false,
                system: false
            },

            toggleDropdown(type, event) {
                event.preventDefault();
                event.stopPropagation();
                this.dropdowns[type] = !this.dropdowns[type];

                // Close other dropdowns
                Object.keys(this.dropdowns).forEach(key => {
                    if (key !== type) {
                        this.dropdowns[key] = false;
                    }
                });
            },

            isExactActive(routeName) {
                return window.location.pathname === route(routeName);
            },

            isActive(routePattern) {
                const currentPath = window.location.pathname;
                if (routePattern.includes('*')) {
                    const baseRoute = routePattern.replace('*', '');
                    return currentPath.startsWith(route(baseRoute.replace(/\.\*$/, '')));
                }
                return currentPath.startsWith(route(routePattern));
            },

            handleLinkClick() {
                Alpine.store('mobileSidebar').close();
            }
        }));
    });

    // Global functions for backward compatibility
    function openMobileSidebar() {
        if (window.Alpine && Alpine.store('mobileSidebar')) {
            Alpine.store('mobileSidebar').open();
        }
    }

    function closeMobileSidebar() {
        if (window.Alpine && Alpine.store('mobileSidebar')) {
            Alpine.store('mobileSidebar').close();
        }
    }

    // Close on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMobileSidebar();
        }
    });
</script>

<style>
    /* Mobile sidebar specific styles */
    #mobileSidebar {
        background: linear-gradient(180deg, #800000 0%, #5f0000 100%) !important;
    }

    .dark #mobileSidebar {
        background: linear-gradient(180deg, #1f2937 0%, #111827 100%) !important;
    }

    /* Active state styling */
    .mobile-nav-link.bg-white i,
    .mobile-nav-link.bg-white span,
    .mobile-dropdown-btn.bg-white i,
    .mobile-dropdown-btn.bg-white span {
        color: #800000 !important;
    }

    /* Dropdown animation */
    .mobile-dropdown-content {
        transition: all 0.2s ease-in-out;
    }

    .fa-chevron-down.rotate-180 {
        transform: rotate(180deg);
    }

    /* Smooth scrolling for mobile sidebar */
    #mobileSidebar nav {
        -webkit-overflow-scrolling: touch;
    }

    #mobileSidebar nav::-webkit-scrollbar {
        width: 4px;
    }

    #mobileSidebar nav::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    #mobileSidebar nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }

    /* Animation for mobile sidebar */
    #mobileSidebar {
        transition: transform 0.3s ease-in-out;
    }

    #mobileSidebarOverlay {
        transition: opacity 0.3s ease-in-out;
    }
</style>
