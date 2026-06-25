# LOCAL TESTING ENVIRONMENT SETUP

**Status:** Production-Ready Backend with Bootstrap 4 Views  
**Last Updated:** June 12, 2026

---

## QUICK START (5 minutes)

### Prerequisites
- PHP 7.4+ (Windows: WAMP/XAMPP)
- MySQL 5.7+ (included in WAMP/XAMPP)
- Apache 2.4+ (included in WAMP/XAMPP)

### Step 1: Install WAMP/XAMPP (if not already installed)
**Windows:**
```
Download WAMP from: https://www.wampserver.com/en/
OR XAMPP from: https://www.apachefriends.org/

Run installer and follow prompts
Choose installation folder (default: c:\wamp64 or c:\xampp)
Start WAMP/XAMPP Control Panel
```

### Step 2: Start Services
```bash
# WAMP Control Panel: Click "Start All Services"
# XAMPP Control Panel: Start Apache and MySQL
```

### Step 3: Create Database
Open MySQL Command Line or phpMyAdmin:

```bash
# Via Command Line:
mysql -u root -p

# Then run:
CREATE DATABASE nhag_savings CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE nhag_savings;
SOURCE "c:\wamp64\www\Template_Blog\database\savings_platform_schema.sql";
```

**OR via phpMyAdmin:**
```
1. Open: http://localhost/phpmyadmin
2. Create new database: nhag_savings
3. Select database
4. Go to Import tab
5. Choose: database/nhag.sql
6. Click Import
```

### Step 4: Create Sessions Table
```bash
# Via MySQL:
USE nhag_savings;
SOURCE "c:\wamp64\www\Template_Blog\database\sessions_table.sql";

# OR via SQL:
CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
```

### Step 5: Update Configuration Files

**File: `application/config/database.php`**
```php
$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',  // Default for local: empty
    'database' => 'nhag_savings',
    'dbdriver' => 'mysqli',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_general_ci',
    'cache_on' => FALSE,
    'cachedir' => '',
    'cache_autoload' => FALSE,
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
```

**File: `application/config/config.php`**
```php
$config['base_url'] = 'http://localhost/Template_Blog/';
$config['index_page'] = '';
$config['uri_protocol'] = 'REQUEST_URI';
$config['encryption_key'] = 'dev-key-change-in-production';
$config['sess_driver'] = 'database';
$config['sess_cookie_name'] = 'nhag_session';
$config['sess_save_path'] = 'sessions';
$config['log_threshold'] = 3;  // Log everything during development
```

### Step 6: Create Upload Directories
```bash
# Windows Command Prompt:
cd c:\wamp64\www\Template_Blog
mkdir uploads\contributions
mkdir uploads\members
mkdir uploads\statements
mkdir application\logs

# On Mac/Linux:
cd /var/www/html/Template_Blog
mkdir -p uploads/{contributions,members,statements}
mkdir -p application/logs
chmod -R 755 uploads
chmod -R 755 application/logs
```

### Step 7: Access the Application
**Open in browser:**
```
http://localhost/Template_Blog/auth/register
```

---

## FIRST TIME LOGIN

### Step 1: Register First User
```
1. Go to: http://localhost/Template_Blog/auth/register
2. Fill in all required fields
3. Click "Create Account"
4. You'll be redirected to login page
5. Login with your credentials
```

### Step 2: Database Verification
After login, verify in database:
```sql
USE nhag_savings;
SELECT * FROM users;
SELECT * FROM members;
SELECT * FROM roles;
```

### Step 3: Access Dashboard
After login, you should see the dashboard matching your role.

---

## TESTING CHECKLIST

### Authentication Testing
- [ ] Register new account
- [ ] Login with credentials
- [ ] Logout successfully
- [ ] Verify session created in database

```bash
# Check sessions table:
SELECT * FROM sessions;
```

### Member Management Testing
- [ ] Create new member (Admin)
- [ ] View member list
- [ ] View member details
- [ ] Edit member profile
- [ ] Suspend/Activate member
- [ ] Search members

**Test URL:**
```
http://localhost/Template_Blog/members
http://localhost/Template_Blog/members/create
```

