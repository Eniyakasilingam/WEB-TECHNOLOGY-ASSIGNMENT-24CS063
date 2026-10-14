CAMPUSVAULT — STUDENT RECORD MANAGEMENT
=======================================

FEATURES
- Dark professional glassmorphism dashboard
- Create, read, update and delete student profiles
- Search students by name, roll number, course or department
- Upload multiple profile images/documents (JPG, PNG, WEBP, PDF)
- Stores file metadata in MySQL and files in the uploads/ folder
- MIME validation using PHP Fileinfo, 5 MB per file limit
- Random stored filenames prevent overwriting existing files
- Prepared SQL statements and CSRF tokens on POST forms
- Student details page lists files and opens them through download.php

REQUIREMENTS
- XAMPP (Apache, PHP 8+, MySQL)
- PHP extensions: pdo_mysql, fileinfo
- Internet connection only for the optional Google Fonts import; system fonts are used as fallback.

SETUP (WINDOWS / XAMPP)
1. Extract the college_student_admin folder into:
   C:\xampp\htdocs\college_student_admin
2. Open XAMPP Control Panel and start Apache and MySQL.
3. Open http://localhost/phpmyadmin
4. Click Import, select database.sql, then click Import/Go.
   Alternatively, paste database.sql into the SQL tab and run it.
5. Open config.php and set $username / $password if your MySQL credentials differ.
6. Ensure this folder exists and PHP can write to it:
   C:\xampp\htdocs\college_student_admin\uploads
   The app tries to create it automatically when the first file is uploaded.
7. Open:
   http://localhost/college_student_admin/

IMPORTANT
- Do not deploy this demo as-is to a public production server. Add authentication/authorization,
  HTTPS, backups, audit logging and server-level access controls before production use.
- Do not allow uploads/ to execute PHP. The application only accepts detected JPEG, PNG, WEBP
  and PDF MIME types and stores files with generated names.
- If uploads fail for files near 5 MB, check php.ini upload_max_filesize and post_max_size.
- This project does not include a default admin password/login because authentication was not
  part of the supplied assignment brief.
