# Student ERP (SIM)

> **Short Description:**  
> A simple Student ERP system with enhanced authentication, user management, and profile features. Built for easy deployment with XAMPP and MySQL, suitable for academic institutions.

## Overview

Student ERP (SIM) is a lightweight web application for managing student registrations, user authentication, and profiles. It includes secure login, role-based access, and a responsive dashboard for students and staff.

## Setup Instructions

1. **Create the Database:**  
   Use phpMyAdmin to create a database named `sim_db`.

2. **Import the Schema:**  
   Refer to `README_DATABASE.md` for the full database schema and SQL commands.

3. **Deploy the Application:**  
   Place this project folder inside your XAMPP `htdocs` directory (e.g., `C:/xampp/htdocs/Project/SIM`).

4. **Access in Browser:**  
   Open your browser and navigate to `/Project/SIM/`.

## Key Features

- User registration with additional fields (registration number, college ID, gender, role, sub-category, department)
- Secure password hashing for all users
- SweetAlert-based notifications for better UX
- Profile editing and a responsive dashboard

## File Structure

- `config.php` — Database connection and session initialization
- `includes/header.php`, `includes/footer.php` — Common layout and assets
- `index.php` — Login page
- `register.php` — Registration page
- `auth/login_action.php` — Handles login logic
- `auth/register_action.php` — Handles registration logic
- `auth/logout.php` — Handles user logout
- `home.php` — User dashboard
- `profile.php`, `auth/profile_update.php` — Profile viewing and updating

# DB info here
> https://drive.google.com/drive/folders/1whSdqdNkXK_EthV2dw_9zjVJzFOdGzy1?usp=sharing

## Make sure to run this in your MySQL server from XAMPP
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
```