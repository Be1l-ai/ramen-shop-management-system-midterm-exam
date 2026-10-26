# Ramen Shop Management System - Midterm Exam

## Background

This repository is the **midterm exam** continuation of the midterm activity (`ramen-shop-one-to-many-with-pdo`). It expands the basic one-to-many ramen shop system into a complete management system with authentication, role-based access, soft deletes, audit logging and public owner registration.

## Project Structure

```
ramen-shop-management-system-midterm-exam/
├── public/          Web document root - everything the browser can request
│   ├── index.php    Dashboard (shop selector, shop actions, menu items)
│   ├── login.php    Login + link to public owner registration
│   ├── logout.php   Ends the session
│   ├── register.php Public shop owner registration (requires admin approval)
│   ├── handleForms.php  Single POST endpoint for every form action
│   ├── partials/    Shared navigation (nav.php)
│   ├── assets/      Stylesheet (style.css)
│   ├── shops/       create, edit, delete, trash
│   ├── menu/        create, edit, delete, trash
│   ├── users/       index, create, edit, delete
│   └── audit/       Audit log browser
├── src/             Application code, never served directly
│   ├── bootstrap.php  Loads config, models and auth; defines APP_ROOT/BASE_URL
│   ├── auth.php       Session handling, login, role checks
│   └── models.php     All SQL (users, shops, menu items, audit logs)
├── config/          config.php (local, git-ignored), config.example.php, database.php
└── database/        schema.sql, seed.sql
```

## Requirements

- PHP 8.x with `pdo_mysql`
- MariaDB / MySQL

## Setup

1. Import the schema and seed data:
   ```bash
   mysql -u root -p < database/schema.sql
   mysql -u root -p < database/seed.sql
   ```
2. Copy the configuration template and fill in your credentials:
   ```bash
   cp config/config.example.php config/config.php
   ```
3. Run the development server with `public/` as the document root:
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
4. Open <http://127.0.0.1:8000/>

## Demo accounts

| Role       | Username | Password   |
|------------|----------|------------|
| Admin      | admin    | admin123   |
| Shop Owner | owner    | owner123   |
| Staff      | staff    | staff123   |

## Expanded Features (Exam Requirements)

- Session authentication with hashed passwords and inactive-account feedback
- Three roles: `admin`, `shop_owner`, `staff` with server-side authorization
- Public owner registration that always creates an inactive account without a shop
- Admin approval workflow through the Users page (assign a shop and activate)
- Shop selection with per-shop management actions and view-only states
- Soft deletes for shops, menu items and users, with admin-only trash and restore
- Audit log for every authenticated create, update, delete and restore action
- Shared navigation that only exposes destinations the current role may use

## Database Extensions

- `users` with `role`, `shop_id`, `is_active`, `deleted_at` and unique credentials
- `ramen_shops` and `menu_items` with `created_by`, `updated_by` and `deleted_at`
- `audit_logs` storing action, table, record id and JSON old/new values

## Relationship Diagram

```
users ──created_by/updated_by──> ramen_shops ──shop_id──> menu_items
  │                                   ▲
  └────────shop_id (owner assignment)─┘
```

`users.shop_id` links an owner to a single shop, `ramen_shops` is the parent of `menu_items`, and `audit_logs.user_id` records who performed each change.

## Tech Stack

Plain PHP (no framework), PDO with prepared statements, MariaDB, server-rendered HTML and a single stylesheet.
