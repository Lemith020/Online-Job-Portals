# Landing Page - Online Job Portal System

Welcome to the **Landing Page & Index Portal** of our Online Job Portal system. This page serves as the primary gateway for job seekers, employers, and visitors, offering an intuitive entering experience to explore job opportunities, understand platform features, and navigate seamlessly across different portals.

---
![Hero Section Screenshot](images/landing_hero.png)
*Figure 1: The main hero banner with an integrated quick-search bar for instant job discovery.*

### 2. Featured Job Categories
A grid-based section highlighting primary industry categories (e.g., Software Engineering, Marketing, Finance, Healthcare) along with real-time active job counters for each category.

![Featured Categories Screenshot](images/landing_categories.png)
*Figure 2: Interactive category cards displaying popular industries and available job counts.*

### 3. Recent & Trending Job Listings
A live feed displaying the latest approved job vacancies posted by verified employers, complete with job tags, salary ranges, location badges, and direct "Apply Now" or "View Details" actions.

![Recent Jobs Screenshot](images/landing_recent_jobs.png)
*Figure 3: Featured job listing cards showcasing top and newly published career opportunities.*

### 4. Platform Statistics & Impact Metrics
An overview section highlighting platform achievements, including total active job postings, registered companies, successful candidate placements, and verified job seekers.

![Platform Metrics Screenshot](images/landing_metrics.png)
*Figure 4: Real-time platform counter display building trust for both job seekers and recruiters.*

### 5. Seamless Multi-Portal Access & Authentication Gateway
Clear navigation links and call-to-action (CTA) banners directing users to register or log in under their respective roles—whether as a Job Seeker or an Employer.

![Portal Navigation Screenshot](images/landing_portals.png)
*Figure 5: Role-selection gateways helping users navigate to Job Seeker, Employer, or Admin portals.*

---

## 🚀 Getting Started

To view or test the main landing page features locally, ensure your local web server (e.g., XAMPP/Apache) and MySQL database are active, then navigate to the root index URL (`/index.php`) in your browser.

## 🌟 Key Features & Overview

Below is an overview of the core features and UI components available on the main landing page, complete with visual previews and brief explanations of their functionalities.

### 1. Interactive Hero Section & Quick Search
A dynamic header section featuring a central search engine that allows users to quickly find open job roles by entering keywords, selecting job categories, or filtering by locations.


# Employer Portal - Online Job Portal System

Welcome to the **Employer Portal** module of our Online Job Portal system. This platform is designed to streamline the recruitment process for employers, HR managers, and hiring teams, empowering them to find and hire top talent efficiently.

---

## 🌟 Key Features & Overview

Below is an overview of the core features available on the employer side, complete with visual previews and brief explanations of their functionalities.

### 1. Company Profile & Account Management
Employers can create, customize, and manage their corporate identity on the platform to attract prospective job seekers.

![Company Profile Screenshot](images/company_profile.png)
*Figure 1: The Company Profile dashboard allows organizations to update their brand details, company description, industry type, and contact information.*

### 2. Job Vacancy Management (CRUD)
A robust job posting system that lets employers publish new career opportunities and manage active listings seamlessly.

![Job Management Screenshot](images/job_management.png)
*Figure 2: The Job Management interface enables recruiters to post new vacancies, specify salary ranges, set job types (Full-time, Remote, etc.), and edit or close active listings.*

### 3. Applicant Tracking System (ATS)
Centralized candidate management to review applicants, inspect qualifications, and move candidates through the hiring pipeline.

![ATS Dashboard Screenshot](images/ats_dashboard.png)
*Figure 3: The Applicant Tracking System (ATS) view where recruiters can filter applicants, inspect uploaded resumes, and update application statuses (*Pending, Shortlisted, Interview Scheduled, Accepted, Rejected*).*

### 4. Interview Scheduling & Communication
Tools to coordinate interviews and communicate directly with prospective candidates within a structured workflow.

![Interview Scheduling Screenshot](images/interview_scheduling.png)
*Figure 4: The scheduling module allows employers to assign interview dates, times, and meeting links, ensuring smooth communication with shortlisted candidates.*

### 5. Employer Analytics Dashboard
An insightful overview metric board providing a snapshot of recruitment performance and engagement.

![Employer Dashboard Screenshot](images/employer_dashboard.png)
*Figure 5: The main employer dashboard displaying quick stats such as total active job postings, application counts, and overall recruitment metrics.*

---

## 🚀 Getting Started

To view or test the employer portal features locally, ensure your backend server and database migrations are fully set up, then navigate to the `/company/dashboard.php` route in your browser.

# Job Seeker Portal - Online Job Portal System

Welcome to the **Job Seeker Portal** module of our Online Job Portal system. This platform is designed to help job seekers, students, and candidates seamlessly search for career opportunities, manage their profiles, upload CVs, and receive personalized job alerts.

---

## 🌟 Key Features & Overview

Below is an overview of the core features available on the job seeker side, complete with visual previews and brief explanations of their functionalities.

### 1. Seeker Dashboard
A centralized dashboard providing job seekers with a quick overview of their job application metrics, profile summary, and navigation access.

![Seeker Dashboard Screenshot](images/dashboard.png)
*Figure 1: The main job seeker dashboard displays quick stats and navigation links for managing jobs and applications.*

### 2. Browse & Search Jobs
An interactive job listings interface where candidates can explore approved job vacancies, filter by location or job type, and search for specific positions.

![Browse Jobs Screenshot](images/browse_jobs.png)
*Figure 2: Candidates can view available job opportunities with complete details including salary ranges, location, and job requirements.*

### 3. Profile Management
Job seekers can personalize and manage their profile details including First Name, Last Name, Date of Birth, Phone Number, Bio, and Category Preferences (e.g., IT, Marketing, Legal).

![My Profile Screenshot](images/profile.png)
*Figure 3: The Profile Management interface allows candidates to maintain accurate and updated personal and professional information.*

### 4. CV Upload & Management
A dedicated resume management section allowing candidates to upload, view, and replace their CVs in PDF format for job applications.

![My CV Screenshot](images/cv_management.png)
*Figure 4: Interface for uploading and managing professional resumes and CV documents.*

### 5. Smart Job Alerts & Preferences
A customized job alert system where candidates save preferred keywords and locations. Active alerts automatically match and display relevant new job postings under **Matching Job Alerts**, with options to toggle (ON/OFF) or delete alerts.

![Job Alerts Screenshot](images/job_alerts.png)
*Figure 5: Configure job alert keywords and view real-time matched job postings as soon as they are approved.*

### 6. Application Tracking System
A dedicated view for tracking the status of all submitted job applications (such as *Pending, Approved, Shortlisted, or Rejected*).

![Applications Screenshot](images/applications.png)
*Figure 6: Candidate application status tracking board to monitor progress with different employers.*

### 7. Interview Management
A structured module allowing candidates to track scheduled interview dates, times, and venue/meeting details assigned by employers.

![Interviews Screenshot](images/interviews.png)
*Figure 7: Manage and view scheduled interview details and updates from recruiters.*

---

## 🚀 Getting Started

To view or test the job seeker portal features locally, ensure your backend server and database migrations are fully set up, then navigate to the `/seeker/dashboard.php` route in your browser.

# Admin Portal - Online Job Portal System

Welcome to the **Admin Portal** module of our Online Job Portal system. This platform provides system administrators with full control over user management, job post approvals, industry category configurations, and overall system monitoring.

---

## 🌟 Key Features & Overview

Below is an overview of the core features available on the admin side, complete with visual previews and brief explanations of their functionalities.

### 1. Admin Analytics Dashboard
A comprehensive overview board displaying real-time metrics, active user statistics, total applications submitted, and pending approval queues.

![Admin Dashboard Screenshot](images/admin_dashboard.png)
*Figure 1: The main admin dashboard provides central system statistics and overview metrics.*

### 2. Job Post Approval & Management
A moderation system allowing administrators to review job vacancies submitted by employers and approve, reject, or delete listings to ensure quality standards.

![Job Approval Screenshot](images/admin_job_approval.png)
*Figure 2: The job management section allows administrators to inspect details and approve or reject job postings.*

### 3. User Management (Seekers & Employers)
A centralized user control module to inspect registered Job Seekers and Employers, view profile details, verify corporate accounts, or suspend non-compliant users.

![User Management Screenshot](images/admin_user_management.png)
*Figure 3: The user management interface for inspecting, verifying, and managing job seeker and employer accounts.*

### 4. Category & Industry Management
Full CRUD management for job sectors and categories (e.g., IT, Marketing, Legal, Customer Service) to keep job classifications organized across the platform.

![Category Management Screenshot](images/admin_category_management.png)
*Figure 4: Interface for adding, updating, and organizing job categories and industries.*

### 5. System Activity & Report Monitoring
A tracking section to monitor ongoing platform activities including application submissions, job alert triggers, scheduled interviews, and user registrations.

![System Monitoring Screenshot](images/admin_system_monitoring.png)
*Figure 5: System logs and analytics tracking platform interactions and recruiter activity.*

---




