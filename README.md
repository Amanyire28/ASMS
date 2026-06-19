# Administrative Student Management System (ASMS)

A comprehensive Laravel-based student management system designed for educational institutions to manage students, teachers, classes, marks, and academic records efficiently.

## 🎯 Overview

ASMS is a full-featured school administration platform that streamlines the management of:
- **Student Records**: Registration, enrollment, profiles, and admission letters
- **Teacher Management**: Assignment to classes and subjects
- **Academic Records**: Marks entry, report card generation, and grade tracking
- **Class Management**: Classes, streams, levels, and category organization
- **Administrative Functions**: System settings, notifications, and announcements

## ✨ Key Features

### Student Management
- ✅ Comprehensive student registration and profile management
- ✅ Bulk student import via CSV
- ✅ Student photo uploads with storage
- ✅ Admission system with on-demand letter generation
- ✅ Tracking of admission numbers and admission dates
- ✅ Active/inactive student status

### Admission Letter System (New)
- 📄 Professional, customizable admission letters
- 📋 On-demand letter generation (not auto-generated)
- 🖨️ Print and regenerate letters anytime
- 💾 Stored letters for future reprinting
- ✏️ Optional remarks for special admission conditions
- 📊 Full student details integration

### Teacher Management
- 👨‍🏫 Teacher registration and profile management
- 📚 Class and subject assignments
- 🔒 Role-based access control
- 👁️ Dashboard restrictions for teacher role
- 🔑 Default password generation and forced password change
- 📊 Teacher statistics and analytics

### Academic Features
- 📝 Marks entry system with class/subject/exam filtering
- 📊 Automatic grade calculation and achievement tracking
- 🏆 Report card generation with on-demand PDF storage
- 📈 Term-wise and academic year tracking
- 🎓 Multiple exam type support
- 📑 Comprehensive academic reports

### System Features
- 🌓 Dark mode support with localStorage persistence
- 📱 Responsive mobile and desktop layouts
- 🔔 Real-time notification system
- 📢 School announcements
- ⚙️ Configurable school settings
- 🛡️ Permission-based access control (Spatie Laravel Permission)
- 🔐 Fortify authentication with password management
- 🎨 Tailwind CSS with Alpine.js reactivity

## 🏗️ Technology Stack

### Backend
- **Framework**: Laravel (latest stable)
- **Authentication**: Laravel Fortify + Jetstream
- **Authorization**: Spatie Laravel Permission
- **Database**: MySQL
- **ORM**: Eloquent

### Frontend
- **CSS**: Tailwind CSS
- **JavaScript**: Alpine.js (reactive components)
- **Icons**: Font Awesome 6.4.0
- **Build Tool**: Mix/Vite + npm

### Architecture
- **Pattern**: SPA-style routing with client-side router (public/js/spa-router.js)
- **API Structure**: RESTful with permission guards
- **Database**: Relational with pivot tables for many-to-many relationships

## 📁 Project Structure

```
ASMS/
├── app/
│   ├── Actions/                 # Fortify actions
│   ├── Console/                 # Artisan commands
│   ├── Exceptions/              # Custom exceptions
│   ├── Helpers/                 # Helper functions (school_setting, etc)
│   ├── Http/
│   │   ├── Controllers/         # Application controllers
│   │   ├── Kernel.php          # HTTP middleware
│   │   └── Middleware/         # Custom middleware
│   ├── Models/                  # Eloquent models
│   ├── Notifications/           # Notification classes
│   ├── Providers/               # Service providers
│   └── Services/                # Business logic services
├── bootstrap/                   # Bootstrap scripts
├── config/                      # Configuration files
├── database/
│   ├── factories/              # Model factories
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── public/
│   ├── css/                    # Compiled CSS
│   ├── js/                     # Compiled JavaScript & router
│   └── storage/                # File storage
├── resources/
│   ├── css/                    # Source CSS (Tailwind)
│   ├── js/                     # Source JavaScript
│   ├── lang/                   # Language files
│   ├── markdown/               # Markdown templates
│   └── views/
│       ├── auth/              # Authentication views
│       ├── layouts/           # Layout templates
│       ├── modules/           # Feature modules
│       └── partials/          # Reusable components
├── routes/
│   ├── api.php                # API routes
│   ├── channels.php           # Broadcast channels
│   ├── console.php            # Console routes
│   └── web.php                # Web routes
├── storage/                    # Application storage
├── tests/                      # Test suite
└── vendor/                     # Composer dependencies
```

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- MySQL 5.7+
- Composer
- Node.js & npm

### Step 1: Clone Repository
```bash
git clone https://github.com/Amanyire28/ASMS.git
cd ASMS
```

### Step 2: Install Dependencies
```bash
composer install
npm install
```

### Step 3: Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asms
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### Step 5: Build Assets
```bash
npm run dev     # Development
npm run build   # Production
```

### Step 6: Start Server
```bash
php artisan serve
```

Access the application at `http://127.0.0.1:8000`

## 👥 User Roles & Permissions

The system implements three primary roles:

### 1. Super Admin
- Full system access
- User management
- Permission management
- System configuration
- All CRUD operations

### 2. Headteacher
- Student management
- Teacher assignment
- Class management
- Marks entry and management
- Report generation
- System settings configuration

### 3. Teacher
- View assigned classes and students
- Enter marks for assigned classes
- View reports for their students
- Limited dashboard access
- Cannot access class management or system settings

## 📋 Database Models

### Core Models
- **User**: Authentication and roles
- **Student**: Student information and relationships
- **Teacher**: Teacher profiles and assignments
- **ClassModel**: Class definitions with levels and streams
- **Subject**: Subject definitions
- **Stream**: Class streams (e.g., Science, Arts)
- **ClassLevel**: Class levels (e.g., Form 1-4)

### Academic Models
- **Mark**: Individual student marks
- **ReportGeneration**: Stored report cards with PDF paths
- **AdmissionLetter**: Generated admission letters with metadata

### Configuration Models
- **SchoolSetting**: Configurable school parameters
- **Announcement**: School-wide announcements
- **Notification**: System notifications
- **NotificationPreference**: User notification preferences

## 🔄 Key Workflows

### Student Admission Flow
1. Admin creates student with basic information
2. Marks student as "admitted" (optional on creation)
3. Admin navigates to student profile
4. Clicks "Generate Admission Letter" button
5. System generates professional letter (on-demand, not auto-generated)
6. Letter is stored for reprinting
7. Can regenerate or print anytime from student profile

### Marks Entry Flow
1. Teacher/Admin navigates to Marks Entry
2. Selects Class → Term → Academic Year → Exam Type
3. For single-assigned-class teachers: class is auto-selected
4. Selects Subject (filtered by teacher assignment)
5. Enters marks for students
6. Submits and marks are recorded

### Report Card Generation
1. Admin/Headteacher navigates to Report Card section
2. Selects academic parameters
3. Generates reports on-demand
4. PDFs are stored for reprinting
5. Can view, print, or regenerate anytime

## 🛡️ Security Features

- **Authentication**: Laravel Fortify with email verification
- **Authorization**: Spatie Laravel Permission with role-based access
- **CSRF Protection**: Automatic CSRF token generation and validation
- **Password Management**: 
  - Forced password change on first login
  - Default format: `ASMS@YYYY` (e.g., ASMS@2026)
  - Secure hashing with bcrypt
- **Middleware Stack**: Permission checks, force password change, verified email

## 🎨 Frontend Architecture

### SPA-Style Routing
- Client-side router: `public/js/spa-router.js`
- HTMX integration for partial page updates
- Dynamic page content loading without full refresh

### Component System
- Alpine.js for reactive components
- Sidebar state management with localStorage
- Theme management (light/dark mode)
- Responsive design with Tailwind CSS

### Layout Structure
- **Desktop**: Fixed sidebar + responsive main content
- **Mobile**: Top navigation + bottom navigation bar
- Synchronized content between desktop and mobile views

## 📊 Recent Features

### Admission Letter System (v2.0)
- Professional letter templates with full context
- On-demand generation (not auto-generated like some systems)
- Comprehensive student information integration
- Remarks field for special conditions
- Print-optimized views
- Letter storage and reprinting
- Regeneration capability

### Teacher Dashboard Restrictions
- Sidebar hides admin-only options (class categories, levels, streams, announcements)
- Mobile nav gated with permission checks
- Single-class auto-selection in marks entry
- Teacher-specific dashboard views

## 🐛 Known Fixes & Improvements

- ✅ Notification route ordering (specific before generic routes)
- ✅ Forced password change middleware exclusion list
- ✅ Teacher password reset with page reload
- ✅ Permission-based UI rendering (not just controller checks)
- ✅ Nested eager loading for relationships (class.stream)
- ✅ SchoolSetting import in controllers

## 🔄 Git Workflow

Recent commits:
- `8d2e86a` - Admission letter system implementation
- Previous - Teacher dashboard restrictions and UI improvements
- Previous - Password reset and authentication fixes

## 📝 Contributing

1. Create a feature branch: `git checkout -b feature/your-feature`
2. Commit changes: `git commit -m "feat: description"`
3. Push to GitHub: `git push origin feature/your-feature`
4. Open a Pull Request

## 📄 License

This project is proprietary and for institutional use only.

## 👨‍💼 Support & Contact

For issues, feature requests, or support:
- GitHub Issues: [ASMS Repository](https://github.com/Amanyire28/ASMS/issues)
- Project Lead: Amanyire28

---

**Last Updated**: June 19, 2026  
**Version**: 2.0  
**Status**: Active Development
