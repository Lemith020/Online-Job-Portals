-- ==========================================================
-- JobPortal.lk - Relational Database Schema
-- Database Name: online_job_portal_db
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `online_job_portal_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `online_job_portal_db`;

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
-- 2. Job Seekers Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `job_seekers` (
    `seeker_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `birth_day` DATE DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `location` VARCHAR(120) DEFAULT 'Colombo, Sri Lanka',
    `experience_years` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 3. Companies Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `companies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL,
    `company_name` VARCHAR(150) NOT NULL,
    `industry_type` VARCHAR(100) NOT NULL DEFAULT 'Information Technology',
    `location` VARCHAR(120) NOT NULL DEFAULT 'Colombo',
    `owner_email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('Approved', 'Pending Approval', 'Suspended', 'Rejected') NOT NULL DEFAULT 'Pending Approval',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 4. Categories Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'briefcase',
    `job_count` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 5. Jobs Table
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
-- 6. Reviews Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `job_id` INT DEFAULT NULL,
    `job_title` VARCHAR(180) NOT NULL,
    `seeker_name` VARCHAR(120) NOT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `comment` TEXT NOT NULL,
    `status` ENUM('Approved', 'Flagged', 'Rejected') DEFAULT 'Approved',
    `is_flagged` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 7. Subscription Plans Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subscription_plans` (
    `plan_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `duration_days` INT NOT NULL DEFAULT 30,
    `max_jobs` INT NOT NULL DEFAULT 5,
    `features` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 8. User / Company Subscriptions Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_subscriptions` (
    `sub_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `plan_id` INT NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans`(`plan_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 9. System Settings Table
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
-- 10. Admin Activity Log Table
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
    `log_id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT NOT NULL DEFAULT 1,
    `action` VARCHAR(255) NOT NULL,
    `details` VARCHAR(100) DEFAULT 'general',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
