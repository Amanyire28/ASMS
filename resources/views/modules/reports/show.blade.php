@extends('layouts.app')
@section('title', 'Report Card - ' . ($report->report_number ?? 'Report'))
@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-lg font-bold">Report: {{ $report->report_number ?? 'N/A' }}</h1>
        <p class="text-sm text-gray-600">Student: {{ $report->student->full_name ?? 'N/A' }}</p>
        <p class="mt-4 text-gray-700">Simplified report view (temporary). Rebuilding full view.</p>
    </div>
</div>

{{-- Header: back, title, actions --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:underline">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        <div class="text-center">
            <h1 class="text-xl font-bold text-gray-900">{{ $report->report_number }}</h1>
            <p class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $report->report_type)) }} &mdash; {{ $report->term }}, {{ $report->academic_year }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.print', $report) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fas fa-print"></i> Print
            </a>
            @canany('reports.delete')
            <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Delete this report?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
            @endcanany
        </div>
    </div>
</div>
<div class="container mx-auto px-4">
    <div class="bg-white shadow-sm rounded-xl border-2 border-gray-300 overflow-hidden">

        {{-- School Header --}}
        <div class="px-6 pt-6 pb-4 text-center">
            @php $logoLeft = school_setting('logo_left_text'); $logoRight = school_setting('logo_right_text'); @endphp
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
        </div>

        {{-- Double-line separator --}}
        <div class="mx-6 border-t-2 border-gray-800"></div>
        <div class="mx-6 mt-1 border-t border-gray-400"></div>

        <div class="p-6 space-y-6">

            {{-- Student Info: details | REPORT CARD title | photo (no-wrap) --}}
            <div class="flex items-stretch gap-0 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden flex-nowrap">
                <div class="flex-1 flex flex-col justify-center gap-1.5 px-5 py-4 min-w-0">
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
                {{-- Centre title column --}}
                <div class="flex-none flex flex-col items-center justify-center px-6 py-4 border-x border-gray-300 min-w-[160px]">
                    <span class="text-2xl font-black uppercase tracking-widest text-gray-800 leading-tight">Report Card</span>
                </div>

                {{-- Right: profile photo (fixed width) --}}
                <div class="flex-none flex flex-col items-center justify-center px-5 py-3 min-w-[92px]">
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
            <style>
                /* Full border for report marks table */
                .report-table { border-collapse: collapse; width: 100%; }
                .report-table th, .report-table td { border: 1px solid #e5e7eb; }
                .report-table thead th { background: #374151; color: #fff; }
            </style>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Academic Performance</h3>
                @if($marks->count() > 0)
                @php $showTotal = count($examTypes) > 1; @endphp
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full report-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                                @foreach($examTypes as $et)
                                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 uppercase leading-tight">
                                    {{ $et['label'] }}<br>
                                    <span class="font-normal text-gray-400 normal-case">/ {{ $et['max_marks'] ?? 100 }}</span>
                                </th>
                                @endforeach
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Average</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Total/100</th>
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
                                    $g = grade_info($subPct);
                                    $subGrade = $g['grade'] ?? null;
                                } else { $subGrade = null; }
                                $firstMark = collect($marksGrouped[$subject->id] ?? [])->first();
                                $remarks   = $firstMark->remarks ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $subject->name }}</td>
                                @foreach($examTypes as $et)
                                @php
                                    // collect exam marks and compute percentages per exam type
                                    $examMarks = [];
                                    foreach ($examTypes as $i => $et2) {
                                        $mm2 = $marksGrouped[$subject->id][$et2['id']] ?? null;
                                        if ($mm2) {
                                            $obt = (float)$mm2->marks_obtained;
                                            $max = !empty($et2['max_marks']) ? (float)$et2['max_marks'] : ((float)$mm2->total_marks ?: 100);
                                            $examMarks[] = ['obt' => $obt, 'max' => $max, 'pct' => $max > 0 ? ($obt / $max * 100) : null];
                                        } else {
                                            $examMarks[] = ['obt' => null, 'max' => null, 'pct' => null];
                                        }
                                    }

                                    // BOT = index 0, MOT = index 1, EOT = index 2 (fallback: use last two for average)
                                    $bot = $examMarks[0]['pct'] ?? null;
                                    $mot = $examMarks[1]['pct'] ?? null;
                                    $eot = $examMarks[2]['pct'] ?? null;
                                    if ($mot !== null && $eot !== null) {
                                        $avg = round(($mot + $eot) / 2, 1);
                                    } elseif ($mot !== null) {
                                        $avg = round($mot, 1);
                                    } elseif ($eot !== null) {
                                        $avg = round($eot, 1);
                                    } else {
                                        $avg = null;
                                    }

                                    $botVal = $bot !== null ? round($bot, 1) : 0;
                                    $total100 = $avg !== null ? round($botVal + $avg, 1) : ($bot !== null ? $botVal : null);
                                    $gradeInfo = $total100 !== null ? grade_info($total100) : null;
                                    $displayGrade = $gradeInfo['grade'] ?? ($subGrade ?? null);
                                @endphp

                                @foreach($examTypes as $et)
                                @php $mm = $marksGrouped[$subject->id][$et['id']] ?? null; @endphp
                                <td class="px-3 py-3 text-center text-gray-700">{{ $mm !== null ? $mm->marks_obtained : '-' }}</td>
                                @endforeach
                                <td class="px-3 py-3 text-center font-medium text-gray-700">{{ $avg !== null ? $avg . '%' : '-' }}</td>
                                <td class="px-3 py-3 text-center font-medium text-gray-700">{{ $total100 !== null ? $total100 . ' / 100' : '-' }}</td>
                                <td class="px-3 py-3 text-center font-semibold text-gray-900">{{ $displayGrade ?? '-' }}</td>
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
                                <td class="px-3 py-3 text-center font-semibold text-gray-800">{{ $summary['average_percentage'] }}%</td>
                                <td class="px-3 py-3 text-center font-semibold text-gray-800">{{ round($summary['average_percentage'],1) }} / 100</td>
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
                    @php $principalSig = school_signature_url('principal'); @endphp
                    @if($principalSig)
                        <img src="{{ e($principalSig) }}" alt="Principal Signature" class="h-14 mx-auto object-contain mb-2">
                    @else
                        <div class="h-14 mb-2 border-b-2 border-gray-400 mx-8"></div>
                    @endif
                    <p class="text-sm font-semibold text-gray-800">{{ school_setting('principal_name', 'Principal') }}</p>
                    <p class="text-xs text-gray-500">Principal</p>
                </div>
                <div class="text-center">
                    @php $headSig = school_signature_url('headteacher'); @endphp
                    @if($headSig)
                        <img src="{{ e($headSig) }}" alt="Head Teacher Signature" class="h-14 mx-auto object-contain mb-2">
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

@endsection
