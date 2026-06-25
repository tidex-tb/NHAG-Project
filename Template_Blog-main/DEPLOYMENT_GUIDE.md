# SAVINGS MANAGEMENT PLATFORM - DEPLOYMENT GUIDE

## PRODUCTION DEPLOYMENT CHECKLIST

### Phase 1: Pre-Deployment Verification (1-2 hours)

#### Code Review
- [ ] All controllers implement permission checks
- [ ] All models use parameterized queries
- [ ] No debug output or error messages exposed to users
- [ ] All form validation rules in place
- [ ] File upload restrictions implemented
- [ ] Session management configured

#### Database
- [ ] Database backup created
- [ ] All tables created successfully
- [ ] Sample data loaded (optional)
- [ ] Indexes verified
- [ ] Foreign keys validated
- [ ] Default roles and permissions populated

#### Security
- [ ] HTTPS certificate installed
- [ ] .htaccess configured (if using Apache)
- [ ] File permissions set correctly (644 files, 755 directories)
- [ ] upload/ directory outside document root
- [ ] database.php credentials secure
- [ ] encryption_key changed in config.php
- [ ] admin accounts secured
- [ ] API credentials configured

#### Performance
- [ ] Caching enabled
- [ ] Database queries optimized
- [ ] Static assets minified (optional)
- [ ] CDN configured (optional)
- [ ] Load testing completed

#### Documentation
- [ ] System documented
- [ ] API documented
- [ ] Admin procedures documented
- [ ] Disaster recovery plan created

---

### Phase 2: Server Preparation (30-45 minutes)

#### Environment Setup
```bash
# 1. Create application directories
mkdir -p /var/www/html/nhag-savings
mkdir -p /var/www/html/nhag-savings/uploads/{contributions,members,statements}
mkdir -p /var/www/html/nhag-savings/backups
mkdir -p /var/www/html/nhag-savings/logs

# 2. Set permissions
chown -R www-data:www-data /var/www/html/nhag-savings
chmod -R 755 /var/www/html/nhag-savings
chmod -R 775 /var/www/html/nhag-savings/uploads
chmod -R 775 /var/www/html/nhag-savings/logs
chmod -R 775 /var/www/html/nhag-savings/backups

# 3. Copy application files
cp -r Template_Blog/* /var/www/html/nhag-savings/

# 4. Set secure permissions
chmod 644 /var/www/html/nhag-savings/application/config/*.php
```

#### Apache Configuration
```apache
<VirtualHost *:443>
    ServerName savings.nhag.org.na
    ServerAlias www.savings.nhag.org.na
    DocumentRoot /var/www/html/nhag-savings

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/your-cert.crt
    SSLCertificateKeyFile /etc/ssl/private/your-key.key
    SSLCertificateChainFile /etc/ssl/certs/your-chain.crt

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/nhag-savings-error.log
    CustomLog ${APACHE_LOG_DIR}/nhag-savings-access.log combined

    # Rewrite rules
    <Directory /var/www/html/nhag-savings>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Remove index.php from URLs
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteBase /
            
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php/$1 [L]
        </IfModule>
    </Directory>

    # Security headers
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName savings.nhag.org.na
    ServerAlias www.savings.nhag.org.na
    Redirect permanent / https://savings.nhag.org.na/
</VirtualHost>
```

#### PHP Configuration
Update `/etc/php/7.4/apache2/php.ini`:
```ini
; Memory and Execution
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M

; Security
display_errors = Off
log_errors = On
error_log = /var/www/html/nhag-savings/logs/php-errors.log

; Session
session.gc_maxlifetime = 86400
session.save_path = /var/www/html/nhag-savings/sessions
```

#### MySQL Configuration
```sql
-- Optimize for production
SET GLOBAL max_connections = 1000;
SET GLOBAL wait_timeout = 600;
SET GLOBAL interactive_timeout = 600;

-- Enable query logging (optional)
SET GLOBAL log_queries_not_using_indexes = ON;
SET GLOBAL slow_query_log = ON;
SET GLOBAL long_query_time = 2;
```

