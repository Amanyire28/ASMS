<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucwords(str_replace('_', ' ', $report->report_type)) }} - {{ $report->report_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #111827;
            background: #f3f4f6;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            padding: 20mm 18mm;
            box-shadow: 0 2px 16px rgba(0,0,0,.12);
        }

        /* ---- Header ---- */
        .header {
            text-align: center;
            padding-bottom: 14px;
            margin-bottom: 0;
        }
        .brand-line {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .brand-line img { height: 64px; width: 64px; object-fit: contain; }
        .header-rule { border: none; border-top: 2px solid #111827; margin: 0 0 3px 0; }
        .header-rule-2 { border: none; border-top: 1px solid #6b7280; margin: 0 0 18px 0; }
        .school-name {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            letter-spacing: .5px;
        }
        .letterhead { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .report-title {
            display: inline-block;
            margin-top: 6px;
            border: 1px solid #374151;
            color: #374151;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4px 16px;
            border-radius: 12px;
        }
            color: #374151;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4px 16px;
            border-radius: 12px;
        }

        /* ---- Student Info ---- */
        .student-section {
            display: flex;
            align-items: stretch;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            background: #f8fafc;
            margin-bottom: 18px;
        }
        .student-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            padding: 10px 14px;
        }
        .info-row {
            display: flex;
            align-items: baseline;
            gap: 4px;
            font-size: 11.5px;
        }
        .info-label {
            font-size: 10px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .6px;
            width: 54px;
            flex-shrink: 0;
        }
        .info-value { font-size: 11.5px; font-weight: 700; color: #111827; }
        .student-title {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-left: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            padding: 8px 20px;
        }
        .student-title .rc-heading {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #111827;
            line-height: 1.2;
        }
        .student-title .rc-subtype {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .student-photo { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px 14px; }
        .student-photo img { width: 72px; height: 90px; object-fit: cover; border-radius: 4px; border: 1px solid #d1d5db; }
        .student-photo .photo-initials {
            width: 72px; height: 90px; background: #dbeafe; color: #1d4ed8;
            display: flex; align-items: center; justify-content: center;
            border-radius: 4px; font-size: 22px; font-weight: 700; border: 1px solid #bfdbfe;
        }
        .student-photo .photo-label { font-size: 9px; color: #9ca3af; margin-top: 3px; text-align: center; }

        /* ---- Marks Table ---- */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #374151;
            margin-bottom: 8px;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th {
            background: #374151;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            padding: 8px 10px;
            text-align: left;
        }
        thead th.center { text-align: center; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        tbody td.center { text-align: center; }
        .grade-badge {
            font-size: 11px;
            font-weight: 700;
            color: #111827;
        }
        tfoot td {
            background: #f3f4f6;
            font-weight: 700;
            font-size: 12px;
            padding: 9px 10px;
            border-top: 2px solid #9ca3af;
        }
        tfoot td.center { text-align: center; }
        .total-grade {
            font-size: 12px;
            font-weight: 800;
            color: #111827;
        }

        /* ---- Signatures ---- */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }
        .sig-block { text-align: center; }
        .sig-img { height: 50px; object-fit: contain; margin-bottom: 6px; }
        .sig-line {
            height: 50px;
            border-bottom: 2px solid #9ca3af;
            margin: 0 40px 6px;
        }
        .sig-name { font-size: 13px; font-weight: 700; color: #111827; }
        .sig-title { font-size: 11px; color: #6b7280; margin-top: 2px; }

        /* ---- Footer ---- */
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #9ca3af;
            font-style: italic;
        }
        .meta {
            text-align: right;
            margin-top: 10px;
            font-size: 10px;
            color: #c1c8d2;
        }

        /* ---- Print button (screen only) ---- */
        .print-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #1d4ed8;
            color: #fff;
            text-align: center;
            padding: 10px;
            z-index: 999;
            font-size: 13px;
        }
        .print-bar button {
            background: #fff;
            color: #1d4ed8;
            border: none;
            padding: 6px 20px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            margin-left: 12px;
            font-size: 13px;
        }
        .print-bar a { color: #bfdbfe; text-decoration: none; margin-right: 16px; font-size: 12px; }

        @media print {
            body { background: #fff; }
            .page { margin: 0; padding: 15mm; box-shadow: none; width: 100%; }
            .print-bar { display: none; }
        }
    </style>
</head>
<body>
    <div class="print-bar">
        <a href="{{ route('reports.show', $report) }}">&larr; Back to Report</a>
        <span>{{ $report->report_number }} &mdash; {{ $report->student->full_name }}</span>
        <button onclick="window.print()">&#128438; Print / Save PDF</button>
    </div>

    <div class="page" style="margin-top: 60px;">

    <script>
        // Auto-trigger print dialog when ?download=1 is in the URL
        if (new URLSearchParams(window.location.search).get('download') === '1') {
            window.addEventListener('load', function () {
                setTimeout(function () { window.print(); }, 400);
            });
        }
    <\/script>

        {{-- School Header --}}
        <div class="header">
            {{-- Brand line: left text | Logo | right text --}}
            @php
                $logoLeft  = school_setting('logo_left_text');
                $logoRight = school_setting('logo_right_text');
                if (!$logoLeft && !$logoRight) {
                    $nameParts = explode(' ', school_setting('school_name', 'School Name'), 2);
                    $logoLeft  = $nameParts[0];
                    $logoRight = $nameParts[1] ?? '';
                }
            @endphp
            <div class="brand-line">
                <span class="school-name">{{ $logoLeft }}</span>
                @if(school_logo_url())
                <img src="{{ school_logo_url() }}" alt="Logo">
                @endif
                <span class="school-name">{{ $logoRight }}</span>
            </div>
            {{-- Details below --}}
            @if(school_setting('letterhead_text'))
            <div class="letterhead">{{ school_setting('letterhead_text') }}</div>
            @endif
            <div class="report-title">{{ ucwords(str_replace('_', ' ', $report->report_type)) }}</div>
        </div>
        <hr class="header-rule">
        <hr class="header-rule-2">

        {{-- Student Info: details left | title centre | photo right --}}
        <div class="student-section">
            {{-- Left: compact label: value rows --}}
            <div class="student-details">
                <div class="info-row">
                    <span class="info-label">Name</span>
                    <span class="info-value">: {{ $report->student->full_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">ID</span>
                    <span class="info-value">: {{ $report->student->student_id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Class</span>
                    <span class="info-value">: {{ $report->student->class->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Term</span>
                    <span class="info-value">: {{ $report->term }}, {{ $report->academic_year }}</span>
                </div>
            </div>
            {{-- Centre: Report Card title --}}
            <div class="student-title">
                <div class="rc-heading">Report<br>Card</div>
                <div class="rc-subtype">{{ ucwords(str_replace('_', ' ', $report->report_type)) }}</div>
            </div>
            {{-- Right: photo --}}
            <div class="student-photo">
                @if($report->student->photo)
                <img src="{{ asset('storage/' . $report->student->photo) }}" alt="Student Photo">
                @else
                <div class="photo-initials">{{ strtoupper(substr($report->student->first_name, 0, 1) . substr($report->student->last_name, 0, 1)) }}</div>
                @endif
                <div class="photo-label">Photo</div>
            </div>
        </div>

        {{-- Marks Table --}}
        <div class="section-title">Academic Performance</div>
        @if($marks->count() > 0)
        @php $showTotal = count($examTypes) > 1; @endphp
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    @foreach($examTypes as $et)
                    <th class="center" style="min-width:55px;">{{ $et['label'] }}<br>
                        <span style="font-weight:400;font-size:9px;color:#6b7280;">/ {{ $et['max_marks'] }}</span>
                    </th>
                    @endforeach
                    @if($showTotal)
                    <th class="center" style="min-width:65px;">Total</th>
                    @endif
                    <th class="center">%</th>
                    <th class="center">Grade</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
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
                <tr>
                    <td>{{ $subject->name }}</td>
                    @foreach($examTypes as $et)
                    @php $mm = $marksGrouped[$subject->id][$et['id']] ?? null; @endphp
                    <td class="center">{{ $mm !== null ? $mm->marks_obtained : '-' }}</td>
                    @endforeach
                    @if($showTotal)
                    <td class="center" style="font-weight:600;">
                        {{ $subTot > 0 ? $subObt . ' / ' . $subTot : '-' }}
                    </td>
                    @endif
                    <td class="center">{{ $subPct !== null ? $subPct . '%' : '-' }}</td>
                    <td class="center">
                        <span class="grade-badge">{{ $subGrade ?? '-' }}</span>
                    </td>
                    <td>{{ $remarks ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL / AVERAGE</td>
                    @foreach($examTypes as $et)
                    <td class="center">-</td>
                    @endforeach
                    @if($showTotal)
                    <td class="center" style="font-weight:600;">
                        {{ $summary['total_marks'] }} / {{ $summary['total_possible'] }}
                    </td>
                    @endif
                    <td class="center">{{ $summary['average_percentage'] }}%</td>
                    <td class="center">
                        <span class="total-grade">{{ $summary['grade'] }}</span>
                    </td>
                    <td>{{ $summary['subject_count'] }} subject(s)</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="text-align:center; color:#9ca3af; padding: 24px 0;">No marks recorded for this term.</p>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            <div class="sig-block">
                @if(school_signature_url('principal'))
                <img src="{{ school_signature_url('principal') }}" alt="Principal Signature" class="sig-img">
                @else
                <div class="sig-line"></div>
                @endif
                <div class="sig-name">{{ school_setting('principal_name', 'Principal') }}</div>
                <div class="sig-title">Principal</div>
            </div>
            <div class="sig-block">
                @if(school_signature_url('headteacher'))
                <img src="{{ school_signature_url('headteacher') }}" alt="Head Teacher Signature" class="sig-img">
                @else
                <div class="sig-line"></div>
                @endif
                <div class="sig-name">Head Teacher</div>
                <div class="sig-title">Class Teacher</div>
            </div>
        </div>

        @if(school_setting('report_footer_text'))
        <div class="footer">{{ school_setting('report_footer_text') }}</div>
        @endif

        <div class="meta">
            Generated by {{ $report->generatedBy->name }} &bull; {{ $report->generated_at->format('M d, Y g:i A') }}
        </div>
    </div>
</body>
</html>
