@extends('layouts.app')

@section('title', 'Generate Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Generate New Report</h2>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Reports
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('reports.generate') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" 
                                            id="class_id" 
                                            name="class_id" 
                                            required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                    <select class="form-select @error('student_id') is-invalid @enderror" 
                                            id="student_id" 
                                            name="student_id" 
                                            required disabled>
                                        <option value="">Select Class First</option>
                                    </select>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="report_type" class="form-label">Report Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('report_type') is-invalid @enderror" 
                                            id="report_type" 
                                            name="report_type" 
                                            required>
                                        <option value="">Select Report Type</option>
                                        <option value="report_card" {{ old('report_type') == 'report_card' ? 'selected' : '' }}>Report Card</option>
                                        <option value="progress_report" {{ old('report_type') == 'progress_report' ? 'selected' : '' }}>Progress Report</option>
                                        <option value="transcript" {{ old('report_type') == 'transcript' ? 'selected' : '' }}>Transcript</option>
                                    </select>
                                    @error('report_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="term" class="form-label">Term <span class="text-danger">*</span></label>
                                    <select class="form-select @error('term') is-invalid @enderror" 
                                            id="term" 
                                            name="term" 
                                            required>
                                        <option value="">Select Term</option>
                                        <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                                        <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                                        <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                                        <option value="Semester 1" {{ old('term') == 'Semester 1' ? 'selected' : '' }}>Semester 1</option>
                                        <option value="Semester 2" {{ old('term') == 'Semester 2' ? 'selected' : '' }}>Semester 2</option>
                                    </select>
                                    @error('term')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <select class="form-select @error('academic_year') is-invalid @enderror" 
                                            id="academic_year" 
                                            name="academic_year" 
                                            required>
                                        <option value="">Select Academic Year</option>
                                        <option value="2024-2025" {{ old('academic_year') == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                                        <option value="2025-2026" {{ old('academic_year') == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                                        <option value="2026-2027" {{ old('academic_year') == '2026-2027' ? 'selected' : '' }}>2026-2027</option>
                                    </select>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> The system will check if marks exist for the selected student, term, and academic year before generating the report.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-alt me-2"></i>Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const studentSelect = document.getElementById('student_id');

    classSelect.addEventListener('change', function() {
        const classId = this.value;
        
        // Reset student dropdown
        studentSelect.innerHTML = '<option value="">Loading...</option>';
        studentSelect.disabled = true;

        if (classId) {
            fetch(`{{ route('api.students-by-class') }}?class_id=${classId}`)
                .then(response => response.json())
                .then(students => {
                    studentSelect.innerHTML = '<option value="">Select Student</option>';
                    students.forEach(student => {
                        studentSelect.innerHTML += `<option value="${student.id}">${student.name} (${student.student_number})</option>`;
                    });
                    studentSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    studentSelect.innerHTML = '<option value="">Error loading students</option>';
                });
        } else {
            studentSelect.innerHTML = '<option value="">Select Class First</option>';
            studentSelect.disabled = true;
        }
    });
});
</script>
@endsection