---

### Phase 3: Database Migration (15-30 minutes)

```bash
# 1. Create database
mysql -u root -p -e "CREATE DATABASE nhag_savings CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

# 2. Import schema
mysql -u root -p nhag_savings < /var/www/html/nhag-savings/database/nhag.sql

# 3. Create application user
mysql -u root -p -e "
CREATE USER 'nhag_app'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON nhag_savings.* TO 'nhag_app'@'localhost';
FLUSH PRIVILEGES;"

# 4. Verify tables
mysql -u root -p nhag_savings -e "SHOW TABLES;"
```

---

### Phase 4: Application Configuration (20-30 minutes)

#### Update Configuration Files

**database.php:**
```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'nhag_app',
    'password' => 'secure_password',
    'database' => 'nhag_savings',
    'dbdriver' => 'mysqli',
);
```

**config.php:**
```php
$config['base_url'] = 'https://savings.nhag.org.na/';
$config['encryption_key'] = 'generate-random-key-here';
$config['sess_driver'] = 'database';
$config['sess_save_path'] = 'sessions';
$config['log_path'] = './application/logs/';
$config['log_threshold'] = 1;  // Production: 1 (errors only)
```

**email.php:**
```php
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'mail.nhag.org.na';
$config['smtp_port'] = 587;
$config['smtp_user'] = 'notifications@nhag.org.na';
$config['smtp_pass'] = 'email_password';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
```

---

### Phase 5: Initial Data Setup (15-20 minutes)

```sql
-- Create initial admin user
INSERT INTO users (name, email, username, password, role_id, is_active, created_at)
VALUES (
    'System Administrator',
    'admin@nhag.org.na',
    'admin',
    '$2y$10$...hash...',  -- bcrypt hash of password
    1,  -- Super Admin role
    1,
    NOW()
);

-- Populate system settings
INSERT INTO system_settings (setting_key, setting_value, data_type, description)
VALUES
('company_name', 'Namibia Housing Action Group', 'string', 'Company Name'),
('company_email', 'info@nhag.org.na', 'string', 'Company Email'),
('notification_email_enabled', '1', 'boolean', 'Email Notifications'),
('max_upload_size_mb', '10', 'integer', 'Max Upload Size');

-- Assign permissions to roles
-- (Run role_permissions inserts for each role)
```

---

### Phase 6: Testing (1-2 hours)

#### Functional Testing
- [ ] Login/Logout working
- [ ] Member registration working
- [ ] Member profile creation/update
- [ ] Contribution submission
- [ ] Contribution approval workflow
- [ ] Statement generation
- [ ] Report generation
- [ ] Ledger tracking
- [ ] Notifications sending
- [ ] File uploads working

#### Security Testing
- [ ] Authentication enforced
- [ ] Authorization checks working
- [ ] Permission denied for unauthorized access
- [ ] SQL injection prevention
- [ ] XSS protection
- [ ] CSRF protection
- [ ] Session security
- [ ] Password security

#### Performance Testing
- [ ] Page load times < 3 seconds
- [ ] Database queries optimized
- [ ] Concurrent user testing
- [ ] Load testing (simulated 100+ users)
- [ ] Memory usage acceptable

#### Compatibility Testing
- [ ] Chrome latest version
- [ ] Firefox latest version
- [ ] Safari latest version
- [ ] Edge latest version
- [ ] Mobile browsers (iOS Safari, Chrome Android)
- [ ] Tablet responsiveness

---

### Phase 7: Monitoring & Logging Setup (20-30 minutes)

#### Log Monitoring
```bash
# 1. Create log rotation config
sudo tee /etc/logrotate.d/nhag-savings <<EOF
/var/www/html/nhag-savings/logs/*.log
{
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
EOF

# 2. Enable Apache logging
sudo a2enmod access_compat
sudo a2enmod log_config

# 3. Setup cron jobs for maintenance
sudo crontab -e
# Add entries:
# 0 2 * * * /usr/bin/mysqldump -u root -p'password' nhag_savings > /var/backups/nhag-$(date +\%Y\%m\%d).sql
# 0 3 * * * rm /var/www/html/nhag-savings/logs/*.log.* -f
```

