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

        <!-- Academic Records -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-maroon"></i>Academic Records
            </h3>
            @if($student->marks->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No marks recorded yet.</p>
            @else
                @php
                    $marksByPeriod = $student->marks
                        ->sortByDesc(fn($m) => $m->academic_year . '-' . $m->term)
                        ->groupBy(fn($m) => $m->academic_year . ' &middot; ' . $m->term);
                @endphp
                @foreach($marksByPeriod as $periodLabel => $periodMarks)
                @php
                    $markMap = [];
                    $periodSubjects = collect();
                    foreach ($periodMarks as $m) {
                        $markMap[$m->subject_id][$m->exam_type] = $m;
                        if ($m->subject && !$periodSubjects->contains('id', $m->subject_id)) {
                            $periodSubjects->push($m->subject);
                        }
                    }
                    $periodSubjects = $periodSubjects->sortBy('name');
                @endphp
                <div class="mb-6 last:mb-0">
                    <p class="text-xs font-semibold text-maroon uppercase tracking-wider mb-3">{!! $periodLabel !!}</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-separate border-spacing-0">
                            <thead>
                                <tr>
                                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-500 uppercase
                                               bg-gray-50 dark:bg-gray-900 border-b-2 border-r
                                               border-gray-200 dark:border-gray-700 min-w-[120px]">Subject</th>
                                    @foreach($examTypes as $et)
                                    <th class="py-2 px-2 text-center text-xs font-semibold text-indigo-600 dark:text-indigo-400
                                               bg-gray-50 dark:bg-gray-900 border-b-2 border-r
                                               border-gray-200 dark:border-gray-700 min-w-[80px] whitespace-nowrap">
                                        {{ $et['label'] }}
                                    </th>
                                    @endforeach
                                    <th class="py-2 px-2 text-center text-xs font-semibold text-gray-500 uppercase
                                               bg-gray-50 dark:bg-gray-900 border-b-2
                                               border-gray-200 dark:border-gray-700 min-w-[80px]">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periodSubjects as $subj)
                                @php $subObt = 0; $subMax = 0; $hasAny = false; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="py-2 px-3 text-gray-900 dark:text-white font-medium border-b border-r
                                               border-gray-200 dark:border-gray-700">{{ $subj->name }}</td>
                                    @foreach($examTypes as $et)
                                    @php
                                        $m = $markMap[$subj->id][$et['id']] ?? null;
                                        if ($m !== null) {
                                            $subObt += (float) $m->marks_obtained;
                                            $subMax += (float) $m->total_marks;
                                            $hasAny = true;
                                        }
                                    @endphp
                                    <td class="py-2 px-2 text-center border-b border-r border-gray-200 dark:border-gray-700">
                                        @if($m !== null)
                                        @php $etPct = $m->total_marks > 0 ? round(($m->marks_obtained / $m->total_marks) * 100) : 0; @endphp
                                        <div class="text-xs font-semibold text-gray-900 dark:text-white">
                                            {{ $m->marks_obtained }}<span class="text-gray-400 font-normal">/{{ $m->total_marks }}</span>
                                        </div>
                                        <div class="text-xs {{ $etPct>=70?'text-green-600':($etPct>=50?'text-blue-500':($etPct>=40?'text-yellow-500':($etPct>=30?'text-orange-500':'text-red-500'))) }}">
                                            {{ $etPct }}%
                                        </div>
                                        @else
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">&mdash;</span>
                                        @endif
                                    </td>
                                    @endforeach
                                    <td class="py-2 px-2 text-center border-b border-gray-200 dark:border-gray-700">
                                        @if($hasAny && $subMax > 0)
                                        @php $totPct = round($subObt / $subMax * 100); @endphp
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $subObt }}<span class="text-xs text-gray-400 font-normal">/{{ $subMax }}</span>
                                        </div>
                                        <div class="text-xs font-semibold {{ $totPct>=70?'text-green-600':($totPct>=50?'text-blue-500':($totPct>=40?'text-yellow-500':($totPct>=30?'text-orange-500':'text-red-500'))) }}">
                                            {{ $totPct }}%
                                        </div>
                                        @else
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">&mdash;</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @php
                                $grandObt = 0; $grandMax = 0;
                                foreach ($periodSubjects as $subj) {
                                    foreach ($examTypes as $et) {
                                        $gm = $markMap[$subj->id][$et['id']] ?? null;
                                        if ($gm) { $grandObt += (float) $gm->marks_obtained; $grandMax += (float) $gm->total_marks; }
                                    }
                                }
                                $grandPct = $grandMax > 0 ? round($grandObt / $grandMax * 100) : 0;
                            @endphp
                            <tfoot>
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-semibold">
                                    <td class="py-2 px-3 text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">
                                        Overall
                                    </td>
                                    @foreach($examTypes as $et)
                                    @php
                                        $etObt = 0; $etMax = 0;
                                        foreach ($periodSubjects as $subj) {
                                            $em = $markMap[$subj->id][$et['id']] ?? null;
                                            if ($em) { $etObt += (float) $em->marks_obtained; $etMax += (float) $em->total_marks; }
                                        }
                                    @endphp
                                    <td class="py-2 px-2 text-center border-r border-gray-200 dark:border-gray-700">
                                        @if($etMax > 0)
                                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $etObt }}/{{ $etMax }}</div>
                                        @else
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">&mdash;</span>
                                        @endif
                                    </td>
                                    @endforeach
                                    <td class="py-2 px-2 text-center">
                                        @if($grandMax > 0)
                                        <div class="font-bold text-sm text-gray-900 dark:text-white">
                                            {{ $grandObt }}<span class="text-xs text-gray-400 font-normal">/{{ $grandMax }}</span>
                                        </div>
                                        <div class="text-xs font-bold {{ $grandPct>=70?'text-green-600':($grandPct>=50?'text-blue-500':($grandPct>=40?'text-yellow-500':($grandPct>=30?'text-orange-500':'text-red-500'))) }}">
                                            {{ $grandPct }}%
                                        </div>
                                        @else
                                        <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                                        @endif
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
