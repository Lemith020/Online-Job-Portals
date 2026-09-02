-- ==========================================================
-- JobPortal.lk - Sample Seed Data
-- ==========================================================

USE `online_job_portal_db`;

-- Users (Password is 'Password123!')
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `status`) VALUES
(1, 'Admin Kamal Perera', 'admin@jobportal.lk', '$2y$10$eA32bLd48l7/mH8aL/bJae0g9UfQO4dJ9q4wHw2F7rA6H7zK0dY3y', 'admin', '+94 77 123 4567', 'Active'),
(2, 'Dilshan Silva', 'dilshan.silva@gmail.com', '$2y$10$eA32bLd48l7/mH8aL/bJae0g9UfQO4dJ9q4wHw2F7rA6H7zK0dY3y', 'seeker', '+94 71 987 6543', 'Active'),
(3, 'Virtusa HR Team', 'careers@virtusa.com', '$2y$10$eA32bLd48l7/mH8aL/bJae0g9UfQO4dJ9q4wHw2F7rA6H7zK0dY3y', 'company', '+94 11 234 5678', 'Active'),
(4, 'Nadeesha Fernando', 'nadeesha.f@hotmail.com', '$2y$10$eA32bLd48l7/mH8aL/bJae0g9UfQO4dJ9q4wHw2F7rA6H7zK0dY3y', 'seeker', '+94 76 555 4321', 'Active'),
(5, 'Dialog Axiata Careers', 'jobs@dialog.lk', '$2y$10$eA32bLd48l7/mH8aL/bJae0g9UfQO4dJ9q4wHw2F7rA6H7zK0dY3y', 'company', '+94 77 733 3333', 'Active'),
(6, 'Kasun Jayawardena', 'kasun.j@yahoo.com', '$2y$10$eA32bLd48l7/mH8aL/bJae0g9UfQO4dJ9q4wHw2F7rA6H7zK0dY3y', 'seeker', '+94 70 111 2233', 'Suspended'),
(7, 'WSO2 Recruitment', 'hr@wso2.com', '$2y$10$eA32bLd48l7/mH8aL/bJae0g9UfQO4dJ9q4wHw2F7rA6H7zK0dY3y', 'company', '+94 11 214 5345', 'Active'),
(8, 'Anura Gunasekara', 'anura.g@gmail.com', '$2y$10$eA32bLd48l7/mH8aL/bJae0g9UfQO4dJ9q4wHw2F7rA6H7zK0dY3y', 'seeker', '+94 72 345 6789', 'Pending')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Job Seekers
INSERT INTO `job_seekers` (`seeker_id`, `user_id`, `birth_day`, `phone`, `bio`, `location`, `experience_years`) VALUES
(1, 2, '1996-05-14', '+94 71 987 6543', 'Full-stack developer proficient in React, PHP, and Node.js.', 'Colombo, Sri Lanka', 4),
(2, 4, '1998-11-20', '+94 76 555 4321', 'UI/UX Designer with a passion for clean aesthetics and mobile design.', 'Kandy, Sri Lanka', 3),
(3, 6, '2000-02-10', '+94 70 111 2233', 'Junior QA Engineer with manual testing experience.', 'Galle, Sri Lanka', 1),
(4, 8, '1993-08-30', '+94 72 345 6789', 'DevOps Specialist experienced in AWS, Docker, and CI/CD pipelines.', 'Kurunegala, Sri Lanka', 6)
ON DUPLICATE KEY UPDATE `location` = VALUES(`location`);

-- Companies
INSERT INTO `companies` (`id`, `user_id`, `company_name`, `industry_type`, `location`, `owner_email`, `phone`, `description`, `status`) VALUES
(1, 3, 'Virtusa (Pvt) Ltd', 'Information Technology', 'Colombo 07', 'careers@virtusa.com', '+94 11 234 5678', 'Global provider of digital engineering and IT services.', 'Approved'),
(2, 5, 'Dialog Axiata PLC', 'Telecommunications', 'Colombo 02', 'jobs@dialog.lk', '+94 77 733 3333', 'Sri Lanka’s premier connectivity provider.', 'Approved'),
(3, 7, 'WSO2 Lanka', 'Enterprise Software', 'Colombo 03', 'hr@wso2.com', '+94 11 214 5345', 'Open-source technology provider for modern enterprise solutions.', 'Approved'),
(4, NULL, 'Apex Digital Media', 'Marketing & Advertising', 'Nugegoda', 'contact@apexdigital.lk', '+94 11 280 9988', 'Creative agency specializing in performance marketing.', 'Pending Approval'),
(5, NULL, 'FastTrack Logistics', 'Supply Chain', 'Peliyagoda', 'support@fasttrack.lk', '+94 11 556 7890', 'Courier and freight logistics service.', 'Suspended')
ON DUPLICATE KEY UPDATE `company_name` = VALUES(`company_name`);

-- Categories
INSERT INTO `categories` (`id`, `name`, `icon`, `job_count`) VALUES
(1, 'Software Engineering', 'code', 142),
(2, 'Cloud & DevOps', 'cloud', 64),
(3, 'Design & Creative', 'pen-nib', 48),
(4, 'Marketing & Sales', 'chart-line', 52),
(5, 'Accounting & Finance', 'sack-dollar', 38),
(6, 'Healthcare & Medical', 'heart-pulse', 29),
(7, 'Human Resources', 'users', 21),
(8, 'Security & Networking', 'shield-halved', 18)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Jobs
INSERT INTO `jobs` (`id`, `company_id`, `category_id`, `title`, `company_name`, `location`, `job_type`, `salary_range`, `description`, `requirements`, `status`) VALUES
(1, 1, 1, 'Senior Full Stack Engineer', 'Virtusa (Pvt) Ltd', 'Colombo 07', 'Full-time', 'Rs. 250,000 - Rs. 400,000', 'Develop mission-critical web applications with React and Node.js.', '4+ years exp with fullstack stack.', 'Approved'),
(2, 2, 2, 'DevOps & Cloud Architect', 'Dialog Axiata PLC', 'Colombo 02', 'Full-time', 'Rs. 300,000 - Rs. 500,000', 'Oversee cloud infrastructure on AWS and Kubernetes.', '5+ years experience in CI/CD and AWS.', 'Approved'),
(3, 3, 3, 'UI/UX Product Designer', 'WSO2 Lanka', 'Colombo 03', 'Remote', 'Rs. 180,000 - Rs. 280,000', 'Design intuitive interfaces and component design systems.', 'Figma proficiency and strong portfolio.', 'Approved'),
(4, 4, 4, 'Digital Marketing Lead', 'Apex Digital Media', 'Nugegoda', 'Full-time', 'Rs. 120,000 - Rs. 180,000', 'Drive growth marketing and social ad campaigns.', 'SEO/SEM expertise and analytics.', 'Pending Approval')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- Reviews
INSERT INTO `reviews` (`id`, `job_title`, `seeker_name`, `rating`, `comment`, `status`, `is_flagged`) VALUES
(1, 'Senior Full Stack Engineer (Virtusa)', 'Dilshan Silva', 5, 'Excellent interview process. Professional panel with relevant technical scenarios.', 'Approved', 0),
(2, 'UI/UX Product Designer (WSO2)', 'Nadeesha Fernando', 4, 'Great company culture and constructive feedback after design assignment.', 'Approved', 0),
(3, 'Digital Marketing Lead (Apex Digital)', 'Kasun Jayawardena', 1, 'THIS COMPANY IS COMPLETE FRAUD AND SCAM DO NOT APPLY!!!', 'Flagged', 1)
ON DUPLICATE KEY UPDATE `seeker_name` = VALUES(`seeker_name`);

-- Subscription Plans
INSERT INTO `subscription_plans` (`plan_id`, `name`, `price`, `duration_days`, `max_jobs`, `features`) VALUES
(1, 'Starter / Free', 0.00, 30, 3, 'Standard job postings, Basic candidate search, 3 Active job listings'),
(2, 'Professional Employer', 15000.00, 30, 15, 'Featured job tag, Unlimited applicant CV downloads, Priority tech support'),
(3, 'Enterprise Unlimited', 45000.00, 90, 100, 'Dedicated account manager, Automated candidate shortlisting, Custom branding')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- User Subscriptions
INSERT INTO `user_subscriptions` (`sub_id`, `user_id`, `plan_id`, `start_date`, `end_date`, `is_active`) VALUES
(101, 3, 3, '2026-02-01', '2026-05-01', 1),
(102, 5, 3, '2026-03-01', '2026-06-01', 1),
(103, 7, 2, '2026-03-15', '2026-04-15', 1)
ON DUPLICATE KEY UPDATE `is_active` = VALUES(`is_active`);

-- Settings
INSERT INTO `settings` (`id`, `site_name`, `site_email`, `maintenance_mode`, `enable_registration`, `enable_job_approval`, `jobs_per_page`) VALUES
(1, 'JobPortal.lk', 'admin@jobportal.lk', 0, 1, 1, 10)
ON DUPLICATE KEY UPDATE `site_name` = VALUES(`site_name`);
