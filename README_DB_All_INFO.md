# Database Documentation (Student ERP — `student_erp`)

> Format: full Markdown DB documentation for a complete Student ERP covering **all modules** shown in your images:
>
> * Student Information Management (SIM)
> * Attendance Tracking
> * Academic Performance & Grading
> * Course & Curriculum Management
> * Examination Management
> * Fee & Financial Management
> * Communication & Notifications
> * Library Management
> * Parent & Guardian Portal
> * Analytics & Reporting

This expands your existing `student_erp_auth`/`users`+`attendance` minimal doc into a complete, production-ready schema with field types, PKs/FKs, indexes, constraints, notes about access control, and suggested relationships. Use this as a canonical reference for migrations, API design, and permissions.

---

# 1. Naming / Conventions / Notes

* Database name suggestion: `student_erp` (you currently have `student_erp_auth` — merge or keep separate based on auth split).
* All `id` columns are `INT UNSIGNED AUTO_INCREMENT` unless noted.
* Timestamps: use `created_at` and `updated_at` (`DATETIME` or `TIMESTAMP`, consistent across tables).
* Soft deletes: where useful, include `deleted_at` `DATETIME NULL` for recoverability.
* Use `FOREIGN KEY` constraints where possible; if cross-db or microservices used, store IDs and validate in application layer.
* Use ENUMs sparingly; prefer small lookup tables for extensibility (but both styles are provided where appropriate).
* All `*_id` foreign keys should be indexed.
* For JSON-capable DBs (MySQL 5.7+/MariaDB/Postgres), fields like `meta`, `settings`, `grades_breakdown` can be `JSON` when useful.

---

# 2. Core Auth / Users (expanded)

### Table: `users`

* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `name` VARCHAR(100) NOT NULL
* `email` VARCHAR(150) NOT NULL UNIQUE
* `reg_no` VARCHAR(50) NULL -- student registration number
* `college_id` VARCHAR(50) NULL -- institutional id
* `gender` ENUM('Male','Female','Other') NOT NULL DEFAULT 'Other'
* `role` ENUM('Student','Teacher','Faculty','Admin','Parent','Accountant','Librarian') NOT NULL
* `course_id` INT UNSIGNED NULL REFERENCES `courses`(`id`)
* `department_id` INT UNSIGNED NULL REFERENCES `departments`(`id`) -- preferred over department text
* `subjects` JSON NULL -- array of subject ids or codes (optional)
* `password_hash` VARCHAR(255) NOT NULL
* `phone` VARCHAR(30) NULL
* `address` TEXT NULL
* `dob` DATE NULL
* `status` ENUM('Active','Inactive','Graduated','Suspended') NOT NULL DEFAULT 'Active'
* `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
* `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
* `deleted_at` DATETIME NULL

Indexes:

* UNIQUE(`email`)
* INDEX `idx_users_role` (`role`)
* INDEX `idx_users_reg_no` (`reg_no`)

Notes:

* `role` drives RBAC. Map roles to permissions via `roles`/`role_permissions` (see below).

---

### Table: `roles` (optional RBAC)

* `id` INT AUTO_INCREMENT PRIMARY KEY
* `name` VARCHAR(50) UNIQUE NOT NULL (e.g., `Admin`, `Teacher`, `Student`)
* `description` TEXT
* `created_at`, `updated_at`

### Table: `permissions`

* `id`, `name`, `description`, timestamps

### Table: `role_permissions`

* `role_id` FK -> `roles.id`
* `permission_id` FK -> `permissions.id`
* PRIMARY KEY (`role_id`,`permission_id`)

### Table: `user_roles` (if users can have multiple roles)

* `user_id`, `role_id`, `assigned_by`, `assigned_at`

---

# 3. Organizational Lookups

### Table: `institutions` (if supporting many colleges)

* `id`, `name`, `address`, `code`, `contact_phone`, `created_at`, `updated_at`

### Table: `departments`

* `id` INT PK
* `name` VARCHAR(100)
* `code` VARCHAR(50)
* `institution_id` INT FK -> `institutions.id`
* `created_at`, `updated_at`

