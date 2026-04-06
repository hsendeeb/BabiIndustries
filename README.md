# BabiIndustries

BabiIndustries is a Laravel application for managing categories, industries, and services, with a Filament admin panel and a JSON API.

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- A database supported by Laravel, such as SQLite or MySQL

## Local Setup

1. Clone the repository:

```bash
git clone https://github.com/hsendeeb/BabiIndustries.git
cd babiindustries
```

2. Install PHP dependencies:

```bash
composer install
```

3. Create your environment file:

```bash
cp .env.example .env
```

If you are on Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

4. Generate the application key:

```bash
php artisan key:generate
```

5. Configure your database in `.env`.

Example for SQLite:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

If you use SQLite, create the file first:

```bash
touch database/database.sqlite
```

On Windows PowerShell:

```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

You can also configure MySQL or another Laravel-supported database instead.

6. Run the migrations:

```bash
php artisan migrate
```

7. Install frontend dependencies:

```bash
npm install
```

8. Start the application:

Run the backend:

```bash
php artisan serve
```

In another terminal, run Vite:

```bash
npm run dev
```

## Admin User Setup

The Filament admin panel is available at:

```text
/admin
```

Only users with `role = admin` can access it.

You can create an admin user with Tinker:

```bash
php artisan tinker
```

Then run:

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => 'password123',
    'role' => 'admin',
]);
```

Notes:

- The `User` model hashes passwords automatically.
- If you already created a normal user, you can promote it to admin:

```php
$user = \App\Models\User::where('email', 'user@example.com')->first();
$user->update(['role' => 'admin']);
```

## API

The API is served under:

```text
/api/v1
```

Available resources include:

- categories
- industries
- services

Authentication for protected endpoints uses Laravel Sanctum.

Public examples:

```text
GET /api/v1/categories
GET /api/v1/industries
GET /api/v1/services
```

Authentication endpoints:

```text
POST /api/v1/register
POST /api/v1/login
POST /api/v1/logout
```

## Password Reset Frontend URL

If your frontend handles password reset pages separately, set this in `.env`:

```env
FRONTEND_RESET_PASSWORD_URL=http://localhost:3000/reset-password
```

## Running Tests

Run the test suite with:

```bash
php artisan test
```

## Helpful Commands

Format PHP code:

```bash
./vendor/bin/pint
```

Run the app using the Composer convenience script:

```bash
composer run dev
```
