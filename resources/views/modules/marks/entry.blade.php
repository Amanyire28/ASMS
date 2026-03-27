@extends('layouts.app')

@section('title', 'Marks Entry')

@section('content')
<div class="px-4 py-6 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Marks Entry</h1>
            <p class="text-sm text-gray-500 mt-1">Select a class, subject, term and year to load the student grid.</p>
        </div>
        <a href="{{ route('marks.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors">
            <i class="fas fa-list mr-2"></i> View All Marks
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ── Step 1: Selector ─────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <span class="w-7 h-7 rounded-full bg-maroon text-white text-xs flex items-center justify-center mr-2 font-bold">1</span>
            Select Class, Subject, Term &amp; Year
        </h2>

        <form method="POST" action="{{ route('marks.entry') }}" id="selectorForm">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Class --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select name="class_id" id="classSelect" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">— Select Class —</option>
                        @foreach($classes as $cls)
                        <option value="{{ $cls->id }}"
                            {{ (isset($selection['class_id']) && $selection['class_id'] == $cls->id) ? 'selected' : '' }}>
                            {{ $cls->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Subject (populated via AJAX) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select name="subject_id" id="subjectSelect" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">— Select Class First —</option>
                        @if(isset($classSubjects))
                        @foreach($classSubjects as $sub)
                        <option value="{{ $sub->id }}"
                            {{ (isset($selection['subject_id']) && $selection['subject_id'] == $sub->id) ? 'selected' : '' }}>
                            {{ $sub->name }}{{ $sub->code ? ' ('.$sub->code.')' : '' }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </div>

                {{-- Term --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Term <span class="text-red-500">*</span>
                    </label>
                    <select name="term" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">— Select Term —</option>
                        @foreach($terms as $t)
                        <option value="{{ $t }}"
                            {{ (isset($selection['term']) && $selection['term'] == $t) ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Academic Year --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Academic Year <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_year" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">— Select Year —</option>
                        @foreach($years as $y)
                        <option value="{{ $y }}"
                            {{ (isset($selection['academic_year']) && $selection['academic_year'] == $y) ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit"
                    class="inline-flex items-center px-5 py-2 bg-maroon hover:bg-maroon-dark
                           text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-users mr-2"></i> Load Students
                </button>
            </div>
        </form>
    </div>

    {{-- ── Step 2: Student Grid (shown only when students were loaded) ── --}}
    @isset($students)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1 flex items-center">
            <span class="w-7 h-7 rounded-full bg-maroon text-white text-xs flex items-center justify-center mr-2 font-bold">2</span>
            Enter Marks &mdash;
            <span class="ml-1 font-normal text-gray-600 dark:text-gray-400">
                {{ $class->name }} &middot; {{ $subject->name }}
                &middot; {{ $selection['term'] }} &middot; {{ $selection['academic_year'] }}
            </span>
        </h2>
        <p class="text-sm text-gray-500 mb-4 ml-9">
            {{ $students->count() }} student(s) enrolled. Existing marks are pre-filled.
        </p>

        @if($students->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-user-slash text-4xl mb-3 block"></i>
            <p>No active students found in this class.</p>
        </div>
        @else
        <form method="POST" action="{{ route('marks.store.multiple') }}">
            @csrf
            <input type="hidden" name="class_id"      value="{{ $selection['class_id'] }}">
            <input type="hidden" name="subject_id"    value="{{ $selection['subject_id'] }}">
            <input type="hidden" name="term"           value="{{ $selection['term'] }}">
            <input type="hidden" name="academic_year"  value="{{ $selection['academic_year'] }}">

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase w-8">#</th>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Student</th>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase w-32">Marks Obtained</th>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase w-24">Out Of</th>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase w-28">% / Grade</th>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($students as $i => $student)
                        @php $existing = $existingMarks[$student->id] ?? null; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors" data-row="{{ $i }}">
                            <td class="py-3 px-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-3 px-3">
                                <input type="hidden" name="marks[{{ $i }}][student_id]" value="{{ $student->id }}">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-maroon text-white text-xs flex items-center
                                                justify-center font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($student->first_name ?? '?', 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $student->student_id ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <input type="number"
                                    name="marks[{{ $i }}][marks_obtained]"
                                    value="{{ $existing?->marks_obtained ?? '' }}"
                                    min="0" step="0.5" placeholder="0" required
                                    class="marks-obtained w-28 border border-gray-300 dark:border-gray-600 rounded-lg
                                           px-2 py-1.5 text-sm focus:ring-2 focus:ring-maroon focus:border-maroon
                                           dark:bg-gray-700 dark:text-white">
                            </td>
                            <td class="py-3 px-3">
                                <input type="number"
                                    name="marks[{{ $i }}][total_marks]"
                                    value="{{ $existing?->total_marks ?? 100 }}"
                                    min="1" max="400" step="0.5" required
                                    class="total-marks w-20 border border-gray-300 dark:border-gray-600 rounded-lg
                                           px-2 py-1.5 text-sm focus:ring-2 focus:ring-maroon focus:border-maroon
                                           dark:bg-gray-700 dark:text-white">
                            </td>
                            <td class="py-3 px-3">
                                <span class="grade-badge text-sm font-semibold text-gray-400">
                                    @if($existing)
                                        {{ $existing->percentage }}% &mdash; {{ $existing->grade }}
                                    @else
                                        &mdash;
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <input type="text"
                                    name="marks[{{ $i }}][remarks]"
                                    value="{{ $existing?->remarks ?? '' }}"
                                    placeholder="Optional..."
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5
                                           text-sm focus:ring-2 focus:ring-maroon focus:border-maroon
                                           dark:bg-gray-700 dark:text-white">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end gap-3">
                <a href="{{ route('marks.entry.form') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors">
                    Reset
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-2 bg-maroon hover:bg-maroon-dark
                           text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Save All Marks
                </button>
            </div>
        </form>
        @endif
    </div>
    @endisset

</div>
@endsection

@push('scripts')
<script>
// ── Grade helpers ────────────────────────────────────────────
function gradeFromPct(pct) {
    if (pct >= 90) return ['A+', 'text-green-600'];
    if (pct >= 80) return ['A',  'text-green-600'];
    if (pct >= 70) return ['B+', 'text-blue-600'];
    if (pct >= 60) return ['B',  'text-blue-600'];
    if (pct >= 50) return ['C+', 'text-yellow-600'];
    if (pct >= 40) return ['C',  'text-yellow-600'];
    if (pct >= 30) return ['D',  'text-orange-500'];
    return ['F', 'text-red-600'];
}

function updateRowGrade(row) {
    const obt   = parseFloat(row.querySelector('.marks-obtained').value);
    const tot   = parseFloat(row.querySelector('.total-marks').value) || 100;
    const badge = row.querySelector('.grade-badge');
    if (!isNaN(obt) && row.querySelector('.marks-obtained').value !== '') {
        const pct         = Math.min((obt / tot) * 100, 100).toFixed(1);
        const [g, color]  = gradeFromPct(parseFloat(pct));
        badge.textContent = pct + '% \u2014 ' + g;
        badge.className   = 'grade-badge text-sm font-semibold ' + color;
    } else {
        badge.textContent = '\u2014';
        badge.className   = 'grade-badge text-sm font-semibold text-gray-400';
    }
}

document.querySelectorAll('tr[data-row]').forEach(function (row) {
    var obt = row.querySelector('.marks-obtained');
    var tot = row.querySelector('.total-marks');
    if (obt) obt.addEventListener('input', function () { updateRowGrade(row); });
    if (tot) tot.addEventListener('input', function () { updateRowGrade(row); });
});

// ── AJAX: subjects for chosen class ──────────────────────────
var classSelect   = document.getElementById('classSelect');
var subjectSelect = document.getElementById('subjectSelect');
var subjectsUrl   = '{{ route("api.subjects-by-class") }}';

// If page reloaded with a class pre-selected (Step 2 shown), the options
// are already server-rendered so we don't need to re-fetch.
var hasPreloaded = subjectSelect && subjectSelect.querySelectorAll('option[value]').length > 1;

if (classSelect && subjectSelect && !hasPreloaded) {
    classSelect.addEventListener('change', function () {
        var classId = this.value;
        subjectSelect.innerHTML = '<option value="">Loading\u2026</option>';
        if (!classId) {
            subjectSelect.innerHTML = '<option value="">\u2014 Select Class First \u2014</option>';
            return;
        }
        fetch(subjectsUrl + '?class_id=' + encodeURIComponent(classId))
            .then(function (r) { return r.json(); })
            .then(function (subjects) {
                if (!subjects.length) {
                    subjectSelect.innerHTML = '<option value="">No subjects assigned to this class</option>';
                    return;
                }
                subjectSelect.innerHTML = '<option value="">\u2014 Select Subject \u2014</option>' +
                    subjects.map(function (s) {
                        var label = s.name + (s.code ? ' (' + s.code + ')' : '');
                        return '<option value="' + s.id + '">' + label + '</option>';
                    }).join('');
            })
            .catch(function () {
                subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
            });
    });
}
</script>
@endpush
