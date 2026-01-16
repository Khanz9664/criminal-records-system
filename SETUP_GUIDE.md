# Quick Setup Guide

Follow these steps to get the enterprise-enhanced Criminal Records Management System running.

## Step 1: Install Composer Dependencies

```bash
cd "/home/shaddy/Desktop/New Folder 4/criminal-records-system"
composer install
```

If you don't have Composer installed:
```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

## Step 2: Configure Environment

```bash
cp .env.example .env
```

Edit `.env` and update database credentials:
```env
DB_HOST=localhost
DB_NAME=CriminalRecordsDB
DB_USER=root
DB_PASS=1234  # Your actual password
```

## Step 3: Set Up Database

1. Create database:
```sql
CREATE DATABASE CriminalRecordsDB;
```

2. Import schema:
```bash
mysql -u root -p CriminalRecordsDB < database/schema.sql
```

Or use PHPMyAdmin to import `database/schema.sql`.

## Step 4: Set Permissions

### Option 1: Use the Fix Permissions Script (Recommended)

```bash
sudo ./fix-permissions.sh
```

This script will:
- Detect your web server user (www-data, apache, or nginx)
- Create necessary directories
- Set proper ownership and permissions

### Option 2: Manual Permission Setup

```bash
# For Apache (www-data)
sudo chown -R www-data:www-data storage/ public/uploads/
sudo chmod -R 755 storage/ public/uploads/
sudo chmod -R 775 storage/logs/ storage/cache/ public/uploads/

# Or for your specific web server user
sudo chown -R $(whoami):$(whoami) storage/ public/uploads/
sudo chmod -R 755 storage/ public/uploads/
sudo chmod -R 775 storage/logs/ storage/cache/ public/uploads/
```

**Important**: The web server user must have write access to:
- `storage/logs/` - For application logs
- `storage/cache/` - For cache files
- `public/uploads/` - For file uploads

## Step 5: Test the Application

1. Start your web server (Apache/Nginx or PHP built-in server)
2. Navigate to: `http://localhost/criminal-records-system/public`
3. Login with:
   - Username: `admin`
   - Password: `password123`

## Troubleshooting

### "Class not found" errors
- Run `composer install` to install dependencies
- Check that `vendor/autoload.php` exists

### Database connection errors
- Verify database credentials in `.env`
- Ensure MySQL is running
- Check database exists

### 404 errors on routes
- Ensure `.htaccess` is in `public/` directory
- Check `mod_rewrite` is enabled (Apache)
- Verify web server is pointing to `public/` directory

### Permission errors (Log directory, uploads)

**Error**: `Permission denied` or `mkdir(): Permission denied` in Log.php

**Solution**:
1. Run the fix permissions script:
   ```bash
   sudo ./fix-permissions.sh
   ```

2. Or manually fix:
   ```bash
   # Find your web server user
   ps aux | grep -E 'apache|httpd|nginx' | head -1
   
   # Set ownership (replace www-data with your web server user)
   sudo chown -R www-data:www-data storage/ public/uploads/
   sudo chmod -R 775 storage/logs/ storage/cache/ public/uploads/
   ```

3. If using SELinux (CentOS/RHEL):
   ```bash
   sudo chcon -R -t httpd_sys_rw_content_t storage/ public/uploads/
   ```

4. Verify permissions:
   ```bash
   ls -la storage/logs/
   # Should show write permissions for web server user
   ```

**Note**: The logging system will automatically fall back to `error_log` if file logging fails, so the application will still work, but logs won't be in `storage/logs/app.log`.

### CSRF token errors
- Clear browser cookies
- Ensure sessions are working
- Check `storage/` directory is writable

## What's New?

### Security Enhancements
- ✅ CSRF protection on all forms
- ✅ Secure session management
- ✅ Rate limiting for login
- ✅ Environment-based configuration

### Architecture Improvements
- ✅ Composer dependency management
- ✅ Middleware system
- ✅ Dependency injection container
- ✅ Structured logging

### Developer Experience
- ✅ Better error handling
- ✅ Comprehensive documentation
- ✅ View helpers
- ✅ Type-safe configuration

## Next Steps

1. Review `ASSESSMENT_REPORT.md` for detailed analysis
2. Check `IMPLEMENTATION_SUMMARY.md` for what's been implemented
3. Read `README.md` for full documentation
4. Start Phase 2: Input validation and testing

## Support

For issues or questions:
1. Check the documentation files
2. Review error logs in `storage/logs/app.log`
3. Ensure all prerequisites are met

---

**Ready to go!** 🚀

