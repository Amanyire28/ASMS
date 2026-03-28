@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Edit User Permissions')
    @section('content')
@endif
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit User Permissions</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $user->name }} ({{ $user->email }})</p>
            </div>
            <button onclick="window.navigateTo('/admin/system/users')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </button>
        </div>

        <!-- Message Container -->
        <div id="messageContainer" class="mb-6"></div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-2xl">
                <div class="spinner"></div>
                <p class="mt-3 text-gray-700 dark:text-gray-300">Saving permissions...</p>
            </div>
        </div>

        <form id="permissionsForm" method="POST" action="{{ route('system.update-user-permissions', $user) }}">
            @csrf

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Staff ID</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $user->staff_id }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Assign Roles</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($roles as $role)
                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="role_{{ $role->id }}"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   {{ in_array($role->id, $userRoles) ? 'checked' : '' }}
                                   class="h-4 w-4 text-maroon border-gray-300 rounded focus:ring-maroon focus:ring-offset-0">
                            <label for="role_{{ $role->id }}"
                                   class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ ucfirst($role->name) }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Direct Permissions</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Note: Direct permissions override role permissions. Use with caution.
                    </p>

                    @foreach($permissions as $module => $modulePermissions)
                    <div class="mb-8 last:mb-0">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3 capitalize">{{ str_replace('-', ' ', $module) }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($modulePermissions as $permission)
                            <div class="flex items-center">
                                <input type="checkbox"
                                       id="permission_{{ $permission->id }}"
                                       name="permissions[]"
                                       value="{{ $permission->id }}"
                                       {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}
                                       class="h-4 w-4 text-maroon border-gray-300 rounded focus:ring-maroon focus:ring-offset-0">
                                <label for="permission_{{ $permission->id }}"
                                       class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ ucwords(str_replace('.', ' ', str_replace($module . '.', '', $permission->name))) }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex justify-end space-x-3">
                        <button type="button"
                                onclick="window.navigateTo('/admin/system/users')"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Cancel
                        </button>
                        <button type="submit"
                                id="submitBtn"
                                class="inline-flex items-center px-4 py-2 bg-maroon border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-maroon-dark active:bg-maroon-dark focus:outline-none focus:ring-2 focus:ring-maroon focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i>
                            <span id="submitText">Save Permissions</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif

@push('styles')
<style>
    .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #800000;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .message {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('permissionsForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const messageContainer = document.getElementById('messageContainer');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Function to show messages
    function showMessage(message, type = 'success') {
        const alertClass = type === 'success'
            ? 'bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300'
            : 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300';

        const iconClass = type === 'success'
            ? 'fa-check-circle text-green-500 dark:text-green-400'
            : 'fa-exclamation-circle text-red-500 dark:text-red-400';

        messageContainer.innerHTML = `
            <div class="${alertClass} p-4 rounded-lg mb-4 message">
                <div class="flex items-center">
                    <i class="fas ${iconClass}"></i>
                    <p class="ml-3">${message}</p>
                </div>
            </div>
        `;

        // Auto-remove message after 5 seconds
        setTimeout(() => {
            const messageEl = messageContainer.querySelector('.message');
            if (messageEl) {
                messageEl.style.opacity = '0';
                messageEl.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    messageEl.remove();
                }, 300);
            }
        }, 5000);
    }

    // Handle form submission
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const originalText = submitText.textContent;

            // Show loading overlay
            loadingOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');

                    // Auto-redirect after 2 seconds
                    setTimeout(() => {
                        window.navigateTo('/admin/system/users');
                    }, 2000);
                } else {
                    showMessage(data.message || 'Failed to save permissions', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Network error. Please try again.', 'error');
            } finally {
                // Hide loading overlay
                loadingOverlay.classList.add('hidden');
                document.body.style.overflow = 'auto';

                // Reset button state
                submitBtn.disabled = false;
                submitText.textContent = originalText;
            }
        });
    }

    // Optional: Add bulk selection helpers
    const allRoleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const allPermissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

    // Add select all/none for each module
    document.querySelectorAll('.permission-module').forEach(module => {
        const header = module.querySelector('h4');
        const checkboxes = module.querySelectorAll('input[type="checkbox"]');

        if (header && checkboxes.length > 0) {
            // Add select all checkbox
            const selectAllId = 'select_all_' + Math.random().toString(36).substr(2, 9);
            header.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${header.textContent}</span>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="${selectAllId}" class="h-4 w-4 text-maroon">
                        <label for="${selectAllId}" class="text-xs text-gray-500">All</label>
                    </div>
                </div>
            `;

            // Add event listener for select all
            const selectAllCheckbox = document.getElementById(selectAllId);
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                        cb.dispatchEvent(new Event('change'));
                    });
                });

                // Update select all when individual checkboxes change
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function() {
                        const allChecked = Array.from(checkboxes).every(c => c.checked);
                        const someChecked = Array.from(checkboxes).some(c => c.checked);
                        selectAllCheckbox.checked = allChecked;
                        selectAllCheckbox.indeterminate = someChecked && !allChecked;
                    });
                });
            }
        }
    });

    // Add keyboard shortcut (Ctrl + S)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (form) {
                form.requestSubmit();
            }
        }
    });

    // Add change logging (optional)
    allPermissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('Permission changed:', this.value, this.checked);
        });
    });
});
</script>
@endpush
