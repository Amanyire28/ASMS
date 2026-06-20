@if(!request()->header('HX-Request'))
@extends('layouts.app')
@section('title', 'Mass Report Download')
@section('content')
@endif

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('reports.index') }}"
           hx-get="{{ route('reports.index') }}" hx-target="#page-content" hx-push-url="true"
           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mass Report Download</h1>
            <p class="text-sm text-gray-500 mt-0.5">Generate and download all student report cards for a class as a single ZIP file</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center gap-2 text-sm">
        <i class="fas fa-exclamation-circle text-red-500 shrink-0"></i>
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('reports.mass-download') }}" method="POST" id="massForm">
        @csrf

        <div class="bg-white shadow-sm rounded-xl border border-gray-200">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-filter text-blue-500"></i> Select Parameters
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                {{-- Class --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select class…</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Term --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term <span class="text-red-500">*</span></label>
                    <select name="term" id="term" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select term…</option>
                        <option value="Term 1">Term 1</option>
                        <option value="Term 2">Term 2</option>
                        <option value="Term 3">Term 3</option>
                    </select>
                </div>

                {{-- Academic Year --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500">*</span></label>
                    <select name="academic_year" id="academic_year" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select year…</option>
                        @php
                            $y = (int) date('Y');
                            $yearOptions = [($y-1).'-'.$y, $y.'-'.($y+1), ($y+1).'-'.($y+2)];
                        @endphp
                        @foreach($yearOptions as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Report Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Type <span class="text-red-500">*</span></label>
                    <select name="report_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="report_card">Report Card</option>
                        <option value="progress_report">Progress Report</option>
                        <option value="transcript">Transcript</option>
                    </select>
                </div>
            </div>

            {{-- Student preview --}}
            <div id="studentPreview" class="px-6 pb-4 hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div id="previewContent" class="text-sm text-blue-800"></div>
                </div>
            </div>

            <div class="px-6 pb-6 flex items-center gap-3">
                <button type="submit" id="downloadBtn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700
                               text-white font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-file-archive"></i>
                    Generate &amp; Download ZIP
                </button>
                <span class="text-xs text-gray-500">Reports without marks will be skipped automatically.</span>
            </div>
        </div>
    </form>

    {{-- How it works --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-500"></i> How it works
        </h3>
        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
            <li>Select the class, term, academic year and report type above.</li>
            <li>Click <strong>Generate &amp; Download ZIP</strong>.</li>
            <li>The system generates a PDF report card for every active student in the class that has marks for the selected period.</li>
            <li>All PDFs are bundled into a single ZIP file named after the class and term.</li>
            <li>Extract the ZIP — each file is named <code class="bg-gray-100 px-1 rounded">StudentID_StudentName.pdf</code>.</li>
        </ol>
    </div>

</div>

{{-- Progress overlay --}}
<div id="progressOverlay"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-80 text-center">
        {{-- Spinning ring --}}
        <div class="relative w-24 h-24 mx-auto mb-5">
            <svg class="w-24 h-24 -rotate-90" viewBox="0 0 96 96">
                <circle cx="48" cy="48" r="40" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                <circle id="progressRing" cx="48" cy="48" r="40" fill="none"
                        stroke="#16a34a" stroke-width="8"
                        stroke-linecap="round"
                        stroke-dasharray="251.2"
                        stroke-dashoffset="251.2"
                        style="transition: stroke-dashoffset 0.4s ease;"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span id="progressPct" class="text-2xl font-bold text-gray-900">0%</span>
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-900 mb-1">Generating Reports</h3>
        <p id="progressLabel" class="text-sm text-gray-500 mb-4">Starting…</p>

        {{-- Step bar --}}
        <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
            <div id="progressBar" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width:0%"></div>
        </div>

        <p id="progressSub" class="text-xs text-gray-400">Please keep this tab open</p>
    </div>
</div>

<script>
(function () {
    const classEl  = document.getElementById('class_id');
    const termEl   = document.getElementById('term');
    const yearEl   = document.getElementById('academic_year');
    const preview  = document.getElementById('studentPreview');
    const previewContent = document.getElementById('previewContent');
    const form     = document.getElementById('massForm');
    const btn      = document.getElementById('downloadBtn');
    const overlay  = document.getElementById('progressOverlay');
    const ring     = document.getElementById('progressRing');
    const pctEl    = document.getElementById('progressPct');
    const labelEl  = document.getElementById('progressLabel');
    const barEl    = document.getElementById('progressBar');
    const subEl    = document.getElementById('progressSub');

    const CIRCUMFERENCE = 251.2; // 2 * π * 40
    let studentCount = 0;
    let progressTimer = null;

    // ── Preview ──────────────────────────────────────────────────────
    function loadPreview() {
        const classId = classEl.value;
        const term    = termEl.value;
        const year    = yearEl.value;
        if (!classId) { preview.classList.add('hidden'); studentCount = 0; return; }

        const url = `{{ route('api.students-by-class') }}?class_id=${classId}&term=${encodeURIComponent(term)}&academic_year=${encodeURIComponent(year)}`;

        fetch(url)
            .then(r => r.json())
            .then(students => {
                studentCount = students.filter(s => s.has_marks === true).length || students.length;
                const total  = students.length;
                const withMarks = students.filter(s => s.has_marks === true).length;

                if (!total) {
                    previewContent.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> No active students found in this class.';
                } else {
                    previewContent.innerHTML =
                        `<i class="fas fa-users mr-1"></i> <strong>${total}</strong> student(s) in this class` +
                        (term && year
                            ? ` — <strong>${withMarks}</strong> have marks for ${term} / ${year} and will be included.`
                            : '. Select term and year to see how many have marks.');
                }
                preview.classList.remove('hidden');
            })
            .catch(() => { preview.classList.add('hidden'); });
    }

    classEl.addEventListener('change', loadPreview);
    termEl.addEventListener('change', loadPreview);
    yearEl.addEventListener('change', loadPreview);

    // ── Progress animation ───────────────────────────────────────────
    function setProgress(pct) {
        const clamped = Math.min(Math.max(pct, 0), 100);
        ring.style.strokeDashoffset = CIRCUMFERENCE - (CIRCUMFERENCE * clamped / 100);
        barEl.style.width = clamped + '%';
        pctEl.textContent = Math.round(clamped) + '%';
    }

    function startProgress(total) {
        // Estimate ~4 seconds per PDF (conservative)
        const secsPerPdf = 4;
        const totalSecs  = Math.max(total * secsPerPdf, 8);
        const tickMs     = 300;
        const increment  = 90 / (totalSecs * (1000 / tickMs)); // reach 90% over estimated time
        let current      = 0;
        let done         = 0;

        setProgress(0);
        labelEl.textContent = `Generating ${total} report${total !== 1 ? 's' : ''}…`;
        subEl.textContent   = 'Please keep this tab open';

        progressTimer = setInterval(() => {
            current += increment;
            done = Math.min(current, 90);
            setProgress(done);

            // Update label with rough student estimate
            const estimated = Math.round((done / 90) * total);
            labelEl.textContent = `Processing student ${Math.min(estimated + 1, total)} of ${total}…`;
        }, tickMs);
    }

    function finishProgress() {
        clearInterval(progressTimer);
        setProgress(100);
        labelEl.textContent = 'Download starting…';
        subEl.textContent   = 'Your ZIP file is on its way!';
        // Hide overlay after a short delay
        setTimeout(() => {
            overlay.classList.add('hidden');
            overlay.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-file-archive mr-2"></i>Generate &amp; Download ZIP';
        }, 1800);
    }

    // ── Form submit ──────────────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const total = studentCount || 5;

        // Show overlay
        overlay.classList.remove('hidden');
        overlay.style.display = 'flex';
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-4 w-4 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Working…';

        startProgress(total);

        // Use a hidden iframe trick so we can detect when download starts
        // Create a hidden iframe target for the form
        const iframeName = 'download_frame_' + Date.now();
        const iframe = document.createElement('iframe');
        iframe.name  = iframeName;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        // Submit form into iframe — when it responds (download or redirect), we know it's done
        form.target = iframeName;

        // Poll: when iframe gets content (error redirect) or download fires, finish
        // For download responses the iframe stays blank, so use a timeout approach
        // Detect via cookie set by server on completion OR fallback timer
        const maxWait    = 300000; // 5 min max
        const checkEvery = 500;
        let waited       = 0;

        const pollDone = setInterval(() => {
            waited += checkEvery;

            // Check if iframe got a redirect (error case — it will have content)
            try {
                const iDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (iDoc && iDoc.body && iDoc.body.innerHTML.length > 10) {
                    clearInterval(pollDone);
                    finishProgress();
                    form.target = '_self';
                    document.body.removeChild(iframe);
                }
            } catch (ex) { /* cross-origin — means download is streaming, which is success */ }

            // Safety: after estimated time + 30s buffer, assume done
            const estimatedMs = (total * 4 + 30) * 1000;
            if (waited >= estimatedMs || waited >= maxWait) {
                clearInterval(pollDone);
                finishProgress();
                form.target = '_self';
                try { document.body.removeChild(iframe); } catch(e) {}
            }
        }, checkEvery);

        // Actually submit
        form.submit();
    });
})();
</script>

@if(!request()->header('HX-Request'))
@endsection
@endif
