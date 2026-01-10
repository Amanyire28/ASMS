<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\ClassLevel;
use App\Notifications\TeacherAssignmentsSummary;
use App\Notifications\TeacherCreated;
use App\Notifications\TeacherNotification;
use App\Notifications\TeacherUpdated;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{

 protected $notificationService;

    /**
     * Constructor - inject NotificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

 public function index()
    {
        abort_unless(auth()->user()->can('teachers.view'), 403);

        $teachers = Teacher::with(['user.roles', 'classes', 'subjects'])
            ->withCount(['classes', 'subjects'])
            ->orderBy('first_name')
            ->paginate(15);

        return view('modules.teachers.index', compact('teachers'));
    }


    /**
     * Display a listing of the resource.
     */


    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    abort_unless(auth()->user()->can('teachers.create'), 403);

    $classLevels = ClassLevel::with('category')->active()->ordered()->get();
    $subjects = Subject::active()->orderBy('name')->get();

    // Fetch roles from database (excluding Super Admin probably)
    $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')
        ->orderBy('name')
        ->get();

    return view('modules.teachers.create', compact('classLevels', 'subjects', 'roles'));
}

    /**
     * Store a newly created teacher (basic info)
     */
    /**
 * Store a newly created teacher (basic info)
 */
public function storeBasicInfo(Request $request)
{
    abort_unless(auth()->user()->can('teachers.create'), 403);

    // Get valid role names from database for validation
    $validRoles = \Spatie\Permission\Models\Role::pluck('name')->toArray();

    $validator = Validator::make($request->all(), [
        'employee_id' => 'required|unique:teachers,employee_id|string|max:50',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'other_names' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:users,email',
        'phone' => 'required|string|max:20',
        'date_of_birth' => 'required|date|before:-18 years',
        'gender' => 'required|in:male,female,other',
        'role' => ['required', 'string', Rule::in($validRoles)], // Dynamic validation
        'employment_date' => 'required|date',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Please correct the errors in the Basic Information section.'
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Generate email if not provided
        if (empty($request->email)) {
            $email = User::generateSchoolEmail(
                $request->first_name . ' ' . $request->last_name,
                $request->employee_id
            );
        } else {
            $email = $request->email;
        }

        // Create User account
        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $email,
            'password' => Hash::make(User::generateDefaultPassword()),
            'staff_id' => $request->employee_id,
            'phone' => $request->phone,
            'must_change_password' => true,
            'is_active' => true,
        ]);

        // Assign Spatie role (using syncRoles for single role)
        $user->syncRoles([$request->role]);

        // Create Teacher profile
        $teacher = Teacher::create([
            'id' => $user->id,
            'employee_id' => $request->employee_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_names' => $request->other_names,
            'email' => $email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'employment_date' => $request->employment_date,
            'is_active' => true,
        ]);

        DB::commit();



        return response()->json([
            'success' => true,
            'message' => 'Teacher basic information saved successfully!',
            'teacher_id' => $teacher->id,
            'email' => $email,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error saving teacher information: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Store additional details (step 2)
     */
    public function storeAdditionalDetails(Request $request)
    {
        abort_unless(auth()->user()->can('teachers.create'), 403);

        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'national_id' => 'nullable|string|max:50|unique:teachers,national_id',
            'address' => 'required|string|max:500',
            'qualifications' => 'nullable|string',

            'designation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Please correct the errors in the Additional Details section.'
            ], 422);
        }

        try {
            $teacher = Teacher::findOrFail($request->teacher_id);
            $data = $request->except(['teacher_id', 'photo']);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                    Storage::disk('public')->delete($teacher->photo);
                }

                $photoPath = $request->file('photo')->store('teachers/photos', 'public');
                $data['photo'] = $photoPath;
            }

            $teacher->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Additional details saved successfully!',
                'teacher_id' => $teacher->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving additional details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store class assignments (step 3)
     */
   /**
 * Store class assignments (step 3)
 */
/**
 * Store class assignments (step 3)
 */
public function storeClassAssignments(Request $request)
{
    abort_unless(auth()->user()->can('teachers.create'), 403);

    Log::info('Class Assignment Request:', [
        'teacher_id' => $request->teacher_id,
        'class_assignments' => $request->class_assignments,
        'full_request' => $request->all()
    ]);

    $validator = Validator::make($request->all(), [
        'teacher_id' => 'required|exists:teachers,id',
        'class_assignments' => 'nullable|array',
        'class_assignments.*.class_id' => 'required|exists:classes,id',
        'class_assignments.*.is_class_teacher' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        Log::error('Validation failed:', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Please correct the errors in the Class Assignments section.'
        ], 422);
    }

    try {
        $teacher = Teacher::findOrFail($request->teacher_id);
        $assignments = [];
        $classesData = []; // Collect classes for summary notification

        Log::info('Processing assignments:', ['raw' => $request->class_assignments]);

        if ($request->has('class_assignments')) {
            foreach ($request->class_assignments as $index => $assignment) {
                Log::info("Assignment {$index}:", $assignment);
                $class = ClassModel::find($assignment['class_id']);
                $isClassTeacher = $assignment['is_class_teacher'] ?? false;

                if ($class) {
                    // Collect class data for summary notification
                    $classesData[] = [
                        'class' => $class,
                        'is_class_teacher' => $isClassTeacher
                    ];
                }

                $assignments[$assignment['class_id']] = [
                    'is_class_teacher' => $isClassTeacher
                ];
            }
        }

        Log::info('Final assignments array:', $assignments);

        $teacher->classes()->sync($assignments);

        Log::info('Sync completed successfully');



        return response()->json([
            'success' => true,
            'message' => 'Class assignments saved successfully!',
            'teacher_id' => $teacher->id,
        ]);

    } catch (\Exception $e) {
        Log::error('Error saving class assignments:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Error saving class assignments: ' . $e->getMessage()
        ], 500);
    }
}


/**
 * Store subject assignments (step 4) - FINAL STEP
 */
public function storeSubjectAssignments(Request $request)
{
    abort_unless(auth()->user()->can('teachers.create'), 403);

    Log::info('=== STORE SUBJECT ASSIGNMENTS (FINAL STEP) ===');
    Log::info('Request:', $request->all());

    $validator = Validator::make($request->all(), [
        'teacher_id' => 'required|exists:teachers,id',
        'subject_ids' => 'nullable|array',
        'subject_ids.*' => 'exists:subjects,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    try {
        DB::beginTransaction();

        $teacher = Teacher::findOrFail($request->teacher_id);

        if ($request->has('subject_ids')) {
            $teacher->subjects()->sync($request->subject_ids);
        } else {
            $teacher->subjects()->detach();
        }

        DB::commit();

        // ✅ Load ALL assignment data for the summary notification
        $teacher->load([
            'classes' => function($query) {
                $query->withPivot('is_class_teacher');
            },
            'subjects'
        ]);

        Log::info('Teacher loaded with assignments:', [
            'id' => $teacher->id,
            'name' => $teacher->full_name,
            'classes_count' => $teacher->classes->count(),
            'subjects_count' => $teacher->subjects->count(),
        ]);

        // Prepare data for TeacherAssignmentsSummary
        $classesData = [];
        foreach ($teacher->classes as $class) {
            $classesData[] = [
                'class' => $class,
                'is_class_teacher' => $class->pivot->is_class_teacher ?? false
            ];
        }

        $subjectsData = $teacher->subjects->all();

        // ✅ Use TeacherAssignmentsSummary to show assignment details
        $notification = new TeacherAssignmentsSummary($teacher, $classesData, $subjectsData);

        $result = $this->notificationService->notifyByPermissionOrRole(
            $notification,
            'teachers.view',
            ['Super Admin', 'Headteacher', 'Admin Staff', 'Teacher']
        );

        Log::info('✅ Teacher creation completed - notification sent', [
            'teacher_id' => $teacher->id,
            'users_notified' => $result,
            'classes_count' => count($classesData),
            'subjects_count' => count($subjectsData),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teacher registration completed successfully!',
            'teacher_id' => $teacher->id,
            'users_notified' => $result,
            'assignments' => [
                'classes' => count($classesData),
                'subjects' => count($subjectsData),
            ],
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error:', ['message' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    /**
     * Display the specified teacher
     */
    public function show(Teacher $teacher)
    {
        abort_unless(auth()->user()->can('teachers.view'), 403);

        $teacher->load([
            'user.roles',
            'classes' => function ($query) {
                $query->with(['classLevel', 'stream'])
                    ->withCount('students');
            },
            'subjects',
            'classTeacherOf' => function ($query) {
                $query->with(['classLevel', 'stream'])
                    ->withCount('students');
            }
        ]);

        // Get available classes and subjects for assignment
        $availableClasses = ClassModel::with(['classLevel', 'stream'])
            ->active()
            ->orderBy('name')
            ->get();

        $availableSubjects = Subject::active()
            ->orderBy('name')
            ->get();

        return view('modules.teachers.show', compact(
            'teacher',
            'availableClasses',
            'availableSubjects'
        ));
    }

    /**
     * Show the form for editing the specified teacher
     */
public function edit(Teacher $teacher)
{
    abort_unless(auth()->user()->can('teachers.edit'), 403);

    // Eager load user with roles using the new relationship
    $teacher->load(['user.roles', 'classes', 'subjects']);

    // If teacher doesn't have a user, try to find by email or create
    if (!$teacher->user) {
        // Try to find user by email
        $user = User::where('email', $teacher->email)->first();

        if (!$user) {
            // Try to find by staff_id
            $user = User::where('staff_id', $teacher->employee_id)->first();
        }

        if ($user) {
            // Found user, associate it
            $teacher->user()->associate($user);
            $teacher->save();
            $teacher->load('user.roles');
        } else {
            // Create new user
            try {
                DB::beginTransaction();

                $user = User::create([
                    'name' => $teacher->full_name,
                    'email' => $teacher->email,
                    'password' => Hash::make(User::generateDefaultPassword()),
                    'staff_id' => $teacher->employee_id,
                    'phone' => $teacher->phone,
                    'must_change_password' => true,
                    'is_active' => $teacher->is_active,
                ]);

                // Assign default teacher role
                $user->assignRole('Teacher');

                // Associate the user with teacher
                $teacher->user()->associate($user);
                $teacher->save();

                DB::commit();

                // Reload the relationship
                $teacher->load(['user.roles']);

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('teachers.index')
                    ->with('error', 'Failed to create user account: ' . $e->getMessage());
            }
        }
    }

    $classLevels = ClassLevel::with('category')->active()->ordered()->get();
    $subjects = Subject::active()->orderBy('name')->get();

    $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')
        ->orderBy('name')
        ->get();

    return view('modules.teachers.edit', compact('teacher', 'classLevels', 'subjects', 'roles'));
}

    /**
     * Update teacher basic information
     */
    /**
 * Update teacher basic information
 */
public function updateBasicInfo(Request $request, Teacher $teacher)
{
    abort_unless(auth()->user()->can('teachers.edit'), 403);

    // Get valid role names from database
    $validRoles = \Spatie\Permission\Models\Role::pluck('name')->toArray();

    $validator = Validator::make($request->all(), [
        'employee_id' => 'required|unique:teachers,employee_id,' . $teacher->id . '|string|max:50',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'other_names' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users,email,' . $teacher->id,
        'phone' => 'required|string|max:20',
        'date_of_birth' => 'required|date|before:-18 years',
        'gender' => 'required|in:male,female,other',
        'role' => ['required', 'string', Rule::in($validRoles)], // Dynamic validation
        'is_active' => 'boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Please correct the errors in the Basic Information section.'
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Track changes
        $changes = [];
        $oldData = [
            'employee_id' => $teacher->employee_id,
            'first_name' => $teacher->first_name,
            'last_name' => $teacher->last_name,
            'email' => $teacher->email,
            'phone' => $teacher->phone,
            'is_active' => $teacher->is_active,
        ];

        // Update User account
        $teacher->user->update([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'staff_id' => $request->employee_id,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Update Spatie role
        $oldRole = $teacher->user->roles->first()?->name;
        $newRole = $request->role;
        if ($oldRole !== $newRole) {
            $teacher->user->syncRoles([$newRole]);
            $changes['role'] = "Changed from {$oldRole} to {$newRole}";
        }

        // Update Teacher profile
        $teacher->update([
            'employee_id' => $request->employee_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_names' => $request->other_names,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Track field changes
        $newData = [
            'employee_id' => $request->employee_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active'),
        ];

        foreach ($newData as $field => $newValue) {
            if ($oldData[$field] != $newValue) {
                $changes[$field] = "Changed from '{$oldData[$field]}' to '{$newValue}'";
            }
        }

        DB::commit();

    if (!empty($changes)) {
    $notification = new TeacherUpdated($teacher, $changes);
    $this->notificationService->notifyByPermissionOrRole(
        $notification,
        'teachers.view',
        ['Super Admin', 'Headteacher', 'Admin Staff']
    );
}

        return response()->json([
            'success' => true,
            'message' => 'Basic information updated successfully!',
            'teacher' => $teacher->fresh(['user.roles']),
            'changes' => $changes, // Optional: return changes for debugging
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error updating teacher information: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update teacher additional details
     */
    /**
 * Update teacher additional details
 */
public function updateAdditionalDetails(Request $request, Teacher $teacher)
{
    abort_unless(auth()->user()->can('teachers.edit'), 403);

    $validator = Validator::make($request->all(), [
        'national_id' => 'nullable|string|max:50|unique:teachers,national_id,' . $teacher->id,
        'address' => 'required|string|max:500',
        'qualifications' => 'nullable|string',
        'designation' => 'nullable|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Please correct the errors in the Additional Details section.'
        ], 422);
    }

    try {
        $data = $request->except(['photo']);

        // Track changes
        $changes = [];
        $oldData = [
            'national_id' => $teacher->national_id,
            'address' => $teacher->address,
            'qualifications' => $teacher->qualifications,
            'designation' => $teacher->designation,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                Storage::disk('public')->delete($teacher->photo);
            }

            $photoPath = $request->file('photo')->store('teachers/photos', 'public');
            $data['photo'] = $photoPath;
            $changes['photo'] = 'Photo updated';
        }

        $teacher->update($data);

        // Track field changes
        foreach ($oldData as $field => $oldValue) {
            $newValue = $data[$field] ?? null;
            if ($oldValue != $newValue) {
                $changes[$field] = "Updated";
            }
        }


        return response()->json([
            'success' => true,
            'message' => 'Additional details updated successfully!',
            'teacher' => $teacher->fresh(),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating additional details: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Assign classes to teacher
     */
    /**
 * Assign classes to teacher
 */
/**
 * Assign classes to teacher
 */
public function assignClasses(Request $request, Teacher $teacher)
{
    abort_unless(auth()->user()->can('teachers.edit'), 403);

    $validator = Validator::make($request->all(), [
        'class_assignments' => 'required|array',
        'class_assignments.*.class_id' => 'required|exists:classes,id',
        'class_assignments.*.is_class_teacher' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Please correct the errors in the class assignments.'
        ], 422);
    }

    try {
        $assignments = [];
        $classesData = []; // Collect classes for summary notification

        foreach ($request->class_assignments as $assignment) {
            $class = ClassModel::find($assignment['class_id']);
            $isClassTeacher = $assignment['is_class_teacher'] ?? false;

            if ($class) {
                // Collect class data for summary notification
                $classesData[] = [
                    'class' => $class,
                    'is_class_teacher' => $isClassTeacher
                ];
            }

            $assignments[$assignment['class_id']] = [
                'is_class_teacher' => $isClassTeacher
            ];
        }

        $teacher->classes()->sync($assignments);

        // ✅ SEND SUMMARY NOTIFICATION instead of individual ones
        if (!empty($classesData)) {
            $notification = new TeacherAssignmentsSummary($teacher, $classesData, []);
            $this->notificationService->notifyByPermissionOrRole(
                $notification,
                'teachers.view',
                ['Super Admin', 'Headteacher', 'Admin Staff', 'Teacher']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Class assignments updated successfully!',
            'teacher' => $teacher->fresh(['classes']),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating class assignments: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Assign subjects to teacher
     */
    /**
 * Assign subjects to teacher
 */
public function assignSubjects(Request $request, Teacher $teacher)
{
    abort_unless(auth()->user()->can('teachers.edit'), 403);

    $validator = Validator::make($request->all(), [
        'subject_ids' => 'required|array',
        'subject_ids.*' => 'exists:subjects,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Please correct the errors in the subject assignments.'
        ], 422);
    }

    try {
        $subjectsData = []; // Collect subjects for summary notification

        // Collect subject data
        foreach ($request->subject_ids as $subjectId) {
            $subject = Subject::find($subjectId);
            if ($subject) {
                $subjectsData[] = $subject;
            }
        }

        $teacher->subjects()->sync($request->subject_ids);

        // ✅ SEND SUMMARY NOTIFICATION instead of individual ones
        if (!empty($subjectsData)) {
            $notification = new TeacherAssignmentsSummary($teacher, [], $subjectsData);
            $this->notificationService->notifyByPermissionOrRole(
                $notification,
                'teachers.view',
                ['Super Admin', 'Headteacher', 'Admin Staff', 'Teacher']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Subject assignments updated successfully!',
            'teacher' => $teacher->fresh(['subjects']),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating subject assignments: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove class assignment
     */
    public function removeClassAssignment(Request $request, Teacher $teacher)
    {
        abort_unless(auth()->user()->can('teachers.edit'), 403);

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Invalid class provided.'
            ], 422);
        }

        try {
            $teacher->classes()->detach($request->class_id);

            return response()->json([
                'success' => true,
                'message' => 'Class assignment removed successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing class assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove subject assignment
     */
    public function removeSubjectAssignment(Request $request, Teacher $teacher)
    {
        abort_unless(auth()->user()->can('teachers.edit'), 403);

        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Invalid subject provided.'
            ], 422);
        }

        try {
            $teacher->subjects()->detach($request->subject_id);

            return response()->json([
                'success' => true,
                'message' => 'Subject assignment removed successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing subject assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete teacher photo
     */
    public function deletePhoto(Teacher $teacher)
    {
        abort_unless(auth()->user()->can('teachers.edit'), 403);

        try {
            if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                Storage::disk('public')->delete($teacher->photo);
                $teacher->update(['photo' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo deleted successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No photo found to delete.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update teacher status
     */
    public function updateStatus(Request $request, Teacher $teacher)
    {
        abort_unless(auth()->user()->can('teachers.edit'), 403);

        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Invalid status provided.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $teacher->update(['is_active' => $request->boolean('is_active')]);
            $teacher->user->update(['is_active' => $request->boolean('is_active')]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'is_active' => $teacher->is_active,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset teacher password
     */
    public function resetPassword(Teacher $teacher)
    {
        abort_unless(auth()->user()->can('teachers.reset_password'), 403);

        try {
            $teacher->user->update([
                'password' => Hash::make(User::generateDefaultPassword()),
                'must_change_password' => true,
                'password_changed_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully! Default password: ' . User::generateDefaultPassword(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified teacher
     */
    public function destroy(Teacher $teacher)
    {
        abort_unless(auth()->user()->can('teachers.delete'), 403);

        try {
            // Check if teacher has any assignments
            if ($teacher->classes()->count() > 0 || $teacher->subjects()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete teacher with class or subject assignments. Remove assignments first.'
                ], 422);
            }

            // Delete photo if exists
            if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                Storage::disk('public')->delete($teacher->photo);
            }

            // Delete teacher and user
            $teacher->user->delete();
            $teacher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Teacher deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting teacher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search teachers (for AJAX)
     */
    public function search(Request $request)
    {
        $search = $request->input('search', '');

        $teachers = Teacher::with(['user', 'classes', 'subjects'])
            ->where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orWhere('employee_id', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orderBy('first_name')
            ->limit(10)
            ->get();

        return response()->json($teachers);
    }

    /**
     * Get teacher's timetable (optional)
     */
    public function getTimetable(Teacher $teacher)
    {
        abort_unless(auth()->user()->can('teachers.view'), 403);

        // This would load the teacher's timetable based on class/subject assignments
        $teacher->load([
            'classes.timetableSlots',
            'subjects'
        ]);

        // You would implement timetable logic here
        return response()->json([
            'success' => true,
            'timetable' => [] // Implement timetable data
        ]);
    }


    /**
 * Display teacher statistics
 */
public function statistics()
{
    abort_unless(auth()->user()->can('teachers.view'), 403);

    return view('modules.teachers.statistics');
}





/**
 * Update teacher assignments (classes and subjects together)
 */
public function updateAssignments(Request $request, Teacher $teacher)
{
    abort_unless(auth()->user()->can('teachers.edit'), 403);

    $validator = Validator::make($request->all(), [
        'class_assignments' => 'nullable|array',
        'class_assignments.*.class_id' => 'required_with:class_assignments|exists:classes,id',
        'class_assignments.*.is_class_teacher' => 'nullable|boolean',
        'subject_ids' => 'nullable|array',
        'subject_ids.*' => 'exists:subjects,id',
        'removed_class_ids' => 'nullable|array',
        'removed_subject_ids' => 'nullable|array',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Please correct the errors in the assignments.'
        ], 422);
    }

    try {
        DB::beginTransaction();

        $assignedClasses = [];
        $assignedSubjects = [];

        // Handle class assignments
        if ($request->has('class_assignments')) {
            $assignments = [];
            foreach ($request->class_assignments as $assignment) {
                $class = ClassModel::find($assignment['class_id']);
                $isClassTeacher = $assignment['is_class_teacher'] ?? false;

                if ($class) {
                    $assignedClasses[] = [
                        'class' => $class,
                        'is_class_teacher' => $isClassTeacher
                    ];
                }

                $assignments[$assignment['class_id']] = [
                    'is_class_teacher' => $isClassTeacher
                ];
            }
            $teacher->classes()->sync($assignments);
        }

        // Handle subject assignments
        if ($request->has('subject_ids')) {
            foreach ($request->subject_ids as $subjectId) {
                $subject = Subject::find($subjectId);
                if ($subject) {
                    $assignedSubjects[] = $subject;
                }
            }
            $teacher->subjects()->sync($request->subject_ids);
        } elseif ($request->has('subject_ids') && empty($request->subject_ids)) {
            $teacher->subjects()->detach();
        }

        DB::commit();

        // ✅ Send ONE unified notification using TeacherAssignmentsSummary
        if (!empty($assignedClasses) || !empty($assignedSubjects)) {

            // Load fresh data for the summary
            $teacher->load([
                'classes' => function($q) {
                    $q->withPivot('is_class_teacher');
                },
                'subjects'
            ]);

            // Prepare ALL current assignments (not just newly assigned)
            $currentClassesData = [];
            foreach ($teacher->classes as $class) {
                $currentClassesData[] = [
                    'class' => $class,
                    'is_class_teacher' => $class->pivot->is_class_teacher ?? false
                ];
            }

            $currentSubjectsData = $teacher->subjects->all();

            $notification = new TeacherAssignmentsSummary($teacher, $currentClassesData, $currentSubjectsData);

            $this->notificationService->notifyByPermissionOrRole(
                $notification,
                'teachers.view',
                ['Super Admin', 'Headteacher', 'Admin Staff', 'Teacher']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Assignments updated successfully!',
            'teacher' => $teacher->fresh(['classes', 'subjects']),
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error updating assignments: ' . $e->getMessage()
        ], 500);
    }
}
}
