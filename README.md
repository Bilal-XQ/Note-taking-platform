# StudyNotes - Intelligent Note-Taking Platform

<div align="center">
  <img src="https://via.pl**Database & Storage**
- MySQL 8.0+ for primary data
- Redis for caching and sessions
- Local/S3 for file storage

## 📁 Project Structure

```
studynotes/
├── 📁 src/                    # React frontend source
│   ├── 📁 components/         # Reusable UI components
│   ├── 📁 pages/             # Page components
│   ├── 📁 hooks/             # Custom React hooks
│   ├── 📁 contexts/          # React contexts
│   ├── 📁 services/          # API services
│   └── 📁 utils/             # Utility functions
│
├── 📁 backend/               # PHP backend
│   ├── 📁 src/               # PHP source code
│   ├── 📁 config/            # Configuration files
│   ├── 📁 migrations/        # Database migrations
│   └── 📁 tests/             # Backend tests
│
├── 📁 public/                # Static assets
├── 📁 .github/               # GitHub workflows and templates
└── 📁 docs/                  # Documentation
```

## 💻 Development

### Available Scripts

**Frontend**
```bash
npm start          # Start development server
npm run build      # Build for production
npm test           # Run tests
npm run lint       # Lint code
npm run format     # Format code with Prettier
```

**Backend**
```bash
composer test      # Run PHPUnit tests
composer phpcs     # Check code standards
composer phpstan   # Static analysis
php scripts/migrate.php  # Run database migrations
```

## 🎨 Design System

### Color Palette
```css
/* Primary Colors */
--primary-50: #eff6ff;
--primary-500: #3b82f6;
--primary-900: #1e3a8a;

/* Gray Scale */
--gray-50: #f9fafb;
--gray-500: #6b7280;
--gray-900: #111827;

/* Semantic Colors */
--success: #10b981;
--warning: #f59e0b;
--error: #ef4444;
```

### Typography
- **Headings**: Inter font family, 48px → 32px → 20px → 16px
- **Body**: 16px regular, 14px small
- **Code**: JetBrains Mono, 14px

### Spacing
- **Sections**: 80px vertical padding
- **Cards**: 32px internal padding
- **Components**: 16px, 24px, 32px spacing scale

## 🔧 API Documentation

### Authentication
```bash
# Login
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "password"
}

# Register
POST /api/auth/register
{
  "email": "user@example.com",
  "password": "password",
  "first_name": "John",
  "last_name": "Doe"
}
```

### Notes Management
```bash
# Get all notes
GET /api/notes
Authorization: Bearer {token}

# Create note
POST /api/notes
Authorization: Bearer {token}
{
  "title": "My Study Note",
  "content": "Note content here...",
  "course_id": 1,
  "tags": ["chemistry", "organic"]
}

# Update note
PUT /api/notes/{id}
Authorization: Bearer {token}

# Delete note
DELETE /api/notes/{id}
Authorization: Bearer {token}
```

## 🚀 Deployment

### Production Build
```bash
# Build frontend
npm run build

# Optimize backend
composer install --no-dev --optimize-autoloader

# Set production environment
export NODE_ENV=production
```

### Environment Variables
```env
# Database
DB_HOST=localhost
DB_NAME=studynotes
DB_USER=username
DB_PASS=password

# JWT
JWT_SECRET=your-secret-key
JWT_EXPIRY=3600

# File Upload
UPLOAD_MAX_SIZE=10485760
UPLOAD_PATH=uploads/

# External APIs
OPENAI_API_KEY=your-openai-key
```

## 📊 Performance

### Optimization Checklist
- [ ] **Code Splitting**: Lazy load routes and components
- [ ] **Image Optimization**: WebP format, proper sizing
- [ ] **Caching**: Redis for API responses, browser caching
- [ ] **Database**: Proper indexing, query optimization
- [ ] **Bundle Size**: Tree shaking, minimize dependencies

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Process
1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes and add tests
4. Ensure all tests pass: `npm test && composer test`
5. Commit with conventional commits: `git commit -m "feat: add amazing feature"`
6. Push to your fork: `git push origin feature/amazing-feature`
7. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

### Getting Help
- 📖 **Documentation**: Check our [Project Instructions](PROJECT_INSTRUCTIONS.md)
- 🐛 **Bug Reports**: Create an issue on GitHub
- 💡 **Feature Requests**: Start a discussion on GitHub
- 💬 **Community**: Join our development Discord

### Troubleshooting

**Common Issues:**

1. **Port already in use**
```bash
# Kill process on port 3005
npx kill-port 3005

# Or use different port
npm start -- --port 3006
```

2. **Database connection issues**
```bash
# Check MySQL status
mysql -u root -p -e "SELECT 1"

# Verify environment variables
php -r "print_r($_ENV);"
```

3. **Build failures**
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rm -rf node_modules
npm install
```

---

<div align="center">
  <p>Made with ❤️ by the StudyNotes Team</p>
  <p>
    <strong>For complete setup instructions, see <a href="PROJECT_INSTRUCTIONS.md">PROJECT_INSTRUCTIONS.md</a></strong>
  </p>
</div>00x200/3B82F6/FFFFFF?text=StudyNotes" alt="StudyNotes Logo" width="200"/>
  
  <p><strong>Transform your study experience with AI-powered note organization</strong></p>
  
  [![React](https://img.shields.io/badge/React-18.x-blue.svg)](https://reactjs.org/)
  [![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)](https://php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com/)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-teal.svg)](https://tailwindcss.com/)
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
</div>

## ✨ Features

🎯 **Smart Organization** - AI-powered categorization and tagging  
📚 **Course Management** - Organize notes by subjects and semesters  
🤝 **Collaboration** - Share notes and create study groups  
🔍 **Advanced Search** - Find content instantly with intelligent filters  
📊 **Progress Tracking** - Study analytics and performance insights  
📱 **Responsive Design** - Perfect experience on any device  
🌙 **Dark Mode** - Easy on the eyes during late-night study sessions  

## 🚀 Quick Start

### Prerequisites
- Node.js 18+ and npm
- PHP 8.0+ with required extensions
- MySQL 8.0+ or MariaDB 10.5+
- Composer

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/studynotes.git
cd studynotes
```

2. **Install dependencies**
```bash
# Frontend dependencies
npm install

# Backend dependencies
composer install
```

3. **Environment setup**
```bash
# Copy environment file
cp .env.example .env

# Configure your database and API settings in .env
```

4. **Database setup**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE studynotes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php scripts/migrate.php
```

5. **Start development servers**
```bash
# Start React development server (port 3005)
npm run dev

# Start PHP backend server (port 8000)
php -S localhost:8000 public/
```

6. **Open your browser**
Visit `http://localhost:3005` to see the application in action!

## 🏗 Architecture

### Technology Stack

**Frontend**
- React 18 with Hooks and Context
- Tailwind CSS for styling
- Framer Motion for animations
- Axios for API communication
- React Router for navigation

**Backend**
- PHP 8.0+ with modern features
- Slim Framework for API routing
- PDO for database operations
- JWT for authentication
- Composer for dependency management

**Database & Storage**
- MySQL 8.0+ for primary data
- Redis for caching and sessions
- Local/S3 for file storage

CSS — Designs the website layout, colors, and structure

JavaScript — Adds interactivity (modals, alerts, dynamic buttons)

Future Enhancements (AI Integration)
Integrate Artificial Intelligence to:

Generate summaries from students’ notes

Create quizzes to help students review their content