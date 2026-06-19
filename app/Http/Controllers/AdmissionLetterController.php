<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AdmissionLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdmissionLetterController extends Controller
{
    /**
     * Show admission letter generation form
     */
    public function create(Student $student)
    {
        abort_unless(auth()->user()->can('students.view-detail'), 403);

        $student->load(['class.stream']);
        $latestLetter = AdmissionLetter::getLatestForStudent($student);

        return view('modules.admissions.generate-letter', compact('student', 'latestLetter'));
    }

    /**
     * Generate and store admission letter
     */
    public function store(Request $request, Student $student)
    {
        abort_unless(auth()->user()->can('students.create'), 403);

        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            // Generate HTML content for the letter
            $letterContent = view('modules.admissions.letter-template', [
                'student' => $student,
                'remarks' => $request->remarks,
            ])->render();

            // Create admission letter record
            $letter = AdmissionLetter::create([
                'student_id' => $student->id,
                'generated_at' => now(),
                'remarks' => $request->remarks,
            ]);

            return redirect()->route('admissions.letter.view', $letter->id)
                ->with('success', 'Admission letter generated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error generating letter: ' . $e->getMessage()]);
        }
    }

    /**
     * View/Display admission letter
     */
    public function show(AdmissionLetter $letter)
    {
        abort_unless(auth()->user()->can('students.view-detail'), 403);

        $letter->load('student.class.stream');

        return view('modules.admissions.view-letter', compact('letter'));
    }

    /**
     * List all admission letters for a student
     */
    public function studentLetters(Student $student)
    {
        abort_unless(auth()->user()->can('students.view-detail'), 403);

        $letters = AdmissionLetter::where('student_id', $student->id)
            ->latest('generated_at')
            ->paginate(10);

        return view('modules.admissions.letters-list', compact('student', 'letters'));
    }

    /**
     * Delete admission letter
     */
    public function destroy(AdmissionLetter $letter)
    {
        abort_unless(auth()->user()->can('students.delete'), 403);

        if ($letter->file_path && Storage::exists($letter->file_path)) {
            Storage::delete($letter->file_path);
        }

        $studentId = $letter->student_id;
        $letter->delete();

        return redirect()->route('students.show', $studentId)
            ->with('success', 'Admission letter deleted successfully.');
    }

    /**
     * Print admission letter (generates PDF or displays for printing)
     */
    public function print(AdmissionLetter $letter)
    {
        abort_unless(auth()->user()->can('students.view-detail'), 403);

        $letter->load('student.class.stream');

        return view('modules.admissions.print-letter', compact('letter'));
    }
}
