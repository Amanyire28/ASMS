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
        $examTypes = $this->getExamTypes();

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

            // 3-D grid: [student_id][subject_id][exam_type] = Mark
            $marksGrid = [];
            foreach ($flat as $m) {
                $marksGrid[$m->student_id][$m->subject_id][$m->exam_type] = $m;
            }

            // Aggregated per (student, subject): sum across all exam types
            $marksAgg = []; // [student_id][subject_id] = ['obtained','total','mark_id']
            foreach ($flat as $m) {
                $agg = &$marksAgg[$m->student_id][$m->subject_id];
                if (!isset($agg)) {
                    $agg = ['obtained' => 0, 'total' => 0, 'mark_id' => $m->id];
                }
                $agg['obtained'] += (float) $m->marks_obtained;
                $agg['total']    += (float) $m->total_marks;
            }

            $selection = $request->only(['class_id', 'term', 'academic_year']);

            return view('modules.marks.index', compact(
                'classes', 'terms', 'years', 'examTypes',
                'class', 'classSubjects', 'students', 'marksGrid', 'marksAgg', 'selection'
            ));
        }

        // ── Prompt mode: not all filters set yet ──
        return view('modules.marks.index', compact('classes', 'terms', 'years', 'examTypes'));
    }

    // ──────────────────────────────────────────────────────────
    //  CREATE — Step 1 selector form
    // ──────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $teacher = $this->currentTeacher();

        $classes         = $teacher
            ? $this->teacherClasses($teacher)
            : ClassModel::where('is_active', true)->orderBy('name')->get();
        $terms           = ['Term 1', 'Term 2', 'Term 3'];
        $years           = $this->academicYears();
        $examTypes       = $this->getExamTypes();
        $subjectsByClass = $this->buildSubjectsByClass($classes, $teacher);

        // GET-based load — for "Next / Prev Subject" redirect after save
        if ($request->filled(['class_id', 'term', 'academic_year', 'exam_type_id', 'subject_id'])) {
            return $this->renderSubjectEntry($request, $classes, $terms, $years, $examTypes, $subjectsByClass);
        }

        return view('modules.marks.entry', compact('classes', 'terms', 'years', 'examTypes', 'subjectsByClass'));
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
    //  ENTRY — Step 2: load student list for one subject
    // ──────────────────────────────────────────────────────────
    public function entry(Request $request)
    {
        $request->validate([
            'class_id'      => 'required|exists:classes,id',
            'term'          => 'required|string',
            'academic_year' => 'required|string',
            'exam_type_id'  => 'required|string',
            'subject_id'    => 'required|exists:subjects,id',
        ]);

        $teacher         = $this->currentTeacher();
        $classes         = $teacher
            ? $this->teacherClasses($teacher)
            : ClassModel::where('is_active', true)->orderBy('name')->get();
        $terms           = ['Term 1', 'Term 2', 'Term 3'];
        $years           = $this->academicYears();
        $examTypes       = $this->getExamTypes();
        $subjectsByClass = $this->buildSubjectsByClass($classes, $teacher);

        return $this->renderSubjectEntry($request, $classes, $terms, $years, $examTypes, $subjectsByClass);
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
            'marks'         => 'required|array',
        ]);

        $teacher = $this->currentTeacher();

        // marks[studentId][subjectId][examTypeId] = obtained
        // total[subjectId][examTypeId]            = max_marks
        foreach ($request->marks as $studentId => $subjects) {
            foreach ($subjects as $subjectId => $examTypes) {
                if (!is_array($examTypes)) {
                    // Fallback for legacy flat submission (no exam type)
                    $examTypes = ['Final' => $examTypes];
                }
                foreach ($examTypes as $examTypeId => $obtained) {
                    if ($obtained === null || $obtained === '') continue;

                    if ($teacher && !$this->teacherOwnsAssignment($teacher, $request->class_id, $subjectId)) {
                        continue;
                    }

                    $total    = (float) (($request->total[$subjectId][$examTypeId] ?? null) ?? ($request->total[$subjectId] ?? 100));
                    $obtained = min((float) $obtained, $total);

                    Mark::updateOrCreate(
                        [
                            'student_id'    => $studentId,
                            'subject_id'    => $subjectId,
                            'class_id'      => $request->class_id,
                            'term'          => $request->term,
                            'academic_year' => $request->academic_year,
                            'exam_type'     => $examTypeId,
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
        }

        $nextSubjectId = $request->input('next_subject_id');
        if ($nextSubjectId) {
            return redirect()->route('marks.entry.form', array_filter([
                'class_id'      => $request->class_id,
                'term'          => $request->term,
                'academic_year' => $request->academic_year,
                'exam_type_id'  => $request->input('exam_type_id'),
                'subject_id'    => $nextSubjectId,
            ]))->with('success', 'Marks saved. Loading next subject…');
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

    /**
     * Load the per-subject mark entry view — shared by GET (prev/next) and POST (form submit).
     */
    private function renderSubjectEntry(
        Request $request,
        $classes,
        array $terms,
        array $years,
        array $examTypes,
        array $subjectsByClass
    ) {
        $teacher = $this->currentTeacher();
        $class   = ClassModel::findOrFail($request->class_id);

        $selectedExamType = collect($examTypes)->firstWhere('id', $request->exam_type_id);
        if (!$selectedExamType) {
            return back()->withErrors(['exam_type_id' => 'Invalid exam type selected.'])->withInput();
        }

        // All subjects for this class (teacher-scoped), ordered alphabetically
        $allSubjectsQuery = $class->subjects()
            ->where('subjects.is_active', true)
            ->orderBy('subjects.name');
        if ($teacher) {
            $allSubjectsQuery->where('class_subject.teacher_id', $teacher->id);
        }
        $allSubjects = $allSubjectsQuery->get();

        if ($teacher && $allSubjects->isEmpty()) {
            abort(403, 'You are not assigned to teach any subjects in this class.');
        }

        $subject = $allSubjects->firstWhere('id', (int) $request->subject_id);
        if (!$subject) {
            return back()->withErrors(['subject_id' => 'Subject not found in this class.'])->withInput();
        }

        // Prev / Next navigation
        $subjectIds   = $allSubjects->pluck('id')->values();
        $currentIndex = $subjectIds->search($subject->id);
        $prevSubject  = $currentIndex > 0 ? $allSubjects[$currentIndex - 1] : null;
        $nextSubject  = $currentIndex < $allSubjects->count() - 1 ? $allSubjects[$currentIndex + 1] : null;

        $students = $class->students()->where('is_active', true)->orderBy('first_name')->get();
        $etId     = $selectedExamType['id'];

        // Marks for this exact class/term/year/exam_type/subject
        $flat = Mark::where([
            'class_id'      => $request->class_id,
            'term'          => $request->term,
            'academic_year' => $request->academic_year,
            'exam_type'     => $etId,
            'subject_id'    => $subject->id,
        ])->get();

        $existingMarks = $flat->keyBy('student_id');

        // initTotals[subject_id][etId] — use saved total if present, else config default
        $savedFirst = $flat->first();
        $initTotVal = $savedFirst ? (float) $savedFirst->total_marks : (float) $selectedExamType['max_marks'];
        $initTotals = [(string) $subject->id => [$etId => $initTotVal]];

        // initialVals[student_id][(string)subject_id][$etId]
        $initialVals = [];
        foreach ($students as $stu) {
            $m = $existingMarks[$stu->id] ?? null;
            $initialVals[$stu->id] = [
                (string) $subject->id => [
                    $etId => $m !== null ? (string) $m->marks_obtained : '',
                ],
            ];
        }

        $selection = $request->only(['class_id', 'term', 'academic_year', 'exam_type_id', 'subject_id']);

        return view('modules.marks.entry', compact(
            'class', 'subject', 'allSubjects', 'prevSubject', 'nextSubject',
            'students', 'examTypes', 'selectedExamType',
            'existingMarks', 'initTotals', 'initialVals', 'selection',
            'classes', 'terms', 'years', 'subjectsByClass'
        ));
    }

    /** Return configured exam types; falls back to single 'Final' type for legacy behaviour. */
    private function getExamTypes(): array
    {
        $types = \App\Models\SchoolSetting::get('exam_types') ?? [];
        if (empty($types)) {
            return [['id' => 'Final', 'label' => 'Final Exam', 'max_marks' => 100, 'order' => 1]];
        }
        return $types;
    }

    private function grade(float $obtained, float $total): string
    {
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
