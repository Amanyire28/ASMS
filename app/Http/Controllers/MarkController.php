<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class MarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marks = Mark::with(['student', 'subject', 'class'])->paginate(15);
        return view('modules.marks.entry', compact('marks'));
    }

    /**
     * Show the form for creating new marks entry.
     */
    public function create()
    {
        $classes = ClassModel::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        return view('modules.marks.entry', compact('classes', 'subjects'));
    }

    /**
     * Show marks entry form for specific class and subject.
     */
    public function entry(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|string',
            'academic_year' => 'required|string'
        ]);

        $class = ClassModel::findOrFail($validated['class_id']);
        $subject = Subject::findOrFail($validated['subject_id']);
        $students = $class->students()->where('is_active', true)->get();

        // Get existing marks for this combination
        $existingMarks = Mark::where([
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'term' => $validated['term'],
            'academic_year' => $validated['academic_year']
        ])->get()->keyBy('student_id');

        return view('modules.marks.entry', compact(
            'class', 'subject', 'students', 'existingMarks', 'validated'
        ));
    }

    /**
     * Store marks for multiple students.
     */
    public function storeMultiple(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0|max:100',
            'marks.*.total_marks' => 'required|numeric|min:1|max:100',
            'marks.*.remarks' => 'nullable|string'
        ]);

        foreach ($validated['marks'] as $markData) {
            Mark::updateOrCreate(
                [
                    'student_id' => $markData['student_id'],
                    'subject_id' => $validated['subject_id'],
                    'class_id' => $validated['class_id'],
                    'term' => $validated['term'],
                    'academic_year' => $validated['academic_year']
                ],
                [
                    'marks_obtained' => $markData['marks_obtained'],
                    'total_marks' => $markData['total_marks'],
                    'remarks' => $markData['remarks'] ?? null
                ]
            );
        }

        return redirect()->route('marks.index')
                        ->with('success', 'Marks saved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'marks_obtained' => 'required|numeric|min:0|max:100',
            'total_marks' => 'required|numeric|min:1|max:100',
            'remarks' => 'nullable|string'
        ]);

        Mark::create($validated);

        return redirect()->route('marks.index')
                        ->with('success', 'Mark created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Mark $mark)
    {
        $mark->load(['student', 'subject', 'class']);
        return view('modules.marks.show', compact('mark'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mark $mark)
    {
        $students = Student::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $classes = ClassModel::where('is_active', true)->get();
        return view('modules.marks.edit', compact('mark', 'students', 'subjects', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mark $mark)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'marks_obtained' => 'required|numeric|min:0|max:100',
            'total_marks' => 'required|numeric|min:1|max:100',
            'grade' => 'nullable|string|max:5',
            'remarks' => 'nullable|string'
        ]);

        $mark->update($validated);

        return redirect()->route('marks.index')
                        ->with('success', 'Mark updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mark $mark)
    {
        $mark->delete();

        return redirect()->route('marks.index')
                        ->with('success', 'Mark deleted successfully.');
    }

    /**
     * Generate report card for a student.
     */
    public function reportCard(Student $student, Request $request)
    {
        $validated = $request->validate([
            'term' => 'required|string',
            'academic_year' => 'required|string'
        ]);

        $marks = Mark::where([
            'student_id' => $student->id,
            'term' => $validated['term'],
            'academic_year' => $validated['academic_year']
        ])->with('subject')->get();

        return view('modules.reports.report-card', compact('student', 'marks', 'validated'));
    }
}
