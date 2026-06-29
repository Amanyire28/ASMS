# Requirements Document

## Introduction

The Student Promotion feature allows administrators to advance students from their current class to the next class level at the end of an academic year. Students retain their stream when promoted (e.g. Primary 1 A → Primary 2 A). Outstanding fee balances accumulate and carry forward to the new class. The system supports both bulk promotion of an entire class and individual student promotion.

## Glossary

- **Class Level**: A named grade level with a sort_order (e.g. Primary 1, Primary 2). Higher sort_order = higher level.
- **Stream**: A section within a class level (e.g. A, B, Red). Students keep their stream when promoted.
- **Class**: The combination of a Class Level and a Stream (e.g. Primary 1 A).
- **Promotion**: Moving a student from their current Class to the Class with the next Class Level and the same Stream.
- **Retention**: Keeping a student in their current Class for another academic year without changing their class_id.
- **Promotion_Record**: An audit log entry recording that a promotion occurred, who authorised it, and when.
- **Student**: An active student enrolled in a Class.
- **Admin**: A system user with permission to manage students.
- **Academic_Year**: The school year string (e.g. 2025-2026) used to scope marks and fees.

---

## Requirements

### Requirement 1: View Promotable Classes

**User Story:** As an admin, I want to see all classes that are eligible for promotion, so that I can select which class to promote.

#### Acceptance Criteria

1. THE Promotion_Module SHALL display a list of all active classes grouped by Class Level.
2. WHEN a class is selected, THE Promotion_Module SHALL show the count of active students in that class.
3. THE Promotion_Module SHALL display the target class (next level, same stream) for each class.
4. IF no next Class Level exists for a class (i.e. it is the highest level), THEN THE Promotion_Module SHALL mark that class as not promotable and display a message indicating it is the final level.
5. THE Promotion_Module SHALL determine the next Class Level by finding the Class Level with the lowest sort_order value that is greater than the current class's Class Level sort_order.

---

### Requirement 2: Preview Students Before Promotion

**User Story:** As an admin, I want to preview which students will be promoted before confirming, so that I can review and adjust the list.

#### Acceptance Criteria

1. WHEN an admin selects a class for bulk promotion, THE Promotion_Module SHALL display a list of all active students in that class.
2. THE Promotion_Module SHALL show each student's name, student ID, current class, target class, and outstanding fee balance.
3. THE Promotion_Module SHALL allow the admin to deselect individual students from the bulk promotion list before confirming.
4. THE Promotion_Module SHALL display a summary showing total students selected, total outstanding balances, and the target class.
5. WHILE students are deselected, THE Promotion_Module SHALL update the summary counts in real time.

---

### Requirement 3: Bulk Class Promotion

**User Story:** As an admin, I want to promote all students in a class at once, so that I can efficiently process end-of-year promotions.

#### Acceptance Criteria

1. WHEN an admin confirms bulk promotion for a class, THE System SHALL update the class_id of all selected active students to the target class.
2. THE System SHALL preserve the student's stream — the target class MUST have the same stream_id as the current class and the next Class Level.
3. IF the target class (next level + same stream) does not exist, THEN THE System SHALL prevent promotion and display an error: "Target class [Level] [Stream] does not exist. Create it first."
4. WHEN a student is promoted, THE System SHALL create a Promotion_Record storing: student_id, from_class_id, to_class_id, promoted_by (user id), promoted_at (timestamp), and academic_year.
5. WHEN a student is promoted, THE System SHALL NOT delete existing StudentFee records — outstanding balances carry forward automatically.
6. THE System SHALL assign new fees to the student from the target class's FeeSchedule for the new academic year, without removing old unpaid fees.
7. WHEN promotion is complete, THE System SHALL display a success summary: number of students promoted, number skipped, and any errors.

---

### Requirement 4: Individual Student Promotion

**User Story:** As an admin, I want to promote a single student to a specific class, so that I can handle exceptions and special cases.

#### Acceptance Criteria

1. WHEN an admin selects an individual student, THE Promotion_Module SHALL display a dropdown of all available target classes the student can be moved to.
2. THE System SHALL default the target class to the next level with the same stream, but allow the admin to select any active class.
3. WHEN the admin confirms, THE System SHALL update the student's class_id to the selected target class.
4. THE System SHALL create a Promotion_Record for the individual promotion with the same fields as bulk promotion.
5. THE System SHALL NOT delete existing StudentFee records on individual promotion.
6. IF the student has an outstanding balance, THE System SHALL display the balance amount and ask for confirmation before proceeding.

---

### Requirement 5: Student Retention

**User Story:** As an admin, I want to mark a student as retained in their current class, so that they repeat the year without being promoted.

#### Acceptance Criteria

1. WHEN viewing the promotion preview, THE Promotion_Module SHALL provide an option to mark individual students as "Retained".
2. WHEN a student is marked as Retained, THE System SHALL create a Promotion_Record with a type of "retained", storing the same student_id, class_id (same as from_class_id), promoted_by, and academic_year.
3. THE System SHALL NOT change the class_id of a retained student.
4. WHEN a student is retained, THE System SHALL display a visual indicator on the student's profile noting their retention status for the current academic year.

---

### Requirement 6: Promotion Audit History

**User Story:** As an admin, I want to view the full promotion history of a student, so that I can track their academic progression over the years.

#### Acceptance Criteria

1. THE System SHALL maintain a promotion_records table logging every promotion and retention event.
2. WHEN viewing a student's profile, THE System SHALL display a timeline of all promotion records for that student showing: from class, to class, academic year, action type (promoted/retained), and who performed the action.
3. THE Promotion_Module SHALL provide a school-wide promotion log filterable by class, academic year, and action type.
4. WHEN a promotion record is viewed, THE System SHALL display the full name of the admin who performed the promotion.

---

### Requirement 7: Promotion Validation

**User Story:** As an admin, I want the system to prevent invalid promotions, so that data integrity is maintained.

#### Acceptance Criteria

1. IF a student is not active (is_active = false), THEN THE System SHALL exclude them from bulk promotion and prevent their individual promotion.
2. IF a student has already been promoted in the selected academic year, THEN THE System SHALL warn the admin and require explicit confirmation to promote again.
3. IF the target class does not exist in the database, THEN THE System SHALL display an error and block the promotion.
4. THE System SHALL validate that the promoting user has the required permission before processing any promotion.
5. IF any student promotion fails during a bulk operation, THEN THE System SHALL roll back only that student's changes, continue with remaining students, and report which students failed and why.
