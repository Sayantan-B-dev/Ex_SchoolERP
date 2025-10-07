1. Create a blank database (e.g., `student_erp`) and run this script against it.
2. The script disables foreign key checks during creation to avoid ordering issues, then re-enables them.

> Note: This is written for MySQL 5.7+/MariaDB (uses `JSON` and `ON UPDATE CURRENT_TIMESTAMP`). Adjust minor details if you target a different RDBMS.

```sql
-- Student ERP - Full Schema (MySQL)
-- Run inside your target database (e.g., USE student_erp;)

SET FOREIGN_KEY_CHECKS = 0;

-- Drop if exists (safe rerun)
DROP TABLE IF EXISTS system_notifications_queue;
DROP TABLE IF EXISTS app_settings;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS grade_summary;
DROP TABLE IF EXISTS attendance_summary;
DROP TABLE IF EXISTS event_logs;
DROP TABLE IF EXISTS parent_student_access;
DROP TABLE IF EXISTS guardian_accounts;
DROP TABLE IF EXISTS library_fines;
DROP TABLE IF EXISTS library_issues;
DROP TABLE IF EXISTS library_copies;
DROP TABLE IF EXISTS library_books;
DROP TABLE IF EXISTS sms_email_logs;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS message_threads;
DROP TABLE IF EXISTS notification_receipts;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS financial_transactions;
DROP TABLE IF EXISTS scholarships;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS invoice_lines;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS fee_structures;
DROP TABLE IF EXISTS fee_heads;
DROP TABLE IF EXISTS result_publishing;
DROP TABLE IF EXISTS exam_registrations;
DROP TABLE IF EXISTS exam_slots;
DROP TABLE IF EXISTS exams;
DROP TABLE IF EXISTS academic_sessions;
DROP TABLE IF EXISTS grade_scales;
DROP TABLE IF EXISTS grades;
DROP TABLE IF EXISTS assessments;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS attendance_settings;
DROP TABLE IF EXISTS timetable;
DROP TABLE IF EXISTS classrooms;
DROP TABLE IF EXISTS course_structure;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS semesters;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS institutions;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS guardians;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS student_profiles;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS users;

-- 1. Institutions, Departments, Courses, Semesters
CREATE TABLE institutions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  code VARCHAR(100) NULL,
  address TEXT NULL,
  contact_phone VARCHAR(50) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE departments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  institution_id INT UNSIGNED NULL,
  name VARCHAR(150) NOT NULL,
  code VARCHAR(50) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_departments_institution (institution_id),
  CONSTRAINT fk_departments_institution FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE courses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  department_id INT UNSIGNED NULL,
  title VARCHAR(255) NOT NULL,
  code VARCHAR(100) NOT NULL UNIQUE,
  duration_months INT NULL,
  level ENUM('Undergraduate','Graduate','Diploma','Certificate','Other') DEFAULT 'Other',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_courses_dept (department_id),
  CONSTRAINT fk_courses_dept FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE semesters (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL, -- e.g., "2025 Spring"
  start_date DATE NULL,
  end_date DATE NULL,
  status ENUM('Open','Closed') DEFAULT 'Open',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. RBAC: roles & permissions
CREATE TABLE roles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE permissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE role_permissions (
  role_id INT UNSIGNED NOT NULL,
  permission_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_roleperm_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_roleperm_perm FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE user_roles (
  user_id INT UNSIGNED NOT NULL,
  role_id INT UNSIGNED NOT NULL,
  assigned_by INT UNSIGNED NULL,
  assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, role_id),
  CONSTRAINT fk_userroles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- user_roles references users which will be created later, we'll add its FK after users creation.

-- 3. Core users table
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  reg_no VARCHAR(50) NULL,
  college_id VARCHAR(50) NULL,
  gender ENUM('Male','Female','Other') NOT NULL DEFAULT 'Other',
  role ENUM('Student','Teacher','Faculty','Admin','Parent','Accountant','Librarian') NOT NULL,
  course_id INT UNSIGNED NULL,
  department_id INT UNSIGNED NULL,
  subjects JSON NULL,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(30) NULL,
  address TEXT NULL,
  dob DATE NULL,
  status ENUM('Active','Inactive','Graduated','Suspended') DEFAULT 'Active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL,
  INDEX idx_users_role (role),
  INDEX idx_users_reg_no (reg_no),
  INDEX idx_users_course (course_id),
  CONSTRAINT fk_users_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
  CONSTRAINT fk_users_dept FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Now we can create user_roles FK referencing users
ALTER TABLE user_roles
  ADD CONSTRAINT fk_userroles_roles FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_userroles_assigned_by FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL;

-- 4. Student Information Management
CREATE TABLE student_profiles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  admission_date DATE NULL,
  enrollment_status ENUM('Enrolled','Alumni','Dropped','Suspended') DEFAULT 'Enrolled',
  current_year INT NULL,
  section VARCHAR(50) NULL,
  photo_url VARCHAR(255) NULL,
  extra_data JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_studentprofiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE enrollments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  semester_id INT UNSIGNED NULL,
  admitted_on DATE NULL,
  status ENUM('Active','Completed','Withdrawn') DEFAULT 'Active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_enroll_student (student_id),
  INDEX idx_enroll_course (course_id),
  CONSTRAINT fk_enroll_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_enroll_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  CONSTRAINT fk_enroll_semester FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guardians (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  relationship VARCHAR(100) NULL,
  email VARCHAR(150) NULL,
  phone VARCHAR(50) NULL,
  address TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_guardians_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE documents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NULL,
  doc_type VARCHAR(150) NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_by INT UNSIGNED NULL,
  uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_documents_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_documents_uploader FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Course & Curriculum: subjects, structure, classrooms, timetable
CREATE TABLE subjects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  code VARCHAR(100) NOT NULL UNIQUE,
  course_id INT UNSIGNED NULL,
  credits DECIMAL(5,2) NULL,
  type ENUM('Core','Elective','Lab','Workshop') DEFAULT 'Core',
  semester_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_subject_course (course_id),
  CONSTRAINT fk_subject_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
  CONSTRAINT fk_subject_semester FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE course_structure (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_id INT UNSIGNED NOT NULL,
  subject_id INT UNSIGNED NOT NULL,
  position INT DEFAULT 0,
  is_mandatory BOOLEAN DEFAULT TRUE,
  CONSTRAINT fk_cs_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  CONSTRAINT fk_cs_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE classrooms (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  capacity INT DEFAULT 0,
  location VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE timetable (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  subject_id INT UNSIGNED NOT NULL,
  teacher_id INT UNSIGNED NOT NULL,
  classroom_id INT UNSIGNED NULL,
  day_of_week ENUM('Mon','Tue','Wed','Thu','Fri','Sat','Sun') NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  semester_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tt_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  CONSTRAINT fk_tt_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tt_classroom FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE SET NULL,
  CONSTRAINT fk_tt_semester FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Attendance & settings
CREATE TABLE attendance_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  department_id INT UNSIGNED NULL,
  min_percentage_for_attendance INT DEFAULT 75,
  auto_mark_rules JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_attset_dept FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE attendance (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  department_id INT UNSIGNED NULL,
  course_id INT UNSIGNED NULL,
  course_code VARCHAR(100) NULL,
  class_date DATE NOT NULL,
  period VARCHAR(50) NULL,
  status ENUM('Present','Absent','Late','Excused') NOT NULL DEFAULT 'Present',
  marked_by INT UNSIGNED NULL,
  reason VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX uniq_attendance (student_id, course_id, class_date, period),
  INDEX idx_attendance_dept_date (department_id, class_date),
  INDEX idx_attendance_student (student_id),
  CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_att_dept FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
  CONSTRAINT fk_att_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
  CONSTRAINT fk_att_markedby FOREIGN KEY (marked_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Academic Performance & assessments, grades
CREATE TABLE assessments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  subject_id INT UNSIGNED NULL,
  course_id INT UNSIGNED NULL,
  type ENUM('Assignment','Quiz','Midterm','Final','Practical','Project') DEFAULT 'Assignment',
  max_marks DECIMAL(8,2) DEFAULT 100,
  weightage DECIMAL(5,2) NULL,
  date DATE NULL,
  duration_mins INT NULL,
  created_by INT UNSIGNED NULL,
  is_published BOOLEAN DEFAULT FALSE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_assess_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
  CONSTRAINT fk_assess_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
  CONSTRAINT fk_assess_createdby FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_assess_subject (subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE grades (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  assessment_id INT UNSIGNED NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  marks_obtained DECIMAL(8,2),
  remarks TEXT NULL,
  graded_by INT UNSIGNED NULL,
  graded_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_assessment_student (assessment_id, student_id),
  CONSTRAINT fk_grades_assessment FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
  CONSTRAINT fk_grades_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_grades_gradedby FOREIGN KEY (graded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE grade_scales (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_id INT UNSIGNED NULL,
  min_mark DECIMAL(6,2) NOT NULL,
  max_mark DECIMAL(6,2) NOT NULL,
  grade VARCHAR(10) NOT NULL,
  grade_point DECIMAL(4,2) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gradescale_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE academic_sessions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  semester_id INT UNSIGNED NOT NULL,
  gpa DECIMAL(4,2) NULL,
  cgpa DECIMAL(4,2) NULL,
  details JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_acadsess_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_acadsess_semester FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Examination Management
CREATE TABLE exams (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  semester_id INT UNSIGNED NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  status ENUM('Scheduled','Ongoing','Completed','Cancelled') DEFAULT 'Scheduled',
  created_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_exams_semester FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL,
  CONSTRAINT fk_exams_createdby FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE exam_slots (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  exam_id INT UNSIGNED NOT NULL,
  subject_id INT UNSIGNED NULL,
  date DATE NULL,
  start_time TIME NULL,
  end_time TIME NULL,
  room_id INT UNSIGNED NULL,
  invigilator_id INT UNSIGNED NULL,
  max_seats INT DEFAULT 0,
  CONSTRAINT fk_examslot_exam FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
  CONSTRAINT fk_examslot_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
  CONSTRAINT fk_examslot_room FOREIGN KEY (room_id) REFERENCES classrooms(id) ON DELETE SET NULL,
  CONSTRAINT fk_examslot_invig FOREIGN KEY (invigilator_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE exam_registrations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  exam_id INT UNSIGNED NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  status ENUM('Registered','Absent','Appeared') DEFAULT 'Registered',
  seat_no VARCHAR(50) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_examreg_exam FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
  CONSTRAINT fk_examreg_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE result_publishing (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  exam_id INT UNSIGNED NOT NULL,
  published_at DATETIME NULL,
  published_by INT UNSIGNED NULL,
  notes TEXT NULL,
  visibility ENUM('Students','Public','Restricted') DEFAULT 'Students',
  CONSTRAINT fk_respub_exam FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
  CONSTRAINT fk_respub_by FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Fee & Financial Management
CREATE TABLE fee_heads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  code VARCHAR(100) NULL,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE fee_structures (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_id INT UNSIGNED NULL,
  semester_id INT UNSIGNED NULL,
  fee_head_id INT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  is_mandatory BOOLEAN DEFAULT TRUE,
  CONSTRAINT fk_fee_struct_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
  CONSTRAINT fk_fee_struct_sem FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL,
  CONSTRAINT fk_fee_struct_head FOREIGN KEY (fee_head_id) REFERENCES fee_heads(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_no VARCHAR(100) NOT NULL UNIQUE,
  student_id INT UNSIGNED NOT NULL,
  due_date DATE NULL,
  status ENUM('Pending','Paid','Partial','Overdue','Cancelled') DEFAULT 'Pending',
  total_amount DECIMAL(12,2) NOT NULL,
  balance DECIMAL(12,2) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_invoice_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_invoices_due (due_date, status),
  INDEX idx_invoices_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoice_lines (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT UNSIGNED NOT NULL,
  fee_head_id INT UNSIGNED NULL,
  description VARCHAR(255) NULL,
  amount DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_invoiceline_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
  CONSTRAINT fk_invoiceline_feehead FOREIGN KEY (fee_head_id) REFERENCES fee_heads(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT UNSIGNED NULL,
  student_id INT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  method ENUM('Cash','Card','UPI','BankTransfer','Cheque','OnlineGateway') DEFAULT 'OnlineGateway',
  reference_no VARCHAR(255) NULL,
  processed_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_payment_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
  CONSTRAINT fk_payment_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_payment_processedby FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_payments_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE scholarships (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NULL,
  percent DECIMAL(5,2) NULL,
  reason VARCHAR(255) NULL,
  applied_by INT UNSIGNED NULL,
  valid_from DATE NULL,
  valid_until DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_scholar_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_scholar_appliedby FOREIGN KEY (applied_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE financial_transactions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type ENUM('Credit','Debit') NOT NULL,
  amount DECIMAL(14,2) NOT NULL,
  narration TEXT NULL,
  related_table VARCHAR(100) NULL,
  related_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Communication & Notifications, Messages, Logs
CREATE TABLE notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  body TEXT,
  type ENUM('Alert','Announcement','Reminder','Custom') DEFAULT 'Announcement',
  created_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NULL,
  priority ENUM('Low','Normal','High') DEFAULT 'Normal',
  CONSTRAINT fk_notifications_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notification_receipts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  notification_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  is_read BOOLEAN DEFAULT FALSE,
  delivered_at DATETIME NULL,
  read_at DATETIME NULL,
  CONSTRAINT fk_notifreceipt_notif FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
  CONSTRAINT fk_notifreceipt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE message_threads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  subject VARCHAR(255) NULL,
  created_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mthread_createdby FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  thread_id INT UNSIGNED NOT NULL,
  from_user_id INT UNSIGNED NULL,
  to_user_id INT UNSIGNED NULL,
  body TEXT NOT NULL,
  attachments JSON NULL,
  is_system BOOLEAN DEFAULT FALSE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_messages_thread FOREIGN KEY (thread_id) REFERENCES message_threads(id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_from FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_messages_to FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE sms_email_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `to` VARCHAR(255) NOT NULL,
  channel ENUM('SMS','Email','Push') NOT NULL,
  content_preview VARCHAR(500) NULL,
  status VARCHAR(100) NULL,
  reference_id VARCHAR(255) NULL,
  sent_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Library Management
CREATE TABLE library_books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  isbn VARCHAR(50) NULL,
  title VARCHAR(255) NOT NULL,
  authors VARCHAR(255) NULL,
  publisher VARCHAR(255) NULL,
  year_published INT NULL,
  copies_total INT DEFAULT 1,
  copies_available INT DEFAULT 1,
  category VARCHAR(150) NULL,
  shelf_location VARCHAR(100) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE library_copies (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  book_id INT UNSIGNED NOT NULL,
  barcode VARCHAR(200) NULL,
  acquired_date DATE NULL,
  `condition` ENUM('New','Good','Fair','Poor') DEFAULT 'Good',
  status ENUM('Available','Issued','Lost','Repair') DEFAULT 'Available',
  CONSTRAINT fk_lc_book FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE library_issues (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  copy_id INT UNSIGNED NULL,
  book_id INT UNSIGNED NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  issued_by INT UNSIGNED NULL,
  issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  due_at DATETIME NULL,
  returned_at DATETIME NULL,
  fine_amount DECIMAL(10,2) DEFAULT 0,
  status ENUM('Issued','Returned','Overdue','Lost') DEFAULT 'Issued',
  CONSTRAINT fk_libissue_copy FOREIGN KEY (copy_id) REFERENCES library_copies(id) ON DELETE SET NULL,
  CONSTRAINT fk_libissue_book FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE,
  CONSTRAINT fk_libissue_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_libissue_issuedby FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_library_issues_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE library_fines (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  reason VARCHAR(255) NULL,
  paid BOOLEAN DEFAULT FALSE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  paid_at DATETIME NULL,
  payment_id INT UNSIGNED NULL,
  CONSTRAINT fk_libfine_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_libfine_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Parent & Guardian Portal - guardian accounts & access mapping
CREATE TABLE guardian_accounts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  guardian_id INT UNSIGNED NOT NULL,
  username VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_guardacc_guardian FOREIGN KEY (guardian_id) REFERENCES guardians(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE parent_student_access (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  guardian_user_id INT UNSIGNED NOT NULL, -- if guardians created as users, else use guardian_accounts
  student_user_id INT UNSIGNED NOT NULL,
  access_level ENUM('ViewOnly','Message','Finance') DEFAULT 'ViewOnly',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_paracc_guardianuser FOREIGN KEY (guardian_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_paracc_studentuser FOREIGN KEY (student_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Analytics & Reporting / Event logs / Summaries
CREATE TABLE event_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  event_type VARCHAR(150) NULL,
  entity VARCHAR(150) NULL,
  entity_id INT UNSIGNED NULL,
  meta JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_eventlogs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE attendance_summary (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  semester_id INT UNSIGNED NULL,
  total_classes INT DEFAULT 0,
  present_count INT DEFAULT 0,
  absent_count INT DEFAULT 0,
  attendance_percent DECIMAL(5,2) DEFAULT 0,
  last_updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_attsum_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_attsum_sem FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE grade_summary (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  semester_id INT UNSIGNED NOT NULL,
  gpa DECIMAL(4,2) NULL,
  rank_in_class INT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gradesum_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_gradesum_sem FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Audit / Settings / System queue
CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(150) NOT NULL,
  table_name VARCHAR(150) NULL,
  record_id INT UNSIGNED NULL,
  `before` JSON NULL,
  `after` JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE app_settings (
  `key` VARCHAR(150) PRIMARY KEY,
  `value` JSON NULL,
  description TEXT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE system_notifications_queue (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(100) NOT NULL,
  payload JSON NOT NULL,
  status ENUM('Pending','InProgress','Done','Failed') DEFAULT 'Pending',
  attempts INT DEFAULT 0,
  next_try_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. Final adjustments: add missing foreign keys referencing tables created later
-- (Some FKs were added inline. Below add user_roles.user_id FK that referenced users earlier.)
-- Already added user_roles.user_id FK earlier referencing users, and role_id/assigned_by constraints.

-- 16. Useful views (optional) - can be created later as needed

SET FOREIGN_KEY_CHECKS = 1;

-- =============================
-- Suggested initial seed rows (optional)
-- =============================
-- Roles
INSERT IGNORE INTO roles (name, description) VALUES
('Admin','Full system administrator'),
('Teacher','Teacher / Faculty'),
('Student','Student user'),
('Parent','Parent / Guardian'),
('Accountant','Finance role'),
('Librarian','Library role');

-- Permissions (add as needed)
INSERT IGNORE INTO permissions (name, description) VALUES
('view_grades','View student grades'),
('edit_grades','Edit/grade assessments'),
('manage_users','Create/update users'),
('view_financials','View invoices/payments');

-- Map some sample role->permission (role_permissions)
-- find ids dynamically or insert with expected ids if fresh DB; recommended to map later via app scripts.

-- End of script
```

---

**Things that can be done after creating this database successfully:**

1. **Create migration files**

   * For Laravel, Knex, Django, or any framework you use, so the database structure can be version-controlled and easily deployed.

2. **Produce seed data**

   * Add initial data for roles, permissions, departments, courses.
   * Create a small sample dataset with a few students and teachers for testing.

3. **Generate DDL and database utilities**

   * Produce a DDL file with `CREATE INDEX` statements for performance optimization.
   * Create sample views like `vw_student_overview`.
   * Write stored procedures such as `sp_publish_results` to automate common tasks.

---
