@if(!request()->header('HX-Request'))
@extends('layouts.app')
@section('title', 'Generate Report Cards')
@section('content')
@endif

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('reports.index') }}"
           hx-get="{{ route('reports.index') }}"
           hx-target="#page-content"
           hx-push-url="true"
           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Generate Report Cards</h1>
            <p class="text-sm text-gray-500 mt-0.5">Select a class, term, year and report type — students load automatically</p>
        </div>
    </div>

    {{-- Flash / Error --}}
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center gap-2 text-sm">
        <i class="fas fa-exclamation-circle text-red-500 shrink-0"></i>
        {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3">
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-filter text-blue-500"></i>
                Filter
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- Class --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select id="filter_class_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select Class —</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Term --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Term <span class="text-red-500">*</span>
                    </label>
                    <select id="filter_term"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select Term —</option>
                        <option value="Term 1">Term 1</option>
                        <option value="Term 2">Term 2</option>
                        <option value="Term 3">Term 3</option>
                        <option value="Semester 1">Semester 1</option>
                        <option value="Semester 2">Semester 2</option>
                    </select>
                </div>

                {{-- Academic Year --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Academic Year <span class="text-red-500">*</span>
                    </label>
                    <select id="filter_academic_year"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select Year —</option>
                        @php
                            $cy = date('Y');
                            $yearOptions = ["{$cy}-" . ($cy+1), ($cy-1) . "-{$cy}", ($cy+1) . "-" . ($cy+2)];
                        @endphp
                        @foreach($yearOptions as $yo)
                        <option value="{{ $yo }}">{{ $yo }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Report Type --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Report Type <span class="text-red-500">*</span>
                    </label>
                    <select id="filter_report_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select Type —</option>
                        <option value="report_card">Report Card</option>
                        <option value="progress_report">Progress Report</option>
                        <option value="transcript">Transcript</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Student List (AJAX-populated) --}}
    <div id="student-list-section" class="hidden">
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800" id="student-list-heading">Students</h2>
                <span id="student-count" class="text-xs text-gray-400"></span>
            </div>

            {{-- Loading skeleton --}}
            <div id="student-list-loading" class="hidden p-6">
                <div class="space-y-3">
                    @for($i=0; $i<4; $i++)
                    <div class="flex items-center gap-4 animate-pulse">
                        <div class="h-9 w-9 bg-gray-200 rounded-full shrink-0"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                            <div class="h-2.5 bg-gray-100 rounded w-1/5"></div>
                        </div>
                        <div class="h-8 w-32 bg-gray-200 rounded-lg"></div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Student table --}}
            <div id="student-list-content" class="hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-8">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student ID</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Marks</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body" class="bg-white divide-y divide-gray-100">
                        {{-- Rows injected by JS --}}
                    </tbody>
                </table>
            </div>

            {{-- Empty state --}}
            <div id="student-list-empty" class="hidden text-center py-12">
                <i class="fas fa-users text-3xl text-gray-300 mb-3"></i>
                <p class="text-gray-400 text-sm">No active students found in this class.</p>
            </div>

            {{-- Error state --}}
            <div id="student-list-error" class="hidden px-6 py-4">
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle shrink-0"></i>
                    <span id="student-list-error-msg">Failed to load students.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Prompt when nothing selected --}}
    <div id="student-list-prompt" class="text-center py-8 text-gray-400">
        <i class="fas fa-arrow-up text-2xl mb-2 opacity-50"></i>
        <p class="text-sm">Fill in all four filters above to load the student list</p>
    </div>
</div>

