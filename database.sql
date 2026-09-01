-- ==========================================================
-- JobPortal.lk - Database Schema & Initial Seed Data
-- Member 1 - Admin & Core/Shared
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `jobportal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jobportal`;

-- ----------------------------------------------------------
-- 1. Users Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'company', 'seeker') NOT NULL DEFAULT 'seeker',
    `phone` VARCHAR(30) DEFAULT NULL,
    `status` ENUM('Active', 'Suspended', 'Pending') NOT NULL DEFAULT 'Active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 2. Companies Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `companies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL,
    `company_name` VARCHAR(150) NOT NULL,
    `industry_type` VARCHAR(100) NOT NULL,
    `location` VARCHAR(120) NOT NULL,
    `owner_email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('Approved', 'Pending Approval', 'Suspended', 'Rejected') NOT NULL DEFAULT 'Pending Approval',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 3. Categories Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'briefcase',
    `job_count` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 4. Jobs Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_id` INT NOT NULL,
    `category_id` INT DEFAULT NULL,
    `title` VARCHAR(180) NOT NULL,
    `company_name` VARCHAR(150) NOT NULL,
    `location` VARCHAR(120) NOT NULL,
    `job_type` ENUM('Full-time', 'Part-time', 'Contract', 'Remote', 'Internship') DEFAULT 'Full-time',
    `salary_range` VARCHAR(100) DEFAULT 'Rs. 150,000 - Rs. 250,000 / month',
    `description` TEXT DEFAULT NULL,
    `requirements` TEXT DEFAULT NULL,
    `status` ENUM('Approved', 'Pending Approval', 'Rejected') NOT NULL DEFAULT 'Pending Approval',
    `posted_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 5. Reviews Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `job_id` INT DEFAULT NULL,
    `job_title` VARCHAR(180) NOT NULL,
    `seeker_name` VARCHAR(120) NOT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `comment` TEXT NOT NULL,
    `status` ENUM('Approved', 'Flagged', 'Rejected') DEFAULT 'Approved',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 6. System Settings Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT PRIMARY KEY DEFAULT 1,
    `site_name` VARCHAR(100) DEFAULT 'JobPortal.lk',
    `site_email` VARCHAR(150) DEFAULT 'admin@jobportal.lk',
    `maintenance_mode` TINYINT(1) DEFAULT 0,
    `enable_registration` TINYINT(1) DEFAULT 1,
    `enable_job_approval` TINYINT(1) DEFAULT 1,
    `jobs_per_page` INT DEFAULT 10,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 7. Recent Activities Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activities` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `type` VARCHAR(50) DEFAULT 'info',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- Initial Seed Data (Matches Wireframes & Spec)
-- ==========================================================

INSERT INTO `settings` (`id`, `site_name`, `site_email`, `maintenance_mode`, `enable_registration`, `enable_job_approval`, `jobs_per_page`)
VALUES (1, 'JobPortal.lk', 'admin@jobportal.lk', 0, 1, 1, 10)
ON DUPLICATE KEY UPDATE `site_name` = VALUES(`site_name`);

