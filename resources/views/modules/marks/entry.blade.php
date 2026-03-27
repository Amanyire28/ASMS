@extends('layouts.app')

@section('title', 'Marks Entry')

@section('content')
<div class="px-4 py-6 max-w-full mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Marks Entry</h1>
            <p class="text-sm text-gray-500 mt-1">Select a class, term and year to load the full mark sheet for all subjects.</p>
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

    {{-- â”€â”€ Step 1: Selector (class + term + year only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <span class="w-7 h-7 rounded-full bg-maroon text-white text-xs flex items-center justify-center mr-2 font-bold">1</span>
            Select Class, Term &amp; Year
        </h2>

        <form method="POST" action="{{ route('marks.entry') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Class --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select name="class_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                        <option value="">&mdash; Select Class &mdash;</option>
                        @foreach($classes as $cls)
                        <option value="{{ $cls->id }}"
                            {{ (isset($selection['class_id']) && $selection['class_id'] == $cls->id) ? 'selected' : '' }}>
                            {{ $cls->name }}
                        </option>
                        @endforeach
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
                    <i class="fas fa-table mr-2"></i> Load Mark Sheet
                </button>
            </div>
        </form>
    </div>

    {{-- â”€â”€ Step 2: Multi-subject spreadsheet â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    @isset($students)

    {{-- Pass per-subject "Out Of" defaults into the Alpine store via a safe non-executed JSON element --}}
    <script type="application/json" id="marksTotalsData">@json($initTotals)</script>

    {{-- Single wrapper x-init reads the JSON element and seeds the global store --}}
    <div x-data x-init='(function(){var d=document.getElementById("marksTotalsData");if(d)try{Object.assign($store.marksTotals.totals,JSON.parse(d.textContent));}catch(e){}})()'>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1 flex items-center">
            <span class="w-7 h-7 rounded-full bg-maroon text-white text-xs flex items-center justify-center mr-2 font-bold">2</span>
            Mark Sheet &mdash;
            <span class="ml-1 font-normal text-gray-600 dark:text-gray-400">
                {{ $class->name }}
                &middot; {{ $selection['term'] }}
                &middot; {{ $selection['academic_year'] }}
            </span>
        </h2>
        <p class="text-sm text-gray-500 mb-4 ml-9">
            {{ $students->count() }} student(s) &middot; {{ $classSubjects->count() }} subject(s).
            Existing marks are pre-filled. Adjust <strong>Out Of</strong> in each column header if needed.
        </p>

        @if($students->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-user-slash text-4xl mb-3 block"></i>
            <p>No active students found in this class.</p>
        </div>
        @elseif($classSubjects->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-book-open text-4xl mb-3 block"></i>
            <p class="mb-2">No subjects are assigned to this class yet.</p>
            <a href="{{ route('classes.show', $class->id) }}"
               class="text-maroon hover:underline text-sm">Go to class page to assign subjects &rarr;</a>
        </div>
        @else
        <form method="POST" action="{{ route('marks.store.multiple') }}">
            @csrf
            <input type="hidden" name="class_id"      value="{{ $selection['class_id'] }}">
            <input type="hidden" name="term"           value="{{ $selection['term'] }}">
            <input type="hidden" name="academic_year"  value="{{ $selection['academic_year'] }}">

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full border-separate border-spacing-0 text-sm">
                    <thead>
                        <tr>
                            {{-- Sticky # --}}
                            <th class="sticky left-0 z-20 bg-gray-50 dark:bg-gray-800
                                       py-3 px-2 w-10 text-center text-xs font-semibold text-gray-500 uppercase
                                       border-b-2 border-r border-gray-200 dark:border-gray-700">#</th>
                            {{-- Sticky student name --}}
                            <th class="sticky left-10 z-20 bg-gray-50 dark:bg-gray-800
                                       py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase min-w-[180px]
                                       border-b-2 border-r border-gray-200 dark:border-gray-700">Student</th>
                            {{-- One column per subject --}}
                            @foreach($classSubjects as $subject)
                            <th class="bg-gray-50 dark:bg-gray-800 py-3 px-3 text-center min-w-[140px]
                                       border-b-2 border-r last:border-r-0 border-gray-200 dark:border-gray-700">
                                <div class="text-xs font-semibold text-gray-700 dark:text-gray-200 leading-tight">
                                    {{ $subject->name }}
                                </div>
                                @if($subject->code)
                                <div class="text-xs text-gray-400 font-normal">{{ $subject->code }}</div>
                                @endif
                                {{-- Editable "Out Of" for this column; x-model keeps store in sync --}}
                                <div class="mt-1.5 flex items-center justify-center gap-1">
                                    <span class="text-xs text-gray-400">/ </span>
                                    <input type="number"
                                        name="total[{{ $subject->id }}]"
                                        x-model.number="$store.marksTotals.totals['{{ $subject->id }}']"
                                        min="1" max="400" step="0.5"
                                        class="w-16 border border-gray-300 dark:border-gray-600 rounded px-1.5 py-0.5
                                               text-xs text-center focus:ring-1 focus:ring-maroon focus:border-maroon
                                               dark:bg-gray-700 dark:text-white font-normal">
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        <tr class="@if($i % 2 === 0) bg-white dark:bg-gray-800 @else bg-gray-50/50 dark:bg-gray-800/60 @endif
                                   hover:bg-blue-50/40 dark:hover:bg-gray-750 transition-colors">
                            {{-- # --}}
                            <td class="sticky left-0 z-10 @if($i % 2 === 0) bg-white dark:bg-gray-800 @else bg-gray-50 dark:bg-gray-800 @endif
                                       py-3 px-2 text-center text-xs text-gray-400
                                       border-b border-r border-gray-200 dark:border-gray-700">
                                {{ $i + 1 }}
                            </td>
                            {{-- Student info --}}
                            <td class="sticky left-10 z-10 @if($i % 2 === 0) bg-white dark:bg-gray-800 @else bg-gray-50 dark:bg-gray-800 @endif
                                       py-3 px-4 border-b border-r border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-maroon text-white text-xs
                                                flex items-center justify-center font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($student->first_name ?? '?', 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div class="leading-tight">
                                        <div class="font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $student->student_id ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            {{-- One cell per subject --}}
                            @foreach($classSubjects as $subject)
                            @php $existing = $existingMarks[$student->id][$subject->id] ?? null; @endphp
                            <td class="py-2 px-2 text-center border-b border-r last:border-r-0 border-gray-200 dark:border-gray-700"
                                x-data="{obt:'{{ $existing?->marks_obtained ?? '' }}',sid:'{{ $subject->id }}',get pct(){var o=parseFloat(this.obt),t=$store.marksTotals.getTotal(this.sid);if(isNaN(o)||String(this.obt).trim()==='')return null;return Math.min(o/t*100,100);},get gradeText(){if(this.pct===null)return'\u2014';var p=this.pct,g=p>=90?'A+':p>=80?'A':p>=70?'B+':p>=60?'B':p>=50?'C+':p>=40?'C':p>=30?'D':'F';return p.toFixed(0)+'%\u00b7'+g;},get gradeClass(){if(this.pct===null)return'text-gray-300 dark:text-gray-600';var p=this.pct;return p>=70?'text-green-600':p>=50?'text-blue-500':p>=40?'text-yellow-500':p>=30?'text-orange-500':'text-red-500';}}">
                                <input type="number"
                                    name="marks[{{ $student->id }}][{{ $subject->id }}]"
                                    x-model="obt"
                                    min="0" step="0.5" placeholder="â€”"
                                    class="w-20 border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5
                                           text-sm text-center focus:ring-2 focus:ring-maroon focus:border-maroon
                                           dark:bg-gray-700 dark:text-white">
                                <div class="text-xs font-semibold mt-0.5 h-4 leading-none"
                                     :class="gradeClass"
                                     x-text="gradeText">&mdash;</div>
                            </td>
                            @endforeach
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
                    <i class="fas fa-save mr-2"></i> Save Mark Sheet
                </button>
            </div>
        </form>
        @endif
    </div>
    </div>{{-- end store-init wrapper --}}

    @endisset

</div>
@endsection

