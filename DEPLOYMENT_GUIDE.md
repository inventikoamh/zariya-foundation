# 🚀 Laravel Cloud Deployment Guide

This guide will help you deploy your Laravel Foundation CRM project to cloud hosting without SSH access.

## 📋 Prerequisites

- ✅ Project uploaded to cloud hosting
- ✅ `.env` file configured with correct database credentials
- ✅ `.htaccess` file in place
- ✅ PHP 8.2+ support on hosting
- ✅ Composer support on hosting

## 🔧 Step-by-Step Deployment Process

### 1. Upload Your Project Files

Make sure all your project files are uploaded to your hosting's public directory (usually `public_html` or `www`).

### 2. Configure Environment Variables

Your `.env` file should contain:

```env
APP_NAME="Foundation CRM"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

# Add other necessary environment variables
```

### 3. Run the Deployment Script

#### Option A: Direct Access (Recommended)
1. Visit: `https://yourdomain.com/deploy.php?key=your-secret-deployment-key-2024`
2. Click "Run Full Deployment"
3. Wait for all steps to complete

#### Option B: Through Laravel Route
1. Visit: `https://yourdomain.com/deploy?key=your-secret-deployment-key-2024`
2. This will redirect you to the deployment script

### 4. What the Deployment Script Does

The deployment script automatically:

1. **✅ Checks PHP Version** - Ensures PHP 8.2+
2. **✅ Verifies Laravel Project** - Confirms it's a valid Laravel installation
3. **✅ Installs Dependencies** - Runs `composer install --no-dev --optimize-autoloader`
4. **✅ Generates App Key** - Creates application key if missing
5. **✅ Clears Caches** - Removes old cached files
6. **✅ Optimizes Caches** - Creates optimized cache files for production
7. **✅ Runs Migrations** - Executes database migrations
8. **✅ Creates Storage Link** - Links storage directory for file uploads
9. **✅ Sets Permissions** - Attempts to set proper file permissions
10. **✅ Runs Seeders** - (Optional) Populates database with initial data

### 5. Verify Deployment

After deployment, check:

1. **Homepage**: Visit `https://yourdomain.com` - should show your welcome page
2. **Login**: Try accessing login pages
3. **Database**: Check if data is accessible
4. **File Uploads**: Test file upload functionality

### 6. Security Cleanup

**⚠️ IMPORTANT**: After successful deployment, delete the deployment files:

```bash
# Delete these files for security:
- public/deploy.php
- Remove the /deploy route from routes/web.php
```

## 🛠️ Alternative: Using Your Existing System Routes

Your project already has built-in database management routes. You can also use:

1. **System Login**: `https://yourdomain.com/system/login`
2. **Database Management**: `https://yourdomain.com/system/database`
3. **Run Migrations**: Use the web interface at `/system/database/migrate`

## 🔍 Troubleshooting

### Common Issues and Solutions

#### 1. "500 Internal Server Error"
- Check `.env` file configuration
- Verify database credentials
- Check file permissions (755 for directories, 644 for files)

#### 2. "Database Connection Failed"
- Verify database credentials in `.env`
- Ensure database server is accessible
- Check if database exists

#### 3. "Storage Link Failed"
- Check if `storage/app/public` directory exists
- Verify file permissions
- Try creating the link manually through your hosting panel

#### 4. "Composer Not Found"
- Contact your hosting provider to enable Composer
- Or upload `vendor` directory from your local development

#### 5. "Permission Denied"
- Set directory permissions to 755
- Set file permissions to 644
- Contact hosting provider if issues persist

### Manual Commands (if hosting supports)

If your hosting provides a command line interface:

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📁 File Structure After Deployment

Your hosting should have this structure:

```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── storage -> ../storage/app/public
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
└── composer.lock
```

## 🔐 Security Checklist

- [ ] Delete `deploy.php` after deployment
- [ ] Remove deployment route from `routes/web.php`
- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL
- [ ] Regular backups of database and files

## 📞 Support

If you encounter issues:

1. Check the deployment script output for specific error messages
2. Verify your hosting environment meets Laravel requirements
3. Contact your hosting provider for PHP/Composer support
4. Check Laravel logs in `storage/logs/laravel.log`

## 🎉 Success!

Once deployment is complete and verified:

1. Your Laravel application should be fully functional
2. All database tables should be created
3. File uploads should work
4. All routes should be accessible
5. The application should be optimized for production

Remember to delete the deployment files for security!