Index `idx_dept_inst` (`institution_id`)

### Table: `courses`

* `id` INT PK
* `title` VARCHAR(150)
* `code` VARCHAR(50) UNIQUE
* `department_id` FK -> `departments.id`
* `duration_months` INT NULL
* `level` ENUM('Undergraduate','Graduate','Diploma','Certificate','Other')
* `created_at`, `updated_at`

### Table: `semesters` (or `academic_terms`)

* `id`, `name` (e.g., '2025 Spring'), `start_date`, `end_date`, `status` (Open/Closed), `created_at`, `updated_at`

---

# 4. Student Information Management (SIM)

This module stores student profiles, enrollment history, guardians, documents.

### Table: `student_profiles`

* `id` INT PK
* `user_id` INT UNIQUE FK -> `users.id`
* `admission_date` DATE
* `enrollment_status` ENUM('Enrolled','Alumni','Dropped','Suspended')
* `current_year` INT
* `section` VARCHAR(10) NULL
* `photo_url` VARCHAR(255) NULL
* `extra_data` JSON NULL
* `created_at`, `updated_at`

### Table: `enrollments`

* `id` INT PK
* `student_id` FK -> `users.id`
* `course_id` FK -> `courses.id`
* `semester_id` FK -> `semesters.id`
* `admitted_on` DATE
* `status` ENUM('Active','Completed','Withdrawn') DEFAULT 'Active'
* `created_at`, `updated_at`

Index:

* UNIQUE (`student_id`,`course_id`,`semester_id`) if no duplicate enrollments desired.

### Table: `guardians`

* `id` INT PK
* `student_id` FK -> `users.id`
* `name` VARCHAR(100)
* `relationship` VARCHAR(50) -- e.g., Father, Mother
* `email` VARCHAR(150) NULL
* `phone` VARCHAR(30) NULL
* `address` TEXT NULL
* `created_at`, `updated_at`

### Table: `documents`

* `id`, `student_id` FK, `doc_type` VARCHAR(100), `file_path` VARCHAR(255), `uploaded_by` FK -> `users.id`, `uploaded_at`

---

# 5. Attendance Tracking (expanded)

### Table: `attendance` (revised)

* `id` INT PK
* `student_id` INT NOT NULL FK -> `users.id`
* `department_id` INT NOT NULL FK -> `departments.id`
* `course_id` INT NOT NULL FK -> `courses.id`
* `course_code` VARCHAR(50) NULL -- denormalized for quick display
* `class_date` DATE NOT NULL
* `period` VARCHAR(50) NULL -- e.g., 'Period 1', or `NULL` if whole day
* `status` ENUM('Present','Absent','Late','Excused') NOT NULL
* `marked_by` INT FK -> `users.id` (faculty)
* `reason` VARCHAR(255) NULL
* `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
* `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP

Indexes:

* UNIQUE KEY `uniq_attendance` (`student_id`,`course_id`,`class_date`,`period`)
* INDEX `idx_attendance_dept_date` (`department_id`,`class_date`)
* INDEX `idx_attendance_student` (`student_id`)

### Table: `attendance_settings` (optional)

* `id`, `department_id`, `min_percentage_for_attendance` INT, `auto_mark_rules` JSON

---

# 6. Course & Curriculum Management

### Table: `subjects`

* `id` INT PK
* `title` VARCHAR(150)
* `code` VARCHAR(50) UNIQUE
* `course_id` FK -> `courses.id`
* `credits` DECIMAL(4,2)
* `type` ENUM('Core','Elective','Lab','Workshop')
* `semester_id` FK -> `semesters.id`
* `created_at`, `updated_at`

Index:

* `idx_subject_course` (`course_id`)

### Table: `course_structure` (mapping courses -> subjects)

* `id`, `course_id`, `subject_id`, `position` INT (order in curriculum), `is_mandatory` BOOLEAN

### Table: `classrooms` / `sections`

* `id`, `name` (e.g., '10A', 'Lab 203'), `capacity`, `location`

### Table: `timetable` (class scheduling)

