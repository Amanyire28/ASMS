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
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 14px;
            margin-bottom: 0;
        }
        .header-left  { flex: 1; text-align: left; }
        .header-center { flex-shrink: 0; text-align: center; }
        .header-right { flex: 1; text-align: right; }
        .header-rule { border: none; border-top: 2px solid #111827; margin: 0 0 3px 0; }
        .header-rule-2 { border: none; border-top: 1px solid #6b7280; margin: 0 0 18px 0; }
        .header-center img { height: 72px; width: 72px; object-fit: contain; }
        .school-name {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            letter-spacing: .5px;
        }
        .letterhead { font-size: 12px; color: #6b7280; margin-top: 3px; }
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

        /* ---- Student Info ---- */
        .student-section {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 18px;
        }
        .student-details {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 10px;
        }
        .student-divider { width: 1px; background: #d1d5db; flex-shrink: 0; align-self: stretch; }
        .student-photo { flex-shrink: 0; text-align: center; width: 72px; }
        .student-photo img { width: 72px; height: 88px; object-fit: cover; border-radius: 4px; border: 1px solid #d1d5db; }
        .student-photo .photo-initials {
            width: 72px; height: 88px; background: #dbeafe; color: #1d4ed8;
            display: flex; align-items: center; justify-content: center;
            border-radius: 4px; font-size: 22px; font-weight: 700; border: 1px solid #bfdbfe;
        }
        .student-photo .photo-label { font-size: 9px; color: #9ca3af; margin-top: 3px; }
        .info-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: .7px; }
        .info-value { font-size: 13px; font-weight: 600; color: #111827; margin-top: 3px; }

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
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
        }
        .grade-a  { background: #dcfce7; color: #166534; }
        .grade-b  { background: #dbeafe; color: #1e40af; }
        .grade-c  { background: #fef9c3; color: #854d0e; }
        .grade-f  { background: #fee2e2; color: #991b1b; }
        tfoot td {
            background: #f3f4f6;
            font-weight: 700;
            font-size: 12px;
            padding: 9px 10px;
            border-top: 2px solid #9ca3af;
        }
        tfoot td.center { text-align: center; }
        .total-grade {
            display: inline-block;
            background: #374151;
            color: #fff;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
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

        {{-- School Header: name left | logo center | letterhead right --}}
        <div class="header">
            <div class="header-left">
                <div class="school-name">{{ school_setting('school_name', 'School Name') }}</div>
            </div>
            <div class="header-center">
                @if(school_logo_url())
                <img src="{{ school_logo_url() }}" alt="Logo">
                @endif
            </div>
            <div class="header-right">
                @if(school_setting('letterhead_text'))
                <div class="letterhead">{{ school_setting('letterhead_text') }}</div>
                @endif
                <div class="report-title">{{ ucwords(str_replace('_', ' ', $report->report_type)) }}</div>
            </div>
        </div>
        <hr class="header-rule">
        <hr class="header-rule-2">

        {{-- Student Info: details on left, photo on right --}}
        <div class="student-section">
            <div class="student-details">
                <div>
                    <div class="info-label">Student Name</div>
                    <div class="info-value">{{ $report->student->full_name }}</div>
                </div>
                <div>
                    <div class="info-label">Student ID</div>
                    <div class="info-value">{{ $report->student->student_id }}</div>
                </div>
                <div>
                    <div class="info-label">Class</div>
                    <div class="info-value">{{ $report->student->class->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="info-label">Term / Year</div>
                    <div class="info-value">{{ $report->term }}, {{ $report->academic_year }}</div>
                </div>
            </div>
            <div class="student-divider"></div>
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
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th class="center">Score</th>
                    <th class="center">Out Of</th>
                    <th class="center">%</th>
                    <th class="center">Grade</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $mark)
                @php
                    $pct = $mark->total_marks > 0
                        ? round(($mark->marks_obtained / $mark->total_marks) * 100, 1)
                        : 0;
                    $badgeClass = match(true) {
                        $pct >= 80 => 'grade-a',
                        $pct >= 60 => 'grade-b',
                        $pct >= 40 => 'grade-c',
                        default    => 'grade-f',
                    };
                @endphp
                <tr>
                    <td>{{ $mark->subject->name ?? 'N/A' }}</td>
                    <td class="center">{{ $mark->marks_obtained }}</td>
                    <td class="center">{{ $mark->total_marks }}</td>
                    <td class="center">{{ $pct }}%</td>
                    <td class="center">
                        <span class="grade-badge {{ $badgeClass }}">{{ $mark->grade ?? '—' }}</span>
                    </td>
                    <td>{{ $mark->remarks ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL / AVERAGE</td>
                    <td class="center">{{ $summary['total_marks'] }}</td>
                    <td class="center">{{ $summary['total_possible'] }}</td>
                    <td class="center">{{ $summary['average_percentage'] }}%</td>
                    <td class="center">
                        <span class="total-grade">{{ $summary['grade'] }}</span>
                    </td>
                    <td>{{ $summary['subject_count'] }} subjects</td>
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
