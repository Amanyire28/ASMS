{{-- resources/views/modules/students/show.blade.php --}}
@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', $student->full_name)
    @section('content')
@endif

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $student->full_name }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Student ID: <span class="font-mono font-semibold">{{ $student->student_id }}</span></p>
    </div>
    <div class="flex items-center gap-3">
        @can('students.edit')
        <a href="{{ route('students.edit', $student) }}"
           hx-get="{{ route('students.edit', $student) }}"
           hx-target="#page-content"
           hx-push-url="true"
           class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark text-white text-sm rounded-lg transition-colors">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        @endcan
        <a href="{{ route('students.index') }}"
           hx-get="{{ route('students.index') }}"
           hx-target="#page-content"
           hx-push-url="true"
           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left column: photo + status -->
    <div class="lg:col-span-1 space-y-4">
        <!-- Profile card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 flex flex-col items-center text-center">
            @if($student->photo)
                <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->full_name }}"
                     class="w-28 h-28 rounded-full object-cover mb-4 ring-4 ring-maroon/20">
            @else
                <div class="w-28 h-28 rounded-full bg-maroon text-white flex items-center justify-center mb-4 text-3xl font-bold ring-4 ring-maroon/20">
                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                </div>
            @endif
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $student->full_name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono mt-1">{{ $student->student_id }}</p>
            <span class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                {{ $student->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $student->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                {{ $student->is_active ? 'Active' : 'Inactive' }}
            </span>
            @if($student->class)
            <div class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                <i class="fas fa-school mr-1 text-maroon"></i>
                {{ $student->class->name ?? $student->class->full_name ?? 'N/A' }}
            </div>
            @endif
        </div>

        <!-- Parent / Guardian -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-4 flex items-center">
                <i class="fas fa-users mr-2 text-maroon"></i>Parent / Guardian
            </h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="text-gray-900 dark:text-white font-medium">{{ $student->parent_name ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="text-gray-900 dark:text-white font-medium">{{ $student->parent_phone ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="text-gray-900 dark:text-white font-medium break-all">{{ $student->parent_email ?: '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Right column: details + marks -->
    <div class="lg:col-span-2 space-y-4">

        <!-- Personal Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-maroon"></i>Personal Information
            </h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Date of Birth</dt>
                    <dd class="text-gray-900 dark:text-white font-medium">
                        {{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Gender</dt>
                    <dd class="text-gray-900 dark:text-white font-medium capitalize">{{ $student->gender ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="text-gray-900 dark:text-white font-medium break-all">{{ $student->email ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="text-gray-900 dark:text-white font-medium">{{ $student->phone ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Address</dt>
                    <dd class="text-gray-900 dark:text-white font-medium">{{ $student->address ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Enrollment Date</dt>
                    <dd class="text-gray-900 dark:text-white font-medium">
                        {{ $student->enrollment_date ? $student->enrollment_date->format('d M Y') : '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Class</dt>
                    <dd class="text-gray-900 dark:text-white font-medium">
                        {{ $student->class ? ($student->class->name ?? $student->class->full_name ?? 'N/A') : '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Academic Marks -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-maroon"></i>Academic Records
            </h3>
            @if($student->marks->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No marks recorded yet.</p>
            @else
                @php
                    $marksByTerm = $student->marks->groupBy(fn($m) => $m->academic_year . ' — Term ' . $m->term);
                @endphp
                @foreach($marksByTerm as $termLabel => $termMarks)
                <div class="mb-5 last:mb-0">
                    <p class="text-xs font-semibold text-maroon uppercase tracking-wider mb-2">{{ $termLabel }}</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-2 pr-4 text-gray-500 dark:text-gray-400 font-medium">Subject</th>
                                    <th class="text-center py-2 px-2 text-gray-500 dark:text-gray-400 font-medium">Obtained</th>
                                    <th class="text-center py-2 px-2 text-gray-500 dark:text-gray-400 font-medium">Out Of</th>
                                    <th class="text-center py-2 px-2 text-gray-500 dark:text-gray-400 font-medium">%</th>
                                    <th class="text-center py-2 pl-2 text-gray-500 dark:text-gray-400 font-medium">Grade</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($termMarks as $mark)
                                @php
                                    $pct = $mark->total_marks > 0 ? round(($mark->marks_obtained / $mark->total_marks) * 100) : 0;
                                    $grade = $pct >= 80 ? 'A' : ($pct >= 65 ? 'B' : ($pct >= 50 ? 'C' : ($pct >= 40 ? 'D' : 'F')));
                                    $gradeColor = match($grade) {
                                        'A' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                        'B' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                        'C' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                        'D' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                        default => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                    };
                                @endphp
                                <tr>
                                    <td class="py-2 pr-4 text-gray-900 dark:text-white">{{ $mark->subject->name ?? '—' }}</td>
                                    <td class="py-2 px-2 text-center text-gray-700 dark:text-gray-300">{{ $mark->marks_obtained }}</td>
                                    <td class="py-2 px-2 text-center text-gray-500 dark:text-gray-400">{{ $mark->total_marks }}</td>
                                    <td class="py-2 px-2 text-center text-gray-700 dark:text-gray-300">{{ $pct }}%</td>
                                    <td class="py-2 pl-2 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $gradeColor }}">{{ $grade }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @php
                                $totalObtained = $termMarks->sum('marks_obtained');
                                $totalPossible = $termMarks->sum('total_marks');
                                $avgPct = $totalPossible > 0 ? round(($totalObtained / $totalPossible) * 100) : 0;
                                $finalGrade = $avgPct >= 80 ? 'A' : ($avgPct >= 65 ? 'B' : ($avgPct >= 50 ? 'C' : ($avgPct >= 40 ? 'D' : 'F')));
                            @endphp
                            <tfoot>
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-semibold">
                                    <td class="py-2 pr-4 text-gray-900 dark:text-white">Total</td>
                                    <td class="py-2 px-2 text-center text-gray-900 dark:text-white">{{ $totalObtained }}</td>
                                    <td class="py-2 px-2 text-center text-gray-900 dark:text-white">{{ $totalPossible }}</td>
                                    <td class="py-2 px-2 text-center text-gray-900 dark:text-white">{{ $avgPct }}%</td>
                                    <td class="py-2 pl-2 text-center">
                                        @php
                                            $finalColor = match($finalGrade) {
                                                'A' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                                'B' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                                'C' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                                'D' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                                default => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $finalColor }}">{{ $finalGrade }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif
