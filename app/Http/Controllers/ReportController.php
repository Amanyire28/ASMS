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
        $marks   = $report->getMarks();
        $summary = $report->calculateSummary($marks);

        return view('modules.reports.show', compact('report', 'marks', 'summary'));
    }

    public function print(ReportGeneration $report)
    {
        $report->load(['student.class', 'generatedBy']);
        $marks   = $report->getMarks();
        $summary = $report->calculateSummary($marks);

        return view('modules.reports.print', compact('report', 'marks', 'summary'));
    }

    public function destroy(ReportGeneration $report)
    {
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    public function viewOrGenerate(Request $request)
    {
        $validated = $request->validate([
            'student_id'    => 'required|exists:students,id',
            'term'          => 'required|string',
            'academic_year' => 'required|string',
            'report_type'   => 'required|in:report_card,progress_report,transcript',
        ]);

        // Find existing report or create a new one
        $report = ReportGeneration::where([
            'student_id'    => $validated['student_id'],
            'term'          => $validated['term'],
            'academic_year' => $validated['academic_year'],
            'report_type'   => $validated['report_type'],
        ])->first();

        if (!$report) {
            $marksExist = Mark::where([
                'student_id'    => $validated['student_id'],
                'term'          => $validated['term'],
                'academic_year' => $validated['academic_year'],
            ])->exists();

            if (!$marksExist) {
                // Return error inline for HTMX, redirect for normal requests
                $errorMsg = 'No marks found for this student in the selected term and academic year.';
                if ($request->header('HX-Request')) {
                    return response(
                        '<div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">'
                        . '<i class="fas fa-exclamation-circle mr-2"></i>' . e($errorMsg)
                        . '</div>',
                        200
                    );
                }
                return redirect()->route('reports.create')->with('error', $errorMsg);
            }

            $report = ReportGeneration::create([
                'report_number' => ReportGeneration::generateReportNumber(),
                'student_id'    => $validated['student_id'],
                'term'          => $validated['term'],
                'academic_year' => $validated['academic_year'],
                'report_type'   => $validated['report_type'],
                'generated_by'  => Auth::id(),
                'generated_at'  => now(),
            ]);
        }

        $report->load(['student.class', 'generatedBy']);
        $marks   = $report->getMarks();
        $summary = $report->calculateSummary($marks);

        return view('modules.reports.show', compact('report', 'marks', 'summary'));
    }

    public function getStudentsByClass(Request $request)
    {
        $classId      = $request->get('class_id');
        $term         = $request->get('term');
        $academicYear = $request->get('academic_year');

        $students = Student::where('class_id', $classId)
                          ->where('is_active', true)
                          ->orderBy('first_name')
                          ->get(['id', 'first_name', 'last_name', 'student_id']);

        // Pre-fetch all distinct term+year combinations that have marks for students in this class
        $studentIds = $students->pluck('id');
        $allPeriods = Mark::whereIn('student_id', $studentIds)
            ->select('student_id', 'term', 'academic_year')
            ->distinct()
            ->get()
            ->groupBy('student_id');

        return response()->json($students->map(function ($s) use ($term, $academicYear, $allPeriods) {
            $hasMarks = ($term && $academicYear)
                ? Mark::where([
                    'student_id'    => $s->id,
                    'term'          => $term,
                    'academic_year' => $academicYear,
                ])->exists()
                : null; // null means "not checked"

            // Build list of periods that actually have marks for this student
            $periods = isset($allPeriods[$s->id])
                ? $allPeriods[$s->id]->map(fn($m) => $m->term . ' / ' . $m->academic_year)->values()->all()
                : [];

            return [
                'id'               => $s->id,
                'name'             => $s->first_name . ' ' . $s->last_name,
                'student_id'       => $s->student_id,
                'has_marks'        => $hasMarks,
                'available_periods' => $periods,
            ];
        }));
    }
}
