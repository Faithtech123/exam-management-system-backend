# ExamDesk

A PHP, MongoDB, and vanilla JavaScript exam management system.

## Requirements

- PHP 8.1+
- Composer
- MongoDB running locally or a MongoDB Atlas URI

## Configure

Edit `.env`:

```env
MONGODB_URI=mongodb://127.0.0.1:27017
DB_NAME=exam_management
```

Install dependencies if `vendor/` is missing:

```powershell
composer install
```

Create the default admin account once:

```powershell
php .\backend\create_admin.php
```

Default admin login:

```text
Email: admin@example.com
Password: Admin123!
```

## Run

From this directory:

```powershell
php -S 127.0.0.1:8088 -t .
```

Open `http://127.0.0.1:8088/frontend/`.

## Workflows

- Students register, sign in, take published exams, and view their results.
- Admins sign in, create exams, add questions, publish exams, and view all submitted results.
- Results are calculated on the server from the stored correct answers.

The public registration form always creates student accounts. Use `create_admin.php` for the first administrator.