### Contribution Testing
- [ ] Submit contribution (Member)
- [ ] View pending contributions (Finance Officer)
- [ ] Upload proof of payment
- [ ] View contribution details
- [ ] Approve contribution (Finance Officer)
- [ ] Verify ledger entry created
- [ ] Verify member balance updated

**Test URL:**
```
http://localhost/Template_Blog/contributions
http://localhost/Template_Blog/contributions/submit
```

### Dashboard Testing
- [ ] Admin dashboard shows statistics
- [ ] Member dashboard shows personal savings
- [ ] Finance dashboard shows pending items
- [ ] Charts display correctly

**Test URL:**
```
http://localhost/Template_Blog/dashboard
```

### Database Verification
```sql
-- Check tables created
SHOW TABLES;

-- Check sample data
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_members FROM members;
SELECT COUNT(*) as total_contributions FROM contributions;
SELECT COUNT(*) as total_ledger FROM savings_ledger;

-- Check audit logs
SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 5;
```

---

## COMMON ISSUES & SOLUTIONS

### Issue: "Database connection failed"
**Solution:**
```bash
# 1. Check MySQL is running
# WAMP: Click "Start All Services"
# XAMPP: Start MySQL in Control Panel

# 2. Verify credentials:
mysql -u root -p -h localhost

# 3. Check database exists:
SHOW DATABASES;
```

### Issue: "Class 'mysqli' not found"
**Solution:**
```php
// Enable MySQLi extension in php.ini:
// Find: ;extension=mysqli
// Change to: extension=mysqli
// Restart Apache
```

### Issue: "404 Page Not Found"
**Solution:**
```bash
# 1. Check .htaccess exists in project root
# 2. Enable mod_rewrite in Apache:

# WAMP: Left-click icon > Apache > Apache modules > Rewrite_module
# XAMPP: Open httpd.conf, uncomment: LoadModule rewrite_module

# 3. Verify base_url in config.php:
$config['base_url'] = 'http://localhost/Template_Blog/';
```

### Issue: "Session not working"
**Solution:**
```bash
# 1. Create sessions table:
mysql -u root -p nhag_savings < database/sessions_table.sql

# 2. Check permissions on application/logs:
chmod 755 application/logs

# 3. Verify config.php:
$config['sess_driver'] = 'database';
$config['sess_save_path'] = 'sessions';
```

### Issue: "Permission denied on uploads"
**Solution:**
```bash
# Set correct permissions:
chmod -R 755 uploads
chmod 644 uploads/*

# Or on Windows, ensure IIS/Apache has write permissions
```

### Issue: "Blank page or HTTP 500"
**Solution:**
```php
// 1. Check error logs:
tail -f application/logs/log-*.php

// 2. Enable error display temporarily (development only):
// In config.php:
$config['log_threshold'] = 4;

// 3. Check PHP error logs:
// Windows: c:\wamp64\logs\php_error.log
// or c:\xampp\logs\error.log
```

---

## TESTING WORKFLOWS

### Complete Member Registration Flow
```
1. Register new account (auth/register)
2. Login with credentials (auth/login)
3. Check user created in database
4. Check member profile created
5. Verify member_number generated
6. Check role assigned
7. Access dashboard
```

### Complete Contribution Flow
```
1. Login as member
2. Go to Contributions > Submit
3. Enter contribution amount
4. Upload proof of payment file
5. Submit contribution
6. Check status is "Pending"
7. Verify audit log entry
8. Login as Finance Officer
9. View pending contributions
10. Approve contribution
11. Verify member balance updated
12. Check ledger entry created
13. Verify notification created
```

### Admin Panel Testing
```
1. Login as Administrator
2. Go to Admin panel
3. Verify users list
4. Check roles and permissions
5. View audit logs
6. Update system settings
7. Verify all changes logged
```

---

## PERFORMANCE TESTING

### Measure Page Load Times
```javascript
// Open browser console and run:
var start = performance.now();
// ... perform action ...
var end = performance.now();
console.log("Time: " + (end - start) + "ms");
```

### Test Concurrent Users
```bash
# Using Apache Bench:
ab -n 100 -c 10 http://localhost/Template_Blog/dashboard

# Using wrk (if available):
wrk -t4 -c10 -d30s http://localhost/Template_Blog/dashboard
```

