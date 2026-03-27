@extends('layouts.app')

@section('title', 'Marks')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Marks</h1>
            <p class="text-sm text-gray-500 mt-1">View and manage all recorded marks.</p>
        </div>
        @can('marks.entry')
        <a href="{{ route('marks.entry.form') }}"
           class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark
                  text-white rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-pen mr-2"></i> Enter Marks
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('marks.index') }}"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Class</label>
                <select name="class_id"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                        {{ $cls->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subject</label>
                <select name="subject_id"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $sub)
                    <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>
                        {{ $sub->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Term</label>
                <select name="term"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Terms</option>
                    @foreach($terms as $t)
                    <option value="{{ $t }}" {{ request('term') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Academic Year</label>
                <select name="academic_year"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Years</option>
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('academic_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 py-2 bg-maroon hover:bg-maroon-dark text-white rounded-lg text-sm transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('marks.index') }}"
                   class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm
                          transition-colors flex items-center justify-center" title="Clear filters">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Term / Year</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Remarks</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($marks as $mark)
                    @php
                        $gradeColors = [
                            'A+' => ['bg-green-100',  'text-green-700'],
                            'A'  => ['bg-green-100',  'text-green-700'],
                            'B+' => ['bg-blue-100',   'text-blue-700'],
                            'B'  => ['bg-blue-100',   'text-blue-700'],
                            'C+' => ['bg-yellow-100', 'text-yellow-700'],
                            'C'  => ['bg-yellow-100', 'text-yellow-700'],
                            'D'  => ['bg-orange-100', 'text-orange-700'],
                            'F'  => ['bg-red-100',    'text-red-700'],
                        ];
                        [$bgColor, $textColor] = $gradeColors[$mark->grade] ?? ['bg-gray-100', 'text-gray-600'];
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-maroon text-white text-xs flex items-center
                                            justify-center font-semibold flex-shrink-0">
                                    {{ strtoupper(
                                        substr($mark->student->first_name ?? '?', 0, 1) .
                                        substr($mark->student->last_name  ?? '',  0, 1)
                                    ) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ trim(($mark->student->first_name ?? '') . ' ' . ($mark->student->last_name ?? '')) ?: 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $mark->student->student_id ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $mark->class->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $mark->subject->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $mark->term }}<br>
                            <span class="text-xs">{{ $mark->academic_year }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $mark->marks_obtained }}
                            </span>
                            <span class="text-xs text-gray-400">/ {{ $mark->total_marks }}</span>
                            <div class="text-xs text-gray-400">{{ $mark->percentage }}%</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $bgColor }} {{ $textColor }}">
                                {{ $mark->grade ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">
                            {{ $mark->remarks ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-1">
                                @can('marks.edit')
                                <a href="{{ route('marks.edit', $mark) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Edit">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>
                                @endcan
                                @can('marks.delete')
                                <form method="POST" action="{{ route('marks.destroy', $mark) }}"
                                      onsubmit="return confirm('Delete this mark record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-14 text-center text-gray-400">
                            <i class="fas fa-clipboard-list text-4xl mb-3 block"></i>
                            <p class="text-sm">No marks found matching your filters.</p>
                            @can('marks.entry')
                            <a href="{{ route('marks.entry.form') }}" class="text-maroon hover:underline text-sm mt-1 inline-block">
                                Enter marks to get started
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($marks->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $marks->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
