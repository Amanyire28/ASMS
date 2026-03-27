@extends('layouts.app')

@section('title', 'Class Details')
@section('page-title', $class->full_name)
@section('page-description', 'Class information and management')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Class Information Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $class->full_name }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Class Details and Management</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('classes.edit', $class) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Class
                    </a>
                    <a href="{{ route('classes.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Classes
                    </a>
                </div>
            </div>
        </div>

        <!-- Class Details -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Class Level -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-layer-group text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Class Level</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $class->classLevel->name }}
                            </p>
                            @if($class->classLevel->category)
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $class->classLevel->category }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stream -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-stream text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Stream</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $class->stream->name }}
                            </p>
                            @if($class->stream->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $class->stream->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Level Teacher -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-tie text-indigo-600 dark:text-indigo-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Level Teacher</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                @if($class->classLevel->levelTeacher)
                                    {{ $class->classLevel->levelTeacher->first_name }} {{ $class->classLevel->levelTeacher->last_name }}
                                @else
                                    <span class="text-gray-400 italic text-sm">Not assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Class Teacher -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chalkboard-teacher text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Class Teacher</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                @if($class->classTeacher)
                                    {{ $class->classTeacher->first_name }} {{ $class->classTeacher->last_name }}
                                @else
                                    <span class="text-gray-400 italic text-sm">Not assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Current Students -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-graduate text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Enrolled</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $class->students->count() }} students
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Classroom -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-door-open text-indigo-600 dark:text-indigo-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Classroom</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $class->classroom ?? 'Not assigned' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-toggle-{{ ($class->is_active ?? true) ? 'on' : 'off' }} text-{{ ($class->is_active ?? true) ? 'green' : 'red' }}-600 dark:text-{{ ($class->is_active ?? true) ? 'green' : 'red' }}-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                @if($class->is_active ?? true)
                                    <span class="text-green-600 dark:text-green-400">Active</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Information -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Teacher Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Level Teacher -->
                    @if($class->classLevel->levelTeacher)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">
                            <i class="fas fa-user-tie text-indigo-600 dark:text-indigo-400 mr-2"></i>
                            Level Teacher
                        </h4>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-lg">
                                    {{ substr($class->classLevel->levelTeacher->first_name, 0, 1) }}{{ substr($class->classLevel->levelTeacher->last_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $class->classLevel->levelTeacher->first_name }} {{ $class->classLevel->levelTeacher->last_name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Oversees all {{ $class->classLevel->name }} classes
                                </p>
                                @if($class->classLevel->levelTeacher->email)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-envelope mr-1"></i>{{ $class->classLevel->levelTeacher->email }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Class Teacher -->
                    @if($class->classTeacher)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">
                            <i class="fas fa-chalkboard-teacher text-green-600 dark:text-green-400 mr-2"></i>
                            Class Teacher
                        </h4>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-maroon to-maroon-dark rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-lg">
                                    {{ substr($class->classTeacher->first_name, 0, 1) }}{{ substr($class->classTeacher->last_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $class->classTeacher->first_name }} {{ $class->classTeacher->last_name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Manages {{ $class->full_name }} specifically
                                </p>
                                @if($class->classTeacher->email)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-envelope mr-1"></i>{{ $class->classTeacher->email }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!$class->classLevel->levelTeacher && !$class->classTeacher)
                    <div class="col-span-2 text-center py-8">
                        <i class="fas fa-user-slash text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Teachers Assigned</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">This class doesn't have any teachers assigned yet.</p>
                        <a href="{{ route('classes.edit', $class) }}" 
                           class="inline-flex items-center px-4 py-2 bg-maroon hover:bg-maroon-dark text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>
                            Assign Teachers
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Students in Class -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Students in {{ $class->full_name }}
                </h3>
                <a href="{{ route('students.create') }}?class_id={{ $class->id }}" 
                   class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add Student
                </a>
            </div>
        </div>

        <div class="p-6">
            @if($class->students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3">Student Name</th>
                                <th class="px-6 py-3">Student ID</th>
                                <th class="px-6 py-3">Gender</th>
                                <th class="px-6 py-3">Date of Birth</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class->students as $student)
                            <tr class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $student->student_id }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ ucfirst($student->gender ?? 'N/A') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('students.show', $student) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300" 
                                       title="View Student">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-user-graduate text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Students Enrolled</h4>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">This class doesn't have any students yet.</p>
                    <a href="{{ route('students.create') }}?class_id={{ $class->id }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add First Student
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Subjects for Class -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Subjects for {{ $class->full_name }}
                </h3>
                <button onclick="showAssignSubjectsModal()" 
                        class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-book mr-2"></i>
                    Assign Subjects
                </button>
            </div>
        </div>

        <div class="p-6">
            @if($class->subjects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($class->subjects as $subject)
                    @php $pivotTeacher = $teachers->find($subject->pivot->teacher_id); @endphp
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $subject->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $subject->code }}</p>
                                @if($pivotTeacher)
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        Teacher: {{ $pivotTeacher->first_name }} {{ $pivotTeacher->last_name }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <a href="{{ route('subjects.show', $subject) }}" 
                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-book text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Subjects Assigned</h4>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">This class doesn't have any subjects assigned yet.</p>
                    <button onclick="showAssignSubjectsModal()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-book mr-2"></i>
                        Assign First Subject
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Assign Subjects Modal ───────────────────────────────────── --}}
<div id="assignSubjectsModal"
     class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4"
     style="display:none">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Assign Subjects &mdash; {{ $class->full_name }}
            </h3>
            <button type="button" onclick="closeAssignModal()"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        {{-- Modal body (scrollable) --}}
        <div class="overflow-y-auto flex-1 px-6 py-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Tick the subjects you want to assign to this class. Optionally pick a teacher per subject.
                Unticking a subject will remove it from the class.
            </p>

            <form id="assignSubjectsForm"
                  method="POST"
                  action="{{ route('classes.assign-subjects', $class) }}">
                @csrf

                @if($allSubjects->isEmpty())
                    <p class="text-center text-gray-400 py-8">No active subjects found. Create subjects first.</p>
                @else
                <div class="space-y-2">
                    @foreach($allSubjects as $subject)
                    @php $assigned = $class->subjects->find($subject->id); @endphp
                    <div class="flex items-center gap-3 p-3 border rounded-lg transition-colors
                                {{ $assigned ? 'border-blue-300 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700' : 'border-gray-200 dark:border-gray-700' }}"
                         id="subrow-{{ $subject->id }}">

                        <input type="checkbox"
                               name="subjects[]"
                               value="{{ $subject->id }}"
                               id="subck-{{ $subject->id }}"
                               {{ $assigned ? 'checked' : '' }}
                               onchange="toggleSubjectRow({{ $subject->id }}, this.checked)"
                               class="w-4 h-4 rounded border-gray-300 text-maroon focus:ring-maroon flex-shrink-0">

                        <label for="subck-{{ $subject->id }}"
                               class="flex-1 text-sm font-medium text-gray-900 dark:text-white cursor-pointer select-none">
                            {{ $subject->name }}
                            @if($subject->code)
                                <span class="font-normal text-gray-400 ml-1">({{ $subject->code }})</span>
                            @endif
                        </label>

                        <select name="teachers[{{ $subject->id }}]"
                                id="subteacher-{{ $subject->id }}"
                                {{ !$assigned ? 'disabled' : '' }}
                                class="border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm
                                       dark:bg-gray-700 dark:text-white w-44 flex-shrink-0 transition-opacity
                                       {{ !$assigned ? 'opacity-40' : '' }}">
                            <option value="">No teacher</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}"
                                {{ $assigned && $assigned->pivot->teacher_id == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
                @endif
            </form>
        </div>

        {{-- Modal footer --}}
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button type="button" onclick="closeAssignModal()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors">
                Cancel
            </button>
            <button type="submit" form="assignSubjectsForm"
                    class="inline-flex items-center px-5 py-2 bg-maroon hover:bg-maroon-dark
                           text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-save mr-2"></i> Save Assignments
            </button>
        </div>
    </div>
</div>

<script>
function showAssignSubjectsModal() {
    var modal = document.getElementById('assignSubjectsModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeAssignModal() {
    document.getElementById('assignSubjectsModal').style.display = 'none';
    document.body.style.overflow = '';
}
function toggleSubjectRow(id, checked) {
    var sel = document.getElementById('subteacher-' + id);
    var row = document.getElementById('subrow-' + id);
    if (checked) {
        sel.disabled = false;
        sel.classList.remove('opacity-40');
        row.classList.add('border-blue-300', 'bg-blue-50', 'dark:bg-blue-900/20', 'dark:border-blue-700');
        row.classList.remove('border-gray-200', 'dark:border-gray-700');
    } else {
        sel.disabled = true;
        sel.classList.add('opacity-40');
        row.classList.remove('border-blue-300', 'bg-blue-50', 'dark:bg-blue-900/20', 'dark:border-blue-700');
        row.classList.add('border-gray-200', 'dark:border-gray-700');
    }
}
// Close on backdrop click
document.getElementById('assignSubjectsModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
</script>
@endsection