<script>
(function () {
    var classEl  = document.getElementById('filter_class_id');
    var termEl   = document.getElementById('filter_term');
    var yearEl   = document.getElementById('filter_academic_year');
    var typeEl   = document.getElementById('filter_report_type');

    var section   = document.getElementById('student-list-section');
    var prompt    = document.getElementById('student-list-prompt');
    var loading   = document.getElementById('student-list-loading');
    var content   = document.getElementById('student-list-content');
    var emptyEl   = document.getElementById('student-list-empty');
    var errorEl   = document.getElementById('student-list-error');
    var errorMsg  = document.getElementById('student-list-error-msg');
    var tbody     = document.getElementById('student-table-body');
    var heading   = document.getElementById('student-list-heading');
    var countEl   = document.getElementById('student-count');

    if (!classEl) return;
    if (classEl.dataset.rcBound) return;
    classEl.dataset.rcBound = 'true';

    function allFilled() {
        return classEl.value && termEl.value && yearEl.value && typeEl.value;
    }

    function showOnly(id) {
        ['student-list-loading','student-list-content','student-list-empty','student-list-error']
            .forEach(function(k){ document.getElementById(k).classList.add('hidden'); });
        document.getElementById(id).classList.remove('hidden');
    }

    function buildViewUrl(studentId) {
        var base = '{{ route('reports.view-or-generate') }}';
        return base
            + '?student_id=' + encodeURIComponent(studentId)
            + '&term='          + encodeURIComponent(termEl.value)
            + '&academic_year=' + encodeURIComponent(yearEl.value)
            + '&report_type='   + encodeURIComponent(typeEl.value);
    }

    function loadStudents() {
        if (!allFilled()) {
            section.classList.add('hidden');
            prompt.classList.remove('hidden');
            return;
        }

        prompt.classList.add('hidden');
        section.classList.remove('hidden');
        showOnly('student-list-loading');

        var url = '{{ route('api.students-by-class') }}'
            + '?class_id='      + encodeURIComponent(classEl.value)
            + '&term='          + encodeURIComponent(termEl.value)
            + '&academic_year=' + encodeURIComponent(yearEl.value);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function(students) {
                if (!students.length) {
                    showOnly('student-list-empty');
                    countEl.textContent = '';
                    return;
                }

                // Build selected class name for heading
                var selectedOpt = classEl.options[classEl.selectedIndex];
                heading.textContent = 'Students in ' + (selectedOpt ? selectedOpt.text : 'Class');
                countEl.textContent = students.length + ' student' + (students.length !== 1 ? 's' : '');

                tbody.innerHTML = '';
                students.forEach(function(s, i) {
                    var hasMarks = s.has_marks;
                    var viewUrl  = buildViewUrl(s.id);
                    var periods  = s.available_periods || [];

                    // Build a helpful tooltip: when no marks for selected period but marks exist elsewhere
                    var noMarksTitle = 'No marks entered for this term/year.';
                    if (hasMarks === false && periods.length > 0) {
                        noMarksTitle = 'Marks available for: ' + periods.join(', ') + '. Please change the Term or Academic Year filter.';
                    }

                    var marksBadge = hasMarks === true
                        ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Has Marks</span>'
                        : (hasMarks === false
                            ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 cursor-help" title="' + noMarksTitle + '">'
                              + '<i class="fas fa-times-circle mr-1"></i>No Marks'
                              + (periods.length > 0 ? ' <i class="fas fa-info-circle ml-1 text-red-500"></i>' : '')
                              + '</span>'
                            : '<span class="text-gray-400 text-xs">—</span>');

                    var actionBtn = hasMarks !== false
                        ? '<a href="' + viewUrl + '" '
                          + 'hx-get="' + viewUrl + '" '
                          + 'hx-target="#page-content" '
                          + 'hx-push-url="true" '
                          + 'class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">'
                          + '<i class="fas fa-eye"></i> View Report Card</a>'
                        : '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed" title="' + noMarksTitle + '">'
                          + '<i class="fas fa-ban"></i> No Marks</span>';

                    var initials = s.name.split(' ').map(function(w){ return w[0] || ''; }).slice(0,2).join('').toUpperCase();

                    tbody.innerHTML +=
                        '<tr class="hover:bg-gray-50 transition-colors">'
                        + '<td class="px-4 py-3 text-xs text-gray-400">' + (i+1) + '</td>'
                        + '<td class="px-4 py-3">'
                        +   '<div class="flex items-center gap-3">'
                        +     '<div class="h-8 w-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0">' + initials + '</div>'
                        +     '<span class="font-medium text-gray-900">' + s.name + '</span>'
                        +   '</div>'
                        + '</td>'
                        + '<td class="px-4 py-3 text-sm text-gray-500">' + (s.student_id || '—') + '</td>'
                        + '<td class="px-4 py-3 text-center">' + marksBadge + '</td>'
                        + '<td class="px-4 py-3 text-right">' + actionBtn + '</td>'
                        + '</tr>';
                });

                showOnly('student-list-content');
            })
            .catch(function(err) {
                console.error('Load students error:', err);
                errorMsg.textContent = 'Failed to load students. Please try again.';
                showOnly('student-list-error');
            });
    }

    [classEl, termEl, yearEl, typeEl].forEach(function(el) {
        el.addEventListener('change', loadStudents);
    });
})();
</script>

@if(!request()->header('HX-Request'))
@endsection
@endif
