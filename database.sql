-- RTRAE Engine Database Schema & Initial Data
-- Real-Time Resource Allocation Engine

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `phone_number` VARCHAR(15) NOT NULL DEFAULT '+910000000000',
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('Admin', 'Manager', 'Coordinator') NOT NULL DEFAULT 'Admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `resources` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `category` ENUM('Human / Staff', 'Room / Facility', 'IT / Cloud Server', 'Equipment / Fleet', 'Service / Catering') NOT NULL,
    `capacity` DECIMAL(8,2) NOT NULL DEFAULT 8.00,
    `active_load` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('available','busy','inactive') NOT NULL DEFAULT 'available',
    `skills` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tasks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `required_skill` VARCHAR(100) NOT NULL,
    `priority` TINYINT NOT NULL DEFAULT 3,
    `estimated_effort` DECIMAL(8,2) NOT NULL,
    `status` ENUM('pending','allocated','completed') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `allocations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `task_id` INT NOT NULL UNIQUE,
    `resource_id` INT NOT NULL,
    `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `score` DECIMAL(8,2) NOT NULL,
    `status` ENUM('active','released') NOT NULL DEFAULT 'active',
    FOREIGN KEY (`task_id`) REFERENCES `tasks`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `system_settings` (
    `setting_key` VARCHAR(60) PRIMARY KEY,
    `setting_value` VARCHAR(255) NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Settings
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('org_name', 'RTRAE Campus & Institutional Operations'),
('weight_skill', '0.50'),
('weight_availability', '0.30'),
('weight_fairness', '0.20'),
('workday_start', '08:00'),
('workday_end', '17:00'),
('max_daily_hours', '8.0'),
('support_phone', '+91 7048700224'),
('support_email', '25amtics@gmail.com');

-- Default Admin: admin@rtrae.edu.in / Admin@123
INSERT IGNORE INTO `users` (`id`, `full_name`, `email`, `phone_number`, `password_hash`, `role`) VALUES
(1, 'System Admin', 'admin@rtrae.edu.in', '+919876543210', '$2y$10$w6lZzO/VwU5hO4x0B9QeJu17aZq9f1o8F27U9i8Yk1t11hY8g15u6', 'Admin');

-- Seed Campus Resources
INSERT IGNORE INTO `resources` (`id`, `name`, `category`, `capacity`, `active_load`, `status`, `skills`) VALUES
(1, 'Classroom B01 (Ground Floor)', 'Room / Facility', 8.00, 0.00, 'available', 'Smartboard,Lecture,Capacity 60'),
(2, 'Classroom B02 (Ground Floor)', 'Room / Facility', 8.00, 2.00, 'available', 'Projector,Lecture,Capacity 60'),
(3, 'Classroom B03 (Ground Floor)', 'Room / Facility', 8.00, 0.00, 'available', 'Smartboard,Seminar,Capacity 90'),
(4, 'Classroom B11 (First Floor)', 'Room / Facility', 8.00, 0.00, 'available', 'Computer Lab,Practical,Capacity 90'),
(5, 'Classroom B12 (First Floor)', 'Room / Facility', 8.00, 4.00, 'available', 'Smartboard,Lecture,Capacity 60'),
(6, 'Classroom B13 (First Floor)', 'Room / Facility', 8.00, 0.00, 'available', 'Projector,Workshop,Capacity 75'),
(7, 'Wing B Seminar Hall', 'Room / Facility', 8.00, 2.00, 'available', 'Conference,Smartboard,Meeting'),
(8, 'Wing C Department Lab', 'Room / Facility', 8.00, 0.00, 'available', 'Coding,Practical,High-Spec PCs'),
(9, 'Wing D Faculty Lounge', 'Room / Facility', 8.00, 1.00, 'available', 'Meeting,Briefing,Discussion'),
(10, 'Wing E Auditorium', 'Room / Facility', 8.00, 0.00, 'available', 'Auditorium,Events,Capacity 300'),
(11, 'Wing F Research Center', 'Room / Facility', 8.00, 0.00, 'available', 'AI Training,Research,High Compute'),
(12, 'Wing G Examination Hall', 'Room / Facility', 8.00, 0.00, 'available', 'Exam Duty,Invigilation,Capacity 200'),
(13, 'Wing H Multi-Purpose Hall', 'Room / Facility', 8.00, 0.00, 'available', 'Workshop,Exhibition,Gathering'),
(14, 'Wing I Executive Boardroom', 'Room / Facility', 8.00, 0.00, 'available', 'Meeting,Conference,Presentation'),
(15, 'Prof. Kartikey Singh', 'Human / Staff', 8.00, 2.00, 'available', 'DBMS,SQL,Backend,Teacher'),
(16, 'Prof. Adhiraj Jha', 'Human / Staff', 8.00, 3.00, 'available', 'Java,Algorithms,Lab Supervision,Teacher'),
(17, 'Prof. Nitin Tiwari', 'Human / Staff', 8.00, 1.00, 'available', 'Frontend,UI/UX,Web Tech,Teacher'),
(18, 'Ramesh Kumar (Senior Peon)', 'Human / Staff', 8.00, 2.00, 'available', 'Peon,Delivery,Document Transport,Errand'),
(19, 'Suresh Patel (Campus Peon)', 'Human / Staff', 8.00, 0.00, 'available', 'Peon,Exam Support,Bell Duty,Cleaning'),
(20, 'Campus Transit Van #1', 'Equipment / Fleet', 10.00, 2.00, 'available', 'Vehicle,Transport,Field Trip,Luggage'),
(21, 'Faculty Utility Car #2', 'Equipment / Fleet', 8.00, 0.00, 'available', 'Vehicle,Official Travel,Pickup'),
(22, 'Campus Central Canteen Catering', 'Service / Catering', 16.00, 4.00, 'available', 'Food,Catering,Event Snacks,Lunch'),
(23, 'VIP Guest High-Tea Services', 'Service / Catering', 8.00, 0.00, 'available', 'Food,High-Tea,Conference Catering');

-- Seed Initial Tasks
INSERT IGNORE INTO `tasks` (`id`, `title`, `required_skill`, `priority`, `estimated_effort`, `status`) VALUES
(1, 'Computer Lab Session for 90 Students (Classroom B03)', 'Computer Lab', 5, 2.00, 'pending'),
(2, 'Database Practical Exam Supervision in B11', 'Practical', 5, 3.00, 'pending'),
(3, 'Document Delivery from Wing B to Wing I', 'Peon', 3, 1.00, 'pending'),
(4, 'Campus Van for Industrial Field Visit', 'Vehicle', 4, 4.00, 'pending'),
(5, 'Catering for Department Annual Meeting in Wing I', 'Food', 4, 2.00, 'pending'),
(6, 'Morning Lecture in Classroom B01', 'Smartboard', 4, 2.00, 'pending');

SET FOREIGN_KEY_CHECKS = 1;
