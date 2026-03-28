@if(!request()->header('HX-Request'))
@extends('layouts.app')
@section('title', 'Generate Report')
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
            <h1 class="text-2xl font-bold text-gray-900">Generate Report Card</h1>
            <p class="text-sm text-gray-500 mt-0.5">Select a student, term and year to generate their report</p>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3">
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Report Details</h2>
        </div>
        <form action="{{ route('reports.generate') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Class --}}
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select id="class_id" name="class_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('class_id') border-red-400 @enderror">
                        <option value="">— Select Class —</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('class_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Student --}}
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select id="student_id" name="student_id" required disabled
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 @error('student_id') border-red-400 @enderror">
                        <option value="">— Select Class First —</option>
                    </select>
                    @error('student_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Report Type --}}
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Report Type <span class="text-red-500">*</span>
                    </label>
                    <select id="report_type" name="report_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('report_type') border-red-400 @enderror">
                        <option value="">— Select Type —</option>
                        <option value="report_card"     {{ old('report_type') == 'report_card'     ? 'selected' : '' }}>Report Card</option>
                        <option value="progress_report" {{ old('report_type') == 'progress_report' ? 'selected' : '' }}>Progress Report</option>
                        <option value="transcript"      {{ old('report_type') == 'transcript'      ? 'selected' : '' }}>Transcript</option>
                    </select>
                    @error('report_type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Term --}}
                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700 mb-1">
                        Term <span class="text-red-500">*</span>
                    </label>
                    <select id="term" name="term" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('term') border-red-400 @enderror">
                        <option value="">— Select Term —</option>
                        <option value="Term 1"    {{ old('term') == 'Term 1'    ? 'selected' : '' }}>Term 1</option>
                        <option value="Term 2"    {{ old('term') == 'Term 2'    ? 'selected' : '' }}>Term 2</option>
                        <option value="Term 3"    {{ old('term') == 'Term 3'    ? 'selected' : '' }}>Term 3</option>
                        <option value="Semester 1"{{ old('term') == 'Semester 1'? 'selected' : '' }}>Semester 1</option>
                        <option value="Semester 2"{{ old('term') == 'Semester 2'? 'selected' : '' }}>Semester 2</option>
                    </select>
                    @error('term')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Academic Year --}}
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">
                        Academic Year <span class="text-red-500">*</span>
                    </label>
                    <select id="academic_year" name="academic_year" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('academic_year') border-red-400 @enderror">
                        <option value="">— Select Year —</option>
                        @php $currentYear = date('Y'); @endphp
                        @for($y = $currentYear - 1; $y <= $currentYear + 1; $y++)
                        @php $yearRange = $y . '-' . ($y + 1); @endphp
                        <option value="{{ $yearRange }}" {{ old('academic_year') == $yearRange ? 'selected' : '' }}>
                            {{ $yearRange }}
                        </option>
                        @endfor
                    </select>
                    @error('academic_year')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info note --}}
                <div class="md:col-span-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 flex items-start gap-2 text-sm text-blue-800">
                        <i class="fas fa-info-circle mt-0.5 shrink-0"></i>
                        <span>The system will check that marks exist for the selected student, term, and academic year before generating the report.</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('reports.index') }}"
                   hx-get="{{ route('reports.index') }}"
                   hx-target="#page-content"
                   hx-push-url="true"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <i class="fas fa-file-alt"></i>
                    Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect   = document.getElementById('class_id');
    const studentSelect = document.getElementById('student_id');

    classSelect.addEventListener('change', function () {
        const classId = this.value;
        studentSelect.innerHTML = '<option value="">Loading...</option>';
        studentSelect.disabled  = true;

        if (!classId) {
            studentSelect.innerHTML = '<option value="">— Select Class First —</option>';
            return;
        }

        fetch(`{{ route('api.students-by-class') }}?class_id=${classId}`)
            .then(r => r.json())
            .then(students => {
                studentSelect.innerHTML = '<option value="">— Select Student —</option>';
                students.forEach(s => {
                    studentSelect.innerHTML += `<option value="${s.id}">${s.name} (${s.student_id})</option>`;
                });
                studentSelect.disabled = false;
            })
            .catch(() => {
                studentSelect.innerHTML = '<option value="">Error loading students</option>';
            });
    });
});
</script>

@if(!request()->header('HX-Request'))
@endsection
@endif
