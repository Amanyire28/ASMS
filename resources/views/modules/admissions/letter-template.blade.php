{{-- This is the letter template content used for rendering --}}
<div class="letter-content" style="font-size: 12px; line-height: 1.4;">
    {{-- School Header (same format as Report Card) --}}
    <div style="text-align: center; margin-bottom: 6px;">
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
        <div style="display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 1px;">
            <span style="font-size: 14px; font-weight: 700; color: #111827;">{{ $logoLeft }}</span>
            @if($logoUrl)
            <img src="{{ asset('storage/' . $logoUrl) }}" alt="Logo" style="height: 40px; width: 40px; object-fit: contain;">
            @endif
            <span style="font-size: 14px; font-weight: 700; color: #111827;">{{ $logoRight }}</span>
        </div>
        
        {{-- Letterhead Details --}}
        @if($schoolSettings['letterhead_text'])
        <div style="font-size: 10px; color: #6b7280; margin-top: 2px; line-height: 1.2; white-space: pre-line;">{{ $schoolSettings['letterhead_text'] }}</div>
        @endif
    </div>
    
    <hr style="border: none; border-top: 2px solid #111827; margin: 2px 0 1px 0;">
    <hr style="border: none; border-top: 1px solid #6b7280; margin: 0 0 10px 0;">

    {{-- Admission Letter Title (Centered) --}}
    <div style="text-align: center; margin-bottom: 12px;">
        <h1 style="margin: 0; font-size: 14px; font-weight: 700; color: #111827; text-transform: uppercase; letter-spacing: 1px;">ADMISSION LETTER</h1>
        <p style="margin: 1px 0 0 0; font-size: 10px; color: #6b7280;">{{ now()->format('d F Y') }}</p>
    </div>

    {{-- Salutation --}}
    <div style="margin-bottom: 6px;">
        <p style="margin: 0; font-size: 11px;">Dear {{ $student->first_name }},</p>
    </div>

    {{-- Letter Body --}}
    <div style="margin-bottom: 10px; line-height: 1.4; font-size: 11px; color: #333;">
        
        {{-- Opening Paragraph --}}
        <p style="margin: 0 0 5px 0;">
            We are delighted to inform you that you have been selected for admission to {{ $schoolSettings['school_name'] ?? 'our school' }}. This is a recognition of your academic excellence and the qualities we value in our students.
        </p>

        {{-- Details Paragraph --}}
        <p style="margin: 0 0 4px 0;">
            Your admission details are as follows:
        </p>

        {{-- Student Details (simple format) --}}
        <div style="margin: 4px 0 6px 0; font-size: 11px; line-height: 1.4;">
            <p style="margin: 1px 0;"><strong>Full Name:</strong> {{ $student->full_name }}</p>
            <p style="margin: 1px 0;"><strong>Student ID:</strong> {{ $student->student_id }}</p>
            @if($student->admission_number)
            <p style="margin: 1px 0;"><strong>Admission Number:</strong> {{ $student->admission_number }}</p>
            @endif
            @if($student->class)
            <p style="margin: 1px 0;"><strong>Class:</strong> {{ $student->class->name }}</p>
            @if($student->class->stream)
            <p style="margin: 1px 0;"><strong>Stream:</strong> {{ $student->class->stream->name }}</p>
            @endif
            @endif
        </div>

        {{-- Requirements Paragraph --}}
        <p style="margin: 0 0 4px 0; font-weight: 600;">
            To finalize your admission:
        </p>

        <div style="margin: 0 0 6px 0; padding-left: 12px; font-size: 10px;">
            <p style="margin: 1px 0;">• Complete admission documentation</p>
            <p style="margin: 1px 0;">• Submit certified academic records</p>
            <p style="margin: 1px 0;">• Pay admission and registration fees</p>
        </div>

        {{-- Contact Information Paragraph --}}
        <p style="margin: 0 0 6px 0;">
            Should you have any questions, please contact our admissions office. We look forward to welcoming you.
        </p>

        @if($remarks)
        <p style="margin: 6px 0; padding: 5px; background-color: #fffbea; border-left: 3px solid #f59e0b; font-size: 10px;">
            <strong>Note:</strong> {{ $remarks }}
        </p>
        @endif

        {{-- Formal Closing --}}
        <p style="margin: 6px 0 2px 0; font-size: 11px;">Yours sincerely,</p>

    </div>

    {{-- Signature Area --}}
    <div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid #ddd;">
        <div>
            <p style="margin: 0; height: 30px;"></p>
            <p style="margin: 2px 0 0 0; font-weight: bold; font-size: 11px;">{{ school_setting('headteacher_name') ?? 'Headteacher' }}</p>
            <p style="margin: 0; font-size: 9px; color: #666;">{{ school_setting('school_name') ?? 'School' }}</p>
        </div>
        <p style="margin: 4px 0 0 0; font-size: 8px; color: #999; text-align: center;">
            Issued {{ now()->format('d F Y') }} | {{ $student->student_id }}
        </p>
    </div>
</div>
