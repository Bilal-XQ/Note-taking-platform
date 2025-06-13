# StudyNotes - Quick Start Guide

## 🚀 Your Application is Now Running!

### Access Points:

1. **React Landing Page (Development)**
   - URL: http://localhost:3002
   - Features: Modern React interface with animations
   - Hot reload enabled for development

2. **Static Landing Page (WAMP)**
   - URL: http://localhost/main/
   - Features: Production-ready static version
   - Served directly by Apache

3. **Student Portal**
   - Login: http://localhost/main/src/views/student/login.php
   - Registration: Available through the login page
   - Features: Note-taking, AI summaries, quiz generation

4. **Admin Dashboard**
   - URL: http://localhost/main/admin/dashboard.php
   - Default credentials: admin / admin123
   - Features: Student management, system overview

## 🔧 Development Workflow

### Frontend Development (React)
```bash
cd c:\wamp64\www\main
npm start                    # Start development server
npm run build               # Build for production
npm test                    # Run tests
```

### Backend Development (PHP)
- Files are served directly by WAMP
- No build step required
- Edit PHP files and refresh browser

### Database Management
- Access phpMyAdmin: http://localhost/phpmyadmin
- Database name: `my_notes`
- Default credentials: root / (empty password)

## 🛠 Development Commands

```bash
# Start React development server
npm start

# Install new dependencies
npm install package-name
composer require vendor/package

# Build for production
npm run build

# Run tests
npm test
```

## 📁 Key Files and Directories

### Frontend (React)
- `src/App.js` - Main React application
- `src/components/` - Reusable UI components
- `src/contexts/` - React context providers
- `public/` - Static assets

### Backend (PHP)
- `src/controllers/` - Business logic
- `src/models/` - Data models
- `src/views/` - PHP templates
- `config/` - Configuration files

### Database
- `SQL/my_notes.sql` - Database schema
- `setup_database.php` - Database initialization script

## 🎯 Next Steps

1. **Customize the Landing Page**
   - Edit components in `src/components/`
   - Modify styles in `src/index.css`
   - Add new features as needed

2. **Configure AI Features**
   - Add your Gemini API key to `.env`
   - Test AI summaries and quiz generation
   - Customize AI prompts in controllers

3. **Deploy to Production**
   - Run `npm run build`
   - Upload files to web server
   - Configure production database
   - Set environment variables

## 🐛 Troubleshooting

### React Server Not Starting
```bash
# Clear cache and reinstall
npm install
rm -rf node_modules package-lock.json
npm install
```

### PHP Errors
- Check WAMP is running (green icon)
- Verify database connection in `config/database.php`
- Check PHP error logs in WAMP

### Database Issues
- Run `setup_database.php` again
- Check MySQL is running in WAMP
- Verify database credentials

## 📖 Documentation

- Project Instructions: `PROJECT_INSTRUCTIONS.md`
- Contributing Guidelines: `CONTRIBUTING.md`
- Security Guidelines: `SECURITY.md`
- Change Log: `CHANGELOG.md`

## 🔐 Security Notes

- Change default admin password immediately
- Add your own JWT secret to `.env`
- Enable HTTPS in production
- Regular security updates

---

**Happy coding! 🎉**

Your StudyNotes application is ready for development and testing.
