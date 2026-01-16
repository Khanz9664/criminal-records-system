# Criminal Records Management System (Enterprise Edition)

A professional, enterprise-grade web application for managing police records, cases, and criminal profiles. Built with modern PHP practices, following PSR standards, and implementing security best practices.

##  Features

### Core Functionality
- **Role-Based Access Control (RBAC)**: Secure logins for Admin, Officer, Detective, and Forensics
- **Interactive Dashboard**: Real-time stats with dynamic Clearance Rate calculation and recent activity logs
- **Case Management**: Complete workflow to file cases, link suspects, upload evidence, investigation notes, and status tracking
- **Criminal Records**: Comprehensive database with photo uploads, status filtering, and linked case history
- **Reporting Module**: Analytics dashboard with visual charts for Crime Types and Status Distribution
- **User Management**: Admin interface to manage system users and roles
- **Modern UI**: Fully responsive, professional design with Tailwind CSS

### Enterprise Features
- ✅ **CSRF Protection**: Token-based protection for all forms
- ✅ **Secure Session Management**: HttpOnly, Secure flags, session regeneration
- ✅ **Rate Limiting**: Protection against brute force attacks
- ✅ **Structured Logging**: Monolog integration with log rotation
- ✅ **Environment Configuration**: `.env` file support for different environments
- ✅ **Dependency Injection**: Loose coupling with service container
- ✅ **Middleware System**: Authentication, authorization, and security middleware
- ✅ **Input Validation**: Framework-ready for Symfony Validator
- ✅ **Error Handling**: Environment-based error reporting
- ✅ **PSR-4 Autoloading**: Composer-based dependency management

##  Prerequisites

- **PHP**: 8.1 or higher
- **Composer**: For dependency management
- **Web Server**: Apache (with mod_rewrite) or Nginx
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Extensions**: PDO, JSON, OpenSSL

##  Installation

### 1. Clone/Download Repository

```bash
git clone https://github.com/Khanz9664/criminal-records-system
cd criminal-records-system
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and configure your settings:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=CriminalRecordsDB
DB_USER=root
DB_PASS=your_password

# Application
APP_ENV=development
APP_DEBUG=true
```

### 4. Database Setup

1. Create a new database:
```sql
CREATE DATABASE CriminalRecordsDB;
```

2. Import the schema:
```bash
mysql -u root -p CriminalRecordsDB < database/schema.sql
```

Or use PHPMyAdmin to import `database/schema.sql`.

### 5. Directory Permissions

Ensure these directories are writable:

```bash
chmod -R 755 storage/logs
chmod -R 755 public/uploads
```

### 6. Web Server Configuration

#### Apache (.htaccess already configured)

Ensure `mod_rewrite` is enabled. The `.htaccess` file in `public/` handles routing.

#### Nginx

Add this to your server block:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 7. Access the Application

Navigate to: `http://localhost/criminal-records-system/public`

##  Default Login Credentials

| Role      | Username   | Password    |
|-----------|------------|-------------|
| **Admin**     | `admin`      | `password123` |
| **Shahid**   | `shaddy`   | `1234` |

##  Project Structure

```
criminal-records-system/
├── app/
│   ├── Controllers/      # Request handlers
│   ├── Core/            # Framework core (Router, Database, Config, etc.)
│   │   ├── Middleware/  # Authentication, CSRF, Rate Limiting
│   │   └── Security/    # CSRF, Encryption utilities
│   ├── Models/          # Data models
│   └── Services/        # Business logic services
├── config/              # Configuration files
├── database/            # Schema and migrations
├── public/              # Web root (entry point)
│   ├── index.php        # Application entry point
│   ├── uploads/         # File uploads
│   └── .htaccess        # URL rewriting
├── storage/
│   └── logs/            # Application logs
├── views/               # View templates
│   ├── layouts/         # Layout templates
│   └── [module]/        # Module-specific views
├── .env.example         # Environment template
├── composer.json        # PHP dependencies
└── README.md           # This file
```

##  Architecture

### MVC Pattern
- **Models**: Data access and business logic
- **Views**: Presentation layer (HTML templates)
- **Controllers**: Request handling and coordination

### Additional Patterns
- **Service Layer**: Business logic separation
- **Repository Pattern**: Data access abstraction (ready for implementation)
- **Middleware**: Cross-cutting concerns (auth, security, logging)
- **Dependency Injection**: Loose coupling via container

##  Security Features

### Implemented
- ✅ CSRF token protection
- ✅ Secure session management (HttpOnly, Secure flags)
- ✅ Password hashing (bcrypt)
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ Rate limiting for login attempts
- ✅ XSS prevention (input sanitization)
- ✅ File upload validation

### Recommended for Production
- Enable HTTPS (set `SESSION_SECURE=true` in `.env`)
- Change default encryption key
- Implement two-factor authentication
- Add data encryption for sensitive fields
- Set up regular backups
- Configure firewall rules

##  Testing

### Run Tests (when implemented)

```bash
composer test
```

### Code Analysis

```bash
composer analyse
```

##  Logging

Logs are stored in `storage/logs/app.log`. The system uses Monolog with:
- Daily log rotation (30 days retention)
- Different log levels (debug, info, warning, error, critical)
- Structured logging with context

##  Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Set `SESSION_SECURE=true` (requires HTTPS)
4. Generate encryption key: `php -r "echo bin2hex(random_bytes(32));"`
5. Update database credentials
6. Set proper file permissions
7. Configure web server for HTTPS
8. Set up automated backups
9. Configure monitoring and alerts

### Docker (Coming Soon)

Docker configuration will be added for easy deployment.

##  Development

### Code Standards

This project follows:
- **PSR-1**: Basic Coding Standard
- **PSR-4**: Autoloading Standard
- **PSR-12**: Extended Coding Style

### Adding New Features

1. Create controller in `app/Controllers/`
2. Add routes in `public/index.php`
3. Create views in `views/[module]/`
4. Add models in `app/Models/` if needed
5. Update database schema if needed

### Middleware Usage

```php
// In public/index.php
$router->post('/route', [Controller::class, 'method'], [
    new AuthMiddleware(),
    new RoleMiddleware(['admin']),
    new CSRFMiddleware()
]);
```

##  API Documentation

API endpoints will be documented here (when REST API is implemented).

##  Author

**Shahid Ul Islam**

---

## Roadmap

### Phase 1: Foundation ✅
- [x] Composer integration
- [x] CSRF protection
- [x] Secure sessions
- [x] Environment configuration
- [x] Middleware system
- [x] Logging framework

### Phase 2: Code Quality (In Progress)
- [ ] Input validation framework
- [ ] Repository pattern
- [ ] Service layer
- [ ] Unit tests
- [ ] Integration tests

### Phase 3: Features
- [ ] REST API endpoints
- [ ] Advanced search/filtering
- [ ] Report generation (PDF/Excel)
- [ ] Email notifications
- [ ] Two-factor authentication

### Phase 4: Performance & DevOps
- [ ] Caching (Redis)
- [ ] Database optimization
- [ ] Docker configuration
- [ ] CI/CD pipeline

---