* `id` INT PK
* `subject_id` FK -> `subjects.id`
* `teacher_id` FK -> `users.id`
* `classroom_id` FK -> `classrooms.id`
* `day_of_week` ENUM('Mon','Tue','Wed','Thu','Fri','Sat','Sun')
* `start_time` TIME
* `end_time` TIME
* `semester_id` FK
* `created_at`, `updated_at`

Indexes:

* UNIQUE constraint on combination (`subject_id`,`teacher_id`,`day_of_week`,`start_time`) to prevent duplicate scheduling.

---

# 7. Academic Performance & Grading

### Table: `assessments` (exams, quizzes, assignments)

* `id` INT PK
* `title` VARCHAR(150)
* `subject_id` FK
* `course_id` FK
* `type` ENUM('Assignment','Quiz','Midterm','Final','Practical','Project')
* `max_marks` DECIMAL(6,2)
* `weightage` DECIMAL(5,2) NULL -- percent towards term
* `date` DATE
* `duration_mins` INT NULL
* `created_by` FK -> `users.id` (teacher)
* `is_published` BOOLEAN DEFAULT FALSE
* `created_at`, `updated_at`

Index `idx_assessments_subject` (`subject_id`)

### Table: `grades`

* `id` INT PK
* `assessment_id` FK -> `assessments.id`
* `student_id` FK -> `users.id`
* `marks_obtained` DECIMAL(6,2)
* `remarks` TEXT NULL
* `graded_by` FK -> `users.id`
* `graded_at` DATETIME
* `created_at`, `updated_at`

Indexes:

* UNIQUE(`assessment_id`,`student_id`)

### Table: `grade_scales`

* `id`, `course_id`, `min_mark`, `max_mark`, `grade` VARCHAR(10), `grade_point` DECIMAL(3,2)

### Table: `academic_sessions` / `transcripts`

* `id`, `student_id`, `semester_id`, `gpa` DECIMAL(4,2), `cgpa` DECIMAL(4,2), `details` JSON (subject-wise marks) , `created_at`

Notes:

* `grades` can store breakdowns in JSON `details` if exam has components. Alternatively store `marks_obtained` per assessment.

---

# 8. Examination Management (scheduling, seating, result publishing)

### Table: `exams` (exam events)

* `id`, `title`, `semester_id`, `start_date`, `end_date`, `status` ENUM('Scheduled','Ongoing','Completed','Cancelled'), `created_by`, `created_at`, `updated_at`

### Table: `exam_slots`

* `id`, `exam_id`, `subject_id`, `date`, `start_time`, `end_time`, `room_id`, `invigilator_id`(FK->users), `max_seats`

### Table: `exam_registrations`

* `id`, `exam_id`, `student_id`, `status` ENUM('Registered','Absent','Appeared'), `seat_no`, `created_at`

### Table: `result_publishing`

* `id`, `exam_id`, `published_at`, `published_by`, `notes`, `visibility` ENUM('Students','Public','Restricted')

---

# 9. Fee & Financial Management

### Table: `fee_heads`

* `id`, `title` (e.g., Tuition, Library Fine), `code`, `description`

### Table: `fee_structures`

* `id`, `course_id`, `semester_id`, `fee_head_id`, `amount` DECIMAL(10,2), `is_mandatory` BOOLEAN

### Table: `invoices`

* `id` INT PK
* `invoice_no` VARCHAR(50) UNIQUE
* `student_id` FK -> `users.id`
* `due_date` DATE
* `status` ENUM('Pending','Paid','Partial','Overdue','Cancelled')
* `total_amount` DECIMAL(12,2)
* `balance` DECIMAL(12,2)
* `created_at`, `updated_at`

### Table: `invoice_lines`

* `id`, `invoice_id` FK, `fee_head_id`, `description`, `amount`

### Table: `payments`

* `id`, `invoice_id` FK, `student_id` FK, `amount` DECIMAL(12,2), `payment_date`, `method` ENUM('Cash','Card','UPI','BankTransfer','Cheque','OnlineGateway'), `reference_no`, `processed_by` FK -> `users.id`, `created_at`

