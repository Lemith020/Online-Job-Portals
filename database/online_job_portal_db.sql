-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 02:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_job_portal_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `app_id` int(11) NOT NULL,
  `seeker_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `cv_id` int(11) NOT NULL,
  `apply_date` date NOT NULL,
  `status` enum('pending','reviewed','rejected','accepted') NOT NULL DEFAULT 'pending',
  `experience` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`app_id`, `seeker_id`, `job_id`, `cv_id`, `apply_date`, `status`, `experience`) VALUES
(10, 8, 34, 26, '2026-09-19', 'reviewed', 'I complete many  project  in my  acedemic period'),
(12, 9, 40, 28, '2026-09-20', 'reviewed', 'kkkkkkkkkk'),
(13, 10, 41, 29, '2026-09-21', 'reviewed', ''),
(14, 8, 43, 26, '2026-09-21', 'accepted', ''),
(15, 8, 42, 26, '2026-09-21', 'pending', ''),
(16, 8, 39, 26, '2026-09-21', 'pending', '');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(9, 'Education'),
(2, 'IT'),
(1, 'marketing'),
(10, 'Other'),
(5, '⚖️ Legal'),
(8, '📊 Business & Management'),
(3, '📞 Customer Service'),
(7, '🏠 Real Estate & Property'),
(6, '🧪 Science & Research'),
(4, '🏛️ Government & Public Sector');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `industry_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `user_id`, `company_name`, `industry_type`, `description`, `location`) VALUES
(5, 11, 'employee', 'IT', 'Innovation company', 'colombo'),
(7, 16, 'IFS', 'IT', 'IFS is a global enterprise software company that provides solutions for businesses to manage areas such as enterprise resource planning (ERP), service management, supply chain management, and asset management. IFS serves customers across industries including manufacturing, aerospace, energy, telecommunications, and construction.', 'colombo'),
(8, 19, 'DAKMA education', 'education', 'A\\L an O\\L students  education', 'Matara'),
(9, 20, 'Legal company', 'Legal', 'for lawyers\r\n', 'Matara');

-- --------------------------------------------------------

--
-- Table structure for table `company_category`
--

CREATE TABLE `company_category` (
  `company_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_category`
--

INSERT INTO `company_category` (`company_id`, `category_id`) VALUES
(5, 1),
(8, 8),
(9, 5);

-- --------------------------------------------------------

--
-- Table structure for table `cvs`
--

CREATE TABLE `cvs` (
  `cv_id` int(11) NOT NULL,
  `seeker_id` int(11) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cvs`
--

INSERT INTO `cvs` (`cv_id`, `seeker_id`, `file_path`, `uploaded_at`) VALUES
(26, 8, '../uploads/cvs/1789824721_SENG21222_2026_Assignment1 (2).pdf', '2026-09-19 19:02:01'),
(28, 9, '../uploads/cvs/1789917538_SENG21222_2026_Assignment1 (2).pdf', '2026-09-20 20:48:58'),
(29, 10, '../uploads/cvs/seeker10_1789973841.pdf', '2026-09-21 12:27:21'),
(30, 8, '../uploads/cvs/seeker8_1790000526.pdf', '2026-09-21 19:52:06');

-- --------------------------------------------------------

--
-- Table structure for table `interviewer`
--

CREATE TABLE `interviewer` (
  `interviewer_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `interviewer_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interviewer`
--

INSERT INTO `interviewer` (`interviewer_id`, `company_id`, `interviewer_name`, `contact_number`) VALUES
(5, 5, 'Lemith Nanditha', '0768092970'),
(7, 7, 'Nimal Perera', '0768092972'),
(9, 9, 'Lemith Nanditha', '0768092970');

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `interview_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `interviewer_id` int(11) NOT NULL,
  `interview_date` date NOT NULL,
  `start_time` time NOT NULL,
  `meeting_link` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled') NOT NULL DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`interview_id`, `app_id`, `interviewer_id`, `interview_date`, `start_time`, `meeting_link`, `notes`, `status`) VALUES
(6, 10, 7, '2026-09-25', '16:00:00', 'link', 'please  prepare your  projects', 'Scheduled'),
(10, 13, 7, '2026-09-22', '14:30:00', 'link', '', 'Completed'),
(11, 13, 7, '2026-09-22', '15:00:00', 'link', 'interview2 \r\nI inform select or not realated job  your  CV contact contact information(you added cv  email) please atention it', 'Scheduled'),
(12, 14, 9, '2026-09-21', '12:00:00', 'link', 'please  come   shedule time', 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(200) NOT NULL,
  `salary_min` decimal(10,2) DEFAULT NULL,
  `salary_max` decimal(10,2) DEFAULT NULL,
  `job_type` enum('Full-time','Part-time','Contract','Remote','Freelance','Internship') NOT NULL,
  `posted_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `company_id`, `category_id`, `title`, `description`, `location`, `salary_min`, `salary_max`, `job_type`, `posted_date`, `expiry_date`, `status`) VALUES
(34, 7, 2, 'Junior Software Engineer', 'Software Development\r\nEntry Level', 'colombo', 100000.00, 200000.00, 'Full-time', '2026-09-19', '2026-09-19', 'approved'),
(35, 7, 2, 'Frontend Developer', 'Web Development\r\n1–2 Years', 'colombo', 150000.00, 200000.00, 'Full-time', '2026-09-19', '2026-09-19', 'approved'),
(37, 7, 2, 'Data Analyst', 'Data & Analytics\r\n1–2 Years', 'Kelaniya', 250000.00, 300000.00, 'Full-time', '2026-09-19', '2026-09-21', 'approved'),
(38, 7, 2, 'Cybersecurity Analyst', 'Cybersecurity\r\n1–2 Years', 'Matara', 150000.00, 200000.00, 'Full-time', '2026-09-19', '2026-09-18', 'approved'),
(39, 7, 1, 'Digital Marketing Executive', 'Manage social media campaigns, online advertising, and digital marketing activities.', 'colombo', 60000.00, 120000.00, 'Full-time', '2026-09-19', '2026-09-19', 'approved'),
(40, 8, 8, 'ET / SFT teaching', '26 year school teacher', 'Matara', 50000.00, 100000.00, 'Part-time', '2026-09-20', '2026-09-30', 'approved'),
(41, 7, 2, 'AI Engineer', 'As an AI Engineer, the role involves tasks such as developing intelligent software solutions to solve business problems using AI/ML technologies, integrating AI models and applications, and adding AI capabilities to existing systems.', 'colombo', 150000.00, 200000.00, 'Full-time', '2026-09-21', '2026-09-21', 'approved'),
(42, 9, 5, 'Junior Legal Associate', 'Assist with legal research, prepare documents, review contracts, and support senior legal professionals with day to day legal matters.', 'Matara', 120000.00, 150000.00, 'Full-time', '2026-09-21', '2026-09-21', 'approved'),
(43, 9, 5, 'Legal Assistant', 'Support the legal team by organizing documents, maintaining records, preparing correspondence, and assisting with legal research and administrative tasks', 'colombo', 100000.00, 150000.00, 'Part-time', '2026-09-21', '2026-09-21', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `job_alerts`
--

CREATE TABLE `job_alerts` (
  `alert_id` int(11) NOT NULL,
  `seeker_id` int(11) NOT NULL,
  `suggest_job` varchar(200) DEFAULT NULL,
  `location_pref` varchar(200) DEFAULT NULL,
  `selects_or_not` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_alerts`
--

INSERT INTO `job_alerts` (`alert_id`, `seeker_id`, `suggest_job`, `location_pref`, `selects_or_not`) VALUES
(4, 7, 'Mobile App  Developer', 'colombo', 1),
(5, 7, 'new job', 'Matara', 1),
(6, 7, 'job1', 'Kelaniya', 1),
(7, 7, 'UI/UX Designer', 'Gampaha', 1),
(8, 8, 'UI/UX Designer', 'Kelaniya', 1),
(9, 8, 'Digital Marketing Executive', 'colombo', 1),
(10, 8, 'Legal Assistant', 'colombo', 1);

-- --------------------------------------------------------

--
-- Table structure for table `job_seekers`
--

CREATE TABLE `job_seekers` (
  `seeker_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `birth_day` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `status` enum('hired','not_hired') DEFAULT 'not_hired'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_seekers`
--

INSERT INTO `job_seekers` (`seeker_id`, `user_id`, `birth_day`, `phone`, `bio`, `status`) VALUES
(7, 10, NULL, NULL, NULL, 'not_hired'),
(8, 17, '2003-04-15', '0768092970', 'Full  stack  ,  Mobile App  &  AI  ML ethuntist\r\nI am   secnd  year undergaraduted student', 'not_hired'),
(9, 18, '2007-09-01', '0762942970', 'name', 'not_hired'),
(10, 16, NULL, NULL, NULL, 'not_hired'),
(11, 13, NULL, NULL, NULL, 'not_hired');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `seeker_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `rank` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seeker_category`
--

CREATE TABLE `seeker_category` (
  `seeker_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `plan_id` int(20) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`plan_id`, `plan_name`, `duration_days`, `price`) VALUES
(1, 'Standard Seeker Plan', 365, 0.00),
(2, 'Professional Seeker Plan', 30, 1500.00),
(3, 'Normal seeker Plan', 30, 1000.00),
(4, 'Normal seeker Plan2', 30, 1000.00),
(5, 'Low Budget  Seeker Plan', 14, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('site_email', 'admin@jobportal.lk'),
('site_name', 'JobPortal.lk');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('admin','job_seeker','company') NOT NULL DEFAULT 'job_seeker',
  `status` enum('Active','Suspended') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `phone`, `role`, `status`) VALUES
(10, 'seeker', NULL, NULL, 'scheck@gmail.com', '$2y$10$JWo7uCz57xGbCAMEbL.a5eqq/f8r.3K1s1x7ND6EpsqpDLmxGh53S', '0768092971', 'job_seeker', 'Active'),
(11, 'employee', NULL, NULL, 'echeck@gmail.com', '$2y$10$hK0aYCJuPE5WoiilRZ1SNe18EOV3bRHxeEHoti7Hzf56iT6qr6Ye2', '0768092971', 'company', 'Active'),
(13, 'Super', NULL, 'Admin', 'admin@jobportal.lk', '$2y$10$PJ5N8Uu6foJkhh1.YYEROe9NKI3AFtjG12qwejCrURzTpMv8RTxKG', '0768092970', 'admin', 'Active'),
(16, 'IFS', NULL, NULL, 'ifs@gmail.com', '$2y$10$Mje0dLDzA8ZlgHgAuKE48uJHCC.flr8.rcSyKkTuFfysQABcKZ5/W', '0768092970', 'company', 'Active'),
(17, 'Lemith', '', 'Nanditha', 'lemithnanditha41@gmail.com', '$2y$10$7falWdEDXZIoBfgbXYPC.e80C1YCgyh3PoJsAWMA/SeZvMlY1j/5.', '0768092970', 'job_seeker', 'Active'),
(18, 'Chemith', '', 'Malinga', 'chemithmalinga@gmail.com', '$2y$10$N4U6tVKlcKNT5/t343VaaeGsYlh/mq.XEXCP7/DCTKKbaJFiVUuuu', '0762942970', 'job_seeker', 'Active'),
(19, 'DAKMA education', NULL, NULL, 'c@gmail.com', '$2y$10$Pps/7i7oUrRWHWWZmRY0r.qzSO5IeiG/Ae2mLxDiKcE0QdpyE7W4e', '0762942970', 'company', 'Active'),
(20, 'Legal company', NULL, NULL, 'l@gmail.com', '$2y$10$W7hU8n8b.P83w4mBMzwKguWGk2P.MffWj/xdpgruHHSjJrn7l1Ifa', '0123456789', 'company', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `sub_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`sub_id`, `user_id`, `plan_id`, `start_date`, `end_date`, `is_active`) VALUES
(10, 17, 1, '2026-09-20', '2027-09-20', 0),
(11, 18, 1, '2026-09-20', '2027-09-20', 1),
(12, 17, 3, '2026-09-21', '2026-10-21', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `fk_app_seeker` (`seeker_id`),
  ADD KEY `fk_app_job` (`job_id`),
  ADD KEY `fk_app_cv` (`cv_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `fk_company_user` (`user_id`);

--
-- Indexes for table `company_category`
--
ALTER TABLE `company_category`
  ADD PRIMARY KEY (`company_id`,`category_id`),
  ADD KEY `fk_cc_category` (`category_id`);

--
-- Indexes for table `cvs`
--
ALTER TABLE `cvs`
  ADD PRIMARY KEY (`cv_id`),
  ADD KEY `fk_cv_seeker` (`seeker_id`);

--
-- Indexes for table `interviewer`
--
ALTER TABLE `interviewer`
  ADD PRIMARY KEY (`interviewer_id`),
  ADD KEY `fk_interviewer_company` (`company_id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`interview_id`),
  ADD KEY `fk_interview_app` (`app_id`),
  ADD KEY `fk_interview_interviewer` (`interviewer_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `fk_job_company` (`company_id`);

--
-- Indexes for table `job_alerts`
--
ALTER TABLE `job_alerts`
  ADD PRIMARY KEY (`alert_id`),
  ADD KEY `fk_alert_seeker` (`seeker_id`);

--
-- Indexes for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD PRIMARY KEY (`seeker_id`),
  ADD KEY `fk_seeker_user` (`user_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_review_job` (`job_id`),
  ADD KEY `fk_review_seeker` (`seeker_id`);

--
-- Indexes for table `seeker_category`
--
ALTER TABLE `seeker_category`
  ADD PRIMARY KEY (`seeker_id`,`category_id`),
  ADD KEY `fk_sc_category` (`category_id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`sub_id`),
  ADD KEY `fk_sub_user` (`user_id`),
  ADD KEY `fk_sub_plan` (`plan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cvs`
--
ALTER TABLE `cvs`
  MODIFY `cv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `interviewer`
--
ALTER TABLE `interviewer`
  MODIFY `interviewer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `interview_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `job_alerts`
--
ALTER TABLE `job_alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `job_seekers`
--
ALTER TABLE `job_seekers`
  MODIFY `seeker_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `plan_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `fk_app_cv` FOREIGN KEY (`cv_id`) REFERENCES `cvs` (`cv_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_app_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_app_seeker` FOREIGN KEY (`seeker_id`) REFERENCES `job_seekers` (`seeker_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `fk_company_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `company_category`
--
ALTER TABLE `company_category`
  ADD CONSTRAINT `fk_cc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cvs`
--
ALTER TABLE `cvs`
  ADD CONSTRAINT `fk_cv_seeker` FOREIGN KEY (`seeker_id`) REFERENCES `job_seekers` (`seeker_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `interviewer`
--
ALTER TABLE `interviewer`
  ADD CONSTRAINT `fk_interviewer_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `interviews`
--
ALTER TABLE `interviews`
  ADD CONSTRAINT `fk_interview_app` FOREIGN KEY (`app_id`) REFERENCES `applications` (`app_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_interview_interviewer` FOREIGN KEY (`interviewer_id`) REFERENCES `interviewer` (`interviewer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `fk_job_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_alerts`
--
ALTER TABLE `job_alerts`
  ADD CONSTRAINT `fk_alert_seeker` FOREIGN KEY (`seeker_id`) REFERENCES `job_seekers` (`seeker_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD CONSTRAINT `fk_seeker_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_review_seeker` FOREIGN KEY (`seeker_id`) REFERENCES `job_seekers` (`seeker_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seeker_category`
--
ALTER TABLE `seeker_category`
  ADD CONSTRAINT `fk_sc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sc_seeker` FOREIGN KEY (`seeker_id`) REFERENCES `job_seekers` (`seeker_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD CONSTRAINT `fk_sub_plan` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`plan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sub_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
