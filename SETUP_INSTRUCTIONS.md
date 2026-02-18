# Laravel Admin Dashboard Setup Instructions

## Changes Made

Your HTML files have been converted to work with Laravel. Here's what was done:

### 1. **Converted HTML to Blade Template**
- `admin-dashboard.html` → `admin-dashboard.blade.php`
- Added Laravel Blade syntax for dynamic content
- Integrated CSRF protection
- Added form validation error handling
- Updated asset paths to use Laravel's `asset()` helper

### 2. **Created AdminController**
- Location: `app/Http/Controllers/AdminController.php`
- Methods:
  - `index()` - Displays the admin dashboard with statistics
  - `storeTeacher()` - Handles teacher registration

### 3. **Updated User Model**
- Added fillable fields: `first_name`, `last_name`, `phone`, `subject`, `grade_level`, `role`, `status`, `must_change_password`
- Added cast for `must_change_password` boolean field

### 4. **Updated Database Migration**
- Modified `0001_01_01_000000_create_users_table.php`
- Added columns for teacher information

### 5. **Created Routes**
- `GET /admin/dashboard` - View admin dashboard
- `POST /admin/teachers` - Register new teacher
- `POST /logout` - Logout functionality

## Setup Steps

### 1. Configure Database

Edit your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 2. Run Migrations

Open your terminal in the project directory and run:

```bash
php artisan migrate
```

This will create the necessary database tables with all the required columns.

### 3. Create an Admin User (Optional)

You can create an admin user using Laravel Tinker:

```bash
php artisan tinker
```

Then run:

```php
\App\Models\User::create([
    'name' => 'Admin User',
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'status' => 'active',
]);
```

### 4. Start the Development Server

```bash
php artisan serve
```

### 5. Access the Admin Dashboard

Open your browser and navigate to:
```
http://localhost:8000/admin/dashboard
```

## Important Notes

### Asset Files
Make sure your logo file exists at:
```
public/assets/logo.png
```

If you don't have this file, either:
1. Create the `public/assets` folder and add your logo
2. Update the path in `admin-dashboard.blade.php` line 41

### Authentication
The current setup doesn't include authentication middleware. To protect the admin routes, you should:

1. Set up Laravel authentication (Laravel Breeze, Jetstream, or custom)
2. Add middleware to the admin routes in `routes/web.php`:

```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
});
```

3. Create an admin middleware to check if the user has admin role

### Email Notifications
The controller has a TODO comment for sending email notifications. To implement this:

1. Configure mail settings in `.env`
2. Create a notification class
3. Send the notification in the `storeTeacher` method

## Testing the Teacher Registration

1. Navigate to `http://localhost:8000/admin/dashboard`
2. Fill in the teacher registration form
3. Click "Register Teacher"
4. The teacher will be added to the database
5. You'll see a success message

## Troubleshooting

### Error: "Class 'App\Models\User' not found"
Run: `composer dump-autoload`

### Error: "SQLSTATE[42S02]: Base table or view not found"
Run: `php artisan migrate`

### Error: "419 Page Expired"
This is a CSRF token error. Make sure your form includes `@csrf` directive.

### Assets not loading
Run: `php artisan storage:link` if you're using storage for assets

## Next Steps

1. **Implement Authentication**: Add login/logout functionality
2. **Add Middleware**: Protect admin routes with authentication
3. **Create Activity Log**: Track admin actions
4. **Add Email Notifications**: Send credentials to new teachers
5. **Convert Other HTML Files**: Apply the same process to other HTML files:
   - `signin-signup.html`
   - `student-dashboard.html`
   - `teacher-dashboard.html`

## File Structure

```
thesis/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── AdminController.php
│   └── Models/
│       └── User.php
├── database/
│   └── migrations/
│       └── 0001_01_01_000000_create_users_table.php
├── resources/
│   └── views/
│       ├── admin-dashboard.blade.php (NEW - use this)
│       └── admin-dashboard.html (OLD - can be deleted)
├── routes/
│   └── web.php
└── public/
    └── assets/
        └── logo.png (make sure this exists)
```

## Questions?

If you encounter any issues, check:
1. Laravel logs: `storage/logs/laravel.log`
2. PHP version (Laravel 11 requires PHP 8.2+)
3. Composer dependencies are installed: `composer install`