### Table: `scholarships` / `discounts`

* `id`, `student_id`, `amount` or `percent`, `reason`, `applied_by`, `valid_from`, `valid_until`

### Table: `financial_transactions` (general ledger-like)

* `id`, `type` ENUM('Credit','Debit'), `amount`, `narration`, `related_id` (invoice/payment), `created_at`

Indexes:

* `idx_payments_student` (`student_id`)
* `idx_invoices_due` (`due_date`,`status`)

---

# 10. Communication & Notifications

### Table: `notifications`

* `id` PK
* `title` VARCHAR(150)
* `body` TEXT
* `type` ENUM('Alert','Announcement','Reminder','Custom')
* `created_by` FK -> `users.id`
* `created_at`
* `expires_at` DATETIME NULL
* `priority` ENUM('Low','Normal','High') DEFAULT 'Normal'

### Table: `notification_receipts`

* `id`, `notification_id` FK, `user_id` FK, `is_read` BOOLEAN DEFAULT FALSE, `delivered_at` DATETIME NULL, `read_at` DATETIME NULL

### Table: `message_threads` (inbox/messaging)

* `id`, `subject`, `created_by`, `created_at`

### Table: `messages`

* `id`, `thread_id` FK, `from_user_id`, `to_user_id`(nullable for group), `body` TEXT, `attachments` JSON, `is_system` BOOL, `created_at`

### Table: `sms_logs` / `email_logs`

* `id`, `to`, `channel` ENUM('SMS','Email','Push'), `content_preview`, `status`, `reference_id`, `sent_at`

Notes:

* Integrate with external providers via async job queues. Store `notification_receipts` to show per-user read/unread.

---

# 11. Library Management

### Table: `library_books`

* `id`, `isbn` VARCHAR(20) NULL, `title`, `authors` VARCHAR(255), `publisher`, `year_published` INT, `copies_total` INT, `copies_available` INT, `category`, `shelf_location`, `created_at`, `updated_at`

### Table: `library_copies` (if tracking each physical copy)

* `id`, `book_id` FK, `barcode`, `acquired_date`, `condition`, `status` ENUM('Available','Issued','Lost','Repair')

### Table: `library_issues`

* `id`, `copy_id` FK, `book_id` FK, `student_id` FK -> `users.id`, `issued_by` FK -> `users.id`, `issued_at` DATETIME, `due_at` DATETIME, `returned_at` DATETIME NULL, `fine_amount` DECIMAL(10,2) DEFAULT 0, `status` ENUM('Issued','Returned','Overdue','Lost')

Index:

* `idx_library_issues_student` (`student_id`)

### Table: `library_fines`

* `id`, `student_id`, `amount`, `reason`, `paid` BOOL DEFAULT FALSE, `created_at`, `paid_at`, `payment_id` FK -> `payments.id`

---

# 12. Parent & Guardian Portal

(Reuses `guardians` table above; link to `users` allowing login for guardians.)

### Table: `guardian_accounts`

* `id`, `guardian_id` FK -> `guardians.id` (or `users.id` if guardians are in `users`)
* `username`, `password_hash`, `is_active`, `created_at`

### Table: `parent_student_access`

* `id`, `guardian_user_id` FK -> `users.id`, `student_user_id` FK -> `users.id`, `access_level` ENUM('ViewOnly','Message','Finance'), `created_at`

Notes:

* Provide permissions to view attendance, grades, invoices, messages.
* Audit all guardian accesses in `access_logs`.

---

# 13. Analytics & Reporting

Store aggregated data in materialized tables or use OLAP pipelines. Provide base event logs to compute metrics.

### Table: `event_logs`

* `id`, `user_id` NULL, `event_type` VARCHAR(100), `entity` VARCHAR(100), `entity_id` INT NULL, `meta` JSON NULL, `created_at` DATETIME

Use this for building reports: login counts, attendance trends, grade distributions.

### Table: `attendance_summary` (materialized)

* `id`, `student_id`, `semester_id`, `total_classes`, `present_count`, `absent_count`, `attendance_percent`, `last_updated`

### Table: `grade_summary`

