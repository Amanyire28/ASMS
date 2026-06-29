# Design Document: Student Promotion

## Overview

The Student Promotion feature adds a dedicated promotion workflow to ASMS. It allows admins to advance students from one class to the next at the end of an academic year, either in bulk (entire class) or individually. Students retain their stream, outstanding fee balances accumulate forward, and every action is logged in an audit table.

The feature integrates with the existing `students`, `classes`, `class_levels`, `student_fees`, and `fee_schedules` tables. A new `promotion_records` table provides the audit trail.

---

## Architecture

The feature follows the existing Laravel MVC pattern used throughout ASMS:

- A new `PromotionController` handles all HTTP requests
- A new `PromotionRecord` Eloquent model wraps the audit table
- Promotion logic lives in a dedicated `PromotionService` class to keep the controller thin and the logic testable
- Routes are added under the existing `admin` prefix group
- Views follow the existing Tailwind + Alpine.js pattern

```
HTTP Request
     │
     ▼
PromotionController
     │
     ▼
PromotionService          ← all business logic here
     │         │
     ▼         ▼
Student     PromotionRecord
ClassModel  StudentFee
ClassLevel  FeeSchedule
```

---

## Components and Interfaces

### PromotionService

Core logic class injected into the controller.

```php
class PromotionService
{
    // Returns the next ClassLevel above $current, or null if highest
    public function nextClassLevel(ClassLevel $current): ?ClassLevel

    // Returns the target ClassModel (same stream, next level), or null
    public function targetClass(ClassModel $from): ?ClassModel

    // Promotes a single student to $toClass, records audit entry
    // Returns ['success' => bool, 'error' => ?string]
    public function promoteStudent(Student $student, ClassModel $toClass, int $promotedBy, string $academicYear): array

    // Promotes all $students to their respective target classes
    // Returns ['promoted' => int, 'skipped' => int, 'errors' => array]
    public function bulkPromote(Collection $students, string $academicYear, int $promotedBy): array

    // Marks a student as retained (no class change, creates audit record)
    public function retainStudent(Student $student, int $promotedBy, string $academicYear): void

    // Returns students eligible for promotion from $class (active, not yet promoted this year)
    public function eligibleStudents(ClassModel $class, string $academicYear): Collection
}
```

### PromotionController

```php
class PromotionController extends Controller
{
    public function index(): View                          // List all classes with promotion status
    public function preview(ClassModel $class): View       // Show students before bulk promote
    public function bulkPromote(Request $request): RedirectResponse
    public function showStudent(Student $student): View    // Individual student promotion form
    public function promoteStudent(Request $request, Student $student): RedirectResponse
    public function retainStudent(Request $request, Student $student): RedirectResponse
    public function log(): View                            // School-wide audit log
}
```

### PromotionRecord Model

```php
class PromotionRecord extends Model
{
    // Fields: student_id, from_class_id, to_class_id, type (promoted|retained),
    //         academic_year, promoted_by, promoted_at
}
```

---

## Data Models

### New Table: `promotion_records`

```
promotion_records
├── id                  bigint unsigned PK
├── student_id          bigint unsigned FK → students.id (cascade)
├── from_class_id       bigint unsigned FK → classes.id (set null)
├── to_class_id         bigint unsigned FK → classes.id (set null) — null for retention
├── type                enum('promoted', 'retained')
├── academic_year       varchar(20)       e.g. "2025-2026"
├── promoted_by         bigint unsigned FK → users.id (set null)
├── promoted_at         timestamp
└── timestamps
```

Index on `(student_id, academic_year)` for fast profile history lookup.
Index on `(from_class_id, academic_year)` for the school-wide log.

### Existing Tables — No Schema Changes

The feature works entirely within the current schema:

- `students.class_id` is updated on promotion — this already exists
- `student_fees` rows are **never deleted** — outstanding balances accumulate naturally
- New fees are assigned to the student via the existing `Student::assignFeesFromClass()` boot hook, which fires when `class_id` changes
- `class_levels.sort_order` is used to determine next level — already exists

### Next Level Algorithm

```
nextLevel = ClassLevel
    .where('is_active', true)
    .where('sort_order', '>', currentLevel.sort_order)
    .orderBy('sort_order', 'asc')
    .first()
```

Target class = class with `class_level_id = nextLevel.id` AND `stream_id = currentClass.stream_id`.

---

## UI Flow

