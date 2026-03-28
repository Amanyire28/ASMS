@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Announcements')
    @section('content')
@endif
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Announcements</h1>
    <a href="{{ route('announcements.create') }}"
       class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark text-white rounded-lg transition-colors">
        <i class="fas fa-plus mr-2"></i>
        New Announcement
    </a>
</div>



<!-- Announcements Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    @if($announcements->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Created By
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Valid Until
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($announcements as $announcement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $announcement->title }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ Str::limit($announcement->content, 60) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $announcement->type === 'general' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                       ($announcement->type === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                       ($announcement->type === 'event' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
                                       'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200')) }}">
                                    {{ ucfirst($announcement->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($announcement->is_active && !$announcement->isExpired())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Active
                                    </span>
                                @elseif($announcement->isExpired())
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        Expired
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-maroon text-white flex items-center justify-center text-xs font-semibold mr-3">
                                        {{ strtoupper(substr($announcement->creator->name ?? 'A', 0, 2)) }}
                                    </div>
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $announcement->creator->name ?? 'System' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $announcement->valid_until ? $announcement->valid_until->format('M d, Y') : 'Never' }}
                                @if($announcement->valid_until && $announcement->valid_until->isPast())
                                    <div class="text-xs text-red-500 dark:text-red-400 mt-1">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Expired
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('announcements.show', $announcement) }}"
                                       class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('announcements.edit', $announcement) }}"
                                       class="p-2 text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('announcements.toggle', $announcement) }}"
                                          method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="p-2 {{ $announcement->is_active ? 'text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/30' : 'text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/30' }} rounded-lg transition-colors"
                                                title="{{ $announcement->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $announcement->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('announcements.destroy', $announcement) }}"
                                          method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                                title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this announcement?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $announcements->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <i class="fas fa-bullhorn text-gray-400 dark:text-gray-600 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400 mb-2">No announcements found</h3>
                <p class="text-gray-400 dark:text-gray-500 text-sm mb-6">Create your first announcement to get started.</p>
                <a href="{{ route('announcements.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Create Announcement
                </a>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript for Enhanced Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to table rows
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.05}s`;
        row.classList.add('animate-fadeIn');
    });

    // Status toggle confirmation
    const toggleButtons = document.querySelectorAll('form[action*="toggle"] button');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const form = this.closest('form');
            const action = form.getAttribute('action');
            const isActivating = this.querySelector('i').classList.contains('fa-play');

            if (isActivating) {
                if (!confirm('Are you sure you want to activate this announcement?')) {
                    e.preventDefault();
                }
            } else {
                if (!confirm('Are you sure you want to deactivate this announcement?')) {
                    e.preventDefault();
                }
            }
        });
    });
});

// CSS for animations
const style = document.createElement('style');
style.textContent = `
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out forwards;
        opacity: 0;
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

    .hover\\:scale-105:hover {
        transform: scale(1.05);
    }

    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
`;
document.head.appendChild(style);
</script>

<style>
    /* Custom badge colors for announcement types */
    .badge-general {
        background-color: rgb(219 234 254);
        color: rgb(29 78 216);
    }

    .dark .badge-general {
        background-color: rgb(30 58 138 / 0.3);
        color: rgb(147 197 253);
    }

    .badge-urgent {
        background-color: rgb(254 226 226);
        color: rgb(220 38 38);
    }

    .dark .badge-urgent {
        background-color: rgb(127 29 29 / 0.3);
        color: rgb(252 165 165);
    }

    .badge-event {
        background-color: rgb(233 213 255);
        color: rgb(126 34 206);
    }

    .dark .badge-event {
        background-color: rgb(76 29 149 / 0.3);
        color: rgb(216 180 254);
    }
</style>

@if(!request()->header('HX-Request'))
    @endsection
@endif
