<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class MarkController extends Controller
{
    // ──────────────────────────────────────────────────────────
    //  INDEX — filterable marks table
    // ──────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $classes  = ClassModel::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $terms    = ['Term 1', 'Term 2', 'Term 3'];
        $years    = $this->academicYears();

        $query = Mark::with(['student', 'subject', 'class']);

        if ($request->filled('class_id'))      $query->where('class_id',      $request->class_id);
        if ($request->filled('subject_id'))    $query->where('subject_id',    $request->subject_id);
        if ($request->filled('term'))          $query->where('term',          $request->term);
        if ($request->filled('academic_year')) $query->where('academic_year', $request->academic_year);

        $marks = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('modules.marks.index', compact('marks', 'classes', 'subjects', 'terms', 'years'));
    }

    // ──────────────────────────────────────────────────────────
    //  CREATE — Step 1 selector form
    // ──────────────────────────────────────────────────────────
    public function create()
    {
        $classes = ClassModel::where('is_active', true)->orderBy('name')->get();
        $terms   = ['Term 1', 'Term 2', 'Term 3'];
        $years   = $this->academicYears();

        $subjectsByClass = $this->buildSubjectsByClass($classes);

        return view('modules.marks.entry', compact('classes', 'terms', 'years', 'subjectsByClass'));
    }

    // ──────────────────────────────────────────────────────────
    //  AJAX — return subjects assigned to a given class
    // ──────────────────────────────────────────────────────────
    public function getSubjectsByClass(Request $request)
    {
        $request->validate(['class_id' => 'required|exists:classes,id']);

        $subjects = ClassModel::findOrFail($request->class_id)
            ->subjects()
            ->where('subjects.is_active', true)
            ->orderBy('subjects.name')
            ->get(['subjects.id', 'subjects.name', 'subjects.code']);

        return response()->json($subjects);
    }

    // ──────────────────────────────────────────────────────────
    //  ENTRY — Step 2: load student grid with existing marks
    // ──────────────────────────────────────────────────────────
    public function entry(Request $request)
    {
        $request->validate([
            'class_id'      => 'required|exists:classes,id',
            'subject_id'    => 'required|exists:subjects,id',
            'term'          => 'required|string',
            'academic_year' => 'required|string',
        ]);

        $class         = ClassModel::findOrFail($request->class_id);
        $subject       = Subject::findOrFail($request->subject_id);
        $classSubjects = $class->subjects()->where('subjects.is_active', true)->orderBy('subjects.name')->get();
        $students      = $class->students()->where('is_active', true)->orderBy('first_name')->get();

        $existingMarks = Mark::where([
            'class_id'      => $request->class_id,
            'subject_id'    => $request->subject_id,
            'term'          => $request->term,
            'academic_year' => $request->academic_year,
        ])->get()->keyBy('student_id');

        $selection = $request->only(['class_id', 'subject_id', 'term', 'academic_year']);
        $classes   = ClassModel::where('is_active', true)->orderBy('name')->get();
        $terms     = ['Term 1', 'Term 2', 'Term 3'];
        $years     = $this->academicYears();

        $subjectsByClass = $this->buildSubjectsByClass($classes);

        return view('modules.marks.entry', compact(
            'class', 'subject', 'classSubjects', 'students',
            'existingMarks', 'selection', 'classes', 'terms', 'years', 'subjectsByClass'
        ));
    }

    // ──────────────────────────────────────────────────────────
    //  STORE MULTIPLE — save the whole student grid
    // ──────────────────────────────────────────────────────────
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'class_id'               => 'required|exists:classes,id',
            'subject_id'             => 'required|exists:subjects,id',
            'term'                   => 'required|string',
            'academic_year'          => 'required|string',
            'marks'                  => 'required|array',
            'marks.*.student_id'     => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0',
            'marks.*.total_marks'    => 'required|numeric|min:1|max:400',
            'marks.*.remarks'        => 'nullable|string|max:255',
        ]);

        foreach ($request->marks as $row) {
            $obtained = (float) $row['marks_obtained'];
            $total    = (float) $row['total_marks'];
            $obtained = min($obtained, $total); // can't exceed total

            Mark::updateOrCreate(
                [
                    'student_id'    => $row['student_id'],
                    'subject_id'    => $request->subject_id,
                    'class_id'      => $request->class_id,
                    'term'          => $request->term,
                    'academic_year' => $request->academic_year,
                ],
                [
                    'marks_obtained' => $obtained,
                    'total_marks'    => $total,
                    'grade'          => $this->grade($obtained, $total),
                    'remarks'        => $row['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('marks.index')->with('success', 'Marks saved successfully.');
    }

    // ──────────────────────────────────────────────────────────
    //  EDIT — single mark edit form
    // ──────────────────────────────────────────────────────────
    public function edit(Mark $mark)
    {
        $mark->load(['student', 'subject', 'class']);
        return view('modules.marks.edit', compact('mark'));
    }

    // ──────────────────────────────────────────────────────────
    //  UPDATE — save single mark edit
    // ──────────────────────────────────────────────────────────
    public function update(Request $request, Mark $mark)
    {
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

    private function buildSubjectsByClass($classes): array
    {
        $classes->load(['subjects' => function ($q) {
            $q->where('subjects.is_active', true)->orderBy('subjects.name');
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
