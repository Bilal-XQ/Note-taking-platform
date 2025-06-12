# StudyNotes - Complete Project Instructions

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [Getting Started](#getting-started)
3. [Technology Stack](#technology-stack)
4. [Architecture Overview](#architecture-overview)
5. [Development Setup](#development-setup)
6. [Frontend (React)](#frontend-react)
7. [Backend (PHP)](#backend-php)
8. [Database](#database)
9. [Development Workflow](#development-workflow)
10. [Deployment](#deployment)
11. [Maintenance](#maintenance)

## 🚀 Project Overview

StudyNotes is a modern, intelligent note-taking platform designed to help students organize their course materials, take better notes, and improve their academic performance through AI-powered features. The platform features a cutting-edge React frontend with a robust PHP backend API.

### Key Features
- **Smart Note Organization**: AI-powered categorization and tagging
- **Course Management**: Organize notes by subjects and semesters
- **Collaboration Tools**: Share notes and study groups
- **Search & Discovery**: Advanced search with filters and suggestions
- **Progress Tracking**: Study analytics and performance insights
- **Multi-format Support**: Text, images, PDFs, and rich media

## 🏁 Getting Started

### Prerequisites
- Node.js 18+ and npm/yarn
- PHP 8.0+ with required extensions
- MySQL 8.0+ or MariaDB 10.5+
- Apache/Nginx web server
- Composer for PHP dependencies

### Quick Start
```bash
# Clone the repository
git clone <repository-url>
cd studynotes

# Install frontend dependencies
npm install

# Install backend dependencies
composer install

# Set up environment variables
cp .env.example .env

# Run database migrations
php artisan migrate

# Start development servers
npm run dev        # React frontend on :3005
php -S localhost:8000 public/  # PHP backend on :8000
```

## 🛠 Technology Stack

### Frontend
- **React 18** - Modern UI library with hooks and context
- **Tailwind CSS** - Utility-first CSS framework
- **Framer Motion** - Animation and gesture library
- **Axios** - HTTP client for API communication
- **React Router** - Client-side routing
- **React Hook Form** - Form handling and validation

### Backend
- **PHP 8.0+** - Server-side language
- **Slim Framework** - Lightweight PHP framework
- **PDO** - Database abstraction layer
- **JWT** - Authentication tokens
- **PHPUnit** - Testing framework
- **Composer** - Dependency management

### Database
- **MySQL 8.0+** - Primary database
- **Redis** - Caching and session storage
- **Elasticsearch** - Full-text search (optional)

### DevOps & Tools
- **Webpack** - Module bundling
- **ESLint/Prettier** - Code formatting and linting
- **PHP_CodeSniffer** - PHP code standards
- **GitHub Actions** - CI/CD pipeline
- **Docker** - Containerization

## 🏗 Architecture Overview

```
StudyNotes/
├── Frontend (React)
│   ├── User Interface
│   ├── State Management
│   ├── API Communication
│   └── Client-side Routing
│
├── Backend (PHP API)
│   ├── RESTful API Endpoints
│   ├── Authentication & Authorization
│   ├── Business Logic
│   └── Database Operations
│
├── Database (MySQL)
│   ├── User Management
│   ├── Course & Note Storage
│   ├── Analytics & Tracking
│   └── File Management
│
└── Infrastructure
    ├── Web Server (Apache/Nginx)
    ├── Caching (Redis)
    ├── Storage (Local/S3)
    └── Monitoring & Logs
```

## 💻 Development Setup

### Environment Configuration

1. **Copy environment file:**
```bash
cp .env.example .env
```

2. **Configure environment variables:**
```env
# Database
DB_HOST=localhost
DB_NAME=studynotes
DB_USER=root
DB_PASS=password

# API
API_BASE_URL=http://localhost:8000
JWT_SECRET=your-jwt-secret-here

# File Storage
UPLOAD_PATH=uploads/
MAX_FILE_SIZE=10485760

# External Services
OPENAI_API_KEY=your-openai-key
SENDGRID_API_KEY=your-sendgrid-key
```

3. **Database Setup:**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE studynotes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php scripts/migrate.php
```

4. **Install Dependencies:**
```bash
# Frontend
npm install

# Backend
composer install
```

## ⚛️ Frontend (React)

### Project Structure
```
src/
├── components/           # Reusable UI components
│   ├── UI/              # Basic UI elements (Button, Card, etc.)
│   ├── Navigation/      # Navigation components
│   ├── Hero/           # Landing page hero section
│   ├── Features/       # Feature showcase components
│   ├── Forms/          # Form components
│   └── Layout/         # Layout components
│
├── pages/              # Page components
│   ├── Landing/        # Landing page
│   ├── Dashboard/      # User dashboard
│   ├── Notes/          # Note management
│   └── Profile/        # User profile
│
├── hooks/              # Custom React hooks
│   ├── useAuth.js      # Authentication hook
│   ├── useApi.js       # API communication hook
│   └── useAnimations.js # Animation utilities
│
├── contexts/           # React contexts
│   ├── AuthContext.js  # Authentication state
│   ├── ThemeContext.js # Theme management
│   └── NotesContext.js # Notes state management
│
├── services/           # API services
│   ├── api.js          # API client configuration
│   ├── auth.js         # Authentication services
│   └── notes.js        # Notes API services
│
├── utils/              # Utility functions
│   ├── formatters.js   # Data formatting
│   ├── validators.js   # Form validation
│   └── constants.js    # App constants
│
└── styles/             # Styling files
    ├── globals.css     # Global styles
    └── components.css  # Component-specific styles
```

### Component Development Guidelines

1. **Functional Components with Hooks:**
```jsx
import React, { useState, useEffect } from 'react';

const ExampleComponent = ({ prop1, prop2 }) => {
  const [state, setState] = useState(null);
  
  useEffect(() => {
    // Side effects here
  }, []);

  return (
    <div className="component-container">
      {/* Component JSX */}
    </div>
  );
};

export default ExampleComponent;
```

2. **Custom Hooks:**
```jsx
import { useState, useEffect } from 'react';

const useCustomHook = (dependency) => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Hook logic here
  }, [dependency]);

  return { data, loading, error };
};

export default useCustomHook;
```

3. **Context Usage:**
```jsx
import React, { createContext, useContext, useReducer } from 'react';

const StateContext = createContext();

export const useStateContext = () => {
  const context = useContext(StateContext);
  if (!context) {
    throw new Error('useStateContext must be used within StateProvider');
  }
  return context;
};
```

### Styling Guidelines

1. **Tailwind CSS Classes:**
```jsx
// Use Tailwind utility classes
<div className="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-6">
  <h2 className="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
    Title
  </h2>
</div>
```

2. **Component-specific Styles:**
```css
/* For complex animations or custom styles */
@layer components {
  .floating-card {
    @apply transform transition-all duration-300 hover:scale-105;
    animation: float 3s ease-in-out infinite;
  }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}
```

### State Management

1. **Local State (useState):**
```jsx
const [formData, setFormData] = useState({
  title: '',
  content: '',
  tags: []
});
```

2. **Global State (Context):**
```jsx
// For app-wide state like authentication, theme, etc.
const { user, login, logout } = useAuth();
const { theme, toggleTheme } = useTheme();
```

3. **Server State (Custom Hook):**
```jsx
const { data: notes, loading, error, refetch } = useNotes();
```

## 🐘 Backend (PHP)

### Project Structure
```
backend/
├── public/             # Public web directory
│   ├── index.php      # Application entry point
│   └── .htaccess      # Apache rewrite rules
│
├── src/               # Source code
│   ├── Controllers/   # API controllers
│   ├── Models/        # Data models
│   ├── Services/      # Business logic services
│   ├── Middleware/    # HTTP middleware
│   └── Utils/         # Utility classes
│
├── config/            # Configuration files
│   ├── database.php   # Database configuration
│   ├── routes.php     # API routes
│   └── cors.php       # CORS configuration
│
├── migrations/        # Database migrations
├── tests/            # Unit and integration tests
└── composer.json     # PHP dependencies
```

### API Development Guidelines

1. **RESTful API Structure:**
```php
// GET /api/notes
// POST /api/notes
// GET /api/notes/{id}
// PUT /api/notes/{id}
// DELETE /api/notes/{id}
```

2. **Controller Pattern:**
```php
<?php

namespace App\Controllers;

class NotesController
{
    private $notesService;

    public function __construct(NotesService $notesService)
    {
        $this->notesService = $notesService;
    }

    public function index($request, $response, $args)
    {
        try {
            $notes = $this->notesService->getAllNotes();
            return $response->withJson(['data' => $notes], 200);
        } catch (Exception $e) {
            return $response->withJson(['error' => $e->getMessage()], 500);
        }
    }
}
```

3. **Service Layer:**
```php
<?php

namespace App\Services;

class NotesService
{
    private $notesModel;

    public function __construct(NotesModel $notesModel)
    {
        $this->notesModel = $notesModel;
    }

    public function getAllNotes($userId)
    {
        return $this->notesModel->findByUserId($userId);
    }

    public function createNote($data)
    {
        // Validation and business logic
        return $this->notesModel->create($data);
    }
}
```

4. **Model Pattern:**
```php
<?php

namespace App\Models;

class NotesModel extends BaseModel
{
    protected $table = 'notes';
    protected $fillable = ['title', 'content', 'user_id', 'course_id'];

    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

### Authentication & Security

1. **JWT Authentication:**
```php
<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $token = $request->getHeaderLine('Authorization');
        
        if (!$token) {
            return $response->withJson(['error' => 'Unauthorized'], 401);
        }

        try {
            $decoded = JWT::decode($token, $_ENV['JWT_SECRET'], ['HS256']);
            $request = $request->withAttribute('user', $decoded);
            return $next($request, $response);
        } catch (Exception $e) {
            return $response->withJson(['error' => 'Invalid token'], 401);
        }
    }
}
```

2. **Input Validation:**
```php
<?php

class Validator
{
    public static function validateNote($data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        }

        if (empty($data['content'])) {
            $errors[] = 'Content is required';
        }

        return $errors;
    }
}
```

## 🗄 Database

### Schema Design

1. **Users Table:**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

2. **Notes Table:**
```sql
CREATE TABLE notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    tags JSON,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_course_id (course_id),
    FULLTEXT INDEX idx_content (title, content)
);
```

3. **Courses Table:**
```sql
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50),
    description TEXT,
    semester VARCHAR(50),
    year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Migration System

1. **Migration File Structure:**
```php
<?php
// migrations/001_create_users_table.php

class CreateUsersTable
{
    public function up($pdo)
    {
        $sql = "CREATE TABLE users (...)";
        $pdo->exec($sql);
    }

    public function down($pdo)
    {
        $sql = "DROP TABLE users";
        $pdo->exec($sql);
    }
}
```

2. **Migration Runner:**
```bash
php scripts/migrate.php
```

## 🔄 Development Workflow

### Git Workflow

1. **Branch Strategy:**
```bash
main                    # Production-ready code
├── develop            # Integration branch
├── feature/user-auth  # Feature branches
├── feature/note-editor
└── hotfix/critical-bug
```

2. **Commit Convention:**
```bash
git commit -m "feat: add user authentication system"
git commit -m "fix: resolve note saving issue"
git commit -m "docs: update API documentation"
git commit -m "refactor: improve database queries"
```

### Code Quality

1. **Frontend Linting:**
```bash
npm run lint          # ESLint
npm run format        # Prettier
npm run test          # Jest tests
```

2. **Backend Standards:**
```bash
composer phpcs        # PHP CodeSniffer
composer phpunit      # Unit tests
composer phpstan      # Static analysis
```

### Testing Strategy

1. **Frontend Testing:**
```jsx
// Component testing with React Testing Library
import { render, screen, fireEvent } from '@testing-library/react';
import Button from './Button';

test('renders button with text', () => {
  render(<Button>Click me</Button>);
  expect(screen.getByText('Click me')).toBeInTheDocument();
});
```

2. **Backend Testing:**
```php
<?php
// PHPUnit tests

class NotesControllerTest extends TestCase
{
    public function testCreateNote()
    {
        $response = $this->post('/api/notes', [
            'title' => 'Test Note',
            'content' => 'Test content'
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }
}
```

## 🚀 Deployment

### Production Setup

1. **Environment Configuration:**
```env
# Production environment
NODE_ENV=production
DB_HOST=production-db-host
API_BASE_URL=https://api.studynotes.com
```

2. **Build Process:**
```bash
# Frontend build
npm run build

# Backend optimization
composer install --no-dev --optimize-autoloader
```

3. **Web Server Configuration:**
```apache
# Apache .htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

### Docker Deployment

1. **Dockerfile:**
```dockerfile
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY . .
RUN npm run build

FROM php:8.0-apache AS backend
COPY --from=frontend /app/build /var/www/html
COPY backend/ /var/www/html/
RUN composer install --no-dev
```

2. **Docker Compose:**
```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "80:80"
    environment:
      - DB_HOST=db
    depends_on:
      - db
  
  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: studynotes
      MYSQL_ROOT_PASSWORD: password
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

## 🔧 Maintenance

### Performance Monitoring

1. **Frontend Performance:**
```javascript
// Web Vitals monitoring
import { getCLS, getFID, getFCP, getLCP, getTTFB } from 'web-vitals';

getCLS(console.log);
getFID(console.log);
getFCP(console.log);
getLCP(console.log);
getTTFB(console.log);
```

2. **Backend Monitoring:**
```php
// Performance logging
$start = microtime(true);
// API operation
$duration = microtime(true) - $start;
error_log("API call duration: {$duration}s");
```

### Database Maintenance

1. **Regular Tasks:**
```sql
-- Optimize tables
OPTIMIZE TABLE notes, users, courses;

-- Update statistics
ANALYZE TABLE notes, users, courses;

-- Clean up old sessions
DELETE FROM sessions WHERE expires_at < NOW() - INTERVAL 30 DAY;
```

2. **Backup Strategy:**
```bash
# Daily database backup
mysqldump -u root -p studynotes > backup_$(date +%Y%m%d).sql

# Weekly full backup with uploads
tar -czf backup_full_$(date +%Y%m%d).tar.gz database_backup.sql uploads/
```

### Security Updates

1. **Dependency Updates:**
```bash
# Frontend dependencies
npm audit
npm update

# Backend dependencies
composer audit
composer update
```

2. **Security Checklist:**
- [ ] Regular dependency updates
- [ ] SSL certificate renewal
- [ ] Security header configuration
- [ ] Input validation review
- [ ] Database query optimization
- [ ] File upload security check

## 📞 Support & Resources

### Documentation Links
- [React Documentation](https://reactjs.org/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [PHP Documentation](https://php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)

### Development Resources
- **Code Standards**: Follow PSR-12 for PHP, ESLint for JavaScript
- **API Documentation**: Available at `/docs` endpoint
- **Component Library**: Storybook available in development
- **Testing**: Run tests before each commit

### Getting Help
1. Check existing documentation
2. Search GitHub issues
3. Create detailed bug reports
4. Follow contribution guidelines

---

**Last Updated**: [Current Date]  
**Version**: 1.0.0  
**Maintainer**: StudyNotes Development Team