-- Insert Users
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `status`) VALUES
(1, 'Admin Kamal', 'admin@jobportal.lk', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'admin', '+94 71 888 9900', 'Active'),
(2, 'Kamal Perera', 'dialog.axiata@email.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'company', '+94 11 987 6890', 'Active'),
(3, 'Nimal Perera', 'dialog.axiata.hr@plc.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'company', '+94 77 464 7460', 'Suspended'),
(4, 'Kamal Perera', 'nimal.perera@gmail.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'seeker', '+94 71 664 7468', 'Active'),
(5, 'Sunil Shantha', 'dialog.cxc@park.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'company', '+94 11 387 4915', 'Active'),
(6, 'Kasun Silva', 'dialog.axiata.sl@plc.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'seeker', '+94 74 485 8875', 'Active'),
(7, 'Nimal Perera', 'dialog.axiata@job.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'seeker', '+94 70 426 8885', 'Active'),
(8, 'Kamal Perera', 'dialog.axiata.test@plc.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'company', '+94 76 467 4855', 'Active'),
(9, 'Dilshan Fernando', 'dialog.carrier@coord.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'seeker', '+94 74 485 0296', 'Suspended'),
(10, 'Nimal Silva', 'dialog12@gmail.com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'admin', '+94 78 638 0233', 'Active'),
(11, 'Kamal Perera', 'didxg.axiata.corp@com', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'admin', '+94 73 406 4298', 'Active');

-- Insert Companies
INSERT INTO `companies` (`id`, `user_id`, `company_name`, `industry_type`, `location`, `owner_email`, `status`) VALUES
(1, 2, 'Dialog Axiata PLC', 'Telecommunications', 'Colombo, Sri Lanka', 'dialog.axiata@email.com', 'Pending Approval'),
(2, 3, 'CodeGen International', 'Software', 'Colombo, Sri Lanka', 'codegen.international@com', 'Approved'),
(3, 8, 'WSO2', 'Finance', 'Colombo, Sri Lanka', 'dialogaxiata@wso2.com', 'Approved'),
(4, 2, 'CodeGen', 'Software', 'Colombo, Sri Lanka', 'dialogaxiata.commercial.com', 'Pending Approval'),
(5, 5, 'Axiata PLC', 'Software', 'Colombo, Sri Lanka', 'dialogaxiata@animal.com', 'Approved'),
(6, 2, 'Cambio', 'Software', 'Colombo, Sri Lanka', 'codegendata@mail.com', 'Suspended'),
(7, 3, 'Carlocoolseal', 'Software', 'Colombo, Sri Lanka', 'dlog.mail@gmial.com', 'Suspended'),
(8, 2, 'CodeGen International', 'Software', 'Colombo, Sri Lanka', 'dialogaxiata@plc.com', 'Approved'),
(9, 8, 'WSO2', 'Finance', 'Colombo, Sri Lanka', 'dialog126@gmail.com', 'Approved'),
(10, 8, 'WSO2', 'Finance', 'Colombo, Sri Lanka', 'dilog.hoste.ccm@com', 'Rejected');

-- Insert Categories
INSERT INTO `categories` (`id`, `name`, `icon`, `job_count`) VALUES
(1, 'Software Engineering', 'code', 1543),
(2, 'Marketing', 'bullhorn', 892),
(3, 'Sales', 'dollar-sign', 710),
(4, 'Healthcare', 'activity', 1543),
(5, 'Finance', 'pie-chart', 1543),
(6, 'Education', 'book-open', 890),
(7, 'Design', 'layout', 890),
(8, 'Operations', 'settings', 1543),
(9, 'Hardware', 'cpu', 450),
(10, 'Secure Engineering', 'shield', 445),
(11, 'Mortalin Care', 'heart', 260);

-- Insert Jobs
INSERT INTO `jobs` (`id`, `company_id`, `category_id`, `title`, `company_name`, `location`, `posted_date`, `status`) VALUES
(1, 1, 1, 'Senior Software Engineer', 'Dialog Axiata PLC', 'Colombo, Sri Lanka', '2026-05-04 19:59:00', 'Approved'),
(2, 2, 2, 'Marketing Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-06 18:25:00', 'Pending Approval'),
(3, 2, 2, 'Marketing Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-05 20:38:00', 'Approved'),
(4, 2, 2, 'Marketing Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-05 17:58:00', 'Approved'),
(5, 1, 2, 'Axiata Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-05 18:00:00', 'Pending Approval'),
(6, 2, 2, 'Marketing Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-05 10:00:00', 'Approved'),
(7, 2, 2, 'Marketing Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-04 12:40:00', 'Approved'),
(8, 2, 1, 'Codeban Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-05 18:00:00', 'Pending Approval'),
(9, 2, 2, 'Marketing Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-04 20:02:00', 'Approved'),
(10, 2, 2, 'Marketing Manager', 'CodeGen', 'Colombo, Sri Lanka', '2026-05-05 10:02:00', 'Rejected');

-- Insert Reviews
INSERT INTO `reviews` (`id`, `job_id`, `job_title`, `seeker_name`, `rating`, `comment`, `status`) VALUES
(1, 1, 'Senior Engineer: Kamal P.', 'Kamal Perera', 5, 'Great interview process and very welcoming team. Fast communication and clear feedback at each step.', 'Approved'),
(2, 1, 'Senior Engineer: Kamal P.', 'Nimal Fernando', 4, 'Solid technical assessment with interesting real-world architecture problems. Took 3 rounds in total.', 'Approved'),
(3, 2, 'Marketing Manager', 'Kasun Silva', 5, 'Exceptional experience with the hiring panel. Culture and expectations were well outlined during the call.', 'Approved'),
(4, 1, 'Senior Engineer: Marketing Manager', 'Dilshan P.', 4, 'Very transparent recruiter and prompt updates. Highly recommend applying here.', 'Approved'),
(5, 1, 'Senior Engineer: Kamal P.', 'Sachith Perera', 5, 'Straightforward interview tasks, reasonable expectations, and great interview atmosphere.', 'Approved'),
(6, 1, 'Senior Engineer: Kamal P.', 'Thilini K.', 3, 'Process was a bit slow between initial screening and technical interview, but the people were polite.', 'Approved'),
(7, 1, 'Senior Engineer: Kamal P.', 'Rohan Dias', 2, 'Scheduled meeting was postponed twice without prior notice before finally happening.', 'Flagged'),
(8, 1, 'Senior Engineer: Kamal P.', 'Nadeeka W.', 5, 'Professional and polite panel. Offered constructive feedback even for the coding assignment.', 'Approved');

-- Insert Activities
INSERT INTO `activities` (`title`, `type`, `created_at`) VALUES
('Job Posted: Senior Engineer at Dialog Axiata PLC', 'job', NOW() - INTERVAL 10 MINUTE),
('User Registered: Nimal Perera (Job Seeker)', 'user', NOW() - INTERVAL 25 MINUTE),
('Company Verified: CodeGen International', 'company', NOW() - INTERVAL 45 MINUTE),
('Flagged Review: Reported comment on Senior Engineer job', 'review', NOW() - INTERVAL 1 HOUR),
('Company Registered: WSO2 entered verification queue', 'company', NOW() - INTERVAL 2 HOUR);
