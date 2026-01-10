@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Announcement</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Create a new announcement</p>
        </div>
        <a href="{{ route('announcements.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Announcements
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">New Announcement</h2>
        </div>

        <form action="{{ route('announcements.store') }}" method="POST">
            @csrf

            <div class="p-6 space-y-6">
                @if($errors->any())
                    <div class="p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mr-3"></i>
                            <p class="text-red-800 dark:text-red-300">Please fix the errors below</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required
                                   placeholder="Enter announcement title"
                                   class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border {{ $errors->has('title') ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:ring-2 focus:ring-maroon/20 focus:border-maroon dark:focus:ring-maroon/40 dark:focus:border-maroon dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-colors">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea id="content"
                                      name="content"
                                      rows="8"
                                      required
                                      placeholder="Enter announcement content"
                                      class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border {{ $errors->has('content') ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:ring-2 focus:ring-maroon/20 focus:border-maroon dark:focus:ring-maroon/40 dark:focus:border-maroon dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-colors">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select id="type"
                                    name="type"
                                    required
                                    class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border {{ $errors->has('type') ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:ring-2 focus:ring-maroon/20 focus:border-maroon dark:focus:ring-maroon/40 dark:focus:border-maroon dark:text-white transition-colors">
                                <option value="">Select Type</option>
                                <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                                <option value="academic" {{ old('type') == 'academic' ? 'selected' : '' }}>Academic</option>
                                <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>Event</option>
                                <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                @foreach(['general', 'academic', 'event', 'urgent'] as $typeOption)
                                    <button type="button"
                                            onclick="document.getElementById('type').value = '{{ $typeOption }}'; updatePreview();"
                                            class="text-xs px-3 py-2 rounded-lg border {{ old('type') == $typeOption ? 'border-maroon bg-maroon/10' : 'border-gray-200 dark:border-gray-600 hover:border-maroon' }} transition-colors">
                                        {{ ucfirst($typeOption) }}
                                    </button>
                                @endforeach
                            </div>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Valid Until -->
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Valid Until
                            </label>
                            <input type="date"
                                   id="valid_until"
                                   name="valid_until"
                                   value="{{ old('valid_until') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border {{ $errors->has('valid_until') ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:ring-2 focus:ring-maroon/20 focus:border-maroon dark:focus:ring-maroon/40 dark:focus:border-maroon dark:text-white transition-colors">
                            <div class="mt-2 flex gap-2">
                                <button type="button"
                                        onclick="setDateOffset(7)"
                                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                    7 days
                                </button>
                                <button type="button"
                                        onclick="setDateOffset(30)"
                                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                    30 days
                                </button>
                                <button type="button"
                                        onclick="document.getElementById('valid_until').value = ''; updatePreview();"
                                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                    Clear
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Leave empty for no expiration</p>
                            @error('valid_until')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Preview</h4>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span id="type-preview" class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        General
                                    </span>
                                    <span id="status-preview" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Active
                                    </span>
                                </div>
                                <p id="date-preview" class="text-xs text-gray-500 dark:text-gray-400">
                                    Valid until: No expiration
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-end gap-3">
                    <a href="{{ route('announcements.index') }}"
                       class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-maroon hover:bg-maroon-dark text-white rounded-lg font-medium transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Create Announcement
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update preview based on form changes
    const typeSelect = document.getElementById('type');
    const dateInput = document.getElementById('valid_until');
    const titleInput = document.getElementById('title');

    const typePreview = document.getElementById('type-preview');
    const datePreview = document.getElementById('date-preview');

    function updatePreview() {
        // Update type preview
        const type = typeSelect.value || 'general';
        const typeClasses = {
            'general': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'academic': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'event': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'urgent': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        };

        typePreview.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        typePreview.className = `px-2 py-1 text-xs font-semibold rounded-full ${typeClasses[type] || typeClasses.general}`;

        // Update date preview
        if (dateInput.value) {
            const date = new Date(dateInput.value);
            const formatted = date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
            datePreview.textContent = `Valid until: ${formatted}`;
        } else {
            datePreview.textContent = 'Valid until: No expiration';
        }
    }

    // Set date offset helper
    window.setDateOffset = function(days) {
        const date = new Date();
        date.setDate(date.getDate() + days);
        const formatted = date.toISOString().split('T')[0];
        dateInput.value = formatted;
        updatePreview();
    };

    // Add event listeners
    typeSelect.addEventListener('change', updatePreview);
    dateInput.addEventListener('change', updatePreview);
    titleInput.addEventListener('input', updatePreview);

    // Initial preview update
    updatePreview();
});
</script>

<style>
    /* Custom focus styles */
    input:focus, textarea:focus, select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
    }

    .dark input:focus, .dark textarea:focus, .dark select:focus {
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.2);
    }

    /* Smooth transitions */
    input, textarea, select, button {
        transition: all 0.2s ease-in-out;
    }

    /* Animation for new announcement form */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .bg-white, .bg-gray-800 {
        animation: slideIn 0.3s ease-out;
    }
</style>
@endsection
