# SARPAMS Setup Guide

## Prerequisites
- XAMPP / WAMP / Laragon (PHP 8.x + MySQL 8.x + Apache)
- Any modern browser

## Setup Steps

### 1. Place project in web root
Copy the entire `PETS MANAGEMET SYSTEM` folder to your Apache web root and rename it to `sarpams`:
```
C:\xampp\htdocs\sarpams\      (XAMPP)
C:\wamp64\www\sarpams\        (WAMP)
```

### 2. Import the database
Open phpMyAdmin (http://localhost/phpmyadmin), then:
1. Click **New** → create database named `sarpams`
2. Select the `sarpams` database
3. Click **Import** → choose `database.sql` → **Go**

Or via command line:
```bash
mysql -u root -p < sarpams/database.sql
```

### 3. Configure database credentials
Edit `config/db.php` if your MySQL credentials differ:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // ← change if you have a password
define('DB_NAME', 'sarpams');
```

### 4. Open in browser
Navigate to: **http://localhost/sarpams/**

## Project Structure
```
sarpams/
├── index.php                  Dashboard
├── database.sql               MySQL schema + sample data
├── config/db.php              Database connection & helpers
├── assets/css/style.css       Global stylesheet
├── includes/
│   ├── header.php             Navbar + sidebar
│   └── footer.php             Footer
├── animals/                   Animal CRUD (index/create/edit/view/delete)
├── rescue_requests/           Rescue Request CRUD
├── rescuers/                  Rescuer CRUD
├── shelters/                  Shelter CRUD
├── cages/                     Cage CRUD
├── veterinarians/             Veterinarian CRUD
├── medical_records/           Medical Record CRUD
├── foster_families/           Foster Family CRUD
├── adoption_applicants/       Adoption Applicant CRUD
└── adoptions/                 Adoption CRUD
```

## Modules & Features
| Module | CRUD | Special Features |
|--------|------|-----------------|
| Animals | ✅ | Search, health badge, cage assignment |
| Rescue Requests | ✅ | Status filter, rescuer dispatch |
| Rescuers | ✅ | Availability tracking |
| Shelters | ✅ | Cage count & occupancy |
| Cages | ✅ | Occupied/free status |
| Veterinarians | ✅ | License tracking |
| Medical Records | ✅ | Per-animal history |
| Foster Families | ✅ | Approval tracking |
| Adoption Applicants | ✅ | Background check fields |
| Adoptions | ✅ | Pipeline (Pending→Approved→Completed) |

## Database Triggers
- `trg_adoption_complete` — marks animal as "Adopted" when adoption is completed
- `trg_prevent_double_adopt` — prevents duplicate adoptions for the same animal
- `trg_cage_occupy_on_assign` — auto-updates cage occupancy when animal is assigned/moved
