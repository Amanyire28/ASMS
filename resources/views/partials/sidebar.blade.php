<aside class="sidebar border-r border-blue-200 min-h-screen flex flex-col"
       x-data="sidebarData">

    <!-- Logo and Brand -->
    <div class="p-4 border-b border-blue-200 sidebar-header">
        <div class="flex items-center gap-3">

            <!-- Logo Container -->
            <div class="w-11 h-11 rounded-full overflow-hidden bg-white dark:bg-gray-700 flex items-center justify-center shadow-sm ring-1 ring-blue-200">
                <img
                    src="{{ asset('storage/' . school_setting('school_logo')) }}"
                    alt="{{ school_setting('school_name') }}"
                    class="w-full h-full "
                >
            </div>

            <!-- Text -->
            <div class="sidebar-text flex flex-col" x-show="!sidebarCollapsed" x-transition>
                <h2 class="font-semibold text-base text-gray-900 leading-tight tracking-wide">
                    {{ config('app.name', 'ASMS') }}
                </h2>
                <p class="text-[11px] text-gray-600 tracking-wide">
                    Academic School System
                </p>
            </div>

        </div>
    </div>

    <!-- Navigation Menu (Expanded Mode) -->
    <nav class="p-4 space-y-1 flex-1 sidebar-content" x-show="!sidebarCollapsed" x-transition>
        <!-- Dashboard (Everyone can see) -->
        <a href="/dashboard"
           @click="handleLinkClick()"
           :class="{
               'bg-blue-700 !text-white font-semibold': isExactActive('/dashboard'),
               'text-gray-900 hover:bg-blue-200': !isExactActive('/dashboard')
           }"
           class="nav-link flex items-center space-x-3 p-3 rounded-lg transition-all duration-200">
            <i class="fas fa-home w-5 text-center"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Students Dropdown (Only if has permission) -->
        @canany('students.view')
        <div class="relative">
            <button @click="toggleDropdown('students', $event)"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isActive('/admin/students'),
                    'text-gray-900 hover:bg-blue-200': !isActive('/admin/students')
                }"
                class="nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="font-medium">Students</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                   :class="dropdowns.students ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="dropdowns.students"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="display: none;"
                 class="ml-8 mt-1 space-y-1 bg-blue-100 rounded-lg p-2">

                @canany('students.view')
                <a href="/admin/students"
                   @click="handleLinkClick()"
                   :class="{
                       'bg-blue-700 !text-white font-semibold': isExactActive('/admin/students'),
                       'text-gray-900 hover:bg-blue-200': !isExactActive('/admin/students')
                   }"
                   class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-list w-4 text-center"></i>
                    <span>All Students</span>
                </a>
                @endcanany

                @canany('students.create')
                <a href="/admin/students/create"
                   @click="handleLinkClick()"
                   :class="{
                       'bg-blue-700 !text-white font-semibold': isExactActive('/admin/students/create'),
                       'text-gray-900 hover:bg-blue-200': !isExactActive('/admin/students/create')
                   }"
                   class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-user-plus w-4 text-center"></i>
                    <span>Add New</span>
                </a>
                @endcanany
            </div>
        </div>
        @endcanany

        <!-- Teachers Dropdown (Only if has permission) -->
        @canany('teachers.view')
        <div class="relative">
            <button @click="toggleDropdown('teachers', $event)"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isActive('/admin/teachers'),
                    'text-gray-900 hover:bg-blue-200': !isActive('/admin/teachers')
                }"
                class="nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chalkboard-teacher w-5 text-center"></i>
                    <span class="font-medium">Teachers</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                   :class="dropdowns.teachers ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="dropdowns.teachers" x-transition style="display: none;"
                 class="ml-8 mt-1 space-y-1 bg-blue-100 rounded-lg p-2">

                @canany('teachers.view')
                <a href="/admin/teachers"
                   @click="handleLinkClick()"
                   :class="{
                       'bg-blue-700 !text-white font-semibold': isExactActive('/admin/teachers'),
                       'text-gray-900 hover:bg-blue-200': !isExactActive('/admin/teachers')
                   }"
                   class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-list w-4 text-center"></i>
                    <span>All Teachers</span>
                </a>
                @endcanany

                @canany('teachers.create')
                <a href="/admin/teachers/create"
                   @click="handleLinkClick()"
                   :class="{
                       'bg-blue-700 !text-white font-semibold': isExactActive('/admin/teachers/create'),
                       'text-gray-900 hover:bg-blue-200': !isExactActive('/admin/teachers/create')
                   }"
                   class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-user-plus w-4 text-center"></i>
                    <span>Add New</span>
                </a>
                @endcanany
            </div>
        </div>
        @endcanany

        <!-- Classes Dropdown -->
        @canany('classes.view')
        <div class="relative">
            <button @click="toggleDropdown('classes', $event)"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isActive('/admin/classes') || isActive('/admin/class-levels') || isActive('/admin/streams'),
                    'text-gray-900 hover:bg-blue-200': !isActive('/admin/classes') && !isActive('/admin/class-levels') && !isActive('/admin/streams')
                }"
                class="nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chalkboard w-5 text-center"></i>
                    <span class="font-medium">Classes</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                :class="dropdowns.classes ? 'rotate-180' : ''"></i>
            </button>

            <!-- UPDATED: Match other dropdowns -->
            <div x-show="dropdowns.classes"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                style="display: none;"
                class="ml-8 mt-1 space-y-1 bg-blue-100 rounded-lg p-2">
                <a href="/admin/classes"
                @click="handleLinkClick()"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isExactActive('/admin/classes'),
                    'text-gray-900 hover:bg-blue-200': !isExactActive('/admin/classes')
                }"
                class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-list w-4 text-center"></i>
                    <span>All Classes</span>
                </a>
                <a href="/admin/class-categories"
                @click="handleLinkClick()"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isActive('/admin/class-categories'),
                    'text-gray-900 hover:bg-blue-200': !isActive('/admin/class-categories')
                }"
                class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-tags w-4 text-center"></i>
                    <span>Categories</span>
                </a>
                <a href="/admin/class-levels"
                @click="handleLinkClick()"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isActive('/admin/class-levels'),
                    'text-gray-900 hover:bg-blue-200': !isActive('/admin/class-levels')
                }"
                class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-layer-group w-4 text-center"></i>
                    <span>Class Levels</span>
                </a>
                <a href="/admin/streams"
                @click="handleLinkClick()"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isActive('/admin/streams'),
                    'text-gray-900 hover:bg-blue-200': !isActive('/admin/streams')
                }"
                class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-stream w-4 text-center"></i>
                    <span>Streams</span>
                </a>
            </div>
        </div>
        @endcanany

        <!-- Subjects (Only if has permission) -->
        @canany('subjects.view')
        <a href="/admin/subjects"
           @click="handleLinkClick()"
           :class="{
               'bg-blue-700 !text-white font-semibold': isActive('/admin/subjects'),
               'text-gray-900 hover:bg-blue-200': !isActive('/admin/subjects')
           }"
           class="nav-link flex items-center space-x-3 p-3 rounded-lg transition-all duration-200">
            <i class="fas fa-book w-5 text-center"></i>
            <span class="font-medium">Subjects</span>
        </a>
        @endcanany


        <a href="/admin/announcements"
           @click="handleLinkClick()"
           :class="{
               'bg-blue-700 !text-white font-semibold': isActive('/admin/announcements'),
               'text-gray-900 hover:bg-blue-200': !isActive('/admin/announcements')
           }"
           class="nav-link flex items-center space-x-3 p-3 rounded-lg transition-all duration-200">
            <i class="fas fa-book w-5 text-center"></i>
            <span class="font-medium">Announce</span>
        </a>


        <!-- Marks Dropdown (Only if has permission) -->
        @canany(['marks.view', 'marks.entry'])
        <div class="relative">
            <button @click="toggleDropdown('marks', $event)"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isActive('/admin/marks'),
                    'text-gray-900 hover:bg-blue-200': !isActive('/admin/marks')
                }"
                class="nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-edit w-5 text-center"></i>
                    <span class="font-medium">Marks</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                   :class="dropdowns.marks ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="dropdowns.marks" x-transition style="display: none;"
                 class="ml-8 mt-1 space-y-1 bg-blue-100 rounded-lg p-2">

                @canany('marks.entry')
                <a href="/admin/marks-entry"
                   @click="handleLinkClick()"
                   :class="{
                       'bg-blue-700 !text-white font-semibold': isExactActive('/admin/marks-entry'),
                       'text-gray-900 hover:bg-blue-200': !isExactActive('/admin/marks-entry')
                   }"
                   class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-pen w-4 text-center"></i>
                    <span>Enter Marks</span>
                </a>
                @endcanany

                @canany('marks.view')
                <a href="/admin/marks"
                   @click="handleLinkClick()"
                   :class="{
                       'bg-blue-700 !text-white font-semibold': isExactActive('/admin/marks'),
                       'text-gray-900 hover:bg-blue-200': !isExactActive('/admin/marks')
                   }"
                   class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-list w-4 text-center"></i>
                    <span>View Marks</span>
                </a>
                @endcanany
            </div>
        </div>
        @endcanany

        <!-- Reports Dropdown (Only if has permission) -->
        @canany('reports.view')
        <div class="relative">
            <button @click="toggleDropdown('reports', $event)"
                :class="{
                    'bg-blue-700 !text-white font-semibold': isActive('/admin/report-card'),
                    'text-gray-900 hover:bg-blue-200': !isActive('/admin/report-card')
                }"
                class="nav-link flex items-center justify-between w-full p-3 rounded-lg transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-file-alt w-5 text-center"></i>
                    <span class="font-medium">Reports</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                   :class="dropdowns.reports ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="dropdowns.reports" x-transition style="display: none;"
                 class="ml-8 mt-1 space-y-1 bg-blue-100 rounded-lg p-2">

                @canany('reports.view')
                <a href="/admin/report-card/form"
                   @click="handleLinkClick()"
                   :class="{
                       'bg-blue-700 !text-white font-semibold': isExactActive('/admin/report-card/form'),
                       'text-gray-900 hover:bg-blue-200': !isExactActive('/admin/report-card/form')
                   }"
                   class="flex items-center space-x-3 p-2 rounded text-sm transition-colors">
                    <i class="fas fa-graduation-cap w-4 text-center"></i>
                    <span>Report Cards</span>
                </a>
                @endcanany
            </div>
        </div>
        @endcanany

       <!-- Settings (using your HEAD branch structure) -->
       @canany(['system.users', 'system.roles'])
        <a href="/admin/system"
        @click="handleLinkClick()"
        :class="{
            'bg-blue-700 !text-white font-semibold': isActive('/admin/system'),
            'text-gray-900 hover:bg-blue-200': !isActive('/admin/system')
        }"
        class="nav-link flex items-center space-x-3 p-3 rounded-lg transition-all duration-200">
            <i class="fas fa-cog w-5 text-center"></i>
            <span class="font-medium">Settings</span>
        </a>
        @endcanany
    </nav>

    <!-- Icons Only Menu (Collapsed Mode) - WITH PERMISSIONS -->
    <nav class="p-2 space-y-1 flex-1 sidebar-icons" x-show="sidebarCollapsed" x-transition style="display: none;">
        <!-- Dashboard Icon (Everyone) -->
        <a href="/dashboard"
           @click="handleLinkClick()"
           :class="{'bg-blue-700': isExactActive('/dashboard')}"
           class="nav-link-icon group relative flex justify-center p-3 rounded-lg transition-all text-gray-900 hover:bg-blue-200">
            <i class="fas fa-home text-xl"></i>
            <span class="sidebar-tooltip">Dashboard</span>
        </a>

        <!-- Students Icon -->
        @canany('students.view')
        <a href="/admin/students"
           @click="handleLinkClick()"
           :class="{'bg-blue-700': isActive('/admin/students')}"
           class="nav-link-icon group relative flex justify-center p-3 rounded-lg transition-all text-gray-900 hover:bg-blue-200">
            <i class="fas fa-users text-xl"></i>
            <span class="sidebar-tooltip">Students</span>
        </a>
        @endcanany

        <!-- Teachers Icon -->
        @canany('teachers.view')
        <a href="/admin/teachers"
           @click="handleLinkClick()"
           :class="{'bg-blue-700': isActive('/admin/teachers')}"
           class="nav-link-icon group relative flex justify-center p-3 rounded-lg transition-all text-gray-900 hover:bg-blue-200">
            <i class="fas fa-chalkboard-teacher text-xl"></i>
            <span class="sidebar-tooltip">Teachers</span>
        </a>
        @endcanany

        <!-- Classes Icon -->
        @canany('classes.view')
        <a href="/admin/classes"
           @click="handleLinkClick()"
           :class="{'bg-blue-700': isActive('/admin/classes')}"
           class="nav-link-icon group relative flex justify-center p-3 rounded-lg transition-all text-gray-900 hover:bg-blue-200">
            <i class="fas fa-chalkboard text-xl"></i>
            <span class="sidebar-tooltip">Classes</span>
        </a>
        @endcanany

        <!-- Subjects Icon -->
        @canany('subjects.view')
        <a href="/admin/subjects"
           @click="handleLinkClick()"
           :class="{'bg-blue-700': isActive('/admin/subjects')}"
           class="nav-link-icon group relative flex justify-center p-3 rounded-lg transition-all text-gray-900 hover:bg-blue-200">
            <i class="fas fa-book text-xl"></i>
            <span class="sidebar-tooltip">Subjects</span>
        </a>
        @endcanany

        <!-- Marks Icon -->
        @canany('marks.entry')
        <a href="/admin/marks-entry"
           @click="handleLinkClick()"
           :class="{'bg-blue-700': isActive('/admin/marks-entry')}"
           class="nav-link-icon group relative flex justify-center p-3 rounded-lg transition-all text-gray-900 hover:bg-blue-200">
            <i class="fas fa-edit text-xl"></i>
            <span class="sidebar-tooltip">Marks</span>
        </a>
        @endcanany

        <!-- Reports Icon -->
        @canany('reports.view')
        <a href="/admin/report-card/form"
           @click="handleLinkClick()"
           :class="{'bg-blue-700': isActive('/admin/report-card/form')}"
           class="nav-link-icon group relative flex justify-center p-3 rounded-lg transition-all text-gray-900 hover:bg-blue-200">
            <i class="fas fa-file-alt text-xl"></i>
            <span class="sidebar-tooltip">Reports</span>
        </a>
        @endcanany

        <!-- Settings Icon -->
        @canany(['system.users', 'system.roles'])
        <a href="/admin/system"
           @click="handleLinkClick()"
           :class="{'bg-blue-700': isActive('/admin/system')}"
           class="nav-link-icon group relative flex justify-center p-3 rounded-lg transition-all text-gray-900 hover:bg-blue-200">
            <i class="fas fa-cog text-xl"></i>
            <span class="sidebar-tooltip">Settings</span>
        </a>
        @endcanany
    </nav>

    <!-- Logout Button (Everyone) -->
    <div class="border-t border-blue-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full p-4 hover:bg-blue-200 text-gray-900 transition-all"
                    :class="sidebarCollapsed ? 'justify-center' : 'space-x-3'">
                <i class="fas fa-sign-out-alt" :class="sidebarCollapsed ? 'text-xl' : 'w-5 text-center'"></i>
                <span class="font-medium" x-show="!sidebarCollapsed" x-transition>Logout</span>
            </button>
        </form>
    </div>
</aside>

<style>
    .sidebar {
        width: 256px;
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(180deg, #E3F2FD 0%, #BBDEFB 100%) !important;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }

    .sidebar.collapsed {
        width: 70px !important;
    }

    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
    }

    /* Blue for dropdown backgrounds */
    .bg-maroon-dark {
        background-color: #90CAF9 !important;
    }

    .dark .bg-maroon-dark {
        background-color: #1f2937 !important;
    }

    /* Tooltip for collapsed mode */
    .sidebar-tooltip {
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        margin-left: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: #1f2937;
        color: white;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
        z-index: 50;
    }

    .group:hover .sidebar-tooltip {
        opacity: 1;
    }

    /* Dark mode */
    .dark .sidebar {
        background: linear-gradient(180deg, #1f2937 0%, #111827 100%) !important;
    }

    /* Active state styling */
    .nav-link.bg-blue-700 i,
    .nav-link.bg-blue-700 span,
    .nav-link-icon.bg-blue-700 i {
        color: #ffffff !important;
    }

    /* Smooth transitions */
    .nav-link, .nav-link-icon {
        transition: all 0.2s ease-in-out;
    }
</style>

