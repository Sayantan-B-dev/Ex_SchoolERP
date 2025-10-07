# Database Documentation (student_erp_auth)

## Overview

- **Database**: `student_erp_auth`

### Table: `users`
- `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name` VARCHAR(100) NOT NULL
- `email` VARCHAR(150) NOT NULL UNIQUE
- `reg_no` VARCHAR(50) NULL
- `college_id` VARCHAR(50) NULL
- `gender` ENUM('Male','Female') NOT NULL
- `role` ENUM('Student','Teacher','Faculty','Admin') NOT NULL
- `course` VARCHAR(100) NOT NULL
- `department` VARCHAR(100) NULL (deprecated; kept for backward compatibility)
- `subjects` TEXT NULL -- optional list of subjects (comma-separated or JSON)
- `password_hash` VARCHAR(255) NOT NULL
- `created_at` DATETIME NOT NULL
- `updated_at` DATETIME NULL

No other tables are required for this feature set; future modules can add relationships to `users.id`.

### Table: `attendance`
- `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `student_id` INT UNSIGNED NOT NULL REFERENCES `users`(`id`)
- `department` VARCHAR(100) NOT NULL
- `course_code` VARCHAR(50) NOT NULL
- `class_date` DATE NOT NULL
- `status` ENUM('Present','Absent','Late','Excused') NOT NULL
- `marked_by` INT UNSIGNED NOT NULL REFERENCES `users`(`id`) -- faculty user id
- `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP

Indexes:
- UNIQUE KEY `uniq_attendance` (`student_id`,`course_code`,`class_date`)
- INDEX `idx_attendance_dept_date` (`department`,`class_date`)

## SQL Commands
```sql
CREATE DATABASE IF NOT EXISTS student_erp_auth CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE student_erp_auth;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  reg_no VARCHAR(50) NULL,
  college_id VARCHAR(50) NULL,
  gender ENUM('Male','Female') NOT NULL,
  course ENUM(
    'BCA', 'BSc CS', 'Diploma CS', 'MBA', 'BBA', 'HR', 'Finance', 'Marketing',
    'Diploma Civil', 'Diploma Mechanical', 'Diploma Electrical', 'LLB', 'BA LLB'
  ) NOT NULL,
  subjects TEXT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

## Import Notes
- Set `student_erp_auth` in `config.php` (already configured).
- After creating `attendance`, ensure faculty users have `department` set correctly for access control.
- Students will only read their own rows by `student_id`.