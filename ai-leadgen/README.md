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
4. Update credentials in `/ai-leadgen/includes/db.php` or set env vars (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`).
5. Ensure your domain path can serve `/ai-leadgen/index.php`.

### Hostinger credential format (very important)
Hostinger usually uses prefixed names. Example:
- DB Name: `u123456789_ai_leadgen`
- DB User: `u123456789_appuser`
- DB Host: `localhost`
- Port: `3306`

If you put just `ai_leadgen` or `appuser` without prefix, connection will fail.

## 3) Import database in phpMyAdmin

1. Open **phpMyAdmin** from hPanel.
2. Select your exact DB (the prefixed one).
3. Click **Import**.
4. Select `/ai-leadgen/database.sql`.
5. Click **Go**.
6. Verify `users` and `campaigns` tables are created and sample campaigns are inserted.

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

## 5) Troubleshooting database connection on deployment

If you see `Database connection failed...`:

1. Confirm DB name/user are prefixed exactly as in Hostinger.
2. Confirm DB user has **ALL privileges** on that DB.
3. Confirm DB host is exactly what Hostinger shows (`localhost` in most cases).
4. Confirm the SQL was imported into the same DB configured in `db.php`.
5. Try removing custom port overrides and keep `3306` unless Hostinger gave you another port.
6. If you used `127.0.0.1`, switch to `localhost` and test again.
7. Open `/ai-leadgen/health.php` after fixing credentials.
8. If still failing, enable temporary debug output with environment variable:
   - `APP_DEBUG=1`
   - refresh once to view exact PDO reason
   - disable `APP_DEBUG` immediately after troubleshooting

## 6) Core module features included

- Campaign create form with validations and CSRF.
- Tag-style job title input with Enter/comma support.
- Campaign table with search, status filter, pagination (10/page), and actions.
- Campaign detail view with AI rules summary sentence.
- Edit, delete (confirmation), and pause/activate toggle.
- Modern reusable dashboard layout with sidebar and topbar.
