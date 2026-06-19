<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\FeeSchedule;
use App\Models\StudentFee;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FeesController extends Controller
{
    /**
     * List all fees
     */
    public function index(): View
    {
        $this->authorize('system.settings');

        $fees = Fee::with('studentFees', 'feeSchedules')
            ->paginate(20);

        return view('modules.fees.index', compact('fees'));
    }

    /**
     * Show fee creation form
     */
    public function create(): View
    {
        $this->authorize('system.settings');

        $categories = Fee::getCategories();

        return view('modules.fees.create', compact('categories'));
    }

    /**
     * Store a new fee
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('system.settings');

        $validated = $request->validate([
            'name' => 'required|string|unique:fees',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'type' => 'required|in:fixed,variable',
            'is_active' => 'boolean',
        ]);

        Fee::create($validated);

        return redirect()->route('fees.index')
            ->with('success', 'Fee created successfully');
    }

    /**
     * Show fee edit form
     */
    public function edit(Fee $fee): View
    {
        $this->authorize('system.settings');

        $categories = Fee::getCategories();

        return view('modules.fees.edit', compact('fee', 'categories'));
    }

    /**
     * Update a fee
     */
    public function update(Request $request, Fee $fee): RedirectResponse
    {
        $this->authorize('system.settings');

        $validated = $request->validate([
            'name' => 'required|string|unique:fees,name,' . $fee->id,
            'description' => 'nullable|string',
            'category' => 'required|string',
            'type' => 'required|in:fixed,variable',
            'is_active' => 'boolean',
        ]);

        $fee->update($validated);

        return redirect()->route('fees.index')
            ->with('success', 'Fee updated successfully');
    }

    /**
     * Delete a fee
     */
    public function destroy(Fee $fee): RedirectResponse
    {
        $this->authorize('system.settings');

        // Check if fee has active assignments
        if ($fee->studentFees()->count() > 0) {
            return back()->with('error', 'Cannot delete fee with active student assignments');
        }

        $fee->delete();

        return redirect()->route('fees.index')
            ->with('success', 'Fee deleted successfully');
    }

    /**
     * Show fee schedule management
     */
    public function schedules(): View
    {
        $this->authorize('system.settings');

        $schedules = FeeSchedule::with('class')
            ->orderBy('academic_year', 'desc')
            ->orderBy('term')
            ->paginate(20);

        return view('modules.fees.schedules.index', compact('schedules'));
    }

    /**
     * Show fee schedule creation form
     */
    public function createSchedule(): View
    {
        $this->authorize('system.settings');

        $classes = ClassModel::with('stream')->get();
        $fees = Fee::active()->get();

        return view('modules.fees.schedules.create', compact('classes', 'fees'));
    }

    /**
     * Store fee schedule and auto-assign to students
     * One schedule per class with all fees stored as JSON
     */
    public function storeSchedule(Request $request): RedirectResponse
    {
        $this->authorize('system.settings');

        $validated = $request->validate([
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
            'fee_ids' => 'required|array',
            'fee_ids.*' => 'exists:fees,id',
            'fee_amounts' => 'required|array',
            'fee_amounts.*' => 'required|numeric|min:0.01',
            'term' => 'required|string',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
        ]);

        $schedulesCreated = 0;
        $assignmentsCreated = 0;

        // Build fee_amounts array (fee_id => amount)
        $feeAmountsArray = [];
        $totalAmount = 0;
        foreach ($validated['fee_ids'] as $feeId) {
            $amount = (float)($validated['fee_amounts'][$feeId] ?? 0);
            $feeAmountsArray[$feeId] = $amount;
            $totalAmount += $amount;
        }

        // Create ONE schedule per class (not per fee)
        foreach ($validated['class_ids'] as $classId) {
            $schedule = FeeSchedule::updateOrCreate(
                [
                    'class_id' => $classId,
                    'term' => $validated['term'],
                    'academic_year' => $validated['academic_year'],
                ],
                [
                    'fee_amounts' => $feeAmountsArray,
                    'total_amount' => $totalAmount,
                    'due_date' => $validated['due_date'],
                ]
            );
            $schedulesCreated++;

            // Auto-assign to all active students in this class
            $students = Student::where('class_id', $classId)
                ->where('is_active', true)
                ->get();

            foreach ($students as $student) {
                // Create individual StudentFee for each fee type
                foreach ($validated['fee_ids'] as $feeId) {
                    $amount = (float)($validated['fee_amounts'][$feeId] ?? 0);
                    
                    $exists = StudentFee::where('student_id', $student->id)
                        ->where('fee_id', $feeId)
                        ->where('term', $validated['term'])
                        ->exists();

                    if (!$exists) {
                        StudentFee::create([
                            'student_id' => $student->id,
                            'fee_id' => $feeId,
                            'amount' => $amount,
                            'term' => $validated['term'],
                            'due_date' => $validated['due_date'],
                        ]);
                        $assignmentsCreated++;
                    }
                }
            }
        }

        return redirect()->route('fees.schedules')
            ->with('success', "Fee schedules created for " . count($validated['class_ids']) . " class(es) and assigned to {$assignmentsCreated} student records");
    }

    /**
     * Edit fee schedule
     */
    public function editSchedule(FeeSchedule $schedule): View
    {
        $this->authorize('system.settings');

        $classes = ClassModel::with('stream')->get();
        $fees = Fee::active()->get();

        return view('modules.fees.schedules.edit', compact('schedule', 'classes', 'fees'));
    }

    /**
     * Update fee schedule (edit fee amounts)
     */
    public function updateSchedule(Request $request, FeeSchedule $schedule): RedirectResponse
    {
        $this->authorize('system.settings');

        $validated = $request->validate([
            'fee_amounts' => 'required|array',
            'fee_amounts.*' => 'required|numeric|min:0.01',
            'term' => 'required|string',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
        ]);

        // Calculate total amount
        $totalAmount = 0;
        foreach ($validated['fee_amounts'] as $amount) {
            $totalAmount += (float)$amount;
        }

        // Convert fee_amounts keys to integers for consistency
        $feeAmounts = [];
        foreach ($validated['fee_amounts'] as $feeId => $amount) {
            $feeAmounts[(int)$feeId] = (float)$amount;
        }

        $schedule->update([
            'fee_amounts' => $feeAmounts,
            'total_amount' => $totalAmount,
            'term' => $validated['term'],
            'due_date' => $validated['due_date'],
            'academic_year' => $validated['academic_year'],
        ]);

        return redirect()->route('fees.schedules')
            ->with('success', 'Fee schedule updated successfully');
    }

    /**
     * Delete fee schedule
     */
    public function destroySchedule(FeeSchedule $schedule): RedirectResponse
    {
        $this->authorize('system.settings');

        $schedule->delete();

        return redirect()->route('fees.schedules')
            ->with('success', 'Fee schedule deleted successfully');
    }

    /**
     * Show allocate fees interface - bursar records payments
     */
    public function allocateFees(): View
    {
        $this->authorize('students.view-detail');

        $students = Student::with(['class'])
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        // Calculate totals for each student
        $studentData = $students->map(function ($student) {
            $totalDue = StudentFee::where('student_id', $student->id)
                ->where('waived', false)
                ->sum('amount');
            
            $paid = StudentFee::where('student_id', $student->id)
                ->join('payments', 'student_fees.id', '=', 'payments.student_fee_id')
                ->sum('payments.amount');
            
            $balance = $totalDue - $paid;
            
            return [
                'student' => $student,
                'total_due' => $totalDue,
                'amount_paid' => $paid,
                'balance' => $balance,
            ];
        });

        return view('modules.fees.allocate.index', compact('studentData'));
    }

    /**
     * Search students via AJAX
     */
    public function searchStudents(Request $request): JsonResponse
    {
        $this->authorize('students.view-detail');

        $query = $request->get('q', '');
        
        if (strlen($query) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Query too short'
            ]);
        }

        $students = Student::with(['class'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->whereRaw('CONCAT(first_name, " ", last_name) LIKE ?', ["%$query%"])
                  ->orWhere('student_id', 'LIKE', "%$query%")
                  ->orWhere('email', 'LIKE', "%$query%");
            })
            ->limit(15)
            ->get()
            ->map(function ($student) {
                $totalDue = StudentFee::where('student_id', $student->id)
                    ->where('waived', false)
                    ->sum('amount');
                
                $paid = StudentFee::where('student_id', $student->id)
                    ->join('payments', 'student_fees.id', '=', 'payments.student_fee_id')
                    ->sum('payments.amount');
                
                $balance = $totalDue - $paid;
                
                $studentFees = StudentFee::with('fee')
                    ->where('student_id', $student->id)
                    ->get();

                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'student_id' => $student->student_id,
                    'email' => $student->email,
                    'class' => $student->class->name ?? 'N/A',
                    'total_due' => $totalDue,
                    'amount_paid' => $paid,
                    'balance' => $balance,
                    'fees' => $studentFees->map(function ($sf) {
                        return [
                            'name' => $sf->fee->name,
                            'amount' => $sf->amount,
                            'paid' => Payment::where('student_fee_id', $sf->id)->sum('amount') ?? 0,
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Show ledger for specific student - all fees and payments
     */
    public function allocateFeesForStudent(Student $student): View
    {
        $this->authorize('students.view-detail');

        $studentFees = StudentFee::with('fee')
            ->where('student_id', $student->id)
            ->get();

        $ledger = [];
        $balance = 0;

        // Build transaction history
        foreach ($studentFees as $studentFee) {
            // Add the fee assignment as a debit
            $ledger[] = [
                'date' => $studentFee->created_at,
                'description' => 'Fee: ' . $studentFee->fee->name,
                'debit' => $studentFee->amount,
                'credit' => 0,
                'type' => 'fee',
            ];
            $balance += $studentFee->amount;

            // Add payments as credits
            $payments = Payment::where('student_fee_id', $studentFee->id)
                ->orderBy('payment_date')
                ->get();

            foreach ($payments as $payment) {
                $ledger[] = [
                    'date' => $payment->payment_date,
                    'description' => 'Payment: ' . ($payment->paymentMethod->name ?? 'N/A'),
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'type' => 'payment',
                ];
                $balance -= $payment->amount;
            }
        }

        // Sort by date
        usort($ledger, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        // Calculate running balance
        $runningBalance = 0;
        foreach ($ledger as &$entry) {
            $runningBalance += $entry['debit'] - $entry['credit'];
            $entry['running_balance'] = $runningBalance;
        }

        $fees = Fee::active()->get();
        $currentBalance = $balance;

        // Convert ledger array to collection for view
        $ledger = collect($ledger);

        return view('modules.fees.allocate.student-ledger', compact('student', 'ledger', 'fees', 'currentBalance', 'studentFees'));
    }

    /**
     * Record payment allocation for a student
     */
    public function recordAllocation(Request $request): RedirectResponse
    {
        $this->authorize('students.view-detail');

        // Handle both single and multiple fee payments
        $fees = $request->input('fees', []);
        if (!is_array($fees) || empty($fees)) {
            // Fallback for old single-fee format
            $fees = [[
                'fee_id' => $request->input('fee_id'),
                'amount' => $request->input('amount'),
            ]];
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'fees' => 'required|array',
            'fees.*.fee_id' => 'required|exists:fees,id',
            'fees.*.amount' => 'required|numeric|min:0.01',
        ]);

        $student = Student::findOrFail($request->input('student_id'));
        $totalPaid = 0;
        $feesCount = 0;

        // Record payment for each selected fee
        foreach ($fees as $feeData) {
            $fee = Fee::findOrFail($feeData['fee_id']);
            $amount = $feeData['amount'];

            // Check if student fee exists, if not create it
            $studentFee = StudentFee::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'fee_id' => $fee->id,
                ],
                [
                    'amount' => 0,
                    'term' => config('school.current_term', '1'),
                    'due_date' => now()->addMonth(),
                ]
            );

            // Record payment
            Payment::create([
                'student_fee_id' => $studentFee->id,
                'amount' => $amount,
                'payment_date' => $request->input('payment_date'),
                'payment_method_id' => $request->input('payment_method_id'),
                'transaction_reference' => $request->input('reference'),
                'user_id' => auth()->id(),
            ]);

            $totalPaid += $amount;
            $feesCount++;
        }

        return redirect()->route('fees.allocate-for-student', $student)
            ->with('success', "Payment of Sh " . number_format($totalPaid, 2) . " recorded for {$student->full_name} ({$feesCount} fee(s))");
    }

    /**
     * Bulk assign fees to class students
     */
    public function bulkAssignFees(Request $request): RedirectResponse
    {
        $this->authorize('students.create');

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'fee_ids' => 'required|array',
            'fee_ids.*' => 'exists:fees,id',
            'term' => 'required|string',
            'academic_year' => 'required|string',
        ]);

        $students = Student::where('class_id', $validated['class_id'])
            ->where('is_active', true)
            ->get();

        $count = 0;
        foreach ($students as $student) {
            foreach ($validated['fee_ids'] as $feeId) {
                // Check if already assigned
                $exists = StudentFee::where('student_id', $student->id)
                    ->where('fee_id', $feeId)
                    ->where('term', $validated['term'])
                    ->exists();

                if (!$exists) {
                    $schedule = FeeSchedule::where('class_id', $validated['class_id'])
                        ->where('fee_id', $feeId)
                        ->where('term', $validated['term'])
                        ->where('academic_year', $validated['academic_year'])
                        ->first();

                    if ($schedule) {
                        StudentFee::create([
                            'student_id' => $student->id,
                            'fee_id' => $feeId,
                            'amount' => $schedule->amount,
                            'term' => $validated['term'],
                            'due_date' => $schedule->due_date,
                        ]);
                        $count++;
                    }
                }
            }
        }

        return redirect()->route('fees.assign')
            ->with('success', "Fees assigned to {$count} student records");
    }

    /**
     * View student fee records
     */
    public function studentFees(Student $student): View
    {
        $this->authorize('students.view-detail');

        $studentFees = $student->studentFees()
            ->with('fee', 'payments')
            ->get();

        $totalOutstanding = $studentFees->sum('outstanding');
        $totalPaid = $studentFees->sum('amount_paid');

        return view('modules.fees.student.index', compact(
            'student',
            'studentFees',
            'totalOutstanding',
            'totalPaid'
        ));
    }

    /**
     * Show payment recording form
     */
    public function recordPayment(StudentFee $studentFee): View
    {
        $this->authorize('students.view-detail');

        $paymentMethods = PaymentMethod::active()->get();

        return view('modules.fees.payments.record', compact('studentFee', 'paymentMethods'));
    }

    /**
     * Store payment record
     */
    public function storePayment(Request $request, StudentFee $studentFee): RedirectResponse
    {
        $this->authorize('students.view-detail');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $studentFee->outstanding,
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['recorded_by'] = auth()->id();
        $validated['receipt_number'] = Payment::generateReceiptNumber();
        $validated['student_fee_id'] = $studentFee->id;

        Payment::create($validated);

        return redirect()->route('fees.student', $studentFee->student_id)
            ->with('success', 'Payment recorded successfully');
    }

    /**
     * View payment details
     */
    public function viewPayment(Payment $payment): View
    {
        $this->authorize('students.view-detail');

        return view('modules.fees.payments.view', compact('payment'));
    }

    /**
     * Download payment receipt
     */
    public function downloadReceipt(Payment $payment)
    {
        $this->authorize('students.view-detail');

        return view('modules.fees.payments.receipt', compact('payment'));
    }

    /**
     * View overdue fees summary
     */
    public function overdueFeesReport(): View
    {
        $this->authorize('system.settings');

        $overdueRecords = StudentFee::overdue()
            ->with('student', 'fee')
            ->orderBy('due_date')
            ->paginate(30);

        $totalOverdue = $overdueRecords->sum('outstanding');
        $countOverdue = $overdueRecords->total();

        return view('modules.fees.reports.overdue', compact(
            'overdueRecords',
            'totalOverdue',
            'countOverdue'
        ));
    }

    /**
     * View fee collection report
     */
    public function collectionReport(): View
    {
        $this->authorize('system.settings');

        $dateFrom = request('from', now()->startOfMonth()->toDateString());
        $dateTo = request('to', now()->toDateString());

        $payments = Payment::betweenDates($dateFrom, $dateTo)
            ->with('studentFee.student', 'studentFee.fee', 'paymentMethod')
            ->orderBy('payment_date', 'desc')
            ->paginate(30);

        $totalCollected = $payments->sum('amount');
        $collectionByMethod = Payment::betweenDates($dateFrom, $dateTo)
            ->selectRaw('payment_methods.name, SUM(payments.amount) as total')
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->groupBy('payment_methods.name')
            ->get();

        return view('modules.fees.reports.collection', compact(
            'payments',
            'totalCollected',
            'collectionByMethod',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * View student account statement
     */
    public function accountStatement(Student $student): View
    {
        $this->authorize('students.view-detail');

        $studentFees = $student->studentFees()
            ->with('fee', 'payments')
            ->get();

        $totalBilled = $studentFees->sum('amount');
        $totalPaid = $studentFees->sum('amount_paid');
        $totalOutstanding = $studentFees->sum('outstanding');

        return view('modules.fees.statements.index', compact(
            'student',
            'studentFees',
            'totalBilled',
            'totalPaid',
            'totalOutstanding'
        ));
    }
}