### Monitor Database Performance
```sql
-- Enable slow query log:
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 0.1;

-- View slow queries:
SELECT * FROM mysql.slow_log;
```

---

## USEFUL DEVELOPMENT COMMANDS

### Reset Database
```bash
# Delete and recreate:
mysql -u root -p -e "DROP DATABASE nhag_savings; CREATE DATABASE nhag_savings CHARACTER SET utf8mb4;"
mysql -u root -p nhag_savings < database/nhag.sql
mysql -u root -p nhag_savings < database/sessions_table.sql
```

### View Error Logs
```bash
# Windows:
tail -f c:\wamp64\www\Template_Blog\application\logs\log-*.php

# Linux/Mac:
tail -f /var/www/html/Template_Blog/application/logs/log-*.php
```

### Clear Logs
```bash
# Windows:
del c:\wamp64\www\Template_Blog\application\logs\*

# Linux/Mac:
rm /var/www/html/Template_Blog/application/logs/*
```

### Test Database Connection
```php
// Create test.php in project root:
<?php
$config['hostname'] = 'localhost';
$config['username'] = 'root';
$config['password'] = '';
$config['database'] = 'nhag_savings';

$conn = mysqli_connect($config['hostname'], $config['username'], $config['password'], $config['database']);

if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
mysqli_close($conn);
?>

// Then visit: http://localhost/Template_Blog/test.php
```

---

## DATA POPULATION SCRIPTS

### Create Sample Data (Optional)
```sql
-- Insert sample members:
INSERT INTO members (name, email, phone_number, member_number, current_balance, is_active)
VALUES 
('John Doe', 'john@example.com', '+264612345001', 'MEM202600001', 5000.00, 1),
('Jane Smith', 'jane@example.com', '+264612345002', 'MEM202600002', 10000.00, 1),
('Bob Johnson', 'bob@example.com', '+264612345003', 'MEM202600003', 7500.00, 1);

-- Insert sample contributions:
INSERT INTO contributions (member_id, amount, status, reference_number, created_at)
VALUES
(1, 500.00, 'approved', 'CONT202600001', NOW()),
(2, 1000.00, 'pending', 'CONT202600002', NOW()),
(3, 750.00, 'approved', 'CONT202600003', NOW());

-- Insert sample ledger entries:
INSERT INTO savings_ledger (member_id, transaction_type, amount, description, balance_after)
VALUES
(1, 'Contribution', 500.00, 'CONT202600001', 5500.00),
(2, 'Contribution', 1000.00, 'CONT202600002', 11000.00);
```

---

## DEPLOYMENT READINESS CHECKLIST

Before moving to production:
- [ ] All views created and tested
- [ ] Authentication working properly
- [ ] Database backup verified
- [ ] SSL certificate ready (for production)
- [ ] Email configuration ready
- [ ] Backup scripts created
- [ ] Monitoring setup planned
- [ ] Documentation reviewed
- [ ] Performance tested
- [ ] Security audit completed

---

## NEXT STEPS

After local testing is successful:

1. **Complete Remaining Views** - Create views for:
   - Statements (list, view, generate)
   - Reports (list, savings, contributions, members)
   - Ledger (list, member view)
   - Admin (users, roles, permissions, settings)

2. **Add Frontend Enhancements:**
   - Chart.js for visualizations
   - DataTables for data display
   - jQuery for AJAX interactions
   - Bootstrap modals for dialogs

3. **PDF Integration:**
   - Install mPDF or TCPDF via Composer
   - Integrate with Statements controller
   - Test statement generation

4. **Email Configuration:**
   - Configure SMTP settings
   - Test email notifications
   - Create email templates

5. **Production Deployment:**
   - Follow DEPLOYMENT_GUIDE.md
   - Configure production server
   - Set up monitoring
   - Enable backups

---

## SUPPORT RESOURCES

**Documentation:**
- [IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md) - Setup and configuration
- [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) - Production deployment
- [README.md](./README.md) - Project overview
- Code comments in controllers and models

**Getting Help:**
- Check error logs: `application/logs/`
- Review code comments in relevant class
- Check database tables with MySQL client
- Compare with working controllers

---

**Version:** 1.0  
**Status:** Ready for Local Testing  
**Created:** June 12, 2026

