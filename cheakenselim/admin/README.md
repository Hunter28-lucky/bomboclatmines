# 🐔 Chicken Road Game - Admin Panel

Complete admin panel for managing the Chicken Road Game with modern UI and full functionality.

## 📁 File Structure

```
admin/
├── config.php              # Database connection & helper functions
├── login.php               # Admin login page
├── dashboard.php           # Main dashboard with statistics
├── users.php               # User management
├── logout.php              # Logout functionality
├── admin_database.sql      # Database structure
├── README.md               # This file
└── includes/
    ├── header.php          # Common header with authentication
    ├── footer.php          # Common footer
    ├── sidebar.php         # Navigation sidebar
    └── styles.php          # All CSS styles
```

## 🚀 Features

### 🔐 Authentication & Security
- **Role-based Access:** Super Admin, Admin, Moderator
- **Session Management:** Secure admin sessions
- **Activity Logging:** All admin actions tracked
- **CSRF Protection:** Form security
- **Input Sanitization:** XSS prevention

### 📊 Dashboard
- **Statistics Cards:** Users, transactions, bets, revenue
- **Recent Data:** Latest transactions and users
- **Real-time Updates:** Pending transaction badges
- **Responsive Design:** Mobile and tablet friendly

### 👥 User Management
- **User List:** Paginated user table
- **User Actions:** Block, unblock, delete users
- **User Details:** Balance, status, referral codes
- **Search & Filter:** Easy user finding

### 🎨 UI/UX Features
- **Modern Design:** Blue-cyan gradient theme
- **Glassmorphism:** Beautiful glass effects
- **Hover Animations:** Interactive elements
- **Responsive Layout:** Works on all devices
- **Consistent Theme:** Same design across all pages

## 🛠️ Installation

1. **Database Setup:**
   ```sql
   -- Import the admin database structure
   mysql -u username -p database_name < admin_database.sql
   ```

2. **Configuration:**
   - Update database credentials in `config.php`
   - Ensure proper file permissions

3. **Access:**
   - Navigate to `yourdomain.com/admin/`
   - Login with default credentials

## 🔑 Default Credentials

| Role | Username | Password |
|------|----------|----------|
| Super Admin | `superadmin` | `admin123` |
| Admin | `admin` | `admin123` |

## 📱 Responsive Design

- **Desktop:** Full sidebar layout
- **Tablet:** Collapsible sidebar
- **Mobile:** Stacked layout with touch-friendly buttons

## 🎯 Navigation Structure

```
📊 Dashboard
├── 👥 Users
├── 💰 Transactions (with pending badge)
├── 🎮 Bet History
├── 📋 KYC Management
├── ⚙️ Settings
├── 📈 Reports
├── 📝 Activity Logs
├── 👤 Profile
└── 🚪 Logout
```

## 🔧 Creating New Pages

To create a new admin page, follow this template:

```php
<?php
require_once 'config.php';

// Set page title
$page_title = 'Your Page Title';

// Your page logic here
// Get data, handle forms, etc.

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Your Page Title</h1>
        <p class="page-subtitle">Page description</p>
    </div>
    
    <!-- Your content here -->
</div>

<?php include 'includes/footer.php'; ?>
```

## 🎨 CSS Classes Available

### Buttons
- `.btn` - Base button
- `.btn-primary` - Primary action
- `.btn-secondary` - Secondary action
- `.btn-danger` - Delete/danger action
- `.btn-success` - Success action

### Messages
- `.message.success` - Success message
- `.message.error` - Error message
- `.message.warning` - Warning message
- `.message.info` - Info message

### Status Badges
- `.status-badge.status-approved` - Approved status
- `.status-badge.status-pending` - Pending status
- `.status-badge.status-rejected` - Rejected status

### Tables
- `.table-container` - Table wrapper
- `.table` - Data table
- `.table th` - Table headers
- `.table td` - Table cells

## 🔒 Security Features

1. **Authentication Required:** All pages check admin login
2. **Role-based Access:** Different permissions per role
3. **Activity Logging:** All actions are logged
4. **Session Security:** Session regeneration on login
5. **Input Validation:** All inputs sanitized
6. **CSRF Protection:** Form tokens for security

## 📊 Database Tables

- `admin_users` - Admin accounts
- `admin_settings` - System settings
- `admin_activity_logs` - Admin actions
- `admin_notifications` - Admin notifications
- `admin_reports` - Report management

## 🚀 Next Steps

You can create these additional pages:
- `transactions.php` - Transaction approval
- `settings.php` - System settings
- `bets.php` - Bet history management
- `kyc.php` - KYC verification
- `reports.php` - Analytics reports
- `logs.php` - Activity logs

## 🎨 Customization

### Colors
All colors are defined in CSS variables in `includes/styles.php`:
```css
:root {
    --main-color: #00ECBE;
    --main-gradient-start: #7AFEC3;
    --main-gradient-end: #02AFB6;
    /* ... more variables */
}
```

### Adding New Features
1. Create new PHP file
2. Include header and footer
3. Add navigation link in sidebar
4. Use existing CSS classes for consistency

## 📞 Support

For any issues or questions:
- Check error logs
- Verify database connection
- Ensure proper file permissions
- Test with default credentials

---

**Made with ❤️ for Chicken Road Game** 