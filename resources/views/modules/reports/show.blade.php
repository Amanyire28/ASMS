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
        <div class="bg-blue-700 text-white px-6 py-5 text-center">
            @if(school_logo_url())
            <img src="{{ school_logo_url() }}" alt="School Logo" class="mx-auto h-16 mb-2 object-contain">
            @endif
            <h2 class="text-xl font-bold tracking-wide">{{ school_setting('school_name', 'School Name') }}</h2>
            @if(school_setting('letterhead_text'))
            <p class="text-blue-200 text-sm mt-1">{{ school_setting('letterhead_text') }}</p>
            @endif
            <div class="mt-3 inline-block bg-white/20 rounded px-3 py-1">
                <span class="text-sm font-semibold uppercase tracking-wider">
                    {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                </span>
            </div>
        </div>

        <div class="p-6 space-y-6">

            {{-- Student Info --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 rounded-lg p-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Student Name</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $report->student->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Student ID</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $report->student->student_id }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Class</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $report->student->class->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Term / Year</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $report->term }}, {{ $report->academic_year }}</p>
                </div>
            </div>

            {{-- Marks Table --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Academic Performance</h3>
                @if($marks->count() > 0)
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Score</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Out Of</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">%</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Grade</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($marks as $mark)
                            @php
                                $pct = $mark->total_marks > 0
                                    ? round(($mark->marks_obtained / $mark->total_marks) * 100, 1)
                                    : 0;
                                $gradeColor = match(true) {
                                    $pct >= 80 => 'bg-green-100 text-green-800',
                                    $pct >= 60 => 'bg-blue-100 text-blue-800',
                                    $pct >= 40 => 'bg-yellow-100 text-yellow-800',
                                    default    => 'bg-red-100 text-red-800',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $mark->subject->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $mark->marks_obtained }}</td>
                                <td class="px-4 py-3 text-center text-gray-500">{{ $mark->total_marks }}</td>
                                <td class="px-4 py-3 text-center font-medium text-gray-700">{{ $pct }}%</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $gradeColor }}">
                                        {{ $mark->grade ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $mark->remarks ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        {{-- Summary Row --}}
                        <tfoot class="bg-blue-50">
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-800">TOTAL / AVERAGE</td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ $summary['total_marks'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-500">{{ $summary['total_possible'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-blue-700">{{ $summary['average_percentage'] }}%</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-600 text-white">
                                        {{ $summary['grade'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $summary['subject_count'] }} subjects</td>
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