### 1. Promotion Index (`/admin/promotions`)
- Cards for each active class showing: class name, student count, target class, promotable status
- "Promote" button per class → goes to Preview
- "View Log" button → goes to audit log

### 2. Bulk Preview (`/admin/promotions/{class}/preview`)
- Table of eligible students with columns: checkbox, name, student ID, outstanding balance
- Each row has a "Retain" toggle
- Summary bar: X selected, X retained, total outstanding
- "Confirm Promotion" button submits the selected/retained split

### 3. Individual Promotion (from student profile)
- Small "Promote" button on the student show page
- Modal: current class → target class dropdown (defaults to next level/same stream)
- Shows outstanding balance warning if > 0
- Confirm button

### 4. Promotion Log (`/admin/promotions/log`)
- Table filterable by class, academic year, action type
- Columns: student name, from class, to class, type, academic year, performed by, date

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Next-level monotonicity
*For any* ClassLevel with a given sort_order, `nextClassLevel()` must return the ClassLevel with the smallest sort_order that is strictly greater — never the same level, never a lower one, never skipping a level when one exists between.
**Validates: Requirements 1.3, 1.5, 4.2**

### Property 2: Stream preservation
*For any* student promotion (bulk or individual), the stream_id of the student's class after promotion must equal the stream_id of their class before promotion.
**Validates: Requirements 3.2**

### Property 3: Class assignment correctness
*For any* student who is successfully promoted, their `class_id` after promotion must equal the `id` of the target class passed to `promoteStudent()`.
**Validates: Requirements 3.1, 4.3**

### Property 4: Promotion record always created
*For any* promotion or retention action that completes without error, exactly one PromotionRecord must exist in `promotion_records` with the correct `student_id`, `academic_year`, and `type`.
**Validates: Requirements 3.4, 4.4, 5.2**

### Property 5: Fees never deleted on promotion
*For any* student with N StudentFee records before promotion, they must still have at least N StudentFee records after promotion — the count must not decrease.
**Validates: Requirements 3.5, 4.5**

### Property 6: Inactive students excluded
*For any* class, `eligibleStudents()` must return only students where `is_active = true`.
**Validates: Requirements 7.1**

### Property 7: Retention does not change class
*For any* student for whom `retainStudent()` is called, their `class_id` must be identical before and after the call.
**Validates: Requirements 5.3**

### Property 8: Missing target class blocks promotion
*For any* call to `promoteStudent()` or `bulkPromote()` where the target class does not exist in the database, the result must contain `success = false` and no `class_id` must be changed.
**Validates: Requirements 3.3, 7.3**

### Property 9: Partial failure isolation
*For any* bulk promotion over a set of students S where student X fails, the promotion records and class_id updates for all students in S minus {X} must be unaffected — only X's row is rolled back.
**Validates: Requirements 7.5**

### Property 10: Promotion log completeness
*For any* student with K promotion/retention actions recorded, querying `PromotionRecord::where('student_id', $id)->get()` must return exactly K records.
**Validates: Requirements 6.2**

### Property 11: Log filter correctness
*For any* filter combination (class, academic_year, type) applied to the school-wide log, every returned record must match all supplied filter values.
**Validates: Requirements 6.3**

---

## Error Handling

| Scenario | Behaviour |
|---|---|
| Target class does not exist | Block promotion, return descriptive error |
| Student already promoted this year | Show warning, require explicit re-confirmation |
| Inactive student in bulk list | Skip silently, include in "skipped" count |
| No next ClassLevel (highest level) | Mark class as not promotable, disable Promote button |
| DB error for one student in bulk | Catch, roll back that student only, continue, report error |
| Unauthorised user | 403 response |

---

## Testing Strategy

### Unit Tests
- `PromotionService::nextClassLevel()` with various sort_order sequences
- `PromotionService::targetClass()` when target exists, when it doesn't, when stream has no match
- `PromotionService::retainStudent()` verifies class_id unchanged and record created
- Edge cases: highest-level class, inactive class levels, missing streams

### Property-Based Tests (via PestPHP + `pest-plugin-snapshots` or raw `PHPUnit` with a simple generator)

Each property above maps to one property-based test using generated input data:

- **Tag format**: `Feature: student-promotion, Property N: <property text>`
- Minimum 100 iterations per property test
- Generators create random sets of ClassLevels with distinct sort_orders, random students with random class assignments, random fee balances

Both unit tests and property tests are required — unit tests verify concrete edge cases, property tests verify the general rules hold across all inputs.
