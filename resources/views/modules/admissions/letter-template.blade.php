{{-- This is the letter template content used for rendering --}}
<div class="letter-content">
    {{-- School Header (same format as Report Card) --}}
    <div style="text-align: center; margin-bottom: 8px;">
        @php
            $logoLeft = $schoolSettings['logo_left_text'] ?? null;
            $logoRight = $schoolSettings['logo_right_text'] ?? null;
            $logoUrl = $schoolSettings['school_logo'] ?? null;
            
            if (!$logoLeft && !$logoRight) {
                $nameParts = explode(' ', $schoolSettings['school_name'] ?? 'School Name', 2);
                $logoLeft = $nameParts[0];
                $logoRight = $nameParts[1] ?? '';
            }
        @endphp
        
        {{-- Brand Line: Left Text | Logo | Right Text --}}
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 2px;">
            <span style="font-size: 16px; font-weight: 700; color: #111827; letter-spacing: 0.3px;">{{ $logoLeft }}</span>
            @if($logoUrl)
            <img src="{{ asset('storage/' . $logoUrl) }}" alt="Logo" style="height: 48px; width: 48px; object-fit: contain;">
            @endif
            <span style="font-size: 16px; font-weight: 700; color: #111827; letter-spacing: 0.3px;">{{ $logoRight }}</span>
        </div>
        
        {{-- Letterhead Details --}}
        @if($schoolSettings['letterhead_text'])
        <div style="font-size: 11px; color: #6b7280; margin-top: 2px; white-space: pre-line; line-height: 1.3;">{{ $schoolSettings['letterhead_text'] }}</div>
        @endif
    </div>
    
    <hr style="border: none; border-top: 2px solid #111827; margin: 0 0 2px 0;">
    <hr style="border: none; border-top: 1px solid #6b7280; margin: 0 0 12px 0;">

    {{-- Admission Letter Title (Centered) --}}
    <div style="text-align: center; margin-bottom: 15px;">
        <h1 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827; text-transform: uppercase; letter-spacing: 1.5px;">ADMISSION LETTER</h1>
        <p style="margin: 2px 0 0 0; font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">{{ now()->format('d F Y') }}</p>
    </div>

    {{-- Recipient Details --}}
    <div style="margin-bottom: 12px; font-size: 12px;">
        <p style="margin: 2px 0; font-weight: 600;">{{ $student->full_name }}</p>
        @if($student->address)
        <p style="margin: 2px 0;">{{ $student->address }}</p>
        @endif
        @if($student->parent_name)
        <p style="margin: 2px 0;">c/o {{ $student->parent_name }}</p>
        @endif
    </div>

    {{-- Salutation --}}
    <div style="margin-bottom: 10px;">
        <p style="margin: 0; font-size: 12px;">Dear {{ $student->first_name }},</p>
    </div>

    {{-- Letter Body --}}
    <div style="margin-bottom: 15px; line-height: 1.5; font-size: 12px; color: #333; text-align: justify;">
        
        {{-- Opening Paragraph --}}
        <p style="margin: 0 0 8px 0;">
            We are delighted to inform you that you have been selected for admission to {{ $schoolSettings['school_name'] ?? 'our school' }}. This is a recognition of your academic excellence and the qualities we value in our students.
        </p>

        {{-- Details Paragraph --}}
        <p style="margin: 0 0 8px 0;">
            Your admission details are as follows:
        </p>

        {{-- Admission Details Box --}}
        <div style="margin: 8px 10px 12px 10px; padding: 10px; background-color: #f0f4f8; border: 1px solid #d0dce6; border-radius: 3px; font-size: 11px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 4px 0; border-bottom: 1px solid #e0e8f0;"><strong>Full Name:</strong></td>
                    <td style="padding: 4px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; border-bottom: 1px solid #e0e8f0;"><strong>Student ID:</strong></td>
                    <td style="padding: 4px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->student_id }}</td>
                </tr>
                @if($student->admission_number)
                <tr>
                    <td style="padding: 4px 0; border-bottom: 1px solid #e0e8f0;"><strong>Admission Number:</strong></td>
                    <td style="padding: 4px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->admission_number }}</td>
                </tr>
                @endif
                @if($student->class)
                <tr>
                    <td style="padding: 4px 0; border-bottom: 1px solid #e0e8f0;"><strong>Class:</strong></td>
                    <td style="padding: 4px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->class->name }}</td>
                </tr>
                @if($student->class->stream)
                <tr>
                    <td style="padding: 4px 0;"><strong>Stream:</strong></td>
                    <td style="padding: 4px 0;">{{ $student->class->stream->name }}</td>
                </tr>
                @endif
                @endif
            </table>
        </div>

        {{-- Requirements Paragraph --}}
        <p style="margin: 0 0 6px 0;">
            To finalize your admission, please ensure that you have:
        </p>

        <div style="margin: 0 0 8px 0; padding-left: 15px; font-size: 11px;">
            <p style="margin: 2px 0;">• Completed all admission documentation</p>
            <p style="margin: 2px 0;">• Submitted certified copies of your academic records</p>
            <p style="margin: 2px 0;">• Paid the required admission and registration fees</p>
        </div>

        {{-- Contact Information Paragraph --}}
        <p style="margin: 0 0 8px 0;">
            Should you require any clarification, please contact our admissions office. We look forward to welcoming you to our school community.
        </p>

        @if($remarks)
        <p style="margin: 8px 0; padding: 8px; background-color: #fffbea; border-left: 3px solid #f59e0b; font-size: 11px;">
            <strong>Additional Information:</strong> {{ $remarks }}
        </p>
        @endif

        {{-- Formal Closing --}}
        <p style="margin: 10px 0 0 0; font-size: 12px;">Yours sincerely,</p>

    </div>

    {{-- Signature Area --}}
    <div style="margin-top: 20px; padding-top: 12px; border-top: 1px solid #ddd;">
        <div style="margin-bottom: 5px;">
            <p style="margin: 0; height: 40px;"></p>
            <p style="margin: 2px 0 0 0; font-weight: bold; font-size: 12px;">{{ school_setting('headteacher_name') ?? 'Headteacher' }}</p>
            <p style="margin: 0; font-size: 10px; color: #666;">{{ school_setting('school_name') ?? 'School' }}</p>
        </div>
        <p style="margin: 6px 0 0 0; font-size: 9px; color: #999; text-align: center;">
            Official admission letter issued on {{ now()->format('d F Y') }}<br>
            {{ $student->student_id }} | Admission Letter
        </p>
    </div>
</div>
