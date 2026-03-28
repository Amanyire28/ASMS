@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Roles & Permissions')
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
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Roles & Permissions</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Roles List -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">System Roles</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($roles as $role)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">
                                        {{ ucfirst($role->name) }}
                                        @if($role->name === 'super-admin')
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300">
                                            <i class="fas fa-crown mr-1"></i> Super Admin
                                        </span>
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                        Created: {{ $role->created_at->format('M d, Y') }}
                                    </p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions as $permission)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                            {{ $permission->name }}
                                        </span>
                                        @endforeach
                                        @if($role->permissions->isEmpty())
                                        <span class="text-sm text-gray-500 dark:text-gray-400 italic">No permissions assigned</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="text-maroon dark:text-maroon-light hover:text-maroon-dark dark:hover:text-maroon-light transition-colors duration-200">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Permissions Overview -->
            <div>
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Available Permissions</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($permissions as $module => $modulePermissions)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2 capitalize">
                                    {{ str_replace('-', ' ', $module) }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        ({{ count($modulePermissions) }})
                                    </span>
                                </h4>
                                <div class="space-y-1">
                                    @foreach($modulePermissions as $permission)
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        • {{ ucwords(str_replace('.', ' ', str_replace($module . '.', '', $permission->name))) }}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="mt-6 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">System Stats</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Users</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ \App\Models\User::count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Roles</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ count($roles) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Permissions</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ \Spatie\Permission\Models\Permission::count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif
