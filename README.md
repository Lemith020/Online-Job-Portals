# 📁 Folder Structure

job-portal/
│
├── config/
├── includes/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── auth/
├── admin/
│
├── company/
│   ├── profile/
│   ├── jobs/
│   ├── applications/
│   ├── interviews/
│   └── subscriptions/
│
├── seeker/
│   ├── profile/
│   ├── cv/
│   ├── skills/
│   ├── jobs/
│   ├── applications/
│   ├── interviews/
│   └── alerts/
│
├── reviews/
│
├── uploads/
│   └── cvs/
│
├── index.php
└── README.md



# 🗂️ Folder → Files

## ⚙️ `config/`


config/
└── database.php

database.php → MySQL database connection.



## 🔧 `includes/`


includes/
├── header.php
├── footer.php
├── navbar.php
├── auth.php
└── functions.php


| File            | Use                    |
| --------------- | ---------------------- |
| `header.php`    | Common page header     |
| `footer.php`    | Common footer          |
| `navbar.php`    | Navigation bar         |
| `auth.php`      | Login/session checking |
| `functions.php` | Common PHP functions   |

---

## 🎨 `assets/`


assets/
├── css/
├── js/
└── images/


| Folder    | Use                  |
| --------- | -------------------- |
| `css/`    | Website styling      |
| `js/`     | JavaScript           |
| `images/` | Images, logos, icons |

---

## 🔐 `auth/`


auth/
├── login.php
├── register.php
├── logout.php
└── forgot-password.php


| File                  | Use               |
| --------------------- | ----------------- |
| `login.php`           | User login        |
| `register.php`        | User registration |
| `logout.php`          | Logout            |
| `forgot-password.php` | Password recovery |

---

# 🏢 `company/`

### `profile/`


profile/
├── view.php
└── edit.php


`view.php` → View profile
`edit.php` → Update profile

### `jobs/`


jobs/
├── index.php
├── create.php
├── view.php
├── edit.php
└── delete.php


`index.php` → View jobs
`create.php` → Add job
`view.php` → Job details
`edit.php` → Edit job
`delete.php` → Delete job

### `applications/`


applications/
├── index.php
├── view.php
└── status.php


`index.php` → View applications
`view.php` → Applicant details
`status.php` → Change application status

### `interviews/`


interviews/
├── index.php
├── schedule.php
├── assign.php
└── status.php


`index.php` → View interviews
`schedule.php` → Schedule interview
`assign.php` → Assign interviewer
`status.php` → Update status

### `subscriptions/`


subscriptions/
├── plans.php
├── subscribe.php
└── current.php


`plans.php` → View plans
`subscribe.php` → Subscribe
`current.php` → Current subscription

---

# 👤 `seeker/`

### `profile/`


profile/
├── view.php
└── edit.php


View / Edit profile.

### `cv/`


cv/
├── upload.php
├── view.php
└── delete.php


Upload / View / Delete CV.

### `skills/`


skills/
├── index.php
├── add.php
└── delete.php


View / Add / Delete skills.

### `jobs/`


jobs/
├── index.php
├── view.php
└── apply.php


Search jobs / View job / Apply.

### `applications/`


applications/
├── index.php
└── view.php


View submitted applications / Application details.

### `interviews/`


interviews/
└── index.php


View scheduled interviews.

### `alerts/`


alerts/
├── index.php
└── settings.php


View job alerts / Manage alert preferences.

---

# 👨‍💼 `admin/`


admin/
├── dashboard.php
├── users.php
├── companies.php
├── job-seekers.php
├── categories.php
├── subscriptions.php
└── reviews.php


| File                | Use                  |
| ------------------- | -------------------- |
| `dashboard.php`     | Admin dashboard      |
| `users.php`         | Manage users         |
| `companies.php`     | Manage companies     |
| `job-seekers.php`   | Manage job seekers   |
| `categories.php`    | Manage categories    |
| `subscriptions.php` | Manage subscriptions |
| `reviews.php`       | Manage reviews       |

---

# ⭐ `reviews/`


reviews/
├── create.php
├── index.php
└── delete.php


`create.php` → Add review/rating
`index.php` → View reviews
`delete.php` → Delete review

---

# 📤 `uploads/`


uploads/
└── cvs/


`cvs/` → Uploaded CV files.

---

# 🏠 `index.php`

Main/Home page.

---







**Shared folders:** `config/`, `includes/`, `assets/`

**Feature එක develop කරන වෙලාවට අදාළ files create කරන්න.**
