# Personalized Curriculum Recommendation System

A student web application that recommends university courses based on individual interests and completed courses.

## 🚀 Setup Instructions

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Web browser

### Installation Steps

1. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `curriculum_recommendation`
   - Import the `database_schema.sql` file into this database

3. **File Placement**
   - Copy all project files to `C:\xampp\htdocs\curriculum-system\` (or your XAMPP htdocs directory)
   - Ensure proper file permissions

4. **Configuration**
   - Update database credentials in `config/config.php` if needed
   - Default credentials (localhost, root, no password) should work with XAMPP

5. **Access the Application**
   - Navigate to `http://localhost/curriculum-system/`
   - Register a new student account or login with admin credentials

### Default Admin Account
- Email: admin@example.com
- Password: admin123

### Features
- Student registration and authentication
- Personalized course recommendations
- Student dashboard with profile management
- Admin panel for course management
- Responsive Bootstrap UI
- Secure password hashing
- Course search functionality

### Database Schema
- `students` - Student user accounts
- `courses` - Available courses
- `completed_courses` - Student course completion tracking
- `recommendations` - Generated course recommendations

## 🛠️ Technical Stack
- PHP 7.4+
- MySQL 5.7+
- HTML5/CSS3
- Bootstrap 5
- JavaScript (jQuery)

## 📁 Project Structure
```
curriculum-system/
├── config/
│   └── config.php
├── includes/
│   ├── auth.php
│   ├── functions.php
│   └── header.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── admin/
│   ├── index.php
│   ├── courses.php
│   └── dashboard.php
├── student/
│   ├── dashboard.php
│   └── profile.php
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── index.php
├── database_schema.sql
└── README.md
```
