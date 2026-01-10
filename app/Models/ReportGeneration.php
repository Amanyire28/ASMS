<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number',
        'student_id',
        'term',
        'academic_year',
        'report_type',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getMarks()
    {
        return Mark::where([
            'student_id' => $this->student_id,
            'term' => $this->term,
            'academic_year' => $this->academic_year,
        ])->with('subject')->get();
    }

    public function calculateSummary()
    {
        $marks = $this->getMarks();
        
        if ($marks->isEmpty()) {
            return [
                'total_marks' => 0,
                'total_possible' => 0,
                'average_percentage' => 0,
                'grade' => 'N/A',
                'subject_count' => 0
            ];
        }

        $totalObtained = $marks->sum('marks_obtained');
        $totalPossible = $marks->sum('total_marks');
        $averagePercentage = $totalPossible > 0 ? ($totalObtained / $totalPossible) * 100 : 0;

        return [
            'total_marks' => $totalObtained,
            'total_possible' => $totalPossible,
            'average_percentage' => round($averagePercentage, 2),
            'grade' => $this->calculateGrade($averagePercentage),
            'subject_count' => $marks->count()
        ];
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 30) return 'D';
        return 'F';
    }

    public static function generateReportNumber()
    {
        $year = date('Y');
        $lastReport = self::where('report_number', 'like', "RPT-{$year}-%")
                         ->orderBy('report_number', 'desc')
                         ->first();

        if ($lastReport) {
            $lastNumber = (int) substr($lastReport->report_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "RPT-{$year}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
