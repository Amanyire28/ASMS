# Implementation Plan: Student Promotion

## Overview

Implement the student promotion feature in phases: database → service logic → controller/routes → views → sidebar integration. Each task builds on the previous. Property-based tests are marked optional (*) and can be skipped for a faster MVP.

## Tasks

- [x] 1. Create the promotion_records migration and model
  - Create `database/migrations/..._create_promotion_records_table.php` with fields: student_id, from_class_id, to_class_id, type (promoted|retained), academic_year, promoted_by, promoted_at
  - Add indexes on (student_id, academic_year) and (from_class_id, academic_year)
  - Create `app/Models/PromotionRecord.php` with fillable fields, casts, and relationships to Student, ClassModel (from/to), and User
  - _Requirements: 3.4, 5.2, 6.1_

- [-] 2. Build the PromotionService
  - [x] 2.1 Implement `nextClassLevel(ClassLevel $current): ?ClassLevel`
    - Query class_levels where sort_order > current, is_active = true, order by sort_order asc, return first
    - _Requirements: 1.5, 4.2_

  - [ ] 2.2 Write property test for next-level monotonicity
    - **Property 1: Next-level monotonicity**
    - **Validates: Requirements 1.3, 1.5, 4.2**

  - [x] 2.3 Implement `targetClass(ClassModel $from): ?ClassModel`
    - Find class where class_level_id = nextLevel.id AND stream_id = from.stream_id
    - Return null if no next level or no matching class
    - _Requirements: 1.3, 3.3_

  - [x] 2.4 Implement `eligibleStudents(ClassModel $class, string $academicYear): Collection`
    - Return students where class_id = class.id AND is_active = true
    - _Requirements: 7.1_

  - [ ] 2.5 Write property test for inactive students excluded
    - **Property 6: Inactive students excluded**
    - **Validates: Requirements 7.1**

  - [x] 2.6 Implement `promoteStudent(Student $student, ClassModel $toClass, int $promotedBy, string $academicYear): array`
    - Validate target class exists
    - Update student class_id (triggers assignFeesFromClass boot hook automatically)
    - Create PromotionRecord with type = promoted
    - Return ['success' => true] or ['success' => false, 'error' => '...']
    - _Requirements: 3.1, 3.2, 3.4, 3.5, 3.6, 4.3, 4.4_

  - [ ] 2.7 Write property tests for core promotion invariants
    - **Property 2: Stream preservation**
    - **Property 3: Class assignment correctness**
    - **Property 4: Promotion record always created**
    - **Property 5: Fees never deleted on promotion**
    - **Validates: Requirements 3.1, 3.2, 3.4, 3.5**

  - [x] 2.8 Implement `bulkPromote(Collection $students, string $academicYear, int $promotedBy): array`
    - Loop over students, call promoteStudent per student wrapped in individual try/catch
    - Accumulate promoted count, skipped count, errors array
    - Return summary array
    - _Requirements: 3.1, 7.5_

  - [ ] 2.9 Write property test for partial failure isolation
    - **Property 9: Partial failure isolation**
    - **Validates: Requirements 7.5**

  - [x] 2.10 Implement `retainStudent(Student $student, int $promotedBy, string $academicYear): void`
    - Do NOT change class_id
    - Create PromotionRecord with type = retained, to_class_id = null
    - _Requirements: 5.2, 5.3_

  - [ ] 2.11 Write property tests for retention invariants
    - **Property 7: Retention does not change class**
    - **Property 4: Promotion record always created (retention variant)**
    - **Validates: Requirements 5.2, 5.3**

- [ ] 3. Checkpoint — run all tests, ensure service layer is solid before building UI

- [ ] 4. Create PromotionController and routes
  - Create `app/Http/Controllers/PromotionController.php` with methods: index, preview, bulkPromote, showStudent, promoteStudent, retainStudent, log
  - Inject PromotionService via constructor
  - Add permission check (`students.edit` or new `students.promote`) to all methods
  - Add routes to `routes/web.php` under admin prefix:
    - GET  `promotions`                       → promotions.index
    - GET  `promotions/{class}/preview`        → promotions.preview
    - POST `promotions/{class}/bulk`           → promotions.bulk
    - GET  `promotions/student/{student}`      → promotions.student
    - POST `promotions/student/{student}`      → promotions.student.promote
    - POST `promotions/student/{student}/retain` → promotions.student.retain
    - GET  `promotions/log`                    → promotions.log
  - _Requirements: 1.1, 3.1, 4.3, 5.2, 6.3_

- [ ] 5. Build the Promotion Index view
  - Create `resources/views/modules/promotions/index.blade.php`
  - Show all active classes grouped by ClassLevel (use sort_order for ordering)
  - Each class card shows: name, student count, target class name, promotable/not-promotable badge
  - "Promote" button links to preview; disabled with tooltip if not promotable
  - "View Log" button links to promotions.log
  - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [ ] 6. Build the Bulk Preview view
  - Create `resources/views/modules/promotions/preview.blade.php`
  - Checklist table: checkbox (default checked), student name, student ID, outstanding balance
  - "Retain" toggle per row (unchecks from promotion, marks as retain)
  - Summary bar showing selected count, retained count, total outstanding balance (Alpine.js reactive)
  - Form POSTs to promotions.bulk with arrays: promote_ids[], retain_ids[], academic_year
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.7_

- [ ] 7. Build the Individual Promotion modal/form
  - Add a "Promote" button to the existing student show page (`resources/views/modules/students/show.blade.php`)
  - On click, show a modal with: current class, target class dropdown (default = next level/same stream, all active classes selectable), outstanding balance warning if > 0
  - Form POSTs to promotions.student.promote
  - Add "Retain this year" button that POSTs to promotions.student.retain
  - _Requirements: 4.1, 4.2, 4.3, 4.6, 5.1_

- [ ] 8. Build the Promotion Log view
  - Create `resources/views/modules/promotions/log.blade.php`
  - Filterable table: class dropdown, academic year dropdown, type filter (all/promoted/retained)
  - Columns: student name, from class, to class, type badge, academic year, performed by, date
  - Paginated, filters applied via GET params
  - _Requirements: 6.3, 6.4_

- [ ] 9. Write property test for log filter correctness
  - **Property 11: Log filter correctness**
  - **Validates: Requirements 6.3**

- [ ] 10. Add promotion history to student profile
  - In the student show view, add a "Promotion History" section
  - List PromotionRecords for this student: from class → to class, type, academic year, performed by, date
  - _Requirements: 6.2_

- [ ] 11. Write property test for promotion log completeness
  - **Property 10: Promotion log completeness**
  - **Validates: Requirements 6.2**

- [ ] 12. Add Promotions link to sidebar
  - Add "Promotions" dropdown item under the Students section in `resources/views/partials/sidebar.blade.php` and `mobile-sidebar.blade.php`
  - Gate with appropriate permission
  - _Requirements: 1.1_

- [ ] 13. Final checkpoint — run all tests, verify end-to-end flow works

## Notes

- Tasks are all required — comprehensive build from the start
- The `Student::assignFeesFromClass()` boot hook fires automatically when `class_id` changes — no extra code needed to assign new fees on promotion
- Outstanding fee balances accumulate automatically because StudentFee rows are never deleted
- Property tests use PHPUnit with manually written generators (no additional library needed beyond what's already in the project)
