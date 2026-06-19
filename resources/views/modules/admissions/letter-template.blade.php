{{-- This is the letter template content used for rendering --}}
<div class="letter-content">
    {{-- School Header --}}
    <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1565C0; padding-bottom: 20px;">
        @php
            $logo = $schoolSettings['school_logo'] ?? null;
            $schoolName = $schoolSettings['school_name'] ?? 'Academic School';
            $schoolPhone = $schoolSettings['school_phone'] ?? '';
            $schoolEmail = $schoolSettings['school_email'] ?? '';
            $schoolAddress = $schoolSettings['school_address'] ?? '';
        @endphp

        @if($logo)
        <img src="{{ asset('storage/' . $logo) }}" alt="{{ $schoolName }}" 
             style="height: 60px; margin-bottom: 10px;">
        @endif

        <h1 style="margin: 10px 0; color: #1565C0; font-size: 24px;">{{ $schoolName }}</h1>
        <p style="margin: 5px 0; color: #666; font-size: 13px;">
            {{ $schoolAddress }}
        </p>
        <p style="margin: 5px 0; color: #666; font-size: 12px;">
            {{ $schoolPhone }}{{ $schoolEmail ? ' | ' . $schoolEmail : '' }}
        </p>
    </div>

    {{-- Date and Reference --}}
    <div style="margin-bottom: 30px;">
        <p style="margin: 5px 0; color: #666; font-size: 12px;">
            <strong>Date:</strong> {{ now()->format('d F Y') }}
        </p>
    </div>

    {{-- Recipient Details --}}
    <div style="margin-bottom: 30px;">
        <p style="margin: 5px 0; font-size: 14px;"><strong>{{ $student->full_name }}</strong></p>
        @if($student->address)
        <p style="margin: 5px 0; font-size: 14px;">{{ $student->address }}</p>
        @endif
        @if($student->parent_name)
        <p style="margin: 5px 0; font-size: 14px;">c/o {{ $student->parent_name }}</p>
        @endif
    </div>

    {{-- Salutation --}}
    <div style="margin-bottom: 20px;">
        <p style="margin: 0; font-size: 14px;">Dear {{ $student->first_name }},</p>
    </div>

    {{-- Letter Body --}}
    <div style="margin-bottom: 30px; line-height: 1.8; font-size: 13px; color: #333; text-align: justify;">
        
        {{-- Opening Paragraph --}}
        <p style="margin: 0 0 15px 0;">
            We are delighted to inform you that you have been selected for admission to {{ $schoolSettings['school_name'] ?? 'our school' }}.
            This is a recognition of your academic excellence, potential, and the qualities that we value in our students. 
            We are confident that you will make a positive contribution to our school community and that your time with us will be 
            enriching and memorable.
        </p>

        {{-- Details Paragraph --}}
        <p style="margin: 0 0 15px 0;">
            Your admission details are as follows:
        </p>

        {{-- Admission Details Box --}}
        <div style="margin: 15px 20px 20px 20px; padding: 15px; background-color: #f0f4f8; border: 1px solid #d0dce6; border-radius: 4px; font-size: 13px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;"><strong>Full Name:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;"><strong>Student ID:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->student_id }}</td>
                </tr>
                @if($student->admission_number)
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;"><strong>Admission Number:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->admission_number }}</td>
                </tr>
                @endif
                @if($student->class)
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;"><strong>Class:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->class->name }}</td>
                </tr>
                @if($student->class->stream)
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;"><strong>Stream:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e0e8f0;">{{ $student->class->stream->name }}</td>
                </tr>
                @endif
                @endif
                <tr>
                    <td style="padding: 8px 0;"><strong>Date of Birth:</strong></td>
                    <td style="padding: 8px 0;">{{ $student->date_of_birth?->format('d F Y') ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Admission Date:</strong></td>
                    <td style="padding: 8px 0;">{{ $student->admission_date?->format('d F Y') ?? now()->format('d F Y') }}</td>
                </tr>
            </table>
        </div>

        {{-- Requirements Paragraph --}}
        <p style="margin: 0 0 15px 0;">
            To finalize your admission, please ensure that you have:
        </p>

        <div style="margin: 0 0 15px 0; padding-left: 20px;">
            <p style="margin: 5px 0;">• Completed all admission documentation</p>
            <p style="margin: 5px 0;">• Submitted certified copies of your academic records</p>
            <p style="margin: 5px 0;">• Paid the required admission and registration fees</p>
            <p style="margin: 5px 0;">• Attended the orientation programme (if applicable)</p>
        </div>

        {{-- Contact Information Paragraph --}}
        <p style="margin: 0 0 15px 0;">
            Should you require any clarification or have questions regarding your admission, please do not hesitate to contact our 
            admissions office. We are here to assist you and ensure a smooth transition into our school community.
        </p>

        {{-- Closing Remarks --}}
        <p style="margin: 0 0 15px 0;">
            Once again, we are pleased to welcome you to {{ school_setting('school_name') ?? 'our school' }}. 
            We look forward to supporting your academic and personal growth throughout your time with us.
        </p>

        @if($remarks)
        <p style="margin: 15px 0; padding: 10px; background-color: #fffbea; border-left: 3px solid #f59e0b; font-size: 12px;">
            <strong>Additional Information:</strong><br>
            {{ $remarks }}
        </p>
        @endif

        {{-- Formal Closing --}}
        <p style="margin: 15px 0; font-size: 13px;">Yours sincerely,</p>

    </div>

    {{-- Signature Area --}}
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
        <div style="margin-bottom: 10px;">
            <p style="margin: 0; height: 60px;"></p>
            <p style="margin: 5px 0 0 0; font-weight: bold; font-size: 13px;">{{ school_setting('headteacher_name') ?? 'Headteacher' }}</p>
            <p style="margin: 0; font-size: 11px; color: #666;">{{ school_setting('school_name') ?? 'School' }}</p>
        </div>
        <p style="margin: 10px 0 0 0; font-size: 10px; color: #999; text-align: center;">
            This is an official admission letter issued on {{ now()->format('d F Y') }}<br>
            {{ $student->student_id }} | Admission Letter
        </p>
    </div>
</div>
