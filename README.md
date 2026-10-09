# Brainware

Brainware is a PHP-based web application designed for managing educational centers, courses, and students. It provides a comprehensive system for student registration, certification, marksheet generation, and result verification.

## Features

* **Admin Dashboard:** Central management of the system.
* **Center Management:** Manage affiliated educational centers (`centers.php`).
* **Student Verification:** Portal for registering and verifying students (`verification.php`, `registration_verification.php`).
* **Course Management:** View and manage course details (`courses.php`).
* **Certificate & Marksheet Generation:** Generate and manage student certificates (`certificate.php`) and marksheets (`marksheet.php`).
* **Advanced Search:** Search for student records and verifications (`advance_search.php`).

## Setup Instructions

1. **Database Setup:** 
   Import the provided database schema file `brain_db.sql` into your MySQL server.
2. **Configuration:**
   Update the database connection details in the `config/` directory to match your environment.
3. **Running the Application:**
   Host the project on a local web server (like XAMPP, WAMP, or MAMP) or any standard web server with PHP and MySQL support.

## Access

* **Main Interface:** `index.php`
* **Login/Admin:** Access the system via `login.php` or the `admin/` and `center/` directories.
