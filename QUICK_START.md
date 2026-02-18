# 🚀 Quick Start - Q2L Laravel Application

## Run These Commands in Order:

### 1. Configure Database
Edit `.env` file and set your database credentials.

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Create Test Users
```bash
php artisan tinker
```

Then paste these commands:

```php
// Admin User
\App\Models\User::create(['name' => 'Admin', 'email' => 'admin@q2l.com', 'password' => bcrypt('password'), 'role' => 'admin', 'status' => 'active']);

// Teacher User
\App\Models\User::create(['name' => 'Teacher', 'email' => 'teacher@q2l.com', 'password' => bcrypt('password'), 'role' => 'teacher', 'status' => 'active']);

// Student User
\App\Models\User::create(['name' => 'Student', 'email' => 'student@q2l.com', 'password' => bcrypt('password'), 'role' => 'student', 'status' => 'active']);

exit
```

### 4. Start Server
```bash
php artisan serve
```

### 5. Open Browser
Go to: **http://localhost:8000**

---

## 🔑 Test Login Credentials

| Role    | Email             | Password |
|---------|-------------------|----------|
| Admin   | admin@q2l.com     | password |
| Teacher | teacher@q2l.com   | password |
| Student | student@q2l.com   | password |

---

## 📄 Files Converted to Laravel:

✅ `signin-signup.html` → `auth/login.blade.php` (Landing Page)  
✅ `admin-dashboard.html` → `admin-dashboard.blade.php`  
✅ `student-dashboard.html` → `student-dashboard.blade.php`  
✅ `teacher-dashboard.html` → `teacher-dashboard.blade.php`  

**You can delete the old .html files now!**

---

## ❓ Having Issues?

See **LARAVEL_SETUP_COMPLETE.md** for detailed troubleshooting.
