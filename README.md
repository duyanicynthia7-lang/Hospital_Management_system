# 🏥 Hospital Management System (Laravel 12)

A web-based Hospital Management System built with **Laravel 12**, **MySQL** and **Bootstrap 5** (dark theme).\
It manages patients, doctors, departments, wards, appointments, admissions, medical records and system users,\
with three user roles: **Admin**, **Doctor** and **Receptionist**.

---

## 📋 Table of Contents

1. [Features](#-features)

2. [Requirements](#-requirements)

3. [Setup on Windows (recommended for this project)](#-setup-on-windows)

4. [Setup on Linux / macOS](#-setup-on-linux--macos)

5. [Verify MySQL is working](#-verify-mysql-is-working)

6. [Demo login accounts](#-demo-login-accounts)

7. [Role permissions](#-role-permissions)

8. [Project structure](#-project-structure)

9. [Useful commands](#-useful-commands)

10. [Troubleshooting](#-troubleshooting)

---

## ✨ Features

| Module | What it does |
| --- | --- |
| **Authentication** | Secure login/logout, bcrypt-hashed passwords, CSRF protection, login rate limiting |
| **Dashboard** | Role-specific statistics cards, today's appointments, ward occupancy |
| **Patients** | Register, search, edit, view and delete patients (auto patient code e.g. `PAT-00001`) |
| **Doctors** | Doctor profiles linked to user accounts and departments, specialization, schedule |
| **Departments** | Hospital departments with a head doctor |
| **Wards** | Ward types, bed capacity, live occupancy bars (green / amber / red) |
| **Appointments** | Book, reschedule, cancel, complete; prevents double-booking a doctor |
| **Admissions** | Admit patients to wards (checks free beds), discharge, length-of-stay |
| **Medical Records** | Complete patient history timeline (appointments + admissions), printable |
| **User Management** | Admin creates/edits users and assigns roles |

Security: CSRF tokens on every form, validation through Form Request classes, role-based middleware,\
Eloquent ORM only (no raw SQL), confirmation pop-ups before deleting.

---

## 🧰 Requirements

| Software | Version | Notes |
| --- | --- | --- |
| PHP | **8.2 or newer** | Extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `curl` |
| Composer | 2.x | PHP package manager – [https://getcomposer.org](https://getcomposer.org) |
| MySQL / MariaDB | MySQL 5.7+/8.x or MariaDB 10.3+ | Comes with XAMPP / Laragon |
| Node.js + npm | 18+ (optional) | Only needed for `npm run build`. The app's styling loads from CDN + `public/css`, so it works without it |

---

## 🪟 Setup on Windows

### Step 1 – Install the tools

Choose **one** of these bundles (both include PHP + MySQL):

* **Laragon** (easiest) – [https://laragon.org/download](https://laragon.org/download) → install "Laragon Full". It includes PHP 8.2+, MySQL, Composer and Node.js.

* **XAMPP** – [https://www.apachefriends.org](https://www.apachefriends.org) → download the version with **PHP 8.2 or newer**.\
  Then install **Composer** separately: [https://getcomposer.org/Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe)\
  (during setup, point it to `C:\xampp\php\php.exe`). Install **Node.js LTS** from [https://nodejs.org](https://nodejs.org) (optional).

Open **PowerShell** or **Command Prompt** and check everything is installed:

```powershell
php -v
composer -V
node -v
```

Each command should print a version number. `php -v` must show **8.2** or higher.

### Step 2 – Start MySQL

* **Laragon:** click **Start All**.

* **XAMPP:** open the XAMPP Control Panel and click **Start** next to **Apache** and **MySQL**.

### Step 3 – Put the project in place

Download/extract the `hospital_management_system` folder, for example to:

* Laragon: `C:\laragon\www\hospital_management_system`

* XAMPP: `C:\xampp\htdocs\hospital_management_system`

Then open PowerShell **inside that folder**:

```powershell
cd C:\xampp\htdocs\hospital_management_system
```

### Step 4 – Install PHP packages

```powershell
composer install
```

This creates the `vendor` folder (it can take a few minutes).

### Step 5 – Create the environment file and app key

```powershell
copy .env.example .env
php artisan key:generate
```

### Step 6 – Create the database

**Option A – phpMyAdmin (point and click):**

1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin) (XAMPP) or click **Database** in Laragon.

2. Click **New** → Database name: `hospital_db` → Collation: `utf8mb4_unicode_ci` → **Create**.

**Option B – command line:**

```powershell
# XAMPP:   C:\xampp\mysql\bin\mysql.exe -u root -p
# Laragon: open Laragon → Terminal, then type: mysql -u root -p
mysql -u root -p
```

Press Enter when asked for a password (default is empty), then run:

```sql
CREATE DATABASE hospital_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 7 – Check the database settings in `.env`

Open `.env` in Notepad / VS Code and make sure these match your MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_db
DB_USERNAME=root
DB_PASSWORD=
```

(XAMPP and Laragon use user `root` with **no password** by default.)

### Step 8 – Create the tables and demo data

```powershell
php artisan migrate --seed
```

You should see each migration marked **DONE**, followed by the seeder.

### Step 9 – (Optional) Build front-end assets

```powershell
npm install
npm run build
```

### Step 10 – Run the application

```powershell
php artisan serve
```

Open **[http://localhost:8000](http://localhost:8000)** in your browser and log in with\
**[admin@hospital.com](mailto:admin@hospital.com) / password123**.

Keep the PowerShell window open while using the app. Press `Ctrl + C` to stop the server.

---

## 🐧 Setup on Linux / macOS

```bash
# 1. Install PHP 8.2+, Composer, MySQL (and optionally Node.js) with your package manager
# 2. Inside the project folder:
composer install
cp .env.example .env
php artisan key:generate

# 3. Create the database
mysql -u root -p -e "CREATE DATABASE hospital_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Edit .env DB_* values if your MySQL user/password differ, then:
php artisan migrate --seed

# 5. Optional assets
npm install && npm run build

# 6. Start
php artisan serve        # → http://localhost:8000
```

---

## ✅ Verify MySQL is working

Run these checks **before** and **after** migrating.

**1. MySQL server is running and the database exists**

```bash
mysql -u root -p -e "SHOW DATABASES;"
```

`hospital_db` must appear in the list. (Windows XAMPP: use `C:\xampp\mysql\bin\mysql.exe` instead of `mysql`.)

**2. Laravel can connect to the database**

```bash
php artisan db:show
```

This prints the connection, database name and tables. An error here means the `.env` DB settings are wrong.

**3. Tables were created (after `php artisan migrate`)**

```bash
php artisan migrate:status
```

or in MySQL:

```sql
USE hospital_db;
SHOW TABLES;
```

Expected tables: `users`, `departments`, `doctors`, `patients`, `wards`, `appointments`, `admissions`,\
`sessions`, `cache`, `jobs`, `migrations`, `password_reset_tokens`, ...

**4. Demo data was seeded**

```sql
SELECT name, email, role FROM users;
SELECT COUNT(*) FROM patients;
```

You should see 5 users (admin, doctors, receptionist) and 5 patients.

**phpMyAdmin alternative:** open [http://localhost/phpmyadmin](http://localhost/phpmyadmin) → click `hospital_db` in the left panel → you should see the tables listed above, and **Browse** on `users` shows the demo accounts.

---

## 🔐 Demo login accounts

All demo accounts use the password **`password123`**.

| Role | Email |
| --- | --- |
| **Admin** | **[admin@hospital.com](mailto:admin@hospital.com)** |
| Doctor | [doctor@hospital.com](mailto:doctor@hospital.com) |
| Doctor | [amina.otieno@hospital.com](mailto:amina.otieno@hospital.com) |
| Doctor | [peter.kamau@hospital.com](mailto:peter.kamau@hospital.com) |
| Receptionist | [reception@hospital.com](mailto:reception@hospital.com) |

> ⚠️ Change these passwords before using the system with real data.

---

## 👥 Role permissions

| Feature | Admin | Doctor | Receptionist |
| --- | --- | --- | --- |
| Dashboard | ✅ | ✅ | ✅ |
| View / add / edit patients | ✅ | ✅ | ✅ |
| Delete patients | ✅ | ❌ | ❌ |
| View doctors | ✅ | ✅ | ✅ |
| Add / edit / delete doctors | ✅ | ❌ | ❌ |
| Appointments | ✅ | ✅ | ✅ |
| Admissions & discharge | ✅ | ✅ | ❌ |
| Medical records | ✅ | ✅ | ❌ |
| Departments & wards | ✅ | ❌ | ❌ |
| User management | ✅ | ❌ | ❌ |

A user who opens a page their role cannot access sees a **403 – Access Denied** page.

---

## 📁 Project structure

```
hospital_management_system/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Auth, Dashboard, Patient, Doctor, Department, Ward,
│   │   │                       # Appointment, Admission, MedicalRecord, User
│   │   ├── Middleware/RoleMiddleware.php   # role:admin / role:admin,doctor
│   │   └── Requests/           # One Form Request (validation) class per module
│   ├── Models/                 # User, Patient, Doctor, Department, Ward, Appointment, Admission
│   └── Providers/AppServiceProvider.php
├── bootstrap/app.php           # Registers the "role" middleware alias
├── config/                     # app, auth, database
├── database/
│   ├── migrations/             # All table definitions
│   ├── seeders/DatabaseSeeder.php  # Demo users & sample data
│   └── factories/UserFactory.php
├── public/
│   ├── css/hms.css             # Dark theme
│   ├── js/hms.js               # Sidebar, confirm modal, small UI helpers
│   └── index.php
├── resources/views/
│   ├── layouts/app.blade.php   # Sidebar + top bar layout
│   ├── partials/               # Flash messages, confirmation modal
│   ├── auth/login.blade.php
│   ├── dashboard/  patients/  doctors/  departments/  wards/
│   ├── appointments/  admissions/  medical_records/  users/
│   └── errors/403.blade.php
├── routes/web.php              # All routes grouped by role
├── tests/Feature/AccessTest.php
├── .env.example
├── composer.json
└── package.json
```

---

## 🛠 Useful commands

| Command | Purpose |
| --- | --- |
| `php artisan serve` | Start the app at [http://localhost:8000](http://localhost:8000) |
| `php artisan serve --port=8080` | Use a different port |
| `php artisan migrate --seed` | Create tables + demo data |
| `php artisan migrate:fresh --seed` | ⚠️ Drop **all** tables and rebuild with fresh demo data |
| `php artisan db:seed` | Re-run the seeder only (users are updated, sample data only added if no patients exist) |
| `php artisan route:list` | Show all routes and their middleware |
| `php artisan optimize:clear` | Clear cached config/routes/views |
| `php artisan test` | Run the automated tests (needs the PHP `pdo_sqlite` extension) |

---

## 🧯 Troubleshooting

| Problem | Fix |
| --- | --- |
| `could not find driver` | Enable MySQL in PHP: open `php.ini` (XAMPP: `C:\xampp\php\php.ini`), remove the `;` in front of `extension=pdo_mysql`, save, restart. |
| `SQLSTATE[HY000] [1045] Access denied for user 'root'` | The `DB_USERNAME` / `DB_PASSWORD` in `.env` do not match MySQL. Fix them, then run `php artisan config:clear`. |
| `SQLSTATE[HY000] [1049] Unknown database 'hospital_db'` | The database was not created – repeat Step 6. |
| `SQLSTATE[HY000] [2002] No connection could be made` | MySQL is not running – start it in XAMPP / Laragon. |
| `No application encryption key has been specified` | Run `php artisan key:generate`. |
| `Failed to listen on 127.0.0.1:8000` | Port in use – run `php artisan serve --port=8080` and open [http://localhost:8080](http://localhost:8080). |
| `Your PHP version does not satisfy...` during `composer install` | Install PHP 8.2 or newer (newer XAMPP/Laragon). |
| `composer` / `php` is not recognized | Restart PowerShell after installing, or add `C:\xampp\php` to the Windows PATH. |
| `Specified key was too long` | Already handled in `AppServiceProvider` (`defaultStringLength(191)`); run `php artisan migrate:fresh --seed`. |
| Page looks unstyled | You need internet access for the Bootstrap CDN, and `public/css/hms.css` must exist. |
| Changes to `.env` not applied | `php artisan config:clear` |

---

## 📄 License

Built as an academic (final-year) project. Free to use and modify under the MIT license.