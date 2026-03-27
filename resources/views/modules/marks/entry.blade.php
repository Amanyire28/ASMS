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
    {{-- JSON stored in a data element so it never conflicts with x-data attribute quotes --}}
    <script type="application/json" id="subjectsMapData">@json($subjectsByClass ?? [])</script>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6"
         data-init-class="{{ isset($selection['class_id']) ? $selection['class_id'] : '' }}"
         data-init-subject="{{ isset($selection['subject_id']) ? $selection['subject_id'] : '' }}"
         x-data="marksEntrySelector()"
         x-init="init()">
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
                        x-model="classId" @change="onClassChange()"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">&mdash; Select Class &mdash;</option>
                        @foreach($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Subject (Alpine-powered) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select name="subject_id" id="subjectSelect" required
                        x-model="subjectId"
                        :disabled="!classId || subjects.length === 0"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white
                               disabled:opacity-50 disabled:cursor-not-allowed">
                        <option value="" x-text="hint"></option>
                        <template x-for="s in subjects" :key="s.id">
                            <option :value="s.id" x-text="s.name + (s.code ? ' (' + s.code + ')' : '')"></option>
                        </template>
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
                        <option value="">&mdash; Select Term &mdash;</option>
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
                        <option value="">&mdash; Select Year &mdash;</option>
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
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors"
                            data-row="{{ $i }}"
                            x-data="{
                                obt: '{{ $existing?->marks_obtained ?? '' }}',
                                tot: '{{ $existing?->total_marks ?? 100 }}',
                                get gradeText() {
                                    var o = parseFloat(this.obt), t = parseFloat(this.tot) || 100;
                                    if (isNaN(o) || String(this.obt).trim() === '') return '\u2014';
                                    var p = Math.min((o/t)*100, 100);
                                    var g = p>=90?'A+':p>=80?'A':p>=70?'B+':p>=60?'B':p>=50?'C+':p>=40?'C':p>=30?'D':'F';
                                    return p.toFixed(1)+'% \u2014 '+g;
                                },
                                get gradeClass() {
                                    var o = parseFloat(this.obt), t = parseFloat(this.tot) || 100;
                                    if (isNaN(o) || String(this.obt).trim() === '') return 'text-gray-400';
                                    var p = Math.min((o/t)*100, 100);
                                    return p>=70?'text-green-600':p>=50?'text-blue-600':p>=40?'text-yellow-600':p>=30?'text-orange-500':'text-red-600';
                                }
                            }">
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
                                    x-model="obt"
                                    min="0" step="0.5" placeholder="0" required
                                    class="marks-obtained w-28 border border-gray-300 dark:border-gray-600 rounded-lg
                                           px-2 py-1.5 text-sm focus:ring-2 focus:ring-maroon focus:border-maroon
                                           dark:bg-gray-700 dark:text-white">
                            </td>
                            <td class="py-3 px-3">
                                <input type="number"
                                    name="marks[{{ $i }}][total_marks]"
                                    x-model="tot"
                                    min="1" max="400" step="0.5" required
                                    class="total-marks w-20 border border-gray-300 dark:border-gray-600 rounded-lg
                                           px-2 py-1.5 text-sm focus:ring-2 focus:ring-maroon focus:border-maroon
                                           dark:bg-gray-700 dark:text-white">
                            </td>
                            <td class="py-3 px-3">
                                <span class="grade-badge text-sm font-semibold"
                                      :class="gradeClass"
                                      x-text="gradeText">—</span>
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

