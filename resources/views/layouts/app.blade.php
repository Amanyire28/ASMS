<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@yield('title', school_setting('school_name') .'Dashboard') </title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script defer src="{{ mix('js/app.js') }}"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>

        /* Layout styles */
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        @media (min-width: 1024px) {
            .desktop-layout {
                display: flex;
                min-height: 100vh;
                width: 100%;
            }

            .sidebar-area {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                width: 256px;
                z-index: 40;
                transition: width 0.3s ease;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .sidebar-area.collapsed {
                width: 70px;
            }

            .main-content-area {
                flex: 1;
                margin-left: 256px;
                transition: margin-left 0.3s ease;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .main-content-area.collapsed {
                margin-left: 70px;
            }
        }

        /* Colors */
        .text-maroon { color: #1565C0 !important; }
        .bg-maroon { background-color: #1565C0 !important; }
        .hover\:bg-maroon:hover { background-color: #1565C0 !important; }
        .border-maroon { border-color: #1565C0 !important; }

        .dark .text-maroon { color: #42A5F5 !important; }
        .dark .bg-maroon { background-color: #0D47A1 !important; }
        .dark .border-maroon { border-color: #0D47A1 !important; }

        /* Spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1565C0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Page transition */
        .page-content {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4a5568;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">


    <div class="desktop-layout hidden lg:flex">
        <!-- Sidebar Area -->
        <div class="sidebar-area">
            @include('partials.sidebar')
        </div>

        <!-- Main Content Area -->
        <div class="main-content-area">
            <!-- Navbar -->
            @include('partials.navbar')

            <!-- Flash Messages Container -->
            <div id="flash-messages" class="px-6 pt-4">
                @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-lg flash-message"
                     x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 dark:text-green-400"></i>
                            <p class="ml-3 text-green-700 dark:text-green-300">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-green-500 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg flash-message"
                     x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 dark:text-red-400"></i>
                            <p class="ml-3 text-red-700 dark:text-red-300">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                @if(session('warning'))
                <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded-lg flash-message"
                     x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 dark:text-yellow-400"></i>
                            <p class="ml-3 text-yellow-700 dark:text-yellow-300">{{ session('warning') }}</p>
                        </div>
                        <button @click="show = false" class="text-yellow-500 dark:text-yellow-400 hover:text-yellow-700 dark:hover:text-yellow-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

            </div>

            <!-- Loading Indicator -->
            <div id="loading-indicator"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center"
                 style="opacity: 0; pointer-events: none; transition: opacity 200ms;">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-2xl">
                    <div class="spinner"></div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                <div id="page-content" class="page-content">
                    @hasSection('content')
                        @yield('content')
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-exclamation-triangle text-6xl text-yellow-500 mb-4"></i>
                            <h2 class="text-2xl font-bold mb-2">No Content</h2>
                            <p class="text-gray-600 dark:text-gray-400">Please define a @section('content') in your view.</p>
                        </div>
                    @endif
                </div>
            </main>

            <!-- Footer -->
            @include('partials.footer')
        </div>
    </div>


    <!-- Mobile Layout -->

    <div class="lg:hidden">
        @include('partials.mobile-nav')
        @include('partials.mobile-sidebar')

        <div class="pt-16 pb-20">
            <!-- Mobile Flash Messages -->
            <div id="flash-messages-mobile" class="p-4">
                @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-3 rounded-lg flash-message"
                     x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-sm"></i>
                            <p class="ml-3 text-green-700 dark:text-green-300 text-sm">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-green-500">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded-lg flash-message"
                     x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-sm"></i>
                            <p class="ml-3 text-red-700 dark:text-red-300 text-sm">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-500">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Mobile Loading Indicator -->
            <div id="loading-indicator-mobile"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center"
                 style="opacity: 0; pointer-events: none; transition: opacity 200ms;">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-2xl">
                    <div class="spinner"></div>
                </div>
            </div>

            <!-- Mobile Page Content -->
            <main class="p-4">
                <div id="page-content-mobile" class="page-content"></div>
            </main>
        </div>

        @include('partials.mobile-bottom-nav')
    </div>

    <!-- SPA Router -->
    <script src="{{ asset('js/spa-router.js') }}"></script>


    <!-- Alpine.js Component Definitions -->
<script>
    document.addEventListener('alpine:init', () => {
        // School Profile Settings Component
        Alpine.data('schoolProfileSettings', () => ({
            activeTab: 'basic',

            submitForm(event) {
                const form = event.target;
                const formData = new FormData(form);
                const action = form.getAttribute('action');
                const method = form.getAttribute('method') || 'POST';

                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                if (window.router && window.router.showLoading) {
                    window.router.showLoading();
                }

                fetch(action, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Cache-Control': 'no-cache, no-store, max-age=0',
                        'Pragma': 'no-cache'
                    },
                    cache: 'no-store',
                    credentials: 'same-origin'
                })
                .then(response => {
                    // Check content type before parsing JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // If not JSON, reload page
                        window.location.reload();
                        return { success: true };
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        // Show success message if router has showAlert
                        if (window.router && window.router.showAlert) {
                            window.router.showAlert(data.message || 'Saved successfully!', 'success');
                        }
                        // Small delay before reload to show message
                        setTimeout(() => window.location.reload(), 1000);
                    } else if (data && data.errors) {
                        // Show validation errors
                        let errorMsg = 'Validation errors:\n';
                        for (const [field, errors] of Object.entries(data.errors)) {
                            errorMsg += `• ${errors.join(', ')}\n`;
                        }
                        alert(errorMsg);
                    } else {
                        throw new Error(data?.message || 'Error saving!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (window.router && window.router.hideLoading) {
                        window.router.hideLoading();
                    }
                    alert('Error: ' + error.message);
                });
            }
        }));

        // Sidebar Data Component
        Alpine.data('sidebarData', () => ({
            dropdowns: {
                students: false,
                teachers: false,
                marks: false,
                reports: false,
                system: false,
                classes: false
            },
            sidebarCollapsed: false,
            currentPath: window.location.pathname,

            init() {
                // Initialize collapsed state from localStorage
                try {
                    this.sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

                    // Load saved dropdown states from localStorage
                    const savedDropdowns = localStorage.getItem('sidebarDropdowns');
                    if (savedDropdowns) {
                        try {
                            const parsed = JSON.parse(savedDropdowns);
                            Object.keys(this.dropdowns).forEach(key => {
                                if (parsed[key] !== undefined) {
                                    this.dropdowns[key] = parsed[key];
                                }
                            });
                        } catch (e) {
                            console.warn('Could not parse saved dropdowns:', e);
                        }
                    }

                    this.applySidebarState();
                } catch (e) {
                    console.warn('Could not read sidebar state:', e);
                    this.sidebarCollapsed = false;
                }

                // Set initial dropdown states based on current URL
                this.updateDropdownsFromURL(this.currentPath);

                // Listen for SPA navigation
                window.addEventListener('spa:navigated', (e) => {
                    this.currentPath = e.detail.path || window.location.pathname;
                    this.updateDropdownsFromURL(this.currentPath);
                });

                // Listen for sidebar toggle from navbar
                document.addEventListener('toggle-sidebar', () => {
                    this.toggleSidebar();
                });

                // Listen for notification updates
                window.addEventListener('notifications:updated', () => {
                    this.updateNotificationBadge();
                });
            },

            updateDropdownsFromURL(path) {
                // Don't close all dropdowns automatically - keep current state
                // Only update based on URL if needed

                // If we're already on a page that should have a dropdown open, ensure it's open
                if (!this.sidebarCollapsed) {
                    const urlBasedState = {
                        students: path.startsWith('/admin/students'),
                        teachers: path.startsWith('/admin/teachers'),
                        marks: path.startsWith('/admin/marks'),
                        reports: path.startsWith('/admin/report-card'),
                        system: path.startsWith('/admin/system'),
                        classes: path.startsWith('/admin/classes') ||
                                path.startsWith('/admin/class-levels') ||
                                path.startsWith('/admin/streams') ||
                                path.startsWith('/admin/class-categories')
                    };

                    // Merge URL-based state with current state
                    Object.keys(this.dropdowns).forEach(key => {
                        // Only update if URL suggests this should be open
                        if (urlBasedState[key]) {
                            this.dropdowns[key] = true;
                        }
                    });
                }

                this.saveDropdownState();
            },

            toggleDropdown(name, event) {
                if (event) {
                    event.stopPropagation();
                    event.preventDefault();
                }

                // Toggle the clicked dropdown
                this.dropdowns[name] = !this.dropdowns[name];

                // If opening a dropdown, close all others
                if (this.dropdowns[name]) {
                    Object.keys(this.dropdowns).forEach(key => {
                        if (key !== name) {
                            this.dropdowns[key] = false;
                        }
                    });
                }

                // Save state to localStorage
                this.saveDropdownState();
            },

            closeAllDropdowns() {
                // This method is available but won't be called automatically
                Object.keys(this.dropdowns).forEach(key => {
                    this.dropdowns[key] = false;
                });
                this.saveDropdownState();
            },

            saveDropdownState() {
                try {
                    localStorage.setItem('sidebarDropdowns', JSON.stringify(this.dropdowns));
                } catch (e) {
                    console.warn('Could not save dropdown state:', e);
                }
            },

            handleLinkClick(event) {
                // When clicking a link inside dropdown, keep the dropdown open
                // This prevents dropdown from closing when navigating
                if (event) {
                    event.stopPropagation();
                }

                // Find which dropdown this link belongs to
                const linkElement = event?.target?.closest('a') || event?.target;
                if (linkElement) {
                    const href = linkElement.getAttribute('href');
                    if (href) {
                        // Determine which dropdown should stay open
                        if (href.startsWith('/admin/students')) {
                            this.dropdowns.students = true;
                        } else if (href.startsWith('/admin/teachers')) {
                            this.dropdowns.teachers = true;
                        } else if (href.startsWith('/admin/marks')) {
                            this.dropdowns.marks = true;
                        } else if (href.startsWith('/admin/report-card')) {
                            this.dropdowns.reports = true;
                        } else if (href.startsWith('/admin/system')) {
                            this.dropdowns.system = true;
                        } else if (href.startsWith('/admin/classes') ||
                                  href.startsWith('/admin/class-levels') ||
                                  href.startsWith('/admin/streams') ||
                                  href.startsWith('/admin/class-categories')) {
                            this.dropdowns.classes = true;
                        }
                        this.saveDropdownState();
                    }
                }

                // Let SPA router handle navigation
                // Dropdown will stay open due to saved state
            },

            isActive(path) {
                if (!path || path === '#') return false;
                return this.currentPath === path || this.currentPath.startsWith(path + '/');
            },

            isExactActive(path) {
                return this.currentPath === path;
            },

            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;

                try {
                    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                } catch (e) {
                    console.warn('Could not save sidebar state:', e);
                }

                this.applySidebarState();

                // Close all dropdowns when collapsing (optional)
                if (this.sidebarCollapsed) {
                    this.closeAllDropdowns();
                } else {
                    // Restore dropdown states when expanding
                    this.restoreDropdownStates();
                }
            },

            restoreDropdownStates() {
                try {
                    const savedDropdowns = localStorage.getItem('sidebarDropdowns');
                    if (savedDropdowns) {
                        const parsed = JSON.parse(savedDropdowns);
                        Object.keys(this.dropdowns).forEach(key => {
                            if (parsed[key] !== undefined) {
                                this.dropdowns[key] = parsed[key];
                            }
                        });
                    }
                } catch (e) {
                    console.warn('Could not restore dropdown states:', e);
                }
            },

            applySidebarState() {
                const sidebar = document.querySelector('.sidebar');
                const sidebarArea = document.querySelector('.sidebar-area');
                const mainContent = document.querySelector('.main-content-area');
                const toggleIcon = document.querySelector('#sidebarToggle i');

                if (this.sidebarCollapsed) {
                    sidebar?.classList.add('collapsed');
                    sidebarArea?.classList.add('collapsed');
                    mainContent?.classList.add('collapsed');
                    toggleIcon?.classList.remove('fa-chevron-left');
                    toggleIcon?.classList.add('fa-chevron-right');
                } else {
                    sidebar?.classList.remove('collapsed');
                    sidebarArea?.classList.remove('collapsed');
                    mainContent?.classList.remove('collapsed');
                    toggleIcon?.classList.remove('fa-chevron-right');
                    toggleIcon?.classList.add('fa-chevron-left');
                }
            },

            updateNotificationBadge() {
                // This method can be called when notifications are updated
                // It will update any notification badge in the sidebar
                const badges = document.querySelectorAll('.notification-badge');
                if (badges.length > 0) {
                    // You might want to fetch the count here or update from event data
                    // For now, we'll just trigger a badge update
                    badges.forEach(badge => {
                        badge.classList.add('animate-pulse');
                        setTimeout(() => badge.classList.remove('animate-pulse'), 1000);
                    });
                }
            }
        }));
    });

</script>

    <!-- Theme Management -->
    <script>
        // Global theme management
        window.appTheme = {
            current: 'light',

            init() {
                try {
                    this.current = localStorage.getItem('theme') || 'light';
                } catch (e) {
                    console.warn('Could not read theme:', e);
                }
                this.apply();
            },

            toggle() {
                this.current = this.current === 'light' ? 'dark' : 'light';
                this.apply();
                try {
                    localStorage.setItem('theme', this.current);
                } catch (e) {
                    console.warn('Could not save theme:', e);
                }

            },

            apply() {
                document.documentElement.classList.toggle('dark', this.current === 'dark');
            }
        };

        // Initialize theme on load
        window.appTheme.init();
    </script>

    <!-- App Initialization & Helpers -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const syncPageContentToMobile = () => {
                const desktopContent = document.getElementById('page-content');
                const mobileContent = document.getElementById('page-content-mobile');

                if (desktopContent && mobileContent) {
                    mobileContent.innerHTML = desktopContent.innerHTML;
                }
            };

            syncPageContentToMobile();
            window.addEventListener('spa:rendered', syncPageContentToMobile);

        });

        // Global helper functions
        window.showAlert = (msg, type = 'success') => {
            if (window.router && typeof window.router.showAlert === 'function') {
                window.router.showAlert(msg, type);
            } else {

                console.warn('Router not available for showAlert');

                alert(msg);
            }
        };

        window.navigateTo = (path, opts = {}) => {
            if (window.router && typeof window.router.navigate === 'function') {
                window.router.navigate(path, opts);
            } else {

                console.warn('Router not available for navigation');

                window.location.href = path;
            }
        };

        window.refreshPage = () => {
            if (window.router && typeof window.router.refresh === 'function') {
                window.router.refresh();
            } else {
                window.location.reload();
            }
        };

        window.toggleTheme = () => {
            if (window.appTheme && typeof window.appTheme.toggle === 'function') {
                window.appTheme.toggle();
            }

        };

        window.toggleSidebar = function() {
            document.dispatchEvent(new CustomEvent('toggle-sidebar'));
        };



    </script>


    @stack('scripts')
</body>
</html>
