-- Personalized Curriculum Recommendation System Database Schema
-- Created for MySQL/MariaDB

-- Create database (run this first)
-- CREATE DATABASE curriculum_recommendation;
-- USE curriculum_recommendation;

-- Students table - stores student user accounts
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    interests TEXT,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses table - stores available courses
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    difficulty ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    keywords TEXT,
    description TEXT,
    credits INT DEFAULT 3,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Completed courses table - tracks which courses students have completed
CREATE TABLE IF NOT EXISTS completed_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    grade VARCHAR(5),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_course (student_id, course_id)
);

-- Recommendations table - stores generated course recommendations
CREATE TABLE IF NOT EXISTS recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    confidence_score DECIMAL(3,2) DEFAULT 0.50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_course_recommendation (student_id, course_id)
);

-- Course ratings table (bonus feature)
CREATE TABLE IF NOT EXISTS course_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_course_rating (student_id, course_id)
);

-- Insert sample admin user
INSERT INTO students (name, email, password, interests, is_admin) VALUES 
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administration, Management', TRUE);

-- Insert sample courses
INSERT INTO courses (name, category, difficulty, keywords, description, credits) VALUES 
('Introduction to Programming', 'Computer Science', 'Beginner', 'programming, coding, basics, python, java', 'Learn the fundamentals of programming with hands-on exercises', 3),
('Data Structures and Algorithms', 'Computer Science', 'Intermediate', 'algorithms, data structures, programming, efficiency', 'Advanced programming concepts and problem-solving techniques', 4),
('Web Development Fundamentals', 'Computer Science', 'Beginner', 'web development, html, css, javascript, frontend', 'Build responsive websites using modern web technologies', 3),
('Database Management Systems', 'Computer Science', 'Intermediate', 'database, sql, mysql, data management', 'Design and implement efficient database systems', 4),
('Machine Learning Basics', 'Computer Science', 'Advanced', 'machine learning, ai, algorithms, data science', 'Introduction to machine learning concepts and applications', 4),
('Calculus I', 'Mathematics', 'Beginner', 'calculus, mathematics, derivatives, integrals', 'Fundamental concepts of differential and integral calculus', 4),
('Linear Algebra', 'Mathematics', 'Intermediate', 'linear algebra, vectors, matrices, mathematics', 'Vector spaces, linear transformations, and matrix theory', 3),
('Statistics and Probability', 'Mathematics', 'Intermediate', 'statistics, probability, data analysis, mathematics', 'Statistical methods and probability theory applications', 3),
('Introduction to Psychology', 'Psychology', 'Beginner', 'psychology, behavior, mental health, research', 'Overview of psychological theories and research methods', 3),
('Cognitive Psychology', 'Psychology', 'Intermediate', 'cognitive psychology, memory, perception, thinking', 'Study of mental processes including memory and perception', 3),
('Business Management', 'Business', 'Beginner', 'business, management, leadership, organization', 'Principles of business management and organizational behavior', 3),
('Marketing Fundamentals', 'Business', 'Beginner', 'marketing, advertising, consumer behavior, strategy', 'Introduction to marketing principles and strategies', 3),
('Financial Accounting', 'Business', 'Intermediate', 'accounting, finance, financial statements, business', 'Principles of financial accounting and reporting', 4),
('International Business', 'Business', 'Advanced', 'international business, global trade, economics', 'Global business strategies and international trade', 3),
('Creative Writing', 'English', 'Beginner', 'writing, creative, literature, communication', 'Develop creative writing skills and literary techniques', 3),
('American Literature', 'English', 'Intermediate', 'literature, american, history, analysis', 'Survey of American literary works and movements', 3),
('World History', 'History', 'Beginner', 'history, world, civilization, culture', 'Comprehensive overview of world historical events', 3),
('Environmental Science', 'Science', 'Beginner', 'environment, ecology, sustainability, science', 'Study of environmental systems and sustainability', 4),
('Organic Chemistry', 'Science', 'Advanced', 'chemistry, organic, molecules, laboratory', 'Structure and reactions of organic compounds', 4),
('Physics I', 'Science', 'Beginner', 'physics, mechanics, motion, energy', 'Fundamental principles of classical mechanics', 4);

-- Insert some sample completed courses for the admin user
INSERT INTO completed_courses (student_id, course_id, grade) VALUES 
(1, 1, 'A'),  -- Introduction to Programming
(1, 6, 'B+'), -- Calculus I
(1, 15, 'A-'); -- Creative Writing

-- Create indexes for better performance
CREATE INDEX idx_students_email ON students(email);
CREATE INDEX idx_courses_category ON courses(category);
CREATE INDEX idx_courses_difficulty ON courses(difficulty);
CREATE INDEX idx_completed_courses_student ON completed_courses(student_id);
CREATE INDEX idx_recommendations_student ON recommendations(student_id);
