@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Marks Entry')
    @section('content')
@endif
<div class="px-4 py-6 max-w-full mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Marks Entry</h1>
            <p class="text-sm text-gray-500 mt-1">Select class, term, year, exam type and subject, then enter marks for each student.</p>
        </div>
        <a href="{{ route('marks.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors">
            <i class="fas fa-list mr-2"></i> View Marks
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

    {{-- ── Step 1: Selector ─────────────────────────────── --}}
    @php
        $singleClass = $classes->count() === 1 ? $classes->first() : null;
        $defaultClassId = old('class_id', $selection['class_id'] ?? ($singleClass ? $singleClass->id : ''));
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6"
         x-data="{
             subsByClass: {{ Js::from($subjectsByClass) }},
             selClass: '{{ $defaultClassId }}',
             selSub: '{{ $selection['subject_id'] ?? '' }}',
             get subjects() { return this.subsByClass[this.selClass] || []; }
         }">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <span class="w-7 h-7 rounded-full bg-maroon text-white text-xs flex items-center justify-center mr-2 font-bold">1</span>
            Select Class, Term, Year, Exam Type &amp; Subject
        </h2>

        <form method="POST" action="{{ route('marks.entry') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

                {{-- Class --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Class <span class="text-red-500">*</span>
                    </label>
                    @if($singleClass)
                    <input type="hidden" name="class_id" value="{{ $singleClass->id }}">
                    <div class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                                bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-100">
                        {{ $singleClass->name }}
                    </div>
                    @else
                    <select name="class_id" x-model="selClass" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">&mdash; Class &mdash;</option>
                        @foreach($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>

                {{-- Term --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Term <span class="text-red-500">*</span>
                    </label>
                    <select name="term" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">&mdash; Term &mdash;</option>
                        @foreach($terms as $t)
                        <option value="{{ $t }}" {{ (isset($selection['term']) && $selection['term'] == $t) ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Academic Year --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Year <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_year" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">&mdash; Year &mdash;</option>
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ (isset($selection['academic_year']) && $selection['academic_year'] == $y) ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Exam Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Exam Type <span class="text-red-500">*</span>
                    </label>
                    <select name="exam_type_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">&mdash; Exam Type &mdash;</option>
                        @foreach($examTypes as $et)
                        <option value="{{ $et['id'] }}"
                            {{ (isset($selection['exam_type_id']) && $selection['exam_type_id'] == $et['id']) ? 'selected' : '' }}>
                            {{ $et['label'] }} ({{ $et['max_marks'] }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Subject (populated dynamically from class selection) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select name="subject_id" x-model="selSub" required
                        :disabled="!selClass || subjects.length === 0"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white
                               disabled:opacity-50 disabled:cursor-not-allowed">
                        <option value="">&mdash; Subject &mdash;</option>
                        <template x-for="s in subjects" :key="s.id">
                            <option :value="s.id"
                                    :selected="String(s.id) === String(selSub)"
                                    x-text="s.name + (s.code ? ' (' + s.code + ')' : '')">
                            </option>
                        </template>
                    </select>
                    <p x-show="selClass && subjects.length === 0"
                       class="text-xs text-amber-500 mt-1" x-cloak>No subjects assigned to this class.</p>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit"
                    class="inline-flex items-center px-5 py-2 bg-maroon hover:bg-maroon-dark
                           text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-clipboard-list mr-2"></i> Load Students
                </button>
            </div>
        </form>
    </div>

    {{-- ── Step 2: Student list for the selected subject ─── --}}
    @isset($students)

    <script type="application/json" id="marksTotalsData">@json($initTotals)</script>
    <script type="application/json" id="marksInitData">@json($initialVals)</script>

    <script>
    (function() {
        var totalsEl = document.getElementById('marksTotalsData');
        var initEl   = document.getElementById('marksInitData');
        try { Object.assign(Alpine.store('marksTotals').totals, JSON.parse(totalsEl.textContent)); } catch(e) {}

        var allInitVals = {};
        try { allInitVals = JSON.parse(initEl.textContent); } catch(e) {}

        Alpine.data('markRow', function() {
            return {
                vals: {},
                error: null,
                init: function() {
                    var sid = this.$el.dataset.studentId;
                    this.vals = allInitVals[sid] || {};
                },
                validate: function(subId, etId, event) {
                    var value = event.target.value;
                    if (value === '' || value === null) {
                        this.error = null;
                        return;
                    }
                    var amount = parseFloat(value);
                    var total = parseFloat(event.target.dataset.total || 0);
                    if (isNaN(amount)) {
                        this.error = 'Invalid number';
                        return;
                    }
                    if (amount > total) {
                        this.error = 'Cannot exceed ' + total + ' for this exam type.';
                    } else {
                        this.error = null;
                    }
                },
                cellGrade: function(subId, etId) {
                    var v = (this.vals[subId] || {})[etId];
                    if (v === '' || v === undefined || v === null) return '\u2014';
                    var o = parseFloat(v);
                    if (isNaN(o)) return '\u2014';
                    var t = Alpine.store('marksTotals').getTotal(subId, etId);
                    var p = Math.min(o / t * 100, 100);
                    var g = p>=90?'A+':p>=80?'A':p>=70?'B+':p>=60?'B':p>=50?'C+':p>=40?'C':p>=30?'D':'F';
                    return Math.round(p) + '% \xb7 ' + g;
                },
                cellGradeClass: function(subId, etId) {
                    var v = (this.vals[subId] || {})[etId];
                    if (v === '' || v === undefined || v === null) return 'text-gray-300 dark:text-gray-600';
                    var o = parseFloat(v);
                    if (isNaN(o)) return 'text-gray-300 dark:text-gray-600';
                    var t = Alpine.store('marksTotals').getTotal(subId, etId);
                    var p = Math.min(o / t * 100, 100);
                    return p>=70 ? 'text-green-600' : p>=50 ? 'text-blue-500' : p>=40 ? 'text-yellow-500' : p>=30 ? 'text-orange-500' : 'text-red-500';
                }
            };
        });
    })();
    </script>

    @php
        $etId         = $selectedExamType['id'];
        $currentIndex = $allSubjects->pluck('id')->search($subject->id);
        $subjectCount = $allSubjects->count();
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">

        {{-- Heading + subject breadcrumb --}}
        <div class="flex flex-wrap items-start justify-between gap-3 mb-1">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white flex flex-wrap items-center gap-1">
                    <span class="w-7 h-7 rounded-full bg-maroon text-white text-xs flex items-center justify-center font-bold flex-shrink-0">2</span>
                    <span>{{ $class->name }}</span>
                    <span class="text-gray-400">&middot;</span>
                    <span class="font-normal text-gray-600 dark:text-gray-400">{{ $selection['term'] }}</span>
                    <span class="text-gray-400">&middot;</span>
                    <span class="font-normal text-gray-600 dark:text-gray-400">{{ $selection['academic_year'] }}</span>
                </h2>
                <div class="flex items-center flex-wrap gap-2 mt-1 ml-9">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                        {{ $selectedExamType['label'] }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-maroon/10 text-maroon dark:bg-maroon/20">
                        {{ $subject->name }}{{ $subject->code ? ' ('.$subject->code.')' : '' }}
                    </span>
                    <span class="text-xs text-gray-400">subject {{ $currentIndex + 1 }} of {{ $subjectCount }}</span>
                </div>
            </div>

            {{-- Progress dots --}}
            @if($subjectCount > 1)
            <div class="flex items-center gap-1.5 flex-wrap" title="Subject progress">
                @foreach($allSubjects as $s)
                <div class="rounded-full transition-all {{ $s->id == $subject->id ? 'w-3 h-3 bg-maroon' : 'w-2 h-2 bg-gray-300 dark:bg-gray-600' }}"
                     title="{{ $s->name }}"></div>
                @endforeach
            </div>
            @endif
        </div>

        <p class="text-sm text-gray-500 mb-5 ml-9">
            {{ $students->count() }} student(s) &middot;
            The <em>Out Of</em> value is fixed by the selected exam type.
        </p>

        @if($students->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-user-slash text-4xl mb-3 block"></i>
            <p>No active students found in this class.</p>
        </div>
        @else
        <form id="marks-form" method="POST" action="{{ route('marks.store.multiple') }}">
            @csrf
            <input type="hidden" name="class_id"      value="{{ $selection['class_id'] }}">
            <input type="hidden" name="term"           value="{{ $selection['term'] }}">
            <input type="hidden" name="academic_year"  value="{{ $selection['academic_year'] }}">
            <input type="hidden" name="exam_type_id"   value="{{ $etId }}">

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="w-full text-sm border-separate border-spacing-0">
                    <thead>
                        <tr>
                            <th class="w-10 py-3 px-2 text-center text-xs font-semibold text-gray-500 uppercase
                                       bg-gray-50 dark:bg-gray-900 border-b-2 border-r
                                       border-gray-200 dark:border-gray-700">#</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase
                                       bg-gray-50 dark:bg-gray-900 border-b-2 border-r
                                       border-gray-200 dark:border-gray-700">Student</th>
                            <th class="py-2 px-4 bg-gray-50 dark:bg-gray-900
                                       border-b-2 border-gray-200 dark:border-gray-700 min-w-[180px]">
                                <div class="text-xs font-semibold text-gray-700 dark:text-gray-200 text-center">
                                    {{ $subject->name }}
                                    @if($subject->code)
                                    <span class="text-gray-400 font-normal">({{ $subject->code }})</span>
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center justify-center gap-1">
                                    <span class="text-xs text-gray-400">Out&nbsp;of:</span>
                                    <span class="w-16 border border-gray-300 dark:border-gray-600 rounded px-1 py-0.5
                                               text-xs text-center bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white font-semibold">
                                        {{ $selectedExamType['max_marks'] }}
                                    </span>
                                    <input type="hidden" name="total[{{ $subject->id }}][{{ $etId }}]" value="{{ $selectedExamType['max_marks'] }}">
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        @php
                            $oddRow = $i % 2 !== 0;
                            $rowBg  = $oddRow ? 'bg-gray-50/50 dark:bg-gray-800/60' : 'bg-white dark:bg-gray-800';
                        @endphp
                        <tr class="{{ $rowBg }} hover:bg-blue-50/40 dark:hover:bg-gray-750 transition-colors"
                            x-data="markRow"
                            data-student-id="{{ $student->id }}">
                            <td class="py-3 px-2 text-center text-xs text-gray-400
                                       border-b border-r border-gray-200 dark:border-gray-700">
                                {{ $i + 1 }}
                            </td>
                            <td class="py-3 px-4 border-b border-r border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-maroon text-white text-xs
                                                flex items-center justify-center font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($student->first_name ?? '?', 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div class="leading-tight">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $student->student_id ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-4 text-center border-b border-gray-200 dark:border-gray-700">
                                <input type="number"
                                    name="marks[{{ $student->id }}][{{ $subject->id }}][{{ $etId }}]"
                                    x-model="vals['{{ $subject->id }}']['{{ $etId }}']"
                                    min="0" step="0.5" placeholder="&mdash;"
                                    max="{{ $selectedExamType['max_marks'] }}"
                                    data-total="{{ $selectedExamType['max_marks'] }}"
                                    @input="validate('{{ $subject->id }}', '{{ $etId }}', $event)"
                                    class="w-24 border border-gray-300 dark:border-gray-600 rounded px-2 py-1.5
                                           text-sm text-center focus:ring-2 focus:ring-maroon focus:border-maroon
                                           dark:bg-gray-700 dark:text-white">
                                <p x-show="error" x-text="error" class="mt-1 text-xs text-red-600"></p>
                                <div class="text-xs font-semibold mt-1 h-4 leading-none whitespace-nowrap"
                                     :class="cellGradeClass('{{ $subject->id }}', '{{ $etId }}')"
                                     x-text="cellGrade('{{ $subject->id }}', '{{ $etId }}')">&mdash;</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Navigation + Save buttons --}}
            <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    @if(isset($prevSubject) && $prevSubject)
                    <a href="{{ route('marks.entry.form', array_merge($selection, ['subject_id' => $prevSubject->id])) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200
                              text-gray-700 rounded-lg text-sm transition-colors">
                        <i class="fas fa-chevron-left mr-1 text-xs"></i> {{ $prevSubject->name }}
                    </a>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('marks.entry.form') }}"
                       class="px-4 py-2 text-gray-500 hover:text-gray-700 rounded-lg text-sm transition-colors">
                        Reset
                    </a>
                    <button type="submit" form="marks-form"
                        class="inline-flex items-center px-5 py-2 bg-maroon hover:bg-maroon-dark
                               text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i> Save
                    </button>
                    @if(isset($nextSubject) && $nextSubject)
                    <button type="submit" form="marks-form"
                            name="next_subject_id" value="{{ $nextSubject->id }}"
                        class="inline-flex items-center gap-1 px-5 py-2 bg-indigo-600 hover:bg-indigo-700
                               text-white rounded-lg text-sm font-medium transition-colors">
                        Save &amp; Next
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-indigo-200 text-xs font-normal truncate max-w-[100px]">{{ $nextSubject->name }}</span>
                    </button>
                    @endif
                </div>
            </div>
        </form>
        @endif
    </div>

    @endisset

</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif
