-- ============================================================
-- Sample Seed Data for online_job_portal_db
-- Import meka phpMyAdmin eken karanna, table structure walata
-- passe (schema import kalata passe witharai run karanna).
-- Password okkomatama: "password123" (bcrypt hashed)
-- ============================================================

-- --------------------------------------------------------
-- USERS  (1 = company, 2-6 = job seekers)
-- --------------------------------------------------------
INSERT INTO `users` (`user_id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `phone`, `role`) VALUES
(1, 'Dialog', '', 'Axiata PLC', 'hr@dialog.lk', '$2b$12$cgKXqju4LKG14SlSFQCYsOQW/B2QPikFs9XKT.b/RT5C6EHQfCQ0a', '0112345678', 'company'),
(2, 'Kamal', '', 'Perera', 'kamal.perera@gmail.com', '$2b$12$cgKXqju4LKG14SlSFQCYsOQW/B2QPikFs9XKT.b/RT5C6EHQfCQ0a', '0771234567', 'job_seeker'),
(3, 'Nethmi', '', 'Lakshan', 'nethmi.l@gmail.com', '$2b$12$cgKXqju4LKG14SlSFQCYsOQW/B2QPikFs9XKT.b/RT5C6EHQfCQ0a', '0712345678', 'job_seeker'),
(4, 'Kasun', '', 'Wickramasinghe', 'kasun.w@gmail.com', '$2b$12$cgKXqju4LKG14SlSFQCYsOQW/B2QPikFs9XKT.b/RT5C6EHQfCQ0a', '0759876543', 'job_seeker'),
(5, 'Sarah', '', 'Fernando', 'sarah.f@gmail.com', '$2b$12$cgKXqju4LKG14SlSFQCYsOQW/B2QPikFs9XKT.b/RT5C6EHQfCQ0a', '0723456789', 'job_seeker'),
(6, 'Ravi', '', 'Bandara', 'ravi.b@gmail.com', '$2b$12$cgKXqju4LKG14SlSFQCYsOQW/B2QPikFs9XKT.b/RT5C6EHQfCQ0a', '0701122334', 'job_seeker');

-- --------------------------------------------------------
-- COMPANY  (company_id = 1 → the mock-logged-in company)
-- --------------------------------------------------------
INSERT INTO `company` (`company_id`, `user_id`, `company_name`, `industry_type`, `description`, `location`) VALUES
(1, 1, 'Dialog Axiata PLC', 'Telecommunications', 'Sri Lanka\'s premier connectivity provider, building digital services for millions of customers.', 'Colombo, Sri Lanka');

-- --------------------------------------------------------
-- CATEGORIES
-- --------------------------------------------------------
INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Software Development'),
(2, 'Marketing'),
(3, 'Data Science'),
(4, 'Customer Service'),
(5, 'Finance'),
(6, 'Design'),
(7, 'Human Resources'),
(8, 'Sales');

-- --------------------------------------------------------
-- COMPANY_CATEGORY  (Dialog tags itself under 3 categories)
-- --------------------------------------------------------
INSERT INTO `company_category` (`company_id`, `category_id`) VALUES
(1, 1),
(1, 3),
(1, 4);

-- --------------------------------------------------------
-- JOB_SEEKERS  (seeker_id 1-5 → user_id 2-6)
-- --------------------------------------------------------
INSERT INTO `job_seekers` (`seeker_id`, `user_id`, `birth_day`, `phone`, `bio`, `status`) VALUES
(1, 2, '1998-04-12', '0771234567', 'Full-stack developer with a passion for building scalable web apps.', 'not_hired'),
(2, 3, '1997-09-03', '0712345678', 'UI/UX designer focused on clean, user-friendly interfaces.', 'not_hired'),
(3, 4, '1999-01-21', '0759876543', 'Data enthusiast skilled in Python, SQL and visualization tools.', 'not_hired'),
(4, 5, '2000-06-15', '0723456789', 'Recent graduate looking for a junior software engineering role.', 'not_hired'),
(5, 6, '1996-11-30', '0701122334', 'QA engineer experienced in manual and automated testing.', 'not_hired');

-- --------------------------------------------------------
-- CVS  (one CV per seeker)
-- --------------------------------------------------------
INSERT INTO `cvs` (`cv_id`, `seeker_id`, `file_path`, `uploaded_at`) VALUES
(1, 1, 'uploads/cvs/kamal_perera_cv.pdf', '2026-08-10 09:15:00'),
(2, 2, 'uploads/cvs/nethmi_lakshan_cv.pdf', '2026-08-11 10:30:00'),
(3, 3, 'uploads/cvs/kasun_wickramasinghe_cv.pdf', '2026-08-11 14:00:00'),
(4, 4, 'uploads/cvs/sarah_fernando_cv.pdf', '2026-08-12 08:45:00'),
(5, 5, 'uploads/cvs/ravi_bandara_cv.pdf', '2026-08-12 16:20:00');

-- --------------------------------------------------------
-- JOBS  (all posted by company_id = 1)
-- --------------------------------------------------------
INSERT INTO `jobs` (`job_id`, `company_id`, `category_id`, `title`, `description`, `location`, `salary_min`, `salary_max`, `job_type`, `posted_date`, `expiry_date`, `status`) VALUES
(1, 1, 1, 'Senior Software Engineer', 'Looking for an experienced software engineer to join our backend team, working on core telecom platforms.', 'Colombo, Sri Lanka', 150000.00, 220000.00, 'Full-time', '2026-07-20', '2026-09-20', 'approved'),
(2, 1, 6, 'UI/UX Designer', 'Design intuitive customer-facing interfaces for our mobile and web products.', 'Colombo, Sri Lanka', 90000.00, 140000.00, 'Full-time', '2026-07-25', '2026-09-25', 'approved'),
(3, 1, 3, 'Data Scientist', 'Analyze customer usage data to drive product and network decisions.', 'Colombo, Sri Lanka', 180000.00, 250000.00, 'Full-time', '2026-08-01', '2026-10-01', 'pending'),
(4, 1, 1, 'QA Engineer', 'Own manual and automated testing for our digital services team.', 'Kandy, Sri Lanka', 100000.00, 150000.00, 'Full-time', '2026-08-05', '2026-10-05', 'approved'),
(5, 1, 4, 'Customer Support Executive', 'Handle customer queries and escalations across chat, email and phone.', 'Colombo, Sri Lanka', 60000.00, 85000.00, 'Part-time', '2026-08-10', '2026-10-10', 'rejected');

-- --------------------------------------------------------
-- APPLICATIONS  (seekers applying to the jobs above)
-- --------------------------------------------------------
INSERT INTO `applications` (`app_id`, `seeker_id`, `job_id`, `cv_id`, `apply_date`, `status`, `experience`) VALUES
(1, 1, 1, 1, '2026-08-12', 'accepted', '5+ years in React/Node.js, previously at WSO2.'),
(2, 2, 2, 2, '2026-08-13', 'reviewed', '3 years UI/UX experience, worked with fintech startups.'),
(3, 3, 3, 3, '2026-08-14', 'pending', '2 years data analysis experience, Python and SQL.'),
(4, 4, 1, 4, '2026-08-15', 'rejected', '1 year junior developer experience.'),
(5, 5, 4, 5, '2026-08-16', 'reviewed', '4 years QA automation testing experience.');

-- --------------------------------------------------------
-- INTERVIEWER  (belongs to company_id = 1)
-- --------------------------------------------------------
INSERT INTO `interviewer` (`interviewer_id`, `company_id`, `interviewer_name`, `contact_number`) VALUES
(1, 1, 'Nadeesha Perera', '0771112233'),
(2, 1, 'Ruwan Silva', '0779998877');

-- --------------------------------------------------------
-- INTERVIEWS
-- --------------------------------------------------------
INSERT INTO `interviews` (`interview_id`, `app_id`, `interviewer_id`, `interview_date`, `start_time`, `meeting_link`, `notes`, `status`) VALUES
(1, 1, 1, '2026-08-20', '10:00:00', 'https://meet.google.com/abc-defg-hij', 'Strong technical round, moving to offer stage.', 'Completed'),
(2, 2, 2, '2026-08-25', '14:00:00', 'https://meet.google.com/xyz-uvwx-klm', NULL, 'Scheduled'),
(3, 5, 1, '2026-08-28', '11:00:00', 'https://meet.google.com/qrt-opqr-stu', NULL, 'Scheduled');

-- --------------------------------------------------------
-- REVIEW  (seekers reviewing jobs/company)
-- --------------------------------------------------------
INSERT INTO `review` (`review_id`, `job_id`, `seeker_id`, `rating`, `rank`, `comment`) VALUES
(1, 1, 2, 5, 1, 'Great interview process and a friendly team.'),
(2, 2, 3, 4, 2, 'Good communication throughout the process.'),
(3, 4, 1, 5, 1, 'Professional and well organized company.');

-- --------------------------------------------------------
-- SEEKER_CATEGORY  (job preferences per seeker)
-- --------------------------------------------------------
INSERT INTO `seeker_category` (`seeker_id`, `category_id`) VALUES
(1, 1),
(2, 6),
(3, 3),
(4, 1),
(5, 1);

-- --------------------------------------------------------
-- SUBSCRIPTION_PLANS
-- --------------------------------------------------------
INSERT INTO `subscription_plans` (`plan_id`, `plan_name`, `duration_days`, `price`) VALUES
(1, 'Basic', 30, 500.00),
(2, 'Standard', 90, 1200.00),
(3, 'Premium', 180, 2000.00);

-- --------------------------------------------------------
-- USER_SUBSCRIPTIONS  (a couple of seekers subscribed)
-- --------------------------------------------------------
INSERT INTO `user_subscriptions` (`sub_id`, `user_id`, `plan_id`, `start_date`, `end_date`, `is_active`) VALUES
(1, 2, 1, '2026-08-01', '2026-08-31', 1),
(2, 3, 2, '2026-07-15', '2026-10-13', 1);

-- --------------------------------------------------------
-- JOB_ALERTS  (seeker alert preferences)
-- --------------------------------------------------------
INSERT INTO `job_alerts` (`alert_id`, `seeker_id`, `suggest_job`, `location_pref`, `selects_or_not`) VALUES
(1, 1, 'Software Engineer', 'Colombo', 1),
(2, 3, 'Data Analyst', 'Colombo', 1),
(3, 5, 'QA Engineer', 'Kandy', 0);
