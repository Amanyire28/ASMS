<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $report->report_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #111827; background: #fff; }
        .page { width: 100%; padding: 14mm 14mm; }

        /* Header */
        .header { text-align: center; padding-bottom: 10px; }
        .brand-line { display: block; text-align: center; }
        .school-name { font-size: 18px; font-weight: 700; color: #111827; }
        .letterhead { font-size: 11px; color: #6b7280; margin-top: 3px; }
        .header-rule  { border: none; border-top: 2px solid #111827; margin: 6px 0 3px; }
        .header-rule-2 { border: none; border-top: 1px solid #6b7280; margin: 0 0 14px; }

        /* Student info */
        .student-section { border: 1px solid #e5e7eb; background: #f8fafc; margin-bottom: 14px; }
        .student-inner { display: block; }
        .info-row { font-size: 11px; margin-bottom: 3px; }
        .info-label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; }
        .rc-heading { font-size: 16px; font-weight: 900; text-transform: uppercase; letter-spacing: 3px; text-align: center; padding: 12px 0; }

        /* Marks table */
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #374151; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; border: 1px solid #e5e7eb; }
        thead th { background: #374151; color: #fff; font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 7px 8px; text-align: left; border: 1px solid #555; }
        thead th.center { text-align: center; }
        tbody td { padding: 7px 8px; border: 1px solid #e5e7eb; font-size: 11px; }
        tbody td.center { text-align: center; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tfoot td { background: #f3f4f6; font-weight: 700; font-size: 11px; padding: 7px 8px; border: 1px solid #e5e7eb; border-top: 2px solid #9ca3af; }
        tfoot td.center { text-align: center; }

        /* Signatures */
        .signatures { margin-top: 20px; padding-top: 12px; border-top: 1px solid #e5e7eb; }
        .sig-table { width: 100%; border: none; }
        .sig-table td { text-align: center; width: 50%; border: none; padding: 0; }
        .sig-line { border-bottom: 2px solid #9ca3af; margin: 0 30px 6px; height: 40px; }
        .sig-name { font-size: 12px; font-weight: 700; }
        .sig-title { font-size: 10px; color: #6b7280; margin-top: 2px; }

        /* Footer */
        .footer { text-align: center; margin-top: 16px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; font-style: italic; }
        .meta { text-align: right; margin-top: 8px; font-size: 9px; color: #c1c8d2; }
    </style>
</head>
<body>
<div class="page">

    {{-- School Header --}}
    <div class="header">
        @php
            $logoLeft  = school_setting('logo_left_text');
            $logoRight = school_setting('logo_right_text');
            if (!$logoLeft && !$logoRight) {
                $nameParts = explode(' ', school_setting('school_name', 'School'), 2);
                $logoLeft  = $nameParts[0];
                $logoRight = $nameParts[1] ?? '';
            }
        @endphp
        <div class="brand-line">
            <span class="school-name">{{ $logoLeft }} {{ $logoRight }}</span>
        </div>
        @if(school_setting('letterhead_text'))
            <div class="letterhead">{{ school_setting('letterhead_text') }}</div>
        @endif
    </div>
    <hr class="header-rule">
    <hr class="header-rule-2">

    {{-- Student block --}}
    <div class="student-section">
        <div class="rc-heading">Report Card</div>
        <table style="border:none; margin-bottom:0;">
            <tr style="background:transparent;">
                <td style="border:none; padding:8px 14px; width:50%;">
                    <div class="info-row"><span class="info-label">Name: </span>{{ $report->student->full_name }}</div>
                    <div class="info-row"><span class="info-label">ID: </span>{{ $report->student->student_id }}</div>
                </td>
                <td style="border:none; padding:8px 14px; width:50%;">
                    <div class="info-row"><span class="info-label">Class: </span>{{ $report->student->class->name ?? 'N/A' }}</div>
                    <div class="info-row"><span class="info-label">Term: </span>{{ $report->term }}, {{ $report->academic_year }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Marks --}}
    <div class="section-title">Academic Performance</div>
    @if($marks->count() > 0)
    @php $showTotal = count($examTypes) > 1; @endphp
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                @foreach($examTypes as $et)
                <th class="center">{{ $et['label'] }} / {{ $et['max_marks'] }}</th>
                @endforeach
                @if($showTotal)<th class="center">Final %</th>@endif
                <th class="center">Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $subject)
            @php
                $subjectMarks = $marksGrouped[$subject->id] ?? [];
                $hasMarks = false;
                $subFinal = 0;
                foreach ($examTypes as $et) {
                    $mm = $subjectMarks[$et['id']] ?? null;
                    if ($mm) {
                        $hasMarks = true;
                        if ($mm->total_marks > 0) {
                            $subFinal += ((float)$mm->marks_obtained / (float)$mm->total_marks)
                                * ((float)($et['weight'] ?? 100) / 100);
                        }
                    }
                }
                $subFinalPct = $hasMarks ? round($subFinal * 100, 1) : null;
                $g = $subFinalPct !== null ? grade_info($subFinalPct) : null;
                $subGrade = $g['grade'] ?? null;
                $firstMark = collect($subjectMarks)->first();
                $remarks   = $firstMark->remarks ?? ($g['achievement'] ?? null);
            @endphp
            <tr>
                <td>{{ $subject->name }}</td>
                @foreach($examTypes as $et)
                @php $mm = $marksGrouped[$subject->id][$et['id']] ?? null; @endphp
                <td class="center">{{ $mm !== null ? $mm->marks_obtained : '-' }}</td>
                @endforeach
                @if($showTotal)
                <td class="center" style="font-weight:600;">{{ $subFinalPct ?? '-' }}</td>
                @endif
                <td class="center" style="font-weight:700;">{{ $subGrade ?? '-' }}</td>
                <td>{{ $remarks ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL / AVERAGE</td>
                @foreach($examTypes as $et)<td class="center">-</td>@endforeach
                @if($showTotal)<td class="center" style="font-weight:700;">{{ $summary['average_percentage'] }}</td>@endif
                <td class="center" style="font-weight:800;">{{ $summary['grade'] }}</td>
                <td>{{ $summary['subject_count'] }} subject(s)</td>
            </tr>
            @php
                $maxTerms = is_three_term_system() ? 3 : 2;
                $currTerm = current_term();
                $nextTerm = $currTerm >= $maxTerms ? 1 : $currTerm + 1;
                $termDates = term_dates();
                $nextStart = $termDates[$nextTerm]['start_date'] ?? null;
                $nextEnd   = $termDates[$nextTerm]['end_date'] ?? null;
                $colspan   = 2 + count($examTypes) + ($showTotal ? 1 : 0);
            @endphp
            <tr>
                <td colspan="{{ $colspan }}">
                    Next term begins: <strong>{{ $nextStart ? \Carbon\Carbon::parse($nextStart)->format('M d, Y') : '-' }}</strong>
                    &mdash; Ends: <strong>{{ $nextEnd ? \Carbon\Carbon::parse($nextEnd)->format('M d, Y') : '-' }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
    @else
    <p style="text-align:center;color:#9ca3af;padding:20px 0;">No marks recorded for this term.</p>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <table class="sig-table">
            <tr>
                <td>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ school_setting('principal_name', 'Principal') }}</div>
                    <div class="sig-title">Principal</div>
                </td>
                <td>
                    <div class="sig-line"></div>
                    <div class="sig-name">Head Teacher</div>
                    <div class="sig-title">Class Teacher</div>
                </td>
            </tr>
        </table>
    </div>

    @if(school_setting('report_footer_text'))
    <div class="footer">{{ school_setting('report_footer_text') }}</div>
    @endif
    <div class="meta">Generated {{ now()->format('M d, Y') }}</div>
</div>
</body>
</html>
