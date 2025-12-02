-- ============================================
-- GYM MANAGEMENT PLATFORM
-- Database Schema
-- ============================================

-- Drop existing tables if they exist
DROP TABLE IF EXISTS course_equipment;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS equipment;
DROP TABLE IF EXISTS users;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSES TABLE
-- ============================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('Yoga', 'Cardio', 'Strength', 'Pilates', 'Combat', 'CrossFit', 'Dance', 'Aquatic', 'Wellness') NOT NULL,
    course_date DATE NOT NULL,
    start_time TIME NOT NULL,
    duration_minutes INT NOT NULL DEFAULT 60,
    max_participants INT NOT NULL DEFAULT 20,
    current_participants INT NOT NULL DEFAULT 0,
    instructor_name VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_date (course_date),
    INDEX idx_instructor (instructor_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- EQUIPMENT TABLE
-- ============================================
CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('Yoga', 'Weights', 'Cardio', 'Combat', 'Accessories', 'Strength', 'Recovery', 'Functional', 'Pilates') NOT NULL,
    brand VARCHAR(100),
    model VARCHAR(100),
    quantity INT NOT NULL DEFAULT 1,
    available_quantity INT NOT NULL DEFAULT 1,
    equipment_condition ENUM('excellent', 'good', 'fair', 'poor', 'needs_repair') DEFAULT 'good',
    location VARCHAR(100),
    purchase_date DATE,
    purchase_price DECIMAL(10,2),
    last_maintenance DATE,
    next_maintenance DATE,
    notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_condition (equipment_condition),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSE EQUIPMENT TABLE (Junction Table)
-- ============================================
CREATE TABLE course_equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    equipment_id INT NOT NULL,
    quantity_needed INT NOT NULL DEFAULT 1,
    assigned_by INT DEFAULT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_course_equipment (course_id, equipment_id),
    INDEX idx_course (course_id),
    INDEX idx_equipment (equipment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAMPLE DATA: Users
-- ============================================
INSERT INTO users (username, email, password, role, is_active) VALUES
('admin', 'admin@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', TRUE),
('staff1', 'staff@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', TRUE);
-- Default password for both users is 'password'

-- ============================================
-- SAMPLE DATA: Courses
-- ============================================
INSERT INTO courses (name, description, category, course_date, start_time, duration_minutes, max_participants, current_participants, instructor_name, location, status) VALUES
('Morning Yoga Flow', 'Start your day with a gentle yoga session', 'Yoga', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '07:00', 60, 20, 12, 'Sarah Johnson', 'Studio A', 'scheduled'),
('High Intensity Cardio', 'Burn calories with this high-energy session', 'Cardio', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00', 45, 25, 18, 'Mike Thompson', 'Main Gym', 'scheduled'),
('Strength Training Basics', 'Learn proper weight training techniques', 'Strength', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00', 60, 15, 8, 'David Chen', 'Weight Room', 'scheduled'),
('Evening Pilates', 'Core strengthening and flexibility', 'Pilates', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '18:00', 50, 20, 15, 'Emma Wilson', 'Studio B', 'scheduled'),
('Boxing Fundamentals', 'Introduction to boxing techniques', 'Combat', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '17:00', 60, 12, 10, 'Carlos Martinez', 'Boxing Ring', 'scheduled'),
('CrossFit WOD', 'Workout of the day challenge', 'CrossFit', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '06:00', 45, 20, 16, 'Alex Strong', 'CrossFit Zone', 'scheduled'),
('Zumba Dance Party', 'Fun dance workout for all levels', 'Dance', CURDATE(), '19:00', 55, 30, 25, 'Maria Garcia', 'Main Hall', 'in_progress'),
('Aqua Aerobics', 'Low-impact water workout', 'Aquatic', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '11:00', 45, 15, 5, 'Lisa Brown', 'Pool', 'scheduled');

-- ============================================
-- SAMPLE DATA: Equipment
-- ============================================
INSERT INTO equipment (name, type, brand, model, quantity, available_quantity, equipment_condition, location, purchase_date, purchase_price, last_maintenance, next_maintenance, notes, is_active) VALUES
('Yoga Mat Premium', 'Yoga', 'Liforme', 'Original', 50, 42, 'excellent', 'Studio A', '2024-01-15', 89.99, '2024-06-01', '2024-12-01', 'High-quality natural rubber mats', TRUE),
('Dumbbells Set', 'Weights', 'Bowflex', 'SelectTech 552', 20, 18, 'good', 'Weight Room', '2023-06-10', 299.00, '2024-05-15', '2024-11-15', 'Adjustable 5-52.5 lbs', TRUE),
('Treadmill', 'Cardio', 'NordicTrack', 'Commercial 1750', 10, 8, 'good', 'Cardio Zone', '2023-03-20', 1799.00, '2024-07-01', '2025-01-01', 'Professional grade with iFit', TRUE),
('Boxing Gloves', 'Combat', 'Everlast', 'Pro Style', 24, 20, 'good', 'Boxing Ring', '2024-02-01', 45.00, NULL, NULL, '12oz training gloves', TRUE),
('Resistance Bands Set', 'Accessories', 'TheraBand', 'Professional Set', 30, 28, 'excellent', 'Storage Room', '2024-04-10', 25.00, NULL, NULL, 'Various resistance levels', TRUE),
('Kettlebell Set', 'Strength', 'Rogue', 'Competition', 15, 12, 'fair', 'Weight Room', '2022-08-15', 150.00, '2024-03-01', '2024-09-01', 'Needs regular inspection', TRUE),
('Foam Roller', 'Recovery', 'TriggerPoint', 'GRID', 25, 22, 'excellent', 'Recovery Zone', '2024-05-01', 40.00, NULL, NULL, 'For myofascial release', TRUE),
('Pull-up Bar', 'Functional', 'Rogue', 'Infinity', 5, 5, 'excellent', 'CrossFit Zone', '2023-09-01', 275.00, '2024-06-15', '2024-12-15', 'Wall-mounted units', TRUE),
('Pilates Reformer', 'Pilates', 'Balanced Body', 'Studio', 8, 6, 'good', 'Studio B', '2022-12-01', 2500.00, '2024-04-01', '2024-10-01', 'Professional reformer machines', TRUE),
('Spin Bike', 'Cardio', 'Peloton', 'Bike+', 15, 13, 'excellent', 'Spin Studio', '2024-01-01', 2495.00, '2024-07-15', '2025-01-15', 'With built-in screen', TRUE),
('Battle Ropes', 'Functional', 'Rogue', '50ft', 6, 6, 'good', 'CrossFit Zone', '2023-05-15', 150.00, NULL, NULL, '1.5 inch diameter', TRUE),
('Medicine Ball Set', 'Strength', 'REP Fitness', 'Slam Ball', 20, 18, 'good', 'Functional Area', '2023-07-01', 50.00, NULL, NULL, 'Various weights 5-30 lbs', TRUE);

-- ============================================
-- SAMPLE DATA: Course-Equipment Assignments
-- ============================================
INSERT INTO course_equipment (course_id, equipment_id, quantity_needed, assigned_by) VALUES
(1, 1, 20, 1),  -- Morning Yoga uses Yoga Mats
(3, 2, 10, 1),  -- Strength Training uses Dumbbells
(3, 6, 8, 1),   -- Strength Training uses Kettlebells
(4, 9, 8, 1),   -- Evening Pilates uses Reformers
(5, 4, 12, 1),  -- Boxing uses Boxing Gloves
(6, 8, 5, 1),   -- CrossFit uses Pull-up Bars
(6, 11, 6, 1),  -- CrossFit uses Battle Ropes
(6, 12, 10, 1); -- CrossFit uses Medicine Balls
