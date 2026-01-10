<?php

namespace App\Http\Controllers;

use App\Models\ReportGeneration;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = ReportGeneration::with(['student', 'generatedBy'])
            ->orderBy('generated_at', 'desc')
            ->paginate(15);

        return view('modules.reports.index', compact('reports'));
    }

    public function create()
    {
        $classes = ClassModel::where('is_active', true)->get();
        return view('modules.reports.create', compact('classes'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'report_type' => 'required|in:report_card,progress_report,transcript'
        ]);

        // Check if report already exists
        $existingReport = ReportGeneration::where([
            'student_id' => $validated['student_id'],
            'term' => $validated['term'],
            'academic_year' => $validated['academic_year'],
            'report_type' => $validated['report_type']
        ])->first();

        if ($existingReport) {
            return redirect()->route('reports.show', $existingReport)
                ->with('info', 'Report already exists. Showing existing report.');
        }

        // Check if student has marks for this term/year
        $marksExist = Mark::where([
            'student_id' => $validated['student_id'],
            'term' => $validated['term'],
            'academic_year' => $validated['academic_year']
        ])->exists();

        if (!$marksExist) {
            return back()->withErrors([
                'student_id' => 'No marks found for this student in the specified term and academic year.'
            ]);
        }

        // Generate report
        $report = ReportGeneration::create([
            'report_number' => ReportGeneration::generateReportNumber(),
            'student_id' => $validated['student_id'],
            'term' => $validated['term'],
            'academic_year' => $validated['academic_year'],
            'report_type' => $validated['report_type'],
            'generated_by' => Auth::id(),
            'generated_at' => now()
        ]);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report generated successfully.');
    }

    public function show(ReportGeneration $report)
    {
        $report->load(['student.class', 'generatedBy']);
        $marks = $report->getMarks();
        $summary = $report->calculateSummary();

        return view('modules.reports.show', compact('report', 'marks', 'summary'));
    }

    public function print(ReportGeneration $report)
    {
        $report->load(['student.class', 'generatedBy']);
        $marks = $report->getMarks();
        $summary = $report->calculateSummary();

        return view('modules.reports.print', compact('report', 'marks', 'summary'));
    }

    public function destroy(ReportGeneration $report)
    {
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    public function getStudentsByClass(Request $request)
    {
        $classId = $request->get('class_id');
        $students = Student::where('class_id', $classId)
                          ->where('is_active', true)
                          ->orderBy('name')
                          ->get(['id', 'name', 'student_number']);

        return response()->json($students);
    }
}
