<?php

namespace App\Notifications;

use App\Notifications\Base\Notification;

class TeacherAssignmentsSummary extends Notification
{
    protected $teacher;
    protected $classes = [];
    protected $subjects = [];

    protected $requiredPermission = 'teachers.view';
    protected $allowedRoles = ['Super Admin', 'Headteacher', 'Admin Staff', 'Teacher'];
    protected $category = 'academic';

    public function __construct($teacher, array $classes = [], array $subjects = [])
    {
        $this->teacher = $teacher;
        $this->classes = $classes; 
        $this->subjects = $subjects; 
    }

    // In TeacherAssignmentsSummary.php
public function toDatabase($notifiable)
{
    $teacherName = $this->getEntityDisplayName($this->teacher);
    $employeeId = $this->teacher->employee_id ?? '';

    // Get class names
    $classNames = [];
    foreach ($this->classes as $assignment) {
        $className = $this->getClassName($assignment['class']);
        if ($assignment['is_class_teacher'] ?? false) {
            $className .= ' (CT)';
        }
        $classNames[] = $className;
    }

    // Get subject names
    $subjectNames = [];
    foreach ($this->subjects as $subject) {
        $subjectNames[] = $this->getSubjectName($subject);
    }

    // Build the details string
    $details = [];

    if (!empty($classNames)) {
        $displayClasses = count($classNames) > 2
            ? count($classNames) . ' classes: ' . implode(', ', array_slice($classNames, 0, 2)) . '...'
            : 'classes: ' . implode(', ', $classNames);
        $details[] = $displayClasses;
    }

    if (!empty($subjectNames)) {
        $displaySubjects = count($subjectNames) > 2
            ? count($subjectNames) . ' subjects: ' . implode(', ', array_slice($subjectNames, 0, 2)) . '...'
            : 'subjects: ' . implode(', ', $subjectNames);
        $details[] = $displaySubjects;
    }

    // Add details to the message
    $detailsString = !empty($details) ? ' • ' . implode(' • ', $details) : '';

    // Check if teacher was created recently (within last 5 minutes)
    $isNewTeacher = $this->teacher->created_at->gt(now()->subMinutes(5));

    // Build the final message with details
    $message = $isNewTeacher
        ? "New teacher: {$teacherName} ({$employeeId}){$detailsString}"
        : "{$teacherName} assignments updated{$detailsString}";

    $data = array_merge($this->getBaseData('teacher_assignments_summary'), [
        'teacher_id' => $this->getSafeId($this->teacher),
        'teacher_name' => $teacherName,
        'employee_id' => $employeeId,
        'classes' => $this->formatClassesData(),
        'subjects' => $this->formatSubjectsData(),
        'message' => $message, 
        'action_url' => route('teachers.show', $this->getSafeId($this->teacher)),
        'action_label' => 'View Teacher',
        'is_new_teacher' => $isNewTeacher,
    ]);

    return $data;
}
    protected function formatClassesData()
    {
        return array_map(function ($assignment) {
            return [
                'class_id' => $this->getSafeId($assignment['class']),
                'class_name' => $this->getClassName($assignment['class']),
                'is_class_teacher' => $assignment['is_class_teacher'] ?? false,
            ];
        }, $this->classes);
    }

    protected function formatSubjectsData()
    {
        return array_map(function ($subject) {
            return [
                'subject_id' => $this->getSafeId($subject),
                'subject_name' => $this->getSubjectName($subject),
            ];
        }, $this->subjects);
    }
}
