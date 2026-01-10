<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Mark;
use App\Models\ReportGeneration;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Subject;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Will add auth middleware later
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get counts
        $totalStudents = Student::where('is_active', true)->count();
        $totalTeachers = Teacher::where('is_active', true)->count();
        $totalClasses = ClassModel::where('is_active', true)->count();
        $totalSubjects = Subject::where('is_active', true)->count();

        // Get recent announcements
        $recentAnnouncements = Announcement::active()
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();

        // Get recent reports
        $recentReports = ReportGeneration::with(['student', 'generatedBy'])
            ->orderBy('generated_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent activities (marks entered recently)
        $recentActivities = Mark::with(['student', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalStudents', 'totalTeachers', 'totalClasses', 'totalSubjects',
            'recentAnnouncements', 'recentReports', 'recentActivities'
        ));
    }

    /**
     * Show the welcome/landing page.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view('index');
    }
}
