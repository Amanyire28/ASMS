@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Edit Mark')
    @section('content')
@endif
<div class="px-4 py-6 max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center mb-6">
        <a href="{{ route('marks.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg mr-3 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Mark</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update the score or remarks for this record.</p>
        </div>
    </div>

    {{-- Info card (read-only context) --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500">Student</span>
                <p class="font-medium text-gray-900 dark:text-white mt-0.5">
                    {{ trim(($mark->student->first_name ?? '') . ' ' . ($mark->student->last_name ?? '')) ?: 'N/A' }}
                </p>
            </div>
            <div>
                <span class="text-gray-500">Class</span>
                <p class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $mark->class->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Subject</span>
                <p class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $mark->subject->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Term &amp; Year</span>
                <p class="font-medium text-gray-900 dark:text-white mt-0.5">
                    {{ $mark->term }} &middot; {{ $mark->academic_year }}
                </p>
            </div>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">

        @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('marks.update', $mark) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="marksObtained"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Marks Obtained <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="marksObtained" name="marks_obtained"
                        value="{{ old('marks_obtained', $mark->marks_obtained) }}"
                        min="0" step="0.5" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="totalMarks"
                           class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Total Marks <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="totalMarks" name="total_marks"
                        value="{{ old('total_marks', $mark->total_marks) }}"
                        min="1" max="400" step="0.5" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            {{-- Live grade preview --}}
            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center space-x-4 text-sm">
                <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium">Preview</span>
                <span id="previewPct" class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ $mark->percentage }}%
                </span>
                <span id="previewGrade"
                    class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                    {{ $mark->grade ?? 'N/A' }}
                </span>
            </div>

            <div class="mt-4">
                <label for="remarks"
                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Remarks
                </label>
                <input type="text" id="remarks" name="remarks"
                    value="{{ old('remarks', $mark->remarks) }}"
                    placeholder="Optional comment…"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-maroon focus:border-maroon dark:bg-gray-700 dark:text-white">
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('marks.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center px-5 py-2 bg-maroon hover:bg-maroon-dark
                           text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Update Mark
                </button>
            </div>
        </form>
    </div>

</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif

<script>
(function () {
    function gradeFromPct(pct) {
        if (pct >= 90) return ['A+', 'bg-green-100',  'text-green-700'];
        if (pct >= 80) return ['A',  'bg-green-100',  'text-green-700'];
        if (pct >= 70) return ['B+', 'bg-blue-100',   'text-blue-700'];
        if (pct >= 60) return ['B',  'bg-blue-100',   'text-blue-700'];
        if (pct >= 50) return ['C+', 'bg-yellow-100', 'text-yellow-700'];
        if (pct >= 40) return ['C',  'bg-yellow-100', 'text-yellow-700'];
        if (pct >= 30) return ['D',  'bg-orange-100', 'text-orange-700'];
        return           ['F',  'bg-red-100',    'text-red-700'];
    }

    function updatePreview() {
        var obt   = parseFloat(document.getElementById('marksObtained').value);
        var tot   = parseFloat(document.getElementById('totalMarks').value) || 100;
        var pctEl = document.getElementById('previewPct');
        var badge = document.getElementById('previewGrade');

        if (!isNaN(obt)) {
            var pct    = Math.min((obt / tot) * 100, 100).toFixed(1);
            var result = gradeFromPct(parseFloat(pct));
            pctEl.textContent = pct + '%';
            badge.textContent = result[0];
            badge.className   = 'px-2 py-0.5 rounded-full text-xs font-bold ' + result[1] + ' ' + result[2];
        }
    }

    var obtEl = document.getElementById('marksObtained');
    var totEl = document.getElementById('totalMarks');
    if (obtEl) obtEl.addEventListener('input', updatePreview);
    if (totEl) totEl.addEventListener('input', updatePreview);
}());
</script>
