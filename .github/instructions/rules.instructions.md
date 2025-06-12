---
applyTo: '**'
---

# StudyNotes - Intelligent Note-Taking Platform

## Project Overview

StudyNotes is a modern web application designed to help students organize their course materials, take better notes, and improve their academic performance through AI-powered features. The platform combines a PHP backend with a React frontend to deliver a seamless user experience.

## Technology Stack

### Frontend (React)
- **React 18** with hooks and modern patterns
- **Tailwind CSS** for styling with custom design system
- **Framer Motion** for smooth animations
- **Lucide React** for consistent iconography
- **React Intersection Observer** for scroll-based animations

### Backend (PHP)
- **PHP 8+** with object-oriented programming
- **MySQL** database for data persistence
- **Composer** for dependency management
- **Gemini API** integration for AI features

### Development Tools
- **PostCSS** for CSS processing
- **npm/yarn** for frontend package management
- **WAMP/XAMPP** for local development

## Architecture

### MVC Pattern
- **Models**: Data layer (`src/models/`)
- **Views**: Presentation layer (`src/views/`)
- **Controllers**: Business logic (`src/controllers/`)

### Component Structure
- **UI Components**: Reusable design system components
- **Feature Components**: Specific functionality components
- **Context Providers**: State management for themes and scroll

## Coding Standards

### PHP Standards
- Use **PSR-4** autoloading standards
- Follow **camelCase** for variables and methods
- Use **PascalCase** for class names
- Implement proper error handling with try-catch blocks
- Use prepared statements for database queries
- Comment complex business logic

### React/JavaScript Standards
- Use **functional components** with hooks
- Follow **camelCase** for variables and functions
- Use **PascalCase** for component names
- Implement **TypeScript-style** prop validation
- Use **semantic HTML** elements
- Prefer **composition over inheritance**

### CSS/Tailwind Standards
- Use **mobile-first** responsive design
- Follow **BEM methodology** for custom CSS
- Implement consistent **spacing system** (8px grid)
- Use **semantic color names** in design tokens
- Maintain **accessibility standards** (WCAG 2.1)

### Database Standards
- Use **snake_case** for table and column names
- Implement proper **foreign key constraints**
- Use **indexes** for frequently queried columns
- Follow **normalization** principles
- Include **created_at** and **updated_at** timestamps

## File Organization

### Frontend Structure
```
src/
├── components/          # Reusable UI components
│   ├── UI/             # Base design system components
│   ├── Hero/           # Landing page hero section
│   ├── Features/       # Feature showcase components
│   └── Navigation/     # Header and navigation
├── contexts/           # React context providers
├── hooks/              # Custom React hooks
└── App.js             # Main application component
```

### Backend Structure
```
src/
├── models/            # Data models and entities
├── controllers/       # Business logic controllers
├── views/            # PHP templates and pages
└── config/           # Configuration files
```

## Development Workflow

### Branch Strategy
- **main**: Production-ready code
- **develop**: Integration branch for features
- **feature/***: Individual feature development
- **hotfix/***: Critical bug fixes

### Commit Messages
- Use **conventional commits** format
- Examples:
  - `feat: add AI quiz generation`
  - `fix: resolve note deletion bug`
  - `style: update button hover states`
  - `docs: update API documentation`

### Code Review Process
1. Create feature branch from `develop`
2. Implement changes with tests
3. Submit pull request with description
4. Address review feedback
5. Merge after approval

## Key Features

### Core Functionality
- **User Authentication**: Secure login/logout system
- **Note Management**: Create, edit, delete, and organize notes
- **Module Organization**: Course-based note categorization
- **AI Integration**: Automated summaries and quiz generation
- **Responsive Design**: Mobile-first user interface

### AI Features
- **Smart Summaries**: AI-generated note summaries
- **Quiz Generation**: Automated quiz creation from notes
- **Content Analysis**: Intelligent content categorization

## API Integration

### Gemini AI API
- Used for generating summaries and quizzes
- Implements proper error handling and rate limiting
- Stores API responses for caching

### Database Schema
```sql
-- Core tables
students (id, name, email, password, created_at)
modules (id, student_id, name, created_at)
notes (id, module_id, title, content, created_at, updated_at)
quizzes (id, note_id, questions, answers, created_at)
```

## Performance Considerations

### Frontend Optimization
- Use **React.memo** for expensive components
- Implement **lazy loading** for route-based code splitting
- Optimize **image loading** with proper sizing
- Use **CSS-in-JS** sparingly to avoid runtime overhead

### Backend Optimization
- Implement **database indexing** on frequently queried columns
- Use **connection pooling** for database connections
- Cache **AI API responses** to reduce external calls
- Optimize **SQL queries** to avoid N+1 problems

## Security Guidelines

### Authentication
- Use **bcrypt** for password hashing
- Implement **session management** with proper timeouts
- Use **CSRF tokens** for form submissions
- Validate all **user inputs** server-side

### Data Protection
- Sanitize **user-generated content**
- Use **prepared statements** for SQL queries
- Implement **rate limiting** for API endpoints
- Log **security events** for monitoring

## Testing Strategy

### Frontend Testing
- **Unit tests** for utility functions
- **Component tests** for UI interactions
- **Integration tests** for user flows
- **E2E tests** for critical paths

### Backend Testing
- **Unit tests** for models and controllers
- **Integration tests** for database operations
- **API tests** for endpoint validation
- **Security tests** for vulnerability assessment

## Deployment

### Production Environment
- Use **environment variables** for sensitive configuration
- Implement **automated backups** for database
- Set up **monitoring** and logging
- Use **HTTPS** for all communications

### CI/CD Pipeline
1. **Code quality** checks (linting, formatting)
2. **Automated testing** suite execution
3. **Security scanning** for vulnerabilities
4. **Build and deployment** to staging/production

## Maintenance

### Regular Tasks
- **Update dependencies** monthly
- **Monitor performance** metrics
- **Review security** logs
- **Backup database** daily
- **Update documentation** as needed

### Monitoring
- Track **user engagement** metrics
- Monitor **API response times**
- Alert on **error rates** exceeding thresholds
- Log **user feedback** for improvements

## Domain Knowledge

### Educational Context
- Understanding of **student workflows** and study habits
- Knowledge of **academic calendars** and deadlines
- Familiarity with **note-taking methodologies**
- Awareness of **accessibility requirements** in education

### AI Integration Best Practices
- Understand **Gemini API limitations** and costs
- Implement **fallback mechanisms** for AI failures
- Design **user-friendly error messages** for AI issues
- Consider **privacy implications** of AI processing

## Contributing Guidelines

### Getting Started
1. **Clone** the repository
2. **Install dependencies** (npm install, composer install)
3. **Set up environment** variables
4. **Run database migrations**
5. **Start development servers**

### Pull Request Process
1. **Fork** the repository
2. **Create feature branch**
3. **Write tests** for new functionality
4. **Update documentation** as needed
5. **Submit pull request** with clear description

This document serves as the foundation for maintaining code quality, consistency, and project direction for the StudyNotes application.