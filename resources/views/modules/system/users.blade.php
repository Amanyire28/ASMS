@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'User Management')
    @section('content')
@endif
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <button onclick="window.navigateTo('/admin/system')"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:ring-offset-2 transition ease-in-out duration-150"
                        title="Back to System Management">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back
                </button>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">User Management</h2>
            <div class="flex space-x-2">
                <!-- AJAX Search -->
                <div class="relative">
                    <input type="text"
                           id="searchInput"
                           placeholder="Search users..."
                           class="w-64 px-4 py-2 pl-10 text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-maroon focus:border-transparent"
                           value="{{ request('search') }}">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <!-- Clear button -->
                    <button id="clearSearch"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hidden">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading indicator -->
        <div id="loadingIndicator" class="hidden mb-4">
            <div class="flex items-center justify-center">
                <div class="spinner"></div>
                <span class="ml-2 text-gray-600 dark:text-gray-400">Searching...</span>
            </div>
        </div>

        <!-- Stats Bar (will be updated via AJAX) -->
        <div id="statsBar" class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-4">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">
                        <span id="totalUsers">{{ $users->total() }}</span> user<span id="pluralS">{{ $users->total() !== 1 ? 's' : '' }}</span>
                    </span>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600 dark:text-gray-400">
                            <span class="inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                <span id="activeCount">{{ $users->where('is_active', true)->count() }}</span> active
                            </span>
                        </span>
                        <span class="text-gray-600 dark:text-gray-400">
                            <span class="inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                                <span id="inactiveCount">{{ $users->where('is_active', false)->count() }}</span> inactive
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Table (will be updated via AJAX) -->
        <div id="usersTableContainer">
            @include('modules.system.partials.users-table', ['users' => $users])
        </div>
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif

@push('styles')
<style>
    .spinner {
        border: 2px solid #f3f3f3;
        border-top: 2px solid #800000;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    #searchInput:focus + div i {
        color: #800000;
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const usersTableContainer = document.getElementById('usersTableContainer');
    const statsBar = document.getElementById('statsBar');

    let searchTimeout;
    let currentSearch = '{{ request('search', '') }}';

    // Show/hide clear button based on input
    function toggleClearButton() {
        if (searchInput.value.trim() !== '') {
            clearSearchBtn.classList.remove('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
        }
    }

    // Initialize clear button state
    toggleClearButton();

    // Clear search
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        toggleClearButton();
        performSearch('');
    });

    // Perform AJAX search
    function performSearch(searchTerm) {
        if (searchTerm === currentSearch) return;

        currentSearch = searchTerm;

        // Show loading
        loadingIndicator.classList.remove('hidden');
        usersTableContainer.classList.add('opacity-50');

        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Set new timeout for debouncing
        searchTimeout = setTimeout(async () => {
            try {
                // Update URL without page reload
                const url = new URL(window.location);
                if (searchTerm) {
                    url.searchParams.set('search', searchTerm);
                } else {
                    url.searchParams.delete('search');
                }
                window.history.pushState({}, '', url);

                // Fetch new data
                const response = await fetch(`/admin/system/users?search=${encodeURIComponent(searchTerm)}&ajax=1`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();

                // Update table
                usersTableContainer.innerHTML = data.table;
                usersTableContainer.classList.add('fade-in');

                // Update stats
                document.getElementById('totalUsers').textContent = data.total;
                document.getElementById('pluralS').textContent = data.total !== 1 ? 's' : '';
                document.getElementById('activeCount').textContent = data.activeCount;
                document.getElementById('inactiveCount').textContent = data.inactiveCount;

                // Reinitialize any event listeners in the new table
                initializeTableEvents();

            } catch (error) {
                console.error('Search error:', error);
                if (window.showAlert) {
                    window.showAlert('Search failed. Please try again.', 'error');
                }
            } finally {
                // Hide loading
                loadingIndicator.classList.add('hidden');
                usersTableContainer.classList.remove('opacity-50');
                setTimeout(() => {
                    usersTableContainer.classList.remove('fade-in');
                }, 300);
            }
        }, 500); // 500ms debounce delay
    }

    // Search input event
    searchInput.addEventListener('input', function() {
        toggleClearButton();
        performSearch(this.value.trim());
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search') || '';
        searchInput.value = searchParam;
        toggleClearButton();
        performSearch(searchParam);
    });

    // Initialize table event listeners
    function initializeTableEvents() {
        // Pagination links
        document.querySelectorAll('#usersTableContainer a[href*="page"]').forEach(link => {
            link.addEventListener('click', async function(e) {
                e.preventDefault();
                await handlePagination(this.getAttribute('href'));
            });
        });

        // Permission buttons
        document.querySelectorAll('#usersTableContainer button[onclick*="navigateTo"]').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('onclick').match(/navigateTo\('([^']+)'/)[1];
                if (window.navigateTo) {
                    window.navigateTo(url);
                }
            });
        });
    }

    // Handle pagination via AJAX
    async function handlePagination(url) {
        try {
            loadingIndicator.classList.remove('hidden');
            usersTableContainer.classList.add('opacity-50');

            const response = await fetch(`${url}&ajax=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();

            // Update table
            usersTableContainer.innerHTML = data.table;
            usersTableContainer.classList.add('fade-in');

            // Update stats
            document.getElementById('totalUsers').textContent = data.total;
            document.getElementById('pluralS').textContent = data.total !== 1 ? 's' : '';
            document.getElementById('activeCount').textContent = data.activeCount;
            document.getElementById('inactiveCount').textContent = data.inactiveCount;

            // Update URL
            window.history.pushState({}, '', url.replace('&ajax=1', ''));

            // Reinitialize events
            initializeTableEvents();

        } catch (error) {
            console.error('Pagination error:', error);
            if (window.showAlert) {
                window.showAlert('Failed to load page. Please try again.', 'error');
            }
        } finally {
            loadingIndicator.classList.add('hidden');
            usersTableContainer.classList.remove('opacity-50');
            setTimeout(() => {
                usersTableContainer.classList.remove('fade-in');
            }, 300);
        }
    }

    // Initial setup
    initializeTableEvents();
});
</script>
@endpush
