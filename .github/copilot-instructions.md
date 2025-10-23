<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Nail Studio Andreea - Project Instructions

## Project Overview
This is a complete website and management system for "Nail Studio Andreea", a professional nail salon offering manicure, pedicure, nail art services, and coaching courses.

## Technical Stack
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (WAMP recommended for local development)

## Project Structure
- `/` - Public website pages
- `/admin/` - Administrative panel
- `/includes/` - Shared PHP files (config, functions, headers)
- `/assets/` - Static resources (CSS, JS, images)
- `/sql/` - Database schema and initial data

## Key Features
1. **Public Website**: Services showcase, gallery, appointment booking, coaching courses, contact form
2. **Admin Panel**: Dashboard, appointments management, services management, gallery management, coaching sessions, contact messages
3. **Database**: Complete schema with services, appointments, gallery, coaching, users, and messages
4. **Responsive Design**: Mobile-first approach with Bootstrap 5
5. **Security**: Input validation, SQL injection protection, admin authentication

## Development Guidelines

### Code Style
- Use consistent PHP coding standards
- Follow Bootstrap 5 conventions for CSS classes
- Use semantic HTML5 elements
- Implement proper error handling and validation

### Database
- All user inputs must be sanitized using `sanitize_input()` function
- Use prepared statements for database queries
- Maintain referential integrity with foreign keys

### Security
- Always validate and sanitize user inputs
- Use `require_admin_login()` for admin-only pages
- Implement proper session management
- Hash passwords using PHP's `password_hash()`

### File Organization
- Place reusable functions in `includes/functions.php`
- Use header/footer includes for consistent layout
- Separate admin functionality from public pages
- Store configuration in `includes/config.php`

### Frontend
- Mobile-first responsive design
- Use CSS custom properties for theming
- Implement proper form validation (client and server-side)
- Optimize images and use appropriate formats

## Default Admin Credentials
- Username: `admin`
- Password: `admin123`

## Database Configuration
Update `includes/config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nail_studio_andreea');
```

## Installation Steps
1. Set up WAMP/XAMPP server
2. Create database and import `sql/database_schema.sql`
3. Configure database connection in `includes/config.php`
4. Ensure proper file permissions for image uploads
5. Test all functionality before deployment

## Common Tasks
- Adding new services: Update `services` table and admin panel
- Managing appointments: Use admin dashboard for status updates
- Gallery updates: Admin panel allows image upload and management
- Contact form: Messages stored in database and accessible via admin panel

## Maintenance
- Regular database backups
- Update admin credentials
- Monitor security logs
- Keep dependencies updated
