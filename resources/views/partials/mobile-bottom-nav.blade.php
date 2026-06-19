<div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-40 lg:hidden" id="mobile-bottom-nav">
    <div class="flex justify-around items-center py-2">
        <a href="{{ route('dashboard') }}"
           class="nav-item flex flex-col items-center p-2 transition-colors {{ request()->routeIs('dashboard') ? 'text-maroon' : 'text-gray-500 dark:text-gray-400' }}"
           data-route="dashboard">
            <i class="fas fa-tachometer-alt text-lg"></i>
            <span class="text-xs mt-1">Home</span>
        </a>
        @canany(['students.view', 'students.view-detail'])
        <a href="{{ route('students.index') }}"
           class="nav-item flex flex-col items-center p-2 transition-colors {{ request()->routeIs('students.*') ? 'text-maroon' : 'text-gray-500 dark:text-gray-400' }}"
           data-route="students">
            <i class="fas fa-users text-lg"></i>
            <span class="text-xs mt-1">Students</span>
        </a>
        @endcanany
        @can('marks.entry')
        <a href="{{ route('marks.entry.form') }}"
           class="nav-item flex flex-col items-center p-2 transition-colors {{ request()->routeIs('marks.entry*') ? 'text-maroon' : 'text-gray-500 dark:text-gray-400' }}"
           data-route="marks">
            <i class="fas fa-edit text-lg"></i>
            <span class="text-xs mt-1">Marks</span>
        </a>
        @endcan
        @canany(['teachers.view', 'teachers.view-detail'])
        <a href="{{ route('teachers.index') }}"
           class="nav-item flex flex-col items-center p-2 transition-colors {{ request()->routeIs('teachers.*') ? 'text-maroon' : 'text-gray-500 dark:text-gray-400' }}"
           data-route="teachers">
            <i class="fas fa-chalkboard text-lg"></i>
            <span class="text-xs mt-1">Teachers</span>
        </a>
        @endcanany
        <button onclick="openMobileSidebar()" class="flex flex-col items-center p-2 text-gray-500 dark:text-gray-400 hover:text-maroon transition-colors">
            <i class="fas fa-bars text-lg"></i>
            <span class="text-xs mt-1">Menu</span>
        </button>
    </div>
</div>

<script>
// Update active state on navigation
document.addEventListener('DOMContentLoaded', function() {
    updateBottomNavActiveState();

    // Listen for SPA navigation events
    window.addEventListener('spa:navigated', function(e) {
        updateBottomNavActiveState();
    });
});

function updateBottomNavActiveState() {
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('#mobile-bottom-nav .nav-item');

    navItems.forEach(item => {
        const route = item.getAttribute('data-route');
        const href = item.getAttribute('href');
        let isActive = false;

        // Check if current path matches this nav item
        if (route === 'dashboard') {
            isActive = currentPath === href || currentPath === '/dashboard';
        } else if (route === 'students') {
            isActive = currentPath.includes('/students');
        } else if (route === 'marks') {
            isActive = currentPath.includes('/marks');
        } else if (route === 'teachers') {
            isActive = currentPath.includes('/teachers');
        }

        // Update classes
        if (isActive) {
            item.classList.remove('text-gray-500', 'dark:text-gray-400');
            item.classList.add('text-maroon');
        } else {
            item.classList.remove('text-maroon');
            item.classList.add('text-gray-500', 'dark:text-gray-400');
        }
    });
}
</script>

<style>
/* Add smooth active state transitions */
#mobile-bottom-nav .nav-item {
    position: relative;
}

#mobile-bottom-nav .nav-item.text-maroon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 3px;
    background-color: #800020;
    border-radius: 0 0 3px 3px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        height: 0;
        opacity: 0;
    }
    to {
        height: 3px;
        opacity: 1;
    }
}

/* Ensure bottom nav doesn't interfere with content */
body {
    padding-bottom: 60px;
}

@media (min-width: 1024px) {
    body {
        padding-bottom: 0;
    }
}
</style>