* `id`, `student_id`, `semester_id`, `gpa`, `rank_in_class` INT NULL

Notes:

* Build nightly ETL jobs to populate these summaries.
* For quick reporting, add views like `vw_student_overview`, `vw_course_performance`.

---

# 14. Audit / Logging / Settings

### Table: `audit_logs`

* `id`, `user_id`, `action` (e.g., `update_grade`), `table_name`, `record_id`, `before` JSON, `after` JSON, `created_at`

### Table: `app_settings`

* `key` VARCHAR(100) PRIMARY KEY
* `value` JSON or TEXT
* `description`

### Table: `system_notifications_queue` (for queued outbound messages)

* `id`, `type`, `payload` JSON, `status`, `attempts`, `next_try_at`

---

# 15. Example Relationships (summary)

* `users.id` — referenced by student_profiles, attendance, grades, payments, library_issues, messages.
* `courses.id` — referenced by subjects, enrollments, fee_structures.
* `subjects.id` — referenced by assessments, timetable, exam_slots.
* `semester_id` — shared across enrollments, assessments, exams.

---

# 16. Indexing & Performance Guidance

* Index all foreign keys.
* Composite indexes for common query patterns:

  * Attendance queries: (`department_id`,`class_date`) and (`student_id`,`class_date`)
  * Grades lookups: (`assessment_id`,`student_id`)
  * Invoices: (`student_id`,`status`)
* Partition large tables by date (`created_at`) for logs/attendance if extremely large.
* Use read replicas for heavy reporting queries or materialized views.

---

# 17. Sample Useful Views / Stored Procs

(You can convert these into SQL views or application queries.)

* `vw_student_profile` — joins `users`, `student_profiles`, `enrollments`, `courses`
* `vw_attendance_monthly(student_id, month)` — aggregated attendance per subject
* `sp_publish_results(exam_id)` — procedural step to mark assessments published and notify students.

---

# 18. Data Migration & Import Notes

* Merge existing `student_erp_auth.users` into `users`. Map `department` text to `departments` table; create a one-time migration to populate `departments`.
* Move `attendance` to new `attendance` table; ensure `course_code` -> `course_id`.
* Ensure password hashes and auth tokens remain intact.
* Sanitize `subjects` field JSON/CSV into `subjects` & `course_structure` tables where needed.

---

# 19. Access Control / Security

* Use RBAC via `roles` and `role_permissions`.
* Enforce row-level access in application:

  * Students: can view only their own `grades`, `attendance`, `invoices`
  * Teachers: can view students in their assigned `courses/subjects`, grade them
  * Admin: full access (with audit)
  * Parents: limited view based on `parent_student_access`
* Log all sensitive actions in `audit_logs`.
* Hashing: use strong adaptive hashing for `password_hash` (bcrypt/argon2).
* Encrypt PII at rest where required.

---

# 20. Example Minimal SQL (MySQL-style) — snippets

```sql
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  reg_no VARCHAR(50),
  college_id VARCHAR(50),
  gender ENUM('Male','Female','Other') NOT NULL DEFAULT 'Other',
  role ENUM('Student','Teacher','Faculty','Admin','Parent','Accountant','Librarian') NOT NULL,
  course_id INT UNSIGNED,
  department_id INT UNSIGNED,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(30),
  address TEXT,
  dob DATE,
  status ENUM('Active','Inactive','Graduated','Suspended') DEFAULT 'Active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL,
  INDEX idx_users_role (role),
  INDEX idx_users_reg_no (reg_no)
);
```

(You can follow this pattern to create other tables above; I kept the doc concise and focused on structure rather than dumping every single CREATE statement.)

---

# 21. Next Steps & Recommendations

1. Convert these schema definitions into migration files (Laravel, Knex, Django, etc.).
2. Add database seeds for `roles`, `departments`, `fee_heads`.
3. Build API endpoints grouped by module (SIM, Attendance, Academics, Finance, Library, Notifications).
4. Implement background job workers for notifications, report generation, and ETL for analytics.
5. Write unit/integration tests for permission checks and data integrity constraints.

---