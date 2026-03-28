{{-- resources/views/modules/students/index.blade.php --}}
@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Students')
    @section('content')
@endif
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Students</h1>
    <a href="{{ route('students.create') }}"
       hx-get="{{ route('students.create') }}"
       hx-target="#page-content"
       hx-push-url="true"
       hx-indicator="#loading-indicator"
       class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark text-white rounded-lg transition-colors">
        <i class="fas fa-plus mr-2"></i>
        Add Student
    </a>
</div>

<!-- Students Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Student ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Class
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($students as $student)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $student->student_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($student->photo)
                            <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->full_name }}" class="h-8 w-8 rounded-full mr-3 object-cover">
                            @else
                            <div class="h-8 w-8 rounded-full bg-maroon text-white flex items-center justify-center mr-3 text-xs font-semibold">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </div>
                            @endif
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $student->full_name }}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $student->class->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $student->email ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($student->is_active)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Active
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            Inactive
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('students.show', $student) }}"
                               hx-get="{{ route('students.show', $student) }}"
                               hx-target="#page-content"
                               hx-push-url="true"
                               hx-indicator="#loading-indicator"
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                               title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('students.edit', $student) }}"
                               hx-get="{{ route('students.edit', $student) }}"
                               hx-target="#page-content"
                               hx-push-url="true"
                               hx-indicator="#loading-indicator"
                               class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                        title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this student?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-lg mb-2">No students found.</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mb-4">Get started by adding your first student to the system.</p>
                            <a href="{{ route('students.create') }}"
                               hx-get="{{ route('students.create') }}"
                               hx-target="#page-content"
                               hx-push-url="true"
                               hx-indicator="#loading-indicator"
                               class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark text-white rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add Your First Student
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($students->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $students->links() }}
    </div>
    @endif
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif
