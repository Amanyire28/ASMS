<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $students = Student::with('class')->paginate(15);

        // DEBUG: Log what type of request this is
        Log::info('Students Index Request', [
            'is_htmx' => $request->header('HX-Request'),
            'headers' => $request->headers->all()
        ]);

        // For HTMX requests - return ONLY content
        if ($request->header('HX-Request')) {
            Log::info('Returning view for HTMX');
            return view('modules.students.index', compact('students'));
        }

        // For regular requests - return full page
        Log::info('Returning full page');
        return view('modules.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $classes = ClassModel::where('is_active', true)->get();

        return view('modules.students.create', compact('classes'));
    }

    /**
     * Show the form for importing students in bulk.
     */
    public function import(Request $request)
    {
        return view('modules.students.import');
    }

    /**
     * Import students from a CSV file.
     */
    public function importSubmit(Request $request)
    {
        $request->validate([
            'students_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('students_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = null;
        $rows = [];
        $line = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $line++;
            if ($line === 1) {
                $header = array_map(fn($value) => strtolower(trim($value)), $row);
                continue;
            }

            if (!array_filter($row)) {
                continue;
            }

            if (count($header) !== count($row)) {
                $failed[] = 'Row ' . ($line) . ': header/column count mismatch';
                continue;
            }

            $rows[] = array_combine($header, array_map('trim', $row));
        }

        fclose($handle);

        if (empty($rows)) {
            return redirect()->back()->with('error', 'The uploaded CSV file is empty or missing rows.');
        }

        $imported = 0;
        $failed = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = [
                'first_name' => $row['first_name'] ?? null,
                'last_name' => $row['last_name'] ?? null,
                'date_of_birth' => $row['date_of_birth'] ?? null,
                'gender' => $row['gender'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'address' => $row['address'] ?? null,
                'parent_name' => $row['parent_name'] ?? null,
                'parent_phone' => $row['parent_phone'] ?? null,
                'parent_email' => $row['parent_email'] ?? null,
                'class_id' => null,
                'enrollment_date' => $row['enrollment_date'] ?? now()->toDateString(),
                'is_active' => filter_var($row['is_active'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            ];

            if (!empty($row['class_id'])) {
                $data['class_id'] = $row['class_id'];
            } elseif (!empty($row['class_name'])) {
                $className = trim($row['class_name']);
                $class = ClassModel::whereRaw('LOWER(name) = ?', [strtolower($className)])->first();
                $data['class_id'] = $class?->id;
            }

            try {
                $validator = \Validator::make($data, [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'date_of_birth' => 'required|date',
                    'gender' => 'required|in:male,female,other',
                    'email' => 'nullable|email|unique:students',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string',
                    'parent_name' => 'nullable|string|max:255',
                    'parent_phone' => 'nullable|string|max:20',
                    'parent_email' => 'nullable|email',
                    'class_id' => 'nullable|exists:classes,id',
                    'enrollment_date' => 'required|date',
                    'is_active' => 'boolean',
                ]);

                if ($validator->fails()) {
                    $failed[] = 'Row ' . $rowNumber . ': ' . implode('; ', $validator->errors()->all());
                    continue;
                }

                Student::create($data);
                $imported++;
            } catch (\Throwable $e) {
                $failed[] = 'Row ' . $rowNumber . ': ' . $e->getMessage();
            }
        }

        $message = "$imported students imported successfully.";
        if (!empty($failed)) {
            $message .= ' ' . count($failed) . ' row(s) were skipped.';
        }

        return redirect()->route('students.index')
            ->with('success', $message)
            ->with('import_errors', $failed);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'nullable|email|unique:students',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email',
            'class_id' => 'nullable|exists:classes,id',
            'enrollment_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_admitted' => 'boolean',
            'admission_number' => 'nullable|string|unique:students,admission_number',
            'admission_date' => 'nullable|date'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        Student::create($validated);

        return redirect()->route('students.index')
                        ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Student $student)
    {
        $student->load('class', 'marks.subject');

        $examTypes = SchoolSetting::examTypes();

        return view('modules.students.show', compact('student', 'examTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Student $student)
    {
        $classes = ClassModel::where('is_active', true)->get();

        return view('modules.students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email',
            'class_id' => 'nullable|exists:classes,id',
            'enrollment_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'is_admitted' => 'boolean',
            'admission_number' => 'nullable|string|unique:students,admission_number,' . $student->id,
            'admission_date' => 'nullable|date'
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        return redirect()->route('students.index')
                        ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()->route('students.index')
                        ->with('success', 'Student deleted successfully.');
    }
}
