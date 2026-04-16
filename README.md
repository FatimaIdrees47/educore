---

## 🚀 Installation

### Requirements
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM

### Setup

```bash
# Clone the repository
git clone https://github.com/FatimaIdrees47/educore.git
cd educore

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure your database in .env
DB_DATABASE=educore
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed --class=DatabaseSeeder
php artisan db:seed --class=DemoDataSeeder

# Build assets
npm run build

# Start server
php artisan serve
```

---

## 👤 Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@educore.com | password |
| School Admin | admin@democschool.com | password |
| Teacher | teacher@democschool.com | password |
| Student | ayesha@demo.com | password |
| Parent | parent@demo.com | password |

---

## 📸 Screenshots

> Admin Dashboard, Student Portal, Report Card PDF, Library Management, Messages

---

## 📊 Database Schema

- **20+ tables** covering all school operations
- Multi-school architecture from day one
- Monetary values stored in **paisas** (integers) for precision
- Soft deletes on critical models

---

## 🗺️ Modules

| Module | Status |
|--------|--------|
| Authentication & RBAC | ✅ Complete |
| Classes, Sections, Subjects | ✅ Complete |
| Student Management | ✅ Complete |
| Teacher Management | ✅ Complete |
| Attendance (Livewire) | ✅ Complete |
| Fee Management | ✅ Complete |
| Exams & Results | ✅ Complete |
| Report Card PDF | ✅ Complete |
| Homework | ✅ Complete |
| Notices | ✅ Complete |
| Timetable | ✅ Complete |
| Leave Management | ✅ Complete |
| Messages (Real-time) | ✅ Complete |
| Library Management | ✅ Complete |
| Settings Panel | ✅ Complete |
| Super Admin Panel | ✅ Complete |

---

## 👩‍💻 Developer

**Fatima Idrees**
- GitHub: [@FatimaIdrees47](https://github.com/FatimaIdrees47)
- Laravel & PHP Developer | Pakistan

---

## 📄 License

This project is open-sourced for portfolio purposes.