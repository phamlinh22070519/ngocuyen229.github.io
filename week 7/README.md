# Blood Donor Registration (PHP + MySQL)


This small project implements a simple donor registration web page using PHP (PDO) and MySQL.


## Features
- Donor registration form (name, email, phone, blood type, birth date, address, notes)
- Server-side validation
- Prepared statements (prevents SQL injection)
- Simple UI (HTML + CSS)


## Requirements
- PHP 7.4+ with PDO MySQL extension
- MySQL / MariaDB
- Web server (Apache / Nginx)


## Setup
1. Import the database schema: `mysql -u root -p < db.sql` (or use phpMyAdmin).
2. Update DB credentials in `config.php`.
3. Place files into your webroot (e.g. `/var/www/html/blood_donor/`).
4. Open `index.php` in your browser.