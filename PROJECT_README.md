# 🎓 Thesis Project - Q2L Learning Management System

A Laravel-based Learning Management System with admin, teacher, and student dashboards featuring Google Sign-in integration.

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed on your machine:

| Software | Version | Download Link |
|----------|---------|---------------|
| PHP | 8.2 or higher | [php.net](https://www.php.net/downloads) |
| Composer | Latest | [getcomposer.org](https://getcomposer.org/download/) |
| MySQL | 5.7+ or 8.0+ | [mysql.com](https://dev.mysql.com/downloads/) or use XAMPP |
| Node.js | 18+ | [nodejs.org](https://nodejs.org/) |
| Git | Latest | [git-scm.com](https://git-scm.com/) |

### ✅ Verify Installations:
```bash
php -v       # Should show PHP 8.2+
composer -v  # Should show Composer version
node -v      # Should show Node.js version
npm -v       # Should show NPM version
mysql --version  # Should show MySQL version
```

---

## 🚀 Installation Steps

### 1️⃣ Clone or Download the Project

**Option A: Clone via Git**
```bash
git clone <repository-url>
cd thesis
```

**Option B: Download ZIP**
- Extract the ZIP file
- Navigate to the project folder in terminal/command prompt

---

### 2️⃣ Install PHP Dependencies
```bash
composer install
```

**If you get errors:**
```bash
composer install --ignore-platform-reqs
```

---

### 3️⃣ Install JavaScript Dependencies
```bash
npm install
```

---

### 4️⃣ Environment Configuration

**Copy the environment file:**
```bash
# Windows (Command Prompt)
copy .env.example .env

# Windows (PowerShell)
cp .env.example .env

# macOS/Linux
cp .env.example .env
```

**Generate Application Key:**
```bash
php artisan key:generate
```

**Edit `.env` file** with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=thesis_db
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

---

### 5️⃣ Create Database

**Option A: Using phpMyAdmin (XAMPP)**
1. Start XAMPP (Apache + MySQL)
2. Open http://localhost/phpmyadmin
3. Create new database named `thesis_db`

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE thesis_db;
EXIT;
```

---

### 6️⃣ Run Database Migrations
```bash
php artisan migrate
```

This will create all necessary tables in your database.

---

### 7️⃣ Seed Test Users (Optional but Recommended)

```bash
php artisan tinker
```

Then paste these commands in the Tinker console:

```php
// Create Admin User
\App\Models\User::create(['name' => 'Admin', 'email' => 'admin@q2l.com', 'password' => bcrypt('password'), 'role' => 'admin', 'status' => 'active']);

// Create Teacher User
\App\Models\User::create(['name' => 'Teacher', 'email' => 'teacher@q2l.com', 'password' => bcrypt('password'), 'role' => 'teacher', 'status' => 'active']);

// Create Student User
\App\Models\User::create(['name' => 'Student', 'email' => 'student@q2l.com', 'password' => bcrypt('password'), 'role' => 'student', 'status' => 'active']);

exit
```

---

### 8️⃣ Build Frontend Assets
```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

---

### 9️⃣ Start the Development Server
```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

---

## 🔐 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@q2l.com | password |
| Teacher | teacher@q2l.com | password |
| Student | student@q2l.com | password |

---

## 🌐 Google Sign-In Setup (Optional)

If you want to enable Google authentication:

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `http://localhost:8000/auth/google/callback`
6. Copy Client ID and Client Secret
7. Add to `.env` file:

```env
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

## 📁 Project Structure

```
thesis/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   └── Models/               # Database models
├── database/
│   └── migrations/           # Database migrations
├── public/
│   └── assets/               # Images, CSS, JS files
├── resources/
│   └── views/                # Blade templates
├── routes/
│   └── web.php              # Application routes
└── .env                     # Environment configuration (DO NOT COMMIT)
```

---

## 🔧 Troubleshooting

### Error: "Class 'App\Models\User' not found"
```bash
composer dump-autoload
```

### Error: "SQLSTATE[42S02]: Base table or view not found"
```bash
php artisan migrate
```

### Error: "419 Page Expired" (CSRF Token Error)
- Clear browser cache
- Hard refresh (Ctrl + Shift + R)

### Port 8000 Already in Use
```bash
php artisan serve --port=8080
```

### Database Connection Failed
1. Make sure XAMPP/MySQL is running
2. Check database credentials in `.env`
3. Verify database `thesis_db` exists

### Assets Not Loading
```bash
npm run build
php artisan storage:link
```

---

## 🛠️ Common Commands

| Command | Description |
|---------|-------------|
| `php artisan serve` | Start development server |
| `php artisan migrate` | Run database migrations |
| `php artisan migrate:fresh` | Drop all tables and re-migrate |
| `php artisan tinker` | Open Laravel console |
| `npm run dev` | Compile assets for development |
| `npm run build` | Compile assets for production |
| `composer dump-autoload` | Regenerate autoload files |

---

## 📱 Features

✅ **Admin Dashboard**
- Teacher registration & management
- Real-time statistics
- System monitoring

✅ **Teacher Dashboard**
- Student management
- Grade tracking
- Activity monitoring

✅ **Student Dashboard**
- Course access
- Grade viewing
- Profile management

✅ **Authentication**
- Traditional email/password login
- Google Sign-in integration
- Role-based access control

---

## 🤝 Support

If you encounter any issues:
1. Check `storage/logs/laravel.log` for errors
2. Refer to [Laravel Documentation](https://laravel.com/docs)
3. See **SETUP_INSTRUCTIONS.md** for detailed setup info

---

## 📝 License

This is a thesis project. Check with the project owner for licensing information.

---

## 👨‍💻 Development

**For local development:**
```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Watch frontend assets
npm run dev
```

**Environment:**
- Laravel 12.x
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- TailwindCSS
- Alpine.js

---

## 📞 Contact

For questions about this project, please contact the project maintainer.
