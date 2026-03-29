@if(!request()->header('HX-Request'))
@extends('layouts.app')
@section('title', 'Report Card - ' . $report->report_number)
@section('content')
@endif

<div class="space-y-6">
    {{-- Action Bar --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index') }}"
               hx-get="{{ route('reports.index') }}"
               hx-target="#page-content"
               hx-push-url="true"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $report->report_number }}</h1>
                <p class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $report->report_type)) }} &mdash; {{ $report->term }}, {{ $report->academic_year }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.print', $report) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fas fa-print"></i> Print
            </a>
            <a href="{{ route('reports.print', $report) }}?download=1" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fas fa-download"></i> Download PDF
            </a>
            @canany('reports.delete')
            <form action="{{ route('reports.destroy', $report) }}" method="POST"
                  onsubmit="return confirm('Delete this report?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
            @endcanany
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2">
        <i class="fas fa-check-circle text-green-500"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Report Card --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">

        {{-- School Header --}}
        <div class="px-6 pt-6 pb-4 text-center">
            @php
                $logoLeft  = school_setting('logo_left_text');
                $logoRight = school_setting('logo_right_text');
                if (!$logoLeft && !$logoRight) {
                    $nameParts = explode(' ', school_setting('school_name', 'School Name'), 2);
                    $logoLeft  = $nameParts[0];
                    $logoRight = $nameParts[1] ?? '';
                }
            @endphp
            {{-- Brand line: left text | Logo | right text --}}
            <div class="flex items-center justify-center gap-3">
                <h2 class="text-xl font-bold tracking-wide text-gray-900">{{ $logoLeft }}</h2>
                <div class="shrink-0">
                    @if(school_logo_url())
                    <img src="{{ school_logo_url() }}" alt="School Logo" class="h-16 w-16 object-contain">
                    @else
                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center border border-gray-300">
                        <i class="fas fa-school text-2xl text-gray-400"></i>
                    </div>
                    @endif
                </div>
                <h2 class="text-xl font-bold tracking-wide text-gray-900">{{ $logoRight }}</h2>
            </div>
            {{-- Details below --}}
            @if(school_setting('letterhead_text'))
            <p class="text-gray-500 text-sm mt-1">{{ school_setting('letterhead_text') }}</p>
            @endif
            <div class="mt-2 inline-block border border-gray-400 rounded px-3 py-1">
                <span class="text-sm font-semibold uppercase tracking-wider text-gray-700">
                    {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                </span>
            </div>
        </div>

        {{-- Double-line separator --}}
        <div class="mx-6 border-t-2 border-gray-800"></div>
        <div class="mx-6 mt-1 border-t border-gray-400"></div>

        <div class="p-6 space-y-6">

            {{-- Student Info: details left | title centre | photo right --}}
            <div class="flex items-stretch gap-0 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">

                {{-- Left: compact label: value rows --}}
                <div class="flex-1 flex flex-col justify-center gap-1.5 px-5 py-4">
                    <div class="flex items-baseline gap-1 text-sm">
                        <span class="font-semibold text-gray-500 shrink-0 w-20 text-xs uppercase">Name</span>
                        <span class="font-bold text-gray-900">: {{ $report->student->full_name }}</span>
                    </div>
                    <div class="flex items-baseline gap-1 text-sm">
                        <span class="font-semibold text-gray-500 shrink-0 w-20 text-xs uppercase">ID</span>
                        <span class="font-bold text-gray-900">: {{ $report->student->student_id }}</span>
                    </div>
                    <div class="flex items-baseline gap-1 text-sm">
                        <span class="font-semibold text-gray-500 shrink-0 w-20 text-xs uppercase">Class</span>
                        <span class="font-bold text-gray-900">: {{ $report->student->class->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-baseline gap-1 text-sm">
                        <span class="font-semibold text-gray-500 shrink-0 w-20 text-xs uppercase">Term</span>
                        <span class="font-bold text-gray-900">: {{ $report->term }}, {{ $report->academic_year }}</span>
                    </div>
                </div>

                {{-- Centre: Report Card title --}}
                <div class="flex flex-col items-center justify-center text-center border-x border-gray-300 px-6 py-4">
                    <span class="text-2xl font-black uppercase tracking-widest text-gray-800 leading-tight">Report<br>Card</span>
                    <div class="mt-1 text-xs text-gray-400 font-medium uppercase tracking-wide">
                        {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                    </div>
                </div>

                {{-- Right: profile photo --}}
                <div class="flex flex-col items-center justify-center px-5 py-3">
                    @if($report->student->photo)
                    <img src="{{ asset('storage/' . $report->student->photo) }}" alt="Student Photo"
                         class="h-24 w-20 object-cover rounded border-2 border-gray-300 shadow-sm">
                    @else
                    <div class="h-24 w-20 rounded bg-blue-100 flex items-center justify-center border-2 border-gray-300">
                        <span class="text-blue-700 font-bold text-xl">
                            {{ strtoupper(substr($report->student->first_name, 0, 1) . substr($report->student->last_name, 0, 1)) }}
                        </span>
                    </div>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">Photo</p>
                </div>

            </div>

            {{-- Marks Table --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Academic Performance</h3>
                @if($marks->count() > 0)
                @php $showTotal = count($examTypes) > 1; @endphp
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                                @foreach($examTypes as $et)
                                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 uppercase leading-tight">
                                    {{ $et['label'] }}<br>
                                    <span class="font-normal text-gray-400 normal-case">/ {{ $et['max_marks'] }}</span>
                                </th>
                                @endforeach
                                @if($showTotal)
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Total</th>
                                @endif
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">%</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Grade</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($subjects as $subject)
                            @php
                                $subObt = 0; $subTot = 0;
                                foreach ($examTypes as $et) {
                                    $mm = $marksGrouped[$subject->id][$et['id']] ?? null;
                                    if ($mm) { $subObt += (float)$mm->marks_obtained; $subTot += (float)$mm->total_marks; }
                                }
                                $subPct = $subTot > 0 ? round($subObt / $subTot * 100, 1) : null;
                                if ($subPct !== null) {
                                    if ($subPct >= 90) $subGrade = 'A+';
                                    elseif ($subPct >= 80) $subGrade = 'A';
                                    elseif ($subPct >= 70) $subGrade = 'B+';
                                    elseif ($subPct >= 60) $subGrade = 'B';
                                    elseif ($subPct >= 50) $subGrade = 'C+';
                                    elseif ($subPct >= 40) $subGrade = 'C';
                                    elseif ($subPct >= 30) $subGrade = 'D';
                                    else $subGrade = 'F';
                                } else { $subGrade = null; }
                                $firstMark = collect($marksGrouped[$subject->id] ?? [])->first();
                                $remarks   = $firstMark->remarks ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $subject->name }}</td>
                                @foreach($examTypes as $et)
                                @php $mm = $marksGrouped[$subject->id][$et['id']] ?? null; @endphp
                                <td class="px-3 py-3 text-center text-gray-700">{{ $mm !== null ? $mm->marks_obtained : '-' }}</td>
                                @endforeach
                                @if($showTotal)
                                <td class="px-3 py-3 text-center font-semibold text-gray-800">
                                    {{ $subTot > 0 ? $subObt . ' / ' . $subTot : '-' }}
                                </td>
                                @endif
                                <td class="px-3 py-3 text-center font-medium text-gray-700">
                                    {{ $subPct !== null ? $subPct . '%' : '-' }}
                                </td>
                                <td class="px-3 py-3 text-center font-semibold text-gray-900">{{ $subGrade ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $remarks ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        {{-- Summary Row --}}
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-800">TOTAL / AVERAGE</td>
                                @foreach($examTypes as $et)
                                <td class="px-3 py-3 text-center text-gray-400">-</td>
                                @endforeach
                                @if($showTotal)
                                <td class="px-3 py-3 text-center font-semibold text-gray-800">
                                    {{ $summary['total_marks'] }} / {{ $summary['total_possible'] }}
                                </td>
                                @endif
                                <td class="px-3 py-3 text-center font-semibold text-gray-800">{{ $summary['average_percentage'] }}%</td>
                                <td class="px-3 py-3 text-center font-bold text-gray-900">{{ $summary['grade'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $summary['subject_count'] }} subject(s)</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-clipboard-list text-3xl mb-2"></i>
                    <p>No marks recorded for this term.</p>
                </div>
                @endif
            </div>

            {{-- Signatures --}}
            <div class="grid grid-cols-2 gap-8 pt-4 border-t border-gray-200">
                <div class="text-center">
                    @if(school_signature_url('principal'))
                    <img src="{{ school_signature_url('principal') }}" alt="Principal Signature"
                         class="h-14 mx-auto object-contain mb-2">
                    @else
                    <div class="h-14 mb-2 border-b-2 border-gray-400 mx-8"></div>
                    @endif
                    <p class="text-sm font-semibold text-gray-800">{{ school_setting('principal_name', 'Principal') }}</p>
                    <p class="text-xs text-gray-500">Principal</p>
                </div>
                <div class="text-center">
                    @if(school_signature_url('headteacher'))
                    <img src="{{ school_signature_url('headteacher') }}" alt="Head Teacher Signature"
                         class="h-14 mx-auto object-contain mb-2">
                    @else
                    <div class="h-14 mb-2 border-b-2 border-gray-400 mx-8"></div>
                    @endif
                    <p class="text-sm font-semibold text-gray-800">Head Teacher</p>
                    <p class="text-xs text-gray-500">Class Teacher</p>
                </div>
            </div>

            {{-- Footer --}}
            @if(school_setting('report_footer_text'))
            <div class="text-center pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 italic">{{ school_setting('report_footer_text') }}</p>
            </div>
            @endif

            {{-- Meta --}}
            <div class="text-xs text-gray-400 text-right">
                Generated by {{ $report->generatedBy->name }} on {{ $report->generated_at->format('M d, Y g:i A') }}
            </div>
        </div>
    </div>
</div>

@if(!request()->header('HX-Request'))
@endsection
@endif
