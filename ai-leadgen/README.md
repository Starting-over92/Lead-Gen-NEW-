# AI Lead Gen - Campaign Setup Module

## 1) Folder structure

```text
/ai-leadgen/
  /assets/
    /css/style.css
    /js/job_titles.js
  /includes/
    db.php
    auth.php
    layout.php
    csrf.php
    functions.php
  /campaigns/
    _form.php
    index.php
    create.php
    edit.php
    view.php
    delete.php
    toggle_status.php
    dashboard.php
  database.sql
  index.php
```

## 2) Hostinger shared hosting setup

1. Open **hPanel → File Manager**.
2. Upload the `ai-leadgen` folder into `public_html`.
3. Create a new MySQL database and user from **Databases → MySQL Databases**.
4. Update credentials in `/ai-leadgen/includes/db.php`:
   - `$host`
   - `$dbName`
   - `$username`
   - `$password`
5. Ensure your domain path can serve `/ai-leadgen/index.php`.

## 3) Import database in phpMyAdmin

1. Open **phpMyAdmin** from hPanel.
2. Click **Import**.
3. Select `/ai-leadgen/database.sql`.
4. Click **Go**.
5. Verify `users` and `campaigns` tables are created and sample campaigns are inserted.

## 4) Local test setup

1. Install PHP 8.1+ and MariaDB/MySQL.
2. Create DB and tables:
   ```bash
   mysql -u root -p < ai-leadgen/database.sql
   ```
3. Update `/includes/db.php` with local DB credentials.
4. Serve with PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
5. Open:
   - `http://localhost:8000/ai-leadgen/`
   - `http://localhost:8000/ai-leadgen/campaigns/index.php`

## 5) Core module features included

- Campaign create form with validations and CSRF.
- Tag-style job title input with Enter/comma support.
- Campaign table with search, status filter, pagination (10/page), and actions.
- Campaign detail view with AI rules summary sentence.
- Edit, delete (confirmation), and pause/activate toggle.
- Modern reusable dashboard layout with sidebar and topbar.
