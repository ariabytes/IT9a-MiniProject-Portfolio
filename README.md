# Web-Based Portfolio Management System 🗂️
### IT 9a — Professional Track for IT 3 | Mini Project

A dynamic, database-driven portfolio website built with PHP and MySQL. Features a public-facing portfolio view and a secure admin panel for managing all portfolio content.

---

## 🌐 Pages

| Page | Description |
|---|---|
| `index.php` | Public portfolio view |
| `login.php` | Admin login page |
| `dashboard.php` | Admin dashboard |
| `admin.php` | Edit profile, bio, and social links |
| `skills.php` | Manage skills and tech stack |
| `projects.php` | Manage portfolio projects |
| `certifications.php` | Manage certifications |
| `school_history.php` | Manage education history |
| `logout.php` | Session logout |

---

## 🛠️ Built With

- PHP
- MySQL
- HTML
- CSS
- JavaScript
- Bootstrap 5

---

## ⚙️ Setup & Installation

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**
2. Clone or download this repository into your `htdocs` folder:
   ```
   C:/xampp/htdocs/Mini_Project_Web_Portfolio/
   ```
3. Open **phpMyAdmin** at `localhost/phpmyadmin`
4. Create a new database named `portfolio_db`
5. Import `portfolio_db.sql` into the database
6. Open your browser and go to:
   ```
   localhost/Mini_Project_Web_Portfolio/
   ```

---

## 🚀 How to Use

### Public Portfolio
- Open `localhost/Mini_Project_Web_Portfolio/index.php`
- Browse the portfolio — skills, projects, education, certifications, and contact
- Click the `●` icon on the top right of the navbar to go to the admin login

### Admin Login
- Go to `localhost/Mini_Project_Web_Portfolio/login.php`
- Enter your credentials *(default: username `admin`, password `admin123`)*
- You will be redirected to the dashboard

### Dashboard
- Links to all management pages
- Click **Logout** when done

### Admin Profile (`admin.php`)
- Update your name, username, password, bio, email, GitHub, and LinkedIn URLs
- Upload a profile picture *(stored as LONGBLOB in the database)*
- Leave the password field blank to keep the current password

### Skills (`skills.php`)
- Add skills with a name, category, and optional logo image *(48x48 PNG recommended)*
- Categories: Languages, Framework / Library, Database, Hardware / Networking, Software / Tools, Design, AI Tools
- Edit or delete existing skills

### Projects (`projects.php`)
- Add projects with a title, description, primary link, secondary link, screenshot, date, and tech stack
- Tech stack uses a multi-select dropdown — select multiple skills per project
- Edit or delete existing projects

### Certifications (`certifications.php`)
- Add certifications with a title, issuing organization, issued date, description, and image
- Edit or delete existing certifications

### School History (`school_history.php`)
- Add schools with name, degree/strand, year start, year end, achievements, and logo
- Leave year end blank if currently enrolled
- Achievements are comma-separated (e.g. `Dean's Lister, With Honors`)
- Edit or delete existing entries

---

## ✨ Features

**Public Portfolio (`index.php`)**
- Responsive sticky navbar with smooth scroll anchor links
- Hero section with pixel art profile photo frame
- Skills grouped by category with logos
- Projects in a 3-column card grid with tech stack tags and links
- Education timeline with achievement badges
- Certifications with images
- Contact section with email, GitHub, and LinkedIn links
- Scroll fade-up animations on all sections
- Vintage glassmorphism design with dark navy palette

**Admin Panel**
- Session-based authentication with login/logout
- Full CRUD for all portfolio content
- Multi-select dropdown checkbox for project tech stack
- LONGBLOB image storage for skills and profile picture
- File path image storage for projects, certifications, and school history
- Dismissible success alerts with URL cleanup via `history.replaceState()`
- Auto-creates database and tables on first run

---

## 💻 Concepts Applied

- PHP server-side scripting
- MySQLi database connection
- CRUD operations (Create, Read, Update, Delete)
- Session-based authentication (`session_start`, `$_SESSION`)
- PRG pattern (Post/Redirect/Get)
- File upload with `move_uploaded_file()`
- LONGBLOB image storage with `base64_encode()` display
- `password_hash()` and `password_verify()` for secure passwords
- `real_escape_string()` for input sanitization
- JavaScript IntersectionObserver for scroll animations
- Bootstrap 5 glassmorphism UI with custom CSS

---

## 🗄️ Database

**Database:** `portfolio_db`

| Table | Description |
|---|---|
| `admin` | Admin profile, credentials, social links |
| `skills` | Tech stack with category and logo |
| `projects` | Portfolio projects with links and screenshots |
| `school_history` | Education background with achievements |
| `certifications` | Certificates and awards |

---

## 📁 Folder Structure

```
Mini_Project_Web_Portfolio/
├── certificate_img/
│   └── (certification images)
├── projects_img/
│   └── (project screenshots)
├── school_img/
│   └── (school logos)
├── scripts/
│   └── script.js
├── styles/
│   └── style.css
├── admin.php
├── certifications.php
├── dashboard.php
├── default_img.png
├── index.php
├── login.php
├── logout.php
├── projects.php
├── school_history.php
├── skills.php
└── portfolio_db.sql
```

---

## 👩‍💻 Author

**Arianne Danielle V. Añora**
2nd Year BSIT Student — University of Mindanao
IT 9a — Professional Track for IT 3