#### Performance Monitoring
```bash
# Install monitoring tools
sudo apt-get install htop iotop nethogs

# Monitor system resources
htop
```

---

### Phase 8: Backup & Disaster Recovery (30 minutes)

#### Automated Backups
```bash
#!/bin/bash
# File: /usr/local/bin/backup-nhag-savings.sh

BACKUP_DIR="/var/backups/nhag-savings"
DATE=$(date +%Y%m%d_%H%M%S)
DBNAME="nhag_savings"
DBUSER="nhag_app"
DBPASS="secure_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DBUSER -p$DBPASS $DBNAME > $BACKUP_DIR/db_$DATE.sql

# Compress
gzip $BACKUP_DIR/db_$DATE.sql

# Backup uploads
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/html/nhag-savings/uploads/

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

Schedule via cron:
```bash
0 2 * * * /usr/local/bin/backup-nhag-savings.sh
```

#### Disaster Recovery Plan
- [ ] Document all system components
- [ ] Maintain off-site backups
- [ ] Test backup restoration quarterly
- [ ] Document recovery procedures
- [ ] Establish RTO/RPO targets

---

### Phase 9: Go-Live (Final Steps)

#### Final Checklist
- [ ] All tests passed
- [ ] Monitoring active
- [ ] Backups running
- [ ] Admin accounts secured
- [ ] Support team trained
- [ ] Documentation distributed
- [ ] User communication sent
- [ ] DNS pointing to production

#### Go-Live Announcement
```
Subject: NHAG Savings Platform - Live Announcement

Dear Members,

The NHAG Savings Management Platform is now live and ready to use.

Key Information:
- Platform URL: https://savings.nhag.org.na
- Registration: Self-service registration enabled
- Support: support@nhag.org.na
- Help Desk: +264 61 123456

Getting Started:
1. Visit the platform URL
2. Click "Register" to create an account
3. Complete registration with your details
4. Start managing your savings

For any questions, please contact our support team.

Best regards,
NHAG Administration Team
```

---

## POST-DEPLOYMENT MONITORING

### Daily Tasks
- [ ] Check error logs
- [ ] Monitor server resources
- [ ] Verify backups completed
- [ ] Check application uptime

### Weekly Tasks
- [ ] Review user activity
- [ ] Check database performance
- [ ] Test failover systems
- [ ] Review security alerts

### Monthly Tasks
- [ ] Analyze performance metrics
- [ ] Review and update documentation
- [ ] Security audit
- [ ] User feedback review

---

## ROLLBACK PLAN

If issues occur post-deployment:

```bash
# 1. Stop application
sudo systemctl stop apache2

# 2. Restore from backup
mysql -u root -p nhag_savings < /var/backups/nhag-savings/db_YYYYMMDD_HHMMSS.sql

# 3. Restore files
cd /var/www/html/nhag-savings
git checkout HEAD~1  # Revert to previous version

# 4. Restart application
sudo systemctl start apache2

# 5. Notify stakeholders
# Send notification of rollback and status
```

---

## PERFORMANCE BASELINES

Record these metrics after deployment:

| Metric | Baseline | Threshold |
|--------|----------|-----------|
| Page Load Time | < 2s | > 5s |
| Database Query Time | < 500ms | > 2000ms |
| Server Response Time | < 1s | > 3s |
| Error Rate | < 0.1% | > 1% |
| CPU Usage | < 40% | > 80% |
| Memory Usage | < 60% | > 90% |
| Disk Usage | < 50% | > 85% |

---

## SUPPORT CONTACT

- **Technical Support:** tech-support@nhag.org.na
- **Administration:** admin@nhag.org.na
- **Emergency:** +264 61 123456
- **On-Call:** [On-call schedule link]

---

**Version:** 1.0  
**Last Updated:** 2026-06-12  
**Deployment Type:** Production
