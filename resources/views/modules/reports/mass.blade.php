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
                    <input type="text" name="academic_year" id="academic_year" required
                           placeholder="e.g. 2025/2026"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

<script>
(function () {
    const classEl = document.getElementById('class_id');
    const termEl  = document.getElementById('term');
    const yearEl  = document.getElementById('academic_year');
    const preview = document.getElementById('studentPreview');
    const previewContent = document.getElementById('previewContent');
    const form    = document.getElementById('massForm');
    const btn     = document.getElementById('downloadBtn');

    function loadPreview() {
        const classId = classEl.value;
        const term    = termEl.value;
        const year    = yearEl.value.trim();
        if (!classId) { preview.classList.add('hidden'); return; }

        const url = `{{ route('api.students-by-class') }}?class_id=${classId}&term=${encodeURIComponent(term)}&academic_year=${encodeURIComponent(year)}`;

        fetch(url)
            .then(r => r.json())
            .then(students => {
                if (!students.length) {
                    previewContent.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> No active students found in this class.';
                    preview.classList.remove('hidden');
                    return;
                }
                const withMarks = students.filter(s => s.has_marks === true).length;
                const total     = students.length;
                previewContent.innerHTML =
                    `<i class="fas fa-users mr-1"></i> <strong>${total}</strong> active student(s) in this class` +
                    (term && year
                        ? ` &mdash; <strong>${withMarks}</strong> have marks for ${term} / ${year} and will be included in the ZIP.`
                        : '. Select term and year to see how many have marks.');
                preview.classList.remove('hidden');
            })
            .catch(() => { preview.classList.add('hidden'); });
    }

    classEl.addEventListener('change', loadPreview);
    termEl.addEventListener('change', loadPreview);
    yearEl.addEventListener('input', loadPreview);

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Generating PDFs…';
        // Re-enable after 30s in case of error
        setTimeout(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-file-archive"></i> Generate &amp; Download ZIP'; }, 30000);
    });
})();
</script>

@if(!request()->header('HX-Request'))
@endsection
@endif
