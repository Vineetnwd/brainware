# Brainware Educational Management System

Brainware is a comprehensive, PHP-based web application designed to manage an educational franchise network. It provides a robust, multi-tier system for central administrators, affiliated study centers, and public verification processes.

## 🚀 Key Features

### 1. Public Portal (Frontend)
The public-facing website allows users to view information and verify credentials:
* **Student Verification (`verification.php`):** Public portal for verifying the authenticity of student registrations, certificates, and marksheets.
* **Registration Verification (`registration_verification.php`):** Specifically for verifying a student's enrollment status.
* **Courses (`courses.php`):** Browse available educational courses and their details.
* **Centers (`centers.php`):** View the list of affiliated study centers.
* **Gallery & About (`gallery.php`, `about.php`):** Information about the institution.
* **Contact & Enquiries (`contact.php`):** Submit inquiries to the administration.

### 2. Administrator Panel (`/admin`)
A centralized dashboard for the main organization to manage the entire ecosystem:
* **Center Management (`center-add.php`, `centers.php`):** Add, edit, and manage affiliated educational centers.
* **Course & Module Management (`courses.php`, `modules.php`):** Define courses, syllabi, and modules.
* **Student Administration (`students.php`, `student-add.php`):** Global view and management of all registered students across all centers.
* **Result & Examination Management (`results.php`, `result-add.php`):** Upload and manage student exam results.
* **Certificate & Marksheet Generation (`certificate-print.php`, `marksheet-print.php`):** Generate, print, and manage official academic documents.
* **User & Settings Management (`users.php`, `settings.php`):** Manage administrative users and global system settings.

### 3. Center Panel (`/center`)
A dedicated portal for individual affiliated study centers:
* **Student Management (`student-add.php`, `students.php`):** Centers can register new students, edit details, and view their specific enrollments.
* **Results View (`results.php`):** View the examination results for students belonging to their center.
* **Profile Management (`profile.php`):** Centers can update their contact details and profile information.

## 📁 Directory Structure

```text
brainware/
├── admin/          # Administrator dashboard and management scripts
├── center/         # Portal for affiliated study centers
├── config/         # Database configuration (db.php) and auth scripts
├── includes/       # Reusable components (headers, footers, sidebars)
├── assets/         # Static files (CSS, JavaScript, Images, Fonts)
├── uploads/        # Directory for user-uploaded files, photos, and documents
├── brain_db.sql    # The complete database schema for the application
└── ...             # Public-facing PHP scripts (index, about, contact, etc.)
```

## 🛠️ Setup & Installation

Follow these steps to get the project running on your local machine or server.

### Prerequisites
* A web server with PHP support (e.g., Apache, Nginx)
* MySQL / MariaDB Database Server
* (Optional but recommended) A local development environment like XAMPP, WAMP, or MAMP.

### Installation Steps

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/Vineetnwd/brainware.git
   cd brainware
   ```

2. **Database Setup:**
   * Open your database management tool (like phpMyAdmin or MySQL Workbench).
   * Create a new empty database (e.g., `brainware_db`).
   * Import the provided `brain_db.sql` file into your newly created database.

3. **Configuration:**
   * Navigate to the `config/` directory.
   * Open `db.php`.
   * Update the database connection variables with your database credentials (host, username, password, and database name).

4. **Permissions:**
   * Ensure that the `uploads/` directory (if it exists or needs to be created) has the correct read/write permissions for the web server to store images and documents.

5. **Run the Application:**
   * Access the application via your web browser: `http://localhost/brainware/` (or your configured virtual host).

## 🔐 Access Information

* **Public Website:** Access the root URL (`index.php`).
* **Admin Login:** Navigate to `/admin` or `/login.php` using administrative credentials.
* **Center Login:** Navigate to `/center` or `/login.php` using center credentials.
