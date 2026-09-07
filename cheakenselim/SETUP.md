# Mines Game Setup Guide

## Quick Setup for Local Development

### 1. Install XAMPP
- Download and install [XAMPP](https://www.apachefriends.org/)
- Start Apache and MySQL from XAMPP Control Panel

### 2. Database Setup
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database: `mines_game`
3. Import the `database.sql` file
4. Default admin credentials:
   - Username: `admin`
   - Password: `admin123`

### 3. File Setup
1. Place all files in: `C:/xampp/htdocs/MINES BY DEV/`
2. Ensure the following structure:
```
MINES BY DEV/
├── index.html
├── login.html
├── auth.js
├── config.php
├── database.sql
├── api/
│   ├── register.php
│   ├── login.php
│   ├── logout.php
│   ├── user.php
│   ├── deposit.php
│   ├── withdraw.php
│   ├── transactions.php
│   ├── bet_history.php
│   ├── save_bet.php
│   └── update_balance.php
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── users.php
│   ├── transactions.php
│   ├── bets.php
│   └── settings.php
├── avatar/
├── icons/
└── ... (other files)
```

### 4. Configuration
1. Edit `config.php` if your MySQL credentials are different:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Your MySQL username
define('DB_PASS', '');      // Your MySQL password
define('DB_NAME', 'mines_game');
```

### 5. Access Your Game
- Main Game: `http://localhost/MINES%20BY%20DEV/login.html`
- Admin Panel: `http://localhost/MINES%20BY%20DEV/admin/login.php`

## Features

### User Features
- ✅ Registration and Login
- ✅ Real-time balance management
- ✅ Deposit/Withdrawal with admin approval
- ✅ User-specific transaction history
- ✅ User-specific bet history
- ✅ Referral system
- ✅ Avatar selection

### Admin Features
- ✅ User management
- ✅ Transaction approval/rejection
- ✅ Bet history monitoring
- ✅ Game settings management
- ✅ Dashboard with statistics

### Game Features
- ✅ 5x5 grid with customizable bomb count
- ✅ Progressive multipliers
- ✅ Cashout functionality
- ✅ Sound effects and music
- ✅ Mobile responsive design

## Troubleshooting

### CSS Not Loading
- Ensure all files are in the correct directory
- Check file permissions
- Verify Apache is running

### Login/Registration Issues
- Check database connection in `config.php`
- Verify all API files are present
- Check browser console for errors

### Admin Panel Issues
- Ensure you're logged in as admin
- Check admin credentials in database
- Verify all admin files are present

## Security Notes
- Change default admin password after first login
- Use HTTPS in production
- Regularly backup your database
- Keep PHP and MySQL updated

## Production Deployment
1. Upload files to your hosting provider
2. Create MySQL database
3. Import `database.sql`
4. Update `config.php` with production credentials
5. Set proper file permissions
6. Enable HTTPS

## Support
If you encounter issues:
1. Check browser console for JavaScript errors
2. Check PHP error logs
3. Verify database connectivity
4. Ensure all files are properly uploaded 