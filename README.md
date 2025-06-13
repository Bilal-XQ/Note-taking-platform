# StudyNotes - Intelligent Note-Taking Platform

<div align="center">
  <img src="https://via.placeholder.com/200x200/3B82F6/FFFFFF?text=StudyNotes" alt="StudyNotes Logo" width="200"/>
  
  <p><strong>Transform your study experience with AI-powered note organization and intelligent learning tools</strong></p>
  
  [![React](https://img.shields.io/badge/React-18.x-blue.svg)](https://reactjs.org/)
  [![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)](https://php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com/)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-teal.svg)](https://tailwindcss.com/)
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
</div>

---

## Overview

StudyNotes is a comprehensive web application designed to revolutionize how students organize, manage, and interact with their academic materials. By combining modern web technologies with artificial intelligence, it provides an intuitive platform for enhanced learning and productivity.

## Key Features

- **🎯 AI-Powered Organization**: Intelligent categorization and tagging of notes using Gemini AI
- **📚 Course Management**: Systematic organization by subjects, modules, and academic periods
- **🤖 Smart Summaries**: Automated note summarization for quick review
- **📝 Quiz Generation**: AI-generated quizzes from your notes for self-assessment
- **🔍 Advanced Search**: Intelligent content discovery with powerful filtering
- **📊 Progress Analytics**: Comprehensive study tracking and performance insights
- **🌙 Modern Interface**: Responsive design with dark mode support
- **🔒 Secure Platform**: Enterprise-grade security with proper data protection

## Technology Stack

### Frontend Architecture
- **React 18** - Modern component-based UI framework
- **Tailwind CSS** - Utility-first styling with custom design system
- **Framer Motion** - Smooth animations and transitions
- **Lucide React** - Consistent iconography
- **React Intersection Observer** - Scroll-based animations

### Backend Infrastructure  
- **PHP 8+** - Object-oriented server-side programming
- **MySQL 8.0+** - Robust relational database management
- **Composer** - Dependency management and autoloading
- **Gemini AI API** - Advanced natural language processing

### Development Tools
- **PostCSS** - Advanced CSS processing
- **npm** - Package management for frontend dependencies
- **WAMP/XAMPP** - Local development environment

## Project Structure

```
studynotes/
├── src/
│   ├── components/           # React UI components
│   │   ├── UI/              # Base design system
│   │   ├── Navigation/      # Header and navigation
│   │   ├── Hero/            # Landing page sections
│   │   └── Features/        # Feature components
│   ├── contexts/            # React context providers
│   ├── hooks/               # Custom React hooks
│   ├── controllers/         # PHP business logic
│   ├── models/              # Data layer classes
│   └── views/               # PHP templates
├── public/                  # Static assets
├── config/                  # Configuration files
├── SQL/                     # Database schemas
└── tests/                   # Test suites
```

## Quick Start Guide

### Prerequisites

Ensure your development environment meets the following requirements:

- **Node.js** 18.0+ with npm
- **PHP** 8.0+ with extensions: PDO, MySQL, mbstring, openssl
- **MySQL** 8.0+ or MariaDB 10.5+
- **Composer** for PHP dependency management

### Installation

1. **Repository Setup**
   ```bash
   git clone https://github.com/Bilal-XQ/Note-taking-platform.git
   cd Note-taking-platform
   ```

2. **Install Dependencies**
   ```bash
   # Frontend dependencies
   npm install
   
   # Backend dependencies  
   composer install
   ```

3. **Environment Configuration**
   ```bash
   # Create environment file
   cp .env.example .env
   ```

### API Configuration

**Important**: This application requires a Gemini AI API key for full functionality.

1. **Obtain API Key**
   - Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
   - Authenticate with your Google account
   - Generate a new API key
   - Securely store the generated key

2. **Configure Environment**
   ```env
   # Open .env file and configure:
   GEMINI_API_KEY=your_actual_api_key_here
   DB_HOST=localhost
   DB_NAME=studynotes
   DB_USER=your_db_user
   DB_PASS=your_db_password
   ```

3. **Security Notice**
   - Never commit API keys to version control
   - The `.env` file is automatically ignored by Git
   - Use environment variables in production deployments
   - Monitor API usage regularly

### Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE studynotes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Initialize schema (if migration script exists)
php setup_database.php
```

### Launch Application

```bash
# Start React development server (port 3002)
npm start

# Start PHP backend server (port 8000)
php -S localhost:8000

# Or use the provided batch script (Windows)
./start_studynotes.bat
```

Access the application at `http://localhost:3002`

## Architecture Overview

### Design Patterns
- **MVC Architecture**: Clear separation of concerns with Models, Views, and Controllers
- **Component-Based UI**: Modular React components for maintainability
- **RESTful API**: Standard HTTP methods for client-server communication
- **Responsive Design**: Mobile-first approach with Tailwind CSS

### Data Flow
1. **Frontend**: React components manage UI state and user interactions
2. **API Layer**: PHP controllers handle business logic and data validation  
3. **Database**: MySQL stores structured data with proper relationships
4. **AI Integration**: Gemini API processes natural language for intelligent features

## API Documentation

### Authentication Endpoints
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "student@university.edu",
  "password": "secure_password"
}
```

```http
POST /api/auth/register  
Content-Type: application/json

{
  "email": "student@university.edu", 
  "password": "secure_password",
  "first_name": "John",
  "last_name": "Doe"
}
```

### Notes Management
```http
GET /api/notes
Authorization: Bearer {jwt_token}

POST /api/notes
Authorization: Bearer {jwt_token}
Content-Type: application/json

{
  "title": "Advanced Algorithms",
  "content": "Detailed notes on sorting algorithms...",
  "module_id": 1,
  "tags": ["algorithms", "computer-science"]
}
```

### AI Features
```http
POST /api/notes/{id}/summary
Authorization: Bearer {jwt_token}

POST /api/notes/{id}/quiz
Authorization: Bearer {jwt_token}
```

## Development Workflow

### Available Commands

**Frontend Development**
```bash
npm start              # Development server with hot reload
npm run build          # Production build
npm test               # Run test suite
npm run lint           # Code quality checks
```

**Backend Development**  
```bash
composer install       # Install PHP dependencies
composer test          # Run PHPUnit tests
composer phpcs         # Code standards verification
```

**Database Operations**
```bash
php setup_database.php # Initialize database schema
php test_connection.php # Verify database connectivity
```

## Security & Best Practices

### API Key Security
- Never commit API keys to version control
- Use environment variables for all sensitive configuration
- The `.env` file is automatically ignored by Git
- Rotate API keys regularly in production
- Monitor API usage for unusual activity

### Development Guidelines
- Always use `.env.example` as a template for environment setup
- Keep dependencies updated for security patches
- Use HTTPS in production environments
- Implement proper input validation and sanitization
- Follow PHP security best practices for database queries

### Production Deployment
- Use dedicated environment variables instead of `.env` files
- Enable error logging but disable error display
- Implement rate limiting for API endpoints
- Use proper database user permissions
- Set up monitoring and alerting

## Performance Optimization

### Frontend Optimization
- **Code Splitting**: Lazy load routes and components for faster initial load
- **Image Optimization**: Use WebP format with proper sizing
- **Bundle Analysis**: Regular auditing to minimize JavaScript payload
- **Caching Strategies**: Implement browser caching for static assets

### Backend Optimization  
- **Database Indexing**: Optimize queries with proper indexes
- **Connection Pooling**: Efficient database connection management
- **API Caching**: Cache frequently accessed endpoints
- **Query Optimization**: Prevent N+1 problems and optimize SQL

## Contributing

We welcome contributions from the community. Please follow these guidelines:

### Development Process
1. **Fork** the repository to your GitHub account
2. **Create** a feature branch: `git checkout -b feature/enhancement-name`
3. **Implement** your changes with appropriate tests
4. **Ensure** all tests pass: `npm test && composer test`
5. **Commit** using conventional commits: `git commit -m "feat: add new feature"`
6. **Push** to your fork: `git push origin feature/enhancement-name`
7. **Submit** a Pull Request with detailed description

### Code Standards
- Follow PSR-4 autoloading standards for PHP
- Use ESLint and Prettier for JavaScript/React code
- Maintain test coverage above 80%
- Document complex business logic
- Follow semantic versioning for releases

## Deployment

### Production Build
```bash
# Build optimized frontend
npm run build

# Install production dependencies
composer install --no-dev --optimize-autoloader

# Set environment
export NODE_ENV=production
```

### Environment Variables
```env
# Database Configuration
DB_HOST=production-db-host
DB_NAME=studynotes_prod
DB_USER=production_user
DB_PASS=secure_password

# Security
JWT_SECRET=production-jwt-secret
JWT_EXPIRY=3600

# File Upload
UPLOAD_MAX_SIZE=10485760
UPLOAD_PATH=/var/www/uploads/

# External APIs
GEMINI_API_KEY=production-api-key
```

### Docker Deployment

This project includes Docker configuration for containerized deployment:

```bash
# Build and start services
docker compose up --build

# Production deployment
docker compose -f docker-compose.prod.yml up -d
```

**Services:**
- **php-app**: PHP 8.2 FPM with required extensions
- **mysql-db**: MySQL with persistent storage
- **nginx**: Web server for production serving

## Support & Documentation

### Getting Help
- 📖 **Documentation**: Comprehensive guides in [PROJECT_INSTRUCTIONS.md](PROJECT_INSTRUCTIONS.md)
- 🐛 **Bug Reports**: Submit issues through GitHub Issues
- 💡 **Feature Requests**: Start discussions for new features
- 📧 **Contact**: Reach out to the development team

### Troubleshooting

**Common Issues:**

1. **Port Conflicts**
   ```bash
   # Find process using port
   netstat -ano | findstr :3002
   
   # Kill process (Windows)
   taskkill /PID <process_id> /F
   ```

2. **Database Connection Issues**
   ```bash
   # Test database connectivity
   php test_connection.php
   
   # Verify MySQL service
   sc query mysql
   ```

3. **Node Module Issues**
   ```bash
   # Clear cache and reinstall
   npm cache clean --force
   rm -rf node_modules package-lock.json
   npm install
   ```

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for detailed terms and conditions.

## Acknowledgments

- Built with modern web technologies and best practices
- AI capabilities powered by Google's Gemini API
- UI components inspired by modern design systems
- Community contributions and feedback

---

<div align="center">
  <p><strong>StudyNotes Team</strong> - Empowering students through intelligent technology</p>
  <p>
    For detailed setup and deployment instructions, see <a href="PROJECT_INSTRUCTIONS.md">PROJECT_INSTRUCTIONS.md</a>
  </p>
</div>
