# birth3-final

A lightweight PHP application for managing birth and death records. It provides an admin interface for CRUD operations on records, districts, tehsils, union councils, and user accounts, as well as public pages for viewing and editing individual records.

---

## Overview

`birth3-final` is a simple, self‑contained system designed for civil registration offices or NGOs that need to store and retrieve birth and death information. The project includes:

* Secure admin panel with role‑based user management  
* Separate pages for birth and death records (view, edit, delete)  
* Management of geographic hierarchies (districts, tehsils, union councils)  
* PDF generation support via the **FPDF** library  
* Minimal front‑end styling (CSS) for a clean UI  

All data is stored in a MySQL database defined in `Database/birthdatabase.sql`.

---

## Features

| Feature | Description |
|---------|-------------|
| **Admin Dashboard** | Central hub (`admin/home.php`) with navigation to all management sections. |
| **User Management** | Add, edit, and delete users (`admin/adduser.php`, `admin/edit_user.php`). |
| **Record Management** | CRUD for birth (`birth_records.php`, `edit_birth_record.php`) and death (`death_records.php`, `edit_death_record.php`) records. |
| **Geographic Hierarchy** | Manage districts, tehsils, and union councils (`admin/managedistricts.php`, `admin/manage_tehsils.php`, `admin/manage_union_councils.php`). |
| **Fee Management** | Define and edit fees for registrations (`admin/manage_fees.php`, `admin/editfee.php`). |
| **PDF Export** | Generate printable PDFs using the bundled **FPDF** library. |
| **Responsive CSS** | Basic styling located in `admin/assets/css/style.css` and `assets/css/style.css`. |
| **Modular Includes** | Header, footer, navigation, and DB connection are abstracted (`admin/include/*.inc`). |
| **Logout** | Secure session termination (`admin/logout.php`). |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL (schema in `Database/birthdatabase.sql`) |
| **PDF Generation** | FPDF (included in `fpdf/`) |
| **Styling** | CSS (no external frameworks) |
| **Server** | Any web server capable of running PHP (Apache, Nginx, etc.) |

---

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/birth3-final.git
   cd birth3-final
   ```

2. **Create the database**

   ```sql
   -- Using MySQL client or phpMyAdmin
   SOURCE Database/birthdatabase.sql;
   ```

3. **Configure database connection**

   Edit `admin/include/db_connect.inc` and replace the placeholder values with your own credentials:

   ```php
   $host = 'YOUR_DB_HOST';
   $user = 'YOUR_DB_USER';
   $pass = 'YOUR_DB_PASSWORD';
   $dbname = 'YOUR_DB_NAME';
   ```

4. **Set up the web server**

   - Place the project folder inside your web root (e.g., `public_html/birth3-final`).
   - Ensure the `admin/` directory is protected (e.g., via `.htaccess` or server config) if you want to restrict direct access.

5. **Adjust file permissions**

   ```bash
   chmod -R 755 .
   ```

6. **Optional: Composer dependencies (FPDF)**

   The FPDF library is already bundled, but if you prefer to manage it via Composer:

   ```bash
   cd fpdf
   composer install
   ```

---

## Usage

### Admin Panel

1. Navigate to `admin/home.php` (e.g., `http://yourdomain.com/birth3-final/admin/home.php