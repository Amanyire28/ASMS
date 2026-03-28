@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'System Management')
    @section('content')
@endif
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">System Management</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @can('system.users')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-maroon bg-opacity-10 dark:bg-opacity-20 mr-4">
                        <i class="fas fa-users text-white dark:text-maroon-light text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">User Management</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Manage users, roles, and permissions</p>
                <a href="/admin/system/users"
                   class="inline-flex items-center px-4 py-2 bg-maroon border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-maroon-dark active:bg-maroon-dark focus:outline-none focus:ring-2 focus:ring-maroon focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Manage Users
                </a>
            </div>
            @endcan

            @can('system.roles')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-maroon bg-opacity-10 dark:bg-opacity-20 mr-4">
                        <i class="fas fa-user-shield text-white dark:text-maroon-light text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Roles & Permissions</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Configure system roles and permissions</p>
                <a href="/admin/system/roles"
                   class="inline-flex items-center px-4 py-2 bg-maroon border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-maroon-dark active:bg-maroon-dark focus:outline-none focus:ring-2 focus:ring-maroon focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Manage Roles
                </a>
            </div>
            @endcan

            @can('system.settings')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-maroon bg-opacity-10 dark:bg-opacity-20 mr-4">
                        <i class="fas fa-cogs text-white dark:text-maroon-light text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">System Settings</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Configure system-wide settings</p>
                <a href="/admin/settings/school-profile"
                   class="inline-flex items-center px-4 py-2 bg-maroon dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                   System M anagement
                </a>
            </div>
            @endcan

        </div>
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif
