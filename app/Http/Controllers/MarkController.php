<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarkController extends Controller
{
    // ──────────────────────────────────────────────────────────
    //  INDEX — filterable marks table
    // ──────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $teacher = $this->currentTeacher();

        $classes = $teacher
            ? $this->teacherClasses($teacher)
            : ClassModel::where('is_active', true)->orderBy('name')->get();

        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $years = $this->academicYears();

        // ── Grid mode: all three filters present → full student × subject sheet ──
        if ($request->filled('class_id') && $request->filled('term') && $request->filled('academic_year')) {
            $class = ClassModel::findOrFail($request->class_id);

            $classSubjectsQuery = $class->subjects()
                ->where('subjects.is_active', true)
                ->orderBy('subjects.name');
            if ($teacher) {
                $classSubjectsQuery->where('class_subject.teacher_id', $teacher->id);
            }
            $classSubjects = $classSubjectsQuery->get();

            $students = $class->students()->where('is_active', true)->orderBy('first_name')->get();

            $flat = Mark::where([
                'class_id'      => $request->class_id,
                'term'          => $request->term,
                'academic_year' => $request->academic_year,
            ])->whereIn('subject_id', $classSubjects->pluck('id'))->get();

            $marksGrid = [];
            foreach ($flat as $m) {
                $marksGrid[$m->student_id][$m->subject_id] = $m;
            }

            $selection = $request->only(['class_id', 'term', 'academic_year']);

            return view('modules.marks.index', compact(
                'classes', 'terms', 'years',
                'class', 'classSubjects', 'students', 'marksGrid', 'selection'
            ));
        }

        // ── Prompt mode: not all filters set yet ──
        return view('modules.marks.index', compact('classes', 'terms', 'years'));
    }

    // ──────────────────────────────────────────────────────────
    //  CREATE — Step 1 selector form
    // ──────────────────────────────────────────────────────────
    public function create()
    {
        $teacher = $this->currentTeacher();

        $classes = $teacher
            ? $this->teacherClasses($teacher)
            : ClassModel::where('is_active', true)->orderBy('name')->get();

        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $years = $this->academicYears();

        return view('modules.marks.entry', compact('classes', 'terms', 'years'));
    }

    // ──────────────────────────────────────────────────────────
    //  AJAX — return subjects assigned to a given class
    // ──────────────────────────────────────────────────────────
    public function getSubjectsByClass(Request $request)
    {
        $request->validate(['class_id' => 'required|exists:classes,id']);

        $teacher = $this->currentTeacher();
        $query   = ClassModel::findOrFail($request->class_id)
            ->subjects()
            ->where('subjects.is_active', true)
            ->orderBy('subjects.name');

        if ($teacher) {
            $query->where('class_subject.teacher_id', $teacher->id);
        }

        return response()->json($query->get(['subjects.id', 'subjects.name', 'subjects.code']));
    }

    // ──────────────────────────────────────────────────────────
    //  ENTRY — Step 2: load student grid with existing marks
    // ──────────────────────────────────────────────────────────
    public function entry(Request $request)
    {
        $request->validate([
            'class_id'      => 'required|exists:classes,id',
            'term'          => 'required|string',
            'academic_year' => 'required|string',
        ]);

        $teacher = $this->currentTeacher();
        $class   = ClassModel::findOrFail($request->class_id);

        // Load subjects for columns — teacher-scoped when applicable
        $classSubjectsQuery = $class->subjects()
            ->where('subjects.is_active', true)
            ->orderBy('subjects.name');
        if ($teacher) {
            $classSubjectsQuery->where('class_subject.teacher_id', $teacher->id);
        }
        $classSubjects = $classSubjectsQuery->get();

        if ($teacher && $classSubjects->isEmpty()) {
            abort(403, 'You are not assigned to teach any subjects in this class.');
        }

        $students = $class->students()->where('is_active', true)->orderBy('first_name')->get();

        // Load all existing marks for this class/term/year and build a 2D map [student_id][subject_id]
        $flat = Mark::where([
            'class_id'      => $request->class_id,
            'term'          => $request->term,
            'academic_year' => $request->academic_year,
        ])->whereIn('subject_id', $classSubjects->pluck('id'))->get();

        $existingMarks = [];
        foreach ($flat as $m) {
            $existingMarks[$m->student_id][$m->subject_id] = $m;
        }

        // Default "Out Of" per subject — use the most recent saved value, or 100
        $initTotals = [];
        foreach ($classSubjects as $s) {
            $initTotals[(string) $s->id] = 100;
            foreach ($flat as $m) {
                if ($m->subject_id == $s->id) {
                    $initTotals[(string) $s->id] = (float) $m->total_marks;
                    break;
                }
            }
        }

        $selection = $request->only(['class_id', 'term', 'academic_year']);

        $classes = $teacher
            ? $this->teacherClasses($teacher)
            : ClassModel::where('is_active', true)->orderBy('name')->get();

        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $years = $this->academicYears();

        return view('modules.marks.entry', compact(
            'class', 'classSubjects', 'students',
            'existingMarks', 'initTotals', 'selection', 'classes', 'terms', 'years'
        ));
    }

    // ──────────────────────────────────────────────────────────
    //  STORE MULTIPLE — save the whole student grid
    // ──────────────────────────────────────────────────────────
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'class_id'      => 'required|exists:classes,id',
            'term'          => 'required|string',
            'academic_year' => 'required|string',
            'total'         => 'required|array',
            'total.*'       => 'required|numeric|min:1|max:400',
            'marks'         => 'required|array',
            'marks.*'       => 'array',
            'marks.*.*'     => 'nullable|numeric|min:0',
        ]);

        $teacher = $this->currentTeacher();

        foreach ($request->marks as $studentId => $subjects) {
            foreach ($subjects as $subjectId => $obtained) {
                if ($obtained === null || $obtained === '') continue;

                // Skip subjects not assigned to this teacher
                if ($teacher && !$this->teacherOwnsAssignment($teacher, $request->class_id, $subjectId)) {
                    continue;
                }

                $total    = (float) ($request->total[$subjectId] ?? 100);
                $obtained = min((float) $obtained, $total);

                Mark::updateOrCreate(
                    [
                        'student_id'    => $studentId,
                        'subject_id'    => $subjectId,
                        'class_id'      => $request->class_id,
                        'term'          => $request->term,
                        'academic_year' => $request->academic_year,
                    ],
                    [
                        'marks_obtained' => $obtained,
                        'total_marks'    => $total,
                        'grade'          => $this->grade($obtained, $total),
                        'remarks'        => null,
                    ]
                );
            }
        }

        return redirect()->route('marks.index')->with('success', 'Marks saved successfully.');
    }

    // ──────────────────────────────────────────────────────────
    //  EDIT — single mark edit form
    // ──────────────────────────────────────────────────────────
    public function edit(Mark $mark)
    {
        $teacher = $this->currentTeacher();

        if ($teacher && !$this->teacherOwnsAssignment($teacher, $mark->class_id, $mark->subject_id)) {
            abort(403, 'You are not assigned to teach this subject in this class.');
        }

        $mark->load(['student', 'subject', 'class']);
        return view('modules.marks.edit', compact('mark'));
    }

    // ──────────────────────────────────────────────────────────
    //  UPDATE — save single mark edit
    // ──────────────────────────────────────────────────────────
    public function update(Request $request, Mark $mark)
    {
        $teacher = $this->currentTeacher();

        if ($teacher && !$this->teacherOwnsAssignment($teacher, $mark->class_id, $mark->subject_id)) {
            abort(403, 'You are not assigned to teach this subject in this class.');
        }

        $request->validate([
            'marks_obtained' => 'required|numeric|min:0',
            'total_marks'    => 'required|numeric|min:1|max:400',
            'remarks'        => 'nullable|string|max:255',
        ]);

        $obtained = min((float) $request->marks_obtained, (float) $request->total_marks);

        $mark->update([
            'marks_obtained' => $obtained,
            'total_marks'    => $request->total_marks,
            'grade'          => $this->grade($obtained, (float) $request->total_marks),
            'remarks'        => $request->remarks,
        ]);

        return redirect()->route('marks.index')->with('success', 'Mark updated successfully.');
    }

    // ──────────────────────────────────────────────────────────
    //  DESTROY
    // ──────────────────────────────────────────────────────────
    public function destroy(Mark $mark)
    {
        $teacher = $this->currentTeacher();

        if ($teacher && !$this->teacherOwnsAssignment($teacher, $mark->class_id, $mark->subject_id)) {
            abort(403, 'You are not assigned to teach this subject in this class.');
        }

        $mark->delete();
        return redirect()->route('marks.index')->with('success', 'Mark deleted.');
    }

    // ──────────────────────────────────────────────────────────
    //  REPORT CARD
    // ──────────────────────────────────────────────────────────
    public function reportCard(Student $student, Request $request)
    {
        $request->validate([
            'term'          => 'required|string',
            'academic_year' => 'required|string',
        ]);

        $marks = Mark::where('student_id', $student->id)
            ->where('term', $request->term)
            ->where('academic_year', $request->academic_year)
            ->with('subject')
            ->get();

        return view('modules.reports.report-card', compact('student', 'marks', 'request'));
    }

    // ──────────────────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────────────────

    /** Returns the Teacher record for the logged-in user, or null for admins. */
    private function currentTeacher(): ?Teacher
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Teacher')) {
            return null;
        }
        return Teacher::where('email', $user->email)->first();
    }

    /** Classes where this teacher is assigned as subject teacher (class_subject pivot). */
    private function teacherClasses(Teacher $teacher)
    {
        return ClassModel::where('is_active', true)
            ->whereHas('subjects', function ($q) use ($teacher) {
                $q->where('class_subject.teacher_id', $teacher->id);
            })
            ->orderBy('name')
            ->get();
    }

    /** Distinct subjects this teacher teaches across any class. */
    private function teacherSubjects(Teacher $teacher)
    {
        return Subject::where('is_active', true)
            ->whereHas('classes', function ($q) use ($teacher) {
                $q->where('class_subject.teacher_id', $teacher->id);
            })
            ->orderBy('name')
            ->get();
    }

    /** Check if a teacher is the subject-teacher for a specific class+subject pair. */
    private function teacherOwnsAssignment(Teacher $teacher, $classId, $subjectId): bool
    {
        return \DB::table('class_subject')
            ->where('class_model_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', $teacher->id)
            ->exists();
    }

    private function grade(float $obtained, float $total): string
    {
        if ($total <= 0) return 'N/A';
        $pct = ($obtained / $total) * 100;
        if ($pct >= 90) return 'A+';
        if ($pct >= 80) return 'A';
        if ($pct >= 70) return 'B+';
        if ($pct >= 60) return 'B';
        if ($pct >= 50) return 'C+';
        if ($pct >= 40) return 'C';
        if ($pct >= 30) return 'D';
        return 'F';
    }

    private function academicYears(): array
    {
        $y = (int) date('Y');
        return ["{$y}-" . ($y + 1), ($y - 1) . "-{$y}", ($y + 1) . "-" . ($y + 2)];
    }

    /**
     * Build a map of class_id → [{id, name, code}] for the Alpine subject dropdown.
     * When $teacher is provided, only includes subjects assigned to that teacher.
     */
    private function buildSubjectsByClass($classes, ?Teacher $teacher = null): array
    {
        $classes->load(['subjects' => function ($q) use ($teacher) {
            $q->where('subjects.is_active', true)->orderBy('subjects.name');
            if ($teacher) {
                $q->where('class_subject.teacher_id', $teacher->id);
            }
        }]);

        $map = [];
        foreach ($classes as $cls) {
            $map[$cls->id] = $cls->subjects->map(fn($s) => [
                'id'   => $s->id,
                'name' => $s->name,
                'code' => $s->code,
            ])->values()->all();
        }
        return $map;
    }
}
