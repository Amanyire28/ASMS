@extends('layouts.app')

@section('title', 'Marks')

@section('content')
<div class="px-4 py-6 max-w-full mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Marks</h1>
            <p class="text-sm text-gray-500 mt-1">Select a class, term and year to view the full mark sheet.</p>
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

    {{-- Filter bar: class + term + year (subject becomes columns in the grid) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('marks.index') }}"
              class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 items-end">

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Class</label>
                <select name="class_id" required
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">â€” Select Class â€”</option>
                    @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                        {{ $cls->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Term</label>
                <select name="term" required
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">â€” Select Term â€”</option>
                    @foreach($terms as $t)
                    <option value="{{ $t }}" {{ request('term') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Academic Year</label>
                <select name="academic_year" required
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">â€” Select Year â€”</option>
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('academic_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 py-2 bg-maroon hover:bg-maroon-dark text-white rounded-lg text-sm transition-colors">
                    <i class="fas fa-table mr-1"></i> View Sheet
                </button>
                <a href="{{ route('marks.index') }}"
                   class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm
                          transition-colors flex items-center justify-center" title="Clear filters">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    @isset($students)
    {{-- â”€â”€ Mark Sheet Grid â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    @php
        $gradeScale = function(float $pct): string {
            if ($pct >= 90) return 'A+';
            if ($pct >= 80) return 'A';
            if ($pct >= 70) return 'B+';
            if ($pct >= 60) return 'B';
            if ($pct >= 50) return 'C+';
            if ($pct >= 40) return 'C';
            if ($pct >= 30) return 'D';
            return 'F';
        };
        $gradeColor = function(string $g): string {
            return match($g) {
                'A+','A'  => 'bg-green-100 text-green-700',
                'B+','B'  => 'bg-blue-100 text-blue-700',
                'C+','C'  => 'bg-yellow-100 text-yellow-700',
                'D'       => 'bg-orange-100 text-orange-700',
                default   => 'bg-red-100 text-red-700',
            };
        };
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center flex-wrap gap-1">
                    <span class="w-7 h-7 rounded-full bg-maroon text-white text-xs flex items-center justify-center font-bold flex-shrink-0">2</span>
                    Mark Sheet &mdash;
                    <span class="font-normal text-gray-600 dark:text-gray-400">
                        {{ $class->name }} &middot; {{ $selection['term'] }} &middot; {{ $selection['academic_year'] }}
                    </span>
                </h2>
                <p class="text-sm text-gray-500 mt-1 ml-9">
                    {{ $students->count() }} student(s) &middot; {{ $classSubjects->count() }} subject(s).
                    Final grade is based on the average across all subjects.
                </p>
            </div>
        </div>

        @if($classSubjects->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-book-open text-4xl mb-3 block"></i>
            <p class="mb-2">No subjects are assigned to this class yet.</p>
            <a href="{{ route('classes.show', $class->id) }}"
               class="text-maroon hover:underline text-sm">Assign subjects to this class &rarr;</a>
        </div>
        @elseif($students->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-user-slash text-4xl mb-3 block"></i>
            <p>No active students in this class.</p>
        </div>
        @else
        <div class="rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full table-fixed border-separate border-spacing-0 text-sm">
                <colgroup>
                    <col class="w-8">
                    <col class="w-36">
                    @foreach($classSubjects as $s)<col>@endforeach
                    <col class="w-24">
                    <col class="w-16">
                    <col class="w-16">
                </colgroup>
                <thead>
                    <tr>
                        <th class="sticky left-0 z-20 bg-gray-50 dark:bg-gray-900 py-3 px-2
                                   text-center text-xs font-semibold text-gray-500 uppercase
                                   border-b-2 border-r border-gray-200 dark:border-gray-700">#</th>
                        <th class="sticky left-8 z-20 bg-gray-50 dark:bg-gray-900 py-3 px-3
                                   text-left text-xs font-semibold text-gray-500 uppercase
                                   border-b-2 border-r border-gray-200 dark:border-gray-700">Student</th>
                        @foreach($classSubjects as $subject)
                        @php
                            $colTotal = null;
                            foreach ($students as $st) {
                                $m = $marksGrid[$st->id][$subject->id] ?? null;
                                if ($m) { $colTotal = $m->total_marks; break; }
                            }
                        @endphp
                        <th class="bg-gray-50 dark:bg-gray-900 py-2 px-1 text-center
                                   border-b-2 border-r border-gray-200 dark:border-gray-700">
                            <div class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate leading-tight"
                                 title="{{ $subject->name }}">{{ $subject->name }}</div>
                            @if($subject->code)
                            <div class="text-xs text-gray-400 font-normal truncate">{{ $subject->code }}</div>
                            @endif
                            @if($colTotal !== null)
                            <div class="text-xs text-gray-400 font-normal">/ {{ $colTotal }}</div>
                            @endif
                        </th>
                        @endforeach
                        <th class="bg-gray-50 dark:bg-gray-900 py-3 px-2 text-center text-xs font-semibold
                                   text-gray-500 uppercase border-b-2 border-r border-gray-200 dark:border-gray-700">
                            Total
                        </th>
                        <th class="bg-gray-50 dark:bg-gray-900 py-3 px-2 text-center text-xs font-semibold
                                   text-gray-500 uppercase border-b-2 border-r border-gray-200 dark:border-gray-700">
                            Avg&nbsp;%
                        </th>
                        <th class="bg-gray-50 dark:bg-gray-900 py-3 px-2 text-center text-xs font-semibold
                                   text-gray-500 uppercase border-b-2 border-gray-200 dark:border-gray-700">
                            Grade
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $student)
                    @php
                        $rowObt   = 0;
                        $rowTotal = 0;
                        $rowCount = 0;
                        foreach ($classSubjects as $s) {
                            $m = $marksGrid[$student->id][$s->id] ?? null;
                            if ($m) {
                                $rowObt   += (float) $m->marks_obtained;
                                $rowTotal += (float) $m->total_marks;
                                $rowCount++;
                            }
                        }
                        $rowAvg   = ($rowCount > 0 && $rowTotal > 0) ? round(($rowObt / $rowTotal) * 100, 1) : null;
                        $rowGrade = $rowAvg !== null ? $gradeScale($rowAvg) : null;
                        $oddRow   = $i % 2 !== 0;
                        $rowBg    = $oddRow ? 'bg-gray-50/50 dark:bg-gray-800/60' : 'bg-white dark:bg-gray-800';
                        $stickyBg = $oddRow ? 'bg-gray-50 dark:bg-gray-800'       : 'bg-white dark:bg-gray-800';
                    @endphp
                    <tr class="{{ $rowBg }} hover:bg-blue-50/40 dark:hover:bg-gray-750 transition-colors">
                        {{-- # --}}
                        <td class="sticky left-0 z-10 {{ $stickyBg }} py-3 px-2 text-center text-xs text-gray-400
                                   border-b border-r border-gray-200 dark:border-gray-700">
                            {{ $i + 1 }}
                        </td>
                        {{-- Student --}}
                        <td class="sticky left-8 z-10 {{ $stickyBg }} py-2 px-3
                                   border-b border-r border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-maroon text-white text-xs flex items-center
                                            justify-center font-semibold flex-shrink-0">
                                    {{ strtoupper(substr($student->first_name ?? '?', 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                                </div>
                                <div class="leading-tight min-w-0">
                                    <div class="font-medium text-gray-900 dark:text-white text-sm truncate">
                                        {{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $student->student_id ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        {{-- One cell per subject --}}
                        @foreach($classSubjects as $subject)
                        @php $mark = $marksGrid[$student->id][$subject->id] ?? null; @endphp
                        <td class="py-2 px-1 text-center border-b border-r border-gray-200 dark:border-gray-700">
                            @if($mark)
                            <div class="font-semibold text-gray-900 dark:text-white text-sm leading-tight">
                                {{ $mark->marks_obtained }}
                            </div>
                            <span class="inline-block text-xs px-1 py-0 rounded font-semibold leading-5 {{ $gradeColor($mark->grade ?? 'F') }}">
                                {{ $mark->grade ?? 'â€”' }}
                            </span>
                            @can('marks.edit')
                            <a href="{{ route('marks.edit', $mark) }}"
                               class="block text-center text-blue-400 hover:text-blue-600 leading-none mt-0.5"
                               title="Edit">
                                <i class="fas fa-pencil-alt" style="font-size:9px"></i>
                            </a>
                            @endcan
                            @else
                            <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                            @endif
                        </td>
                        @endforeach
                        {{-- Total obtained / total possible --}}
                        <td class="py-2 px-2 text-center border-b border-r border-gray-200 dark:border-gray-700">
                            @if($rowCount > 0)
                            <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ $rowObt }}</div>
                            <div class="text-xs text-gray-400">/ {{ $rowTotal }}</div>
                            @else
                            <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                            @endif
                        </td>
                        {{-- Average % --}}
                        <td class="py-2 px-2 text-center border-b border-r border-gray-200 dark:border-gray-700">
                            @if($rowAvg !== null)
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $rowAvg }}%</span>
                            @else
                            <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                            @endif
                        </td>
                        {{-- Final grade (based on average) --}}
                        <td class="py-2 px-2 text-center border-b border-gray-200 dark:border-gray-700">
                            @if($rowGrade)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $gradeColor($rowGrade) }}">
                                {{ $rowGrade }}
                            </span>
                            @else
                            <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    @else
    {{-- â”€â”€ Prompt: select filters first â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700
                py-16 text-center text-gray-400">
        <i class="fas fa-table text-5xl mb-4 block opacity-30"></i>
        <p class="text-base font-medium text-gray-500 dark:text-gray-400">Select a class, term, and year above</p>
        <p class="text-sm mt-1">The mark sheet will load with all students and their subject scores.</p>
    </div>
    @endisset

</div>
@endsection
