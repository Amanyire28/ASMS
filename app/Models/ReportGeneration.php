<?php

namespace App\Models;

use App\Models\SchoolSetting;
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

    public function calculateSummary($marks = null)
    {
        if ($marks === null) {
            $marks = $this->getMarks();
        }

        if ($marks->isEmpty()) {
            return [
                'total_marks' => 0,
                'total_possible' => 0,
                'average_percentage' => 0,
                'grade' => 'N/A',
                'subject_count' => 0
            ];
        }

        $examTypes = SchoolSetting::examTypes();
        $marksBySubject = $marks->groupBy('subject_id');
        $subjectScores = [];

        foreach ($marksBySubject as $subjectId => $subjectMarks) {
            $subjectScores[] = $this->calculateSubjectFinal($subjectMarks, $examTypes);
        }

        $totalObtained = array_sum($subjectScores);
        $totalPossible = count($subjectScores) * 100;
        $averagePercentage = $totalPossible > 0 ? ($totalObtained / $totalPossible) * 100 : 0;

        return [
            'total_marks' => round($totalObtained, 2),
            'total_possible' => round($totalPossible, 2),
            'average_percentage' => round($averagePercentage, 2),
            'grade' => $this->calculateGrade($averagePercentage),
            'subject_count' => $marksBySubject->keys()->count()
        ];
    }

    private function calculateSubjectFinal($subjectMarks, array $examTypes): float
    {
        $marksByType = $subjectMarks->keyBy('exam_type');
        $subjectFinal = 0.0;

        foreach ($examTypes as $examType) {
            $mark = $marksByType[$examType['id']] ?? null;
            if ($mark && $mark->total_marks > 0) {
                $subjectPct = (float) $mark->marks_obtained / (float) $mark->total_marks;
            } else {
                $subjectPct = 0;
            }

            $weight = isset($examType['weight']) ? ((float) $examType['weight'] / 100) : 1.0;
            $subjectFinal += $subjectPct * $weight;
        }

        return $subjectFinal * 100;
    }

    private function calculateGrade($percentage)
    {
        $info = grade_info($percentage);
        return $info['grade'] ?? 'F';
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
