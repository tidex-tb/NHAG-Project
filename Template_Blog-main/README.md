# SAVINGS MANAGEMENT PLATFORM

**Namibia Housing Action Group (NHAG) - Savings Management System**

A comprehensive, production-ready savings management platform built on CodeIgniter 3 and Bootstrap 4, designed to enable members of NHAG to manage their savings contributions efficiently with role-based access control, automated workflows, and comprehensive reporting capabilities.

---

## QUICK START

### Prerequisites
- PHP 7.4+, MySQL 5.7+, Apache 2.4+

### Installation (5 minutes)
1. Copy project to `/var/www/html`
2. Run: `mysql -u root -p < database/nhag.sql`
3. Edit `application/config/database.php` with your credentials
4. Edit `application/config/config.php` and set `base_url`
5. Create directories: `mkdir -p uploads/{contributions,members,statements}`
6. Access: http://localhost/Template_Blog/auth/register

### First Access
- Register your account
- First user becomes Super Administrator
- Login and start using the platform

---

## KEY FEATURES

✅ **10 Complete Modules**
1. Dashboard (role-based)
2. Member Management
3. Savings Contributions
4. Savings Ledger
5. Member Statements
6. Reporting & Analytics
7. Notifications
8. Document Management
9. Audit Logging
10. Role-Based Access Control

✅ **User Roles**
- Super Administrator
- Administrator
- Finance Officer
- Member

✅ **Core Functionality**
- Secure authentication with bcrypt
- Member registration and profile management
- Contribution submission and approval workflow
- Automatic balance tracking
- PDF statement generation
- Comprehensive reporting and exports
- Email notifications
- Complete audit trails
- Multi-level permissions

---

## DOCUMENTATION

- **DEPLOYMENT_GUIDE.md** - Production deployment instructions
- **Code Comments** - Detailed comments in all controller and model files

---

## QUICK REFERENCE

### System Requirements
- PHP 7.4+ with PDO MySQL, JSON, OpenSSL, cURL, mbstring, file_info, gd
- MySQL 5.7+ or MariaDB 10.3+
- Apache 2.4+ with mod_rewrite
- 2GB RAM minimum

### User Roles
- Super Administrator (ID: 1) - Full access
- Administrator (ID: 2) - Member and system management
- Finance Officer (ID: 3) - Contribution processing
- Member (ID: 4) - Self-service access

### Main Routes
- Auth: /auth/login, /auth/register, /auth/logout
- Dashboard: /dashboard
- Members: /members (CRUD operations)
- Contributions: /contributions (Submit, approve, reject)
- Statements: /statements (Generate, download)
- Reports: /reports (Analytics, exports)
- Admin: /admin (System administration)

---

## SUPPORT

- **Documentation:** See DEPLOYMENT_GUIDE.md and LOCAL_TESTING_SETUP.md
- **Email:** support@nhag.org.na
- **Phone:** +264 61 123456

---

**Version 1.0 | Production Ready | Last Updated June 12, 2026**
