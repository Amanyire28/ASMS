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
        $totalStudents = Cache::remember('dashboard:count:students:active', 60, function () {
            return Student::where('is_active', true)->count();
        });

        $totalTeachers = Cache::remember('dashboard:count:teachers:active', 60, function () {
            return Teacher::where('is_active', true)->count();
        });

        $totalClasses = Cache::remember('dashboard:count:classes:active', 60, function () {
            return ClassModel::where('is_active', true)->count();
        });

        $totalSubjects = Cache::remember('dashboard:count:subjects:active', 60, function () {
            return Subject::where('is_active', true)->count();
        });

        $recentAnnouncements = Cache::remember('dashboard:recent:announcements', 60, function () {
            return Announcement::active()
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();
        });

        $recentReports = Cache::remember('dashboard:recent:reports', 60, function () {
            return ReportGeneration::with(['student', 'generatedBy'])
                ->orderBy('generated_at', 'desc')
                ->limit(5)
                ->get();
        });

        $recentActivities = Cache::remember('dashboard:recent:activities', 30, function () {
            return Mark::with(['student', 'subject'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        });

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
