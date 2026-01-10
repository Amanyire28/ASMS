<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassLevelController;
use App\Http\Controllers\ClassCategoryController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\SchoolSettingController;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/index', function () {
    return view('index');
});
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Dashboard with SPA support
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth:sanctum', 'verified'])
    ->name('dashboard');

// Password Change Routes (no permission required)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/password/change', function () {
        return view('auth.change-password');
    })->name('password.change');

    Route::post('/password/change', [PasswordChangeController::class, 'update'])
        ->name('password.update');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Admin Routes - Protected by Permissions
Route::prefix('admin')->middleware(['auth:sanctum', 'verified'])->group(function () {

    // ========================================
    // STUDENT MANAGEMENT - Protected
    // ========================================
    Route::middleware('permission:students.view')->group(function () {
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
    });

    Route::get('students/create', [StudentController::class, 'create'])
        ->middleware('permission:students.create')
        ->name('students.create');

    Route::post('students', [StudentController::class, 'store'])
        ->middleware('permission:students.create')
        ->name('students.store');

    Route::get('students/{student}', [StudentController::class, 'show'])
        ->middleware('permission:students.view-detail')
        ->name('students.show');

    Route::get('students/{student}/edit', [StudentController::class, 'edit'])
        ->middleware('permission:students.edit')
        ->name('students.edit');





        Route::post('/teachers/{teacher}/complete-creation', [TeacherController::class, 'completeTeacherCreation'])
    ->name('teachers.complete-creation')
    ->middleware(['auth:sanctum', 'verified', 'permission:teachers.create']);
Route::post('/teachers/{teacher}/update-assignments', [TeacherController::class, 'updateAssignments'])
    ->name('teachers.update-assignments');




    Route::put('students/{student}', [StudentController::class, 'update'])
        ->middleware('permission:students.edit')
        ->name('students.update');

    Route::delete('students/{student}', [StudentController::class, 'destroy'])
        ->middleware('permission:students.delete')
        ->name('students.destroy');

    // ========================================
    // TEACHER MANAGEMENT - Protected
    // ========================================
    Route::middleware('permission:teachers.view')->group(function () {
        Route::get('teachers', [TeacherController::class, 'index'])->name('teachers.index');
    });

    Route::get('teachers/create', [TeacherController::class, 'create'])
        ->middleware('permission:teachers.create')
        ->name('teachers.create');

    Route::get('teachers/{teacher}', [TeacherController::class, 'show'])
        ->middleware('permission:teachers.view-detail')
        ->name('teachers.show');

    Route::get('teachers/{teacher}/edit', [TeacherController::class, 'edit'])
        ->middleware('permission:teachers.edit')
        ->name('teachers.edit');

    Route::delete('teachers/{teacher}', [TeacherController::class, 'destroy'])
        ->middleware('permission:teachers.delete')
        ->name('teachers.destroy');

    // Teacher Statistics
    Route::get('teachers/statistics', [TeacherController::class, 'statistics'])
        ->middleware('permission:teachers.view')
        ->name('teachers.statistics');

    // ========================================
    // TEACHER SPA-LIKE ROUTES (AJAX)
    // ========================================

    // Create Teacher (Multi-step form)
    Route::post('teachers/store-basic', [TeacherController::class, 'storeBasicInfo'])
        ->middleware('permission:teachers.create')
        ->name('teachers.store.basic');

    Route::post('teachers/store-additional', [TeacherController::class, 'storeAdditionalDetails'])
        ->middleware('permission:teachers.create')
        ->name('teachers.store.additional');

    Route::post('teachers/store-classes', [TeacherController::class, 'storeClassAssignments'])
        ->middleware('permission:teachers.create')
        ->name('teachers.store.classes');

    Route::post('teachers/store-subjects', [TeacherController::class, 'storeSubjectAssignments'])
        ->middleware('permission:teachers.create')
        ->name('teachers.store.subjects');

    // Update Teacher (Multi-section form)
    Route::post('teachers/{teacher}/update-basic', [TeacherController::class, 'updateBasicInfo'])
        ->middleware('permission:teachers.edit')
        ->name('teachers.update.basic');

    Route::post('teachers/{teacher}/update-additional', [TeacherController::class, 'updateAdditionalDetails'])
        ->middleware('permission:teachers.edit')
        ->name('teachers.update.additional');

    Route::post('teachers/{teacher}/assign-classes', [TeacherController::class, 'assignClasses'])
        ->middleware('permission:teachers.assign')
        ->name('teachers.assign.classes');

    Route::post('teachers/{teacher}/assign-subjects', [TeacherController::class, 'assignSubjects'])
        ->middleware('permission:teachers.assign')
        ->name('teachers.assign.subjects');

    // Remove assignments
    Route::post('teachers/{teacher}/remove-class', [TeacherController::class, 'removeClassAssignment'])
        ->middleware('permission:teachers.assign')
        ->name('teachers.remove.class');

    Route::post('teachers/{teacher}/remove-subject', [TeacherController::class, 'removeSubjectAssignment'])
        ->middleware('permission:teachers.assign')
        ->name('teachers.remove.subject');

    // Other teacher operations
    Route::post('teachers/{teacher}/update-status', [TeacherController::class, 'updateStatus'])
        ->middleware('permission:teachers.edit')
        ->name('teachers.update.status');

    Route::post('teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])
        ->middleware('permission:teachers.reset_password')
        ->name('teachers.reset.password');

    Route::delete('teachers/{teacher}/delete-photo', [TeacherController::class, 'deletePhoto'])
        ->middleware('permission:teachers.edit')
        ->name('teachers.delete.photo');

    // Search and timetable
    Route::get('teachers/search', [TeacherController::class, 'search'])
        ->middleware('permission:teachers.view')
        ->name('teachers.search');

    Route::get('teachers/{teacher}/timetable', [TeacherController::class, 'getTimetable'])
        ->middleware('permission:teachers.view-detail')
        ->name('teachers.timetable');





// Route::put('teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    // ========================================
    // CLASS CATEGORY MANAGEMENT
    // ========================================
    Route::middleware('permission:classes.view')->group(function () {
        Route::get('class-categories', [ClassCategoryController::class, 'index'])->name('class-categories.index');
    });

    Route::get('class-categories/create', [ClassCategoryController::class, 'create'])
        ->middleware('permission:classes.create')
        ->name('class-categories.create');

    Route::post('class-categories', [ClassCategoryController::class, 'store'])
        ->middleware('permission:classes.create')
        ->name('class-categories.store');

    Route::get('class-categories/{class_category}', [ClassCategoryController::class, 'show'])
        ->middleware('permission:classes.view-detail')
        ->name('class-categories.show');

    Route::get('class-categories/{class_category}/edit', [ClassCategoryController::class, 'edit'])
        ->middleware('permission:classes.edit')
        ->name('class-categories.edit');

    Route::put('class-categories/{class_category}', [ClassCategoryController::class, 'update'])
        ->middleware('permission:classes.edit')
        ->name('class-categories.update');

    Route::delete('class-categories/{class_category}', [ClassCategoryController::class, 'destroy'])
        ->middleware('permission:classes.delete')
        ->name('class-categories.destroy');

    // ========================================
    // CLASS LEVEL MANAGEMENT
    // ========================================
    Route::middleware('permission:classes.view')->group(function () {
        Route::get('class-levels', [ClassLevelController::class, 'index'])->name('class-levels.index');
    });

    Route::get('class-levels/create', [ClassLevelController::class, 'create'])
        ->middleware('permission:classes.create')
        ->name('class-levels.create');

    Route::post('class-levels', [ClassLevelController::class, 'store'])
        ->middleware('permission:classes.create')
        ->name('class-levels.store');

    Route::get('class-levels/{class_level}', [ClassLevelController::class, 'show'])
        ->middleware('permission:classes.view-detail')
        ->name('class-levels.show');

    Route::get('class-levels/{class_level}/edit', [ClassLevelController::class, 'edit'])
        ->middleware('permission:classes.edit')
        ->name('class-levels.edit');

    Route::put('class-levels/{class_level}', [ClassLevelController::class, 'update'])
        ->middleware('permission:classes.edit')
        ->name('class-levels.update');

    Route::delete('class-levels/{class_level}', [ClassLevelController::class, 'destroy'])
        ->middleware('permission:classes.delete')
        ->name('class-levels.destroy');

    // ========================================
    // STREAM MANAGEMENT
    // ========================================
    Route::middleware('permission:classes.view')->group(function () {
        Route::get('streams', [StreamController::class, 'index'])->name('streams.index');
    });

    Route::get('streams/create', [StreamController::class, 'create'])
        ->middleware('permission:classes.create')
        ->name('streams.create');

    Route::post('streams', [StreamController::class, 'store'])
        ->middleware('permission:classes.create')
        ->name('streams.store');

    Route::get('streams/{stream}', [StreamController::class, 'show'])
        ->middleware('permission:classes.view-detail')
        ->name('streams.show');

    Route::get('streams/{stream}/edit', [StreamController::class, 'edit'])
        ->middleware('permission:classes.edit')
        ->name('streams.edit');

    Route::put('streams/{stream}', [StreamController::class, 'update'])
        ->middleware('permission:classes.edit')
        ->name('streams.update');

    Route::delete('streams/{stream}', [StreamController::class, 'destroy'])
        ->middleware('permission:classes.delete')
        ->name('streams.destroy');

    // ========================================
    // CLASS MANAGEMENT - Protected
    // ========================================
    Route::middleware('permission:classes.view')->group(function () {
        Route::get('classes', [ClassController::class, 'index'])->name('classes.index');
    });

    Route::get('classes/create', [ClassController::class, 'create'])
        ->middleware('permission:classes.create')
        ->name('classes.create');

    Route::post('classes', [ClassController::class, 'store'])
        ->middleware('permission:classes.create')
        ->name('classes.store');

    Route::get('classes/{class}', [ClassController::class, 'show'])
        ->middleware('permission:classes.view-detail')
        ->name('classes.show');

    Route::get('classes/{class}/edit', [ClassController::class, 'edit'])
        ->middleware('permission:classes.edit')
        ->name('classes.edit');

    Route::put('classes/{class}', [ClassController::class, 'update'])
        ->middleware('permission:classes.edit')
        ->name('classes.update');

    Route::delete('classes/{class}', [ClassController::class, 'destroy'])
        ->middleware('permission:classes.delete')
        ->name('classes.destroy');

    Route::post('classes/{class}/assign-subjects', [ClassController::class, 'assignSubjects'])
        ->middleware('permission:classes.assign-subjects')
        ->name('classes.assign-subjects');

    // ========================================
    // SUBJECT MANAGEMENT - Protected
    // ========================================
    Route::middleware('permission:subjects.view')->group(function () {
        Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
    });

    Route::get('subjects/create', [SubjectController::class, 'create'])
        ->middleware('permission:subjects.create')
        ->name('subjects.create');

    Route::post('subjects', [SubjectController::class, 'store'])
        ->middleware('permission:subjects.create')
        ->name('subjects.store');

    Route::get('subjects/{subject}', [SubjectController::class, 'show'])
        ->middleware('permission:subjects.view-detail')
        ->name('subjects.show');

    Route::get('subjects/{subject}/edit', [SubjectController::class, 'edit'])
        ->middleware('permission:subjects.edit')
        ->name('subjects.edit');

    Route::put('subjects/{subject}', [SubjectController::class, 'update'])
        ->middleware('permission:subjects.edit')
        ->name('subjects.update');

    Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])
        ->middleware('permission:subjects.delete')
        ->name('subjects.destroy');

    // ========================================
    // MARKS MANAGEMENT - Protected
    // ========================================
    Route::middleware('permission:marks.view')->group(function () {
        Route::get('marks', [MarkController::class, 'index'])->name('marks.index');
    });

    Route::get('marks-entry', [MarkController::class, 'create'])
        ->middleware('permission:marks.entry')
        ->name('marks.entry.form');

    Route::post('marks-entry', [MarkController::class, 'entry'])
        ->middleware('permission:marks.entry')
        ->name('marks.entry');

    Route::post('marks-store-multiple', [MarkController::class, 'storeMultiple'])
        ->middleware('permission:marks.entry')
        ->name('marks.store.multiple');

    Route::get('marks/{mark}/edit', [MarkController::class, 'edit'])
        ->middleware('permission:marks.edit')
        ->name('marks.edit');

    Route::put('marks/{mark}', [MarkController::class, 'update'])
        ->middleware('permission:marks.edit')
        ->name('marks.update');

    Route::delete('marks/{mark}', [MarkController::class, 'destroy'])
        ->middleware('permission:marks.delete')
        ->name('marks.destroy');

    // ========================================
    // REPORT CARD - Protected
    // ========================================
    Route::get('report-card/form', [ReportController::class, 'form'])
        ->middleware('permission:reports.view')
        ->name('report.card.form');

    Route::post('report-card/generate', [ReportController::class, 'generate'])
        ->middleware('permission:reports.generate')
        ->name('report.card.generate');

    Route::get('report-card/{student}', [ReportController::class, 'show'])
        ->middleware('permission:reports.view')
        ->name('report.card');

    Route::get('report-card/{student}/download', [ReportController::class, 'download'])
        ->middleware('permission:reports.export')
        ->name('report.card.download');

    // ========================================
    // SYSTEM MANAGEMENT - Protected
    // ========================================
    Route::middleware('permission:system.users')->group(function () {
        Route::get('system', [SystemController::class, 'index'])->name('system.index');
        Route::get('system/users', [SystemController::class, 'users'])->name('system.users');
        Route::get('system/users/{user}/permissions', [SystemController::class, 'editUserPermissions'])->name('system.user-permissions');
        Route::post('system/users/{user}/permissions', [SystemController::class, 'updateUserPermissions'])->name('system.update-user-permissions');
    });

    Route::middleware('permission:system.roles')->group(function () {
        Route::get('system/roles', [SystemController::class, 'roles'])->name('system.roles');
    });

    Route::middleware('permission:system.settings')->group(function () {
        Route::get('system/settings', [SystemController::class, 'settings'])->name('system.settings');
        Route::post('system/settings', [SystemController::class, 'updateSettings'])->name('system.settings.update');
    });

    // ========================================
    // SCHOOL SETTINGS - Protected
    // ========================================
    Route::middleware('permission:system.settings')->group(function () {
        // Main settings page
        Route::get('settings/school-profile', [SchoolSettingController::class, 'edit'])
            ->name('settings.school-profile');

        // Update routes for each section
        Route::post('settings/school-profile/basic-info', [SchoolSettingController::class, 'updateBasicInfo'])
            ->name('settings.update-basic-info');

        Route::post('settings/school-profile/contact-info', [SchoolSettingController::class, 'updateContactInfo'])
            ->name('settings.update-contact-info');

        Route::post('settings/school-profile/academic-structure', [SchoolSettingController::class, 'updateAcademicStructure'])
            ->name('settings.update-academic-structure');

        Route::post('settings/school-profile/email-config', [SchoolSettingController::class, 'updateEmailConfig'])
            ->name('settings.update-email-config');

        Route::post('settings/school-profile/report-card', [SchoolSettingController::class, 'updateReportCardSettings'])
            ->name('settings.update-report-card');

        // Delete routes
        Route::delete('settings/school-profile/delete-logo', [SchoolSettingController::class, 'deleteLogo'])
            ->name('settings.delete-logo');

        Route::delete('settings/school-profile/delete-signature', [SchoolSettingController::class, 'deleteSignature'])
            ->name('settings.delete-signature');
    });

});

// Notification Routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');
    Route::post('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::get('notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
});

// Custom Profile Routes (REPLACING Jetstream)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Main profile page
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Force password change
    Route::get('/profile/force-password-change', [ProfileController::class, 'forcePasswordChange'])
        ->name('profile.force-password-change');
    Route::post('/profile/force-password-change', [ProfileController::class, 'updateForcedPassword'])
        ->name('profile.update-forced-password');

    // Additional profile pages (optional)
    Route::get('/profile/activity', [ProfileController::class, 'activity'])->name('profile.activity');
    Route::get('/profile/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
    Route::put('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.update-notifications');
});

// Add middleware to redirect Jetstream profile URLs to our custom ones
Route::get('/user/profile', function () {
    return redirect()->route('profile.edit');
})->middleware(['auth:sanctum', 'verified']);

Route::get('/user/profile-information', function () {
    return redirect()->route('profile.edit');
})->middleware(['auth:sanctum', 'verified']);






