# Contributing to StudyNotes

Thank you for your interest in contributing to StudyNotes! This document provides guidelines for contributing to the project.

## 🚀 Getting Started

### Prerequisites
- Node.js 18+ and npm
- PHP 8.0+ with required extensions
- MySQL 8.0+ or MariaDB 10.5+
- Composer
- Git

### Development Setup

1. **Fork and clone the repository**
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

3. **Set up your development environment**
```bash
# Copy environment file
cp .env.example .env

# Configure your local database settings
# Edit .env with your database credentials
```

4. **Set up the database**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE studynotes_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php scripts/migrate.php
```

5. **Start development servers**
```bash
# Start React development server
npm run dev

# Start PHP backend server (in another terminal)
php -S localhost:8000 public/
```

## 📋 How to Contribute

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find that you don't need to create one. When creating a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples to demonstrate the steps**
- **Describe the behavior you observed and what behavior you expected**
- **Include screenshots if possible**
- **Include your environment details** (OS, browser, PHP version, etc.)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a step-by-step description of the suggested enhancement**
- **Provide specific examples to demonstrate the steps**
- **Describe the current behavior and expected behavior**
- **Explain why this enhancement would be useful**

### Pull Requests

1. **Create a feature branch**
```bash
git checkout -b feature/amazing-feature
```

2. **Make your changes**
- Follow the coding standards outlined below
- Add tests for new functionality
- Update documentation as needed

3. **Test your changes**
```bash
# Run frontend tests
npm test

# Run backend tests
composer test

# Run linting
npm run lint
composer phpcs
```

4. **Commit your changes**
```bash
git commit -m "feat: add amazing feature"
```

5. **Push to your fork**
```bash
git push origin feature/amazing-feature
```

6. **Open a Pull Request**
- Use a clear title and description
- Reference any related issues
- Include screenshots for UI changes

## 📝 Coding Standards

### Frontend (React/JavaScript)

#### Code Style
- Use **ESLint** and **Prettier** for consistent formatting
- Follow **React best practices** with functional components and hooks
- Use **TypeScript** for type safety where applicable

#### Component Guidelines
```jsx
// ✅ Good: Functional component with hooks
import React, { useState, useEffect } from 'react';

const ExampleComponent = ({ title, onAction }) => {
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    // Side effects here
  }, []);

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg p-6">
      <h2 className="text-xl font-semibold mb-4">{title}</h2>
      {/* Component content */}
    </div>
  );
};

export default ExampleComponent;
```

#### Naming Conventions
- **Components**: PascalCase (`UserProfile.jsx`)
- **Functions**: camelCase (`handleSubmit`)
- **Constants**: UPPER_SNAKE_CASE (`API_BASE_URL`)
- **Files**: kebab-case for utilities (`api-client.js`)

#### CSS/Tailwind Guidelines
- Use **Tailwind utility classes** for styling
- Create **component-specific styles** only when necessary
- Follow **mobile-first responsive design**
- Use **CSS variables** for consistent theming

```jsx
// ✅ Good: Tailwind classes with responsive design
<div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 md:p-6 lg:p-8">
  <h2 className="text-lg md:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-white">
    Title
  </h2>
</div>
```

### Backend (PHP)

#### Code Style
- Follow **PSR-12** coding standards
- Use **PHP_CodeSniffer** for code validation
- Use **PHPStan** for static analysis

#### Class Structure
```php
<?php

namespace App\Controllers;

use App\Services\NotesService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotesController
{
    private NotesService $notesService;

    public function __construct(NotesService $notesService)
    {
        $this->notesService = $notesService;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $notes = $this->notesService->getAllNotes();
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200)
                ->withBody($this->createJsonBody(['data' => $notes]));
        } catch (\Exception $e) {
            return $this->handleError($response, $e);
        }
    }
}
```

#### Naming Conventions
- **Classes**: PascalCase (`NotesController`)
- **Methods**: camelCase (`getAllNotes`)
- **Variables**: camelCase (`$userNotes`)
- **Constants**: UPPER_SNAKE_CASE (`MAX_FILE_SIZE`)

#### Database Guidelines
- Use **prepared statements** for all queries
- Follow **database naming conventions** (snake_case)
- Add **proper indexes** for performance
- Use **transactions** for data consistency

```php
// ✅ Good: Prepared statement with error handling
public function findByUserId(int $userId): array
{
    try {
        $stmt = $this->db->prepare(
            "SELECT id, title, content, created_at 
             FROM notes 
             WHERE user_id = ? 
             ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        throw new \RuntimeException("Failed to fetch notes");
    }
}
```

### Documentation

#### Code Comments
```php
/**
 * Retrieves all notes for a specific user with pagination
 *
 * @param int $userId The ID of the user
 * @param int $page The page number (1-based)
 * @param int $limit The number of items per page
 * @return array Array of notes with pagination metadata
 * @throws \InvalidArgumentException When parameters are invalid
 * @throws \RuntimeException When database operation fails
 */
public function getUserNotes(int $userId, int $page = 1, int $limit = 20): array
{
    // Implementation here
}
```

#### README Updates
- Update relevant documentation when adding features
- Include code examples for new APIs
- Add screenshots for UI changes

## 🧪 Testing Guidelines

### Frontend Testing

#### Component Tests
```jsx
import { render, screen, fireEvent } from '@testing-library/react';
import { ThemeProvider } from '../contexts/ThemeContext';
import Button from './Button';

describe('Button Component', () => {
  test('renders with correct text', () => {
    render(
      <ThemeProvider>
        <Button>Click me</Button>
      </ThemeProvider>
    );
    
    expect(screen.getByText('Click me')).toBeInTheDocument();
  });

  test('calls onClick handler when clicked', () => {
    const handleClick = jest.fn();
    
    render(
      <ThemeProvider>
        <Button onClick={handleClick}>Click me</Button>
      </ThemeProvider>
    );
    
    fireEvent.click(screen.getByText('Click me'));
    expect(handleClick).toHaveBeenCalledTimes(1);
  });
});
```

#### Integration Tests
```jsx
test('user can create a new note', async () => {
  render(<App />);
  
  // Navigate to notes section
  fireEvent.click(screen.getByText('My Notes'));
  
  // Create new note
  fireEvent.click(screen.getByText('Add Note'));
  fireEvent.change(screen.getByLabelText('Title'), {
    target: { value: 'Test Note' }
  });
  fireEvent.change(screen.getByLabelText('Content'), {
    target: { value: 'Test content' }
  });
  fireEvent.click(screen.getByText('Save'));
  
  // Verify note was created
  await screen.findByText('Test Note');
});
```

### Backend Testing

#### Unit Tests
```php
<?php

namespace Tests\Unit;

use App\Models\Note;
use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
{
    public function testCanCreateNote(): void
    {
        $note = new Note([
            'title' => 'Test Note',
            'content' => 'Test content',
            'user_id' => 1
        ]);

        $this->assertEquals('Test Note', $note->getTitle());
        $this->assertEquals('Test content', $note->getContent());
        $this->assertEquals(1, $note->getUserId());
    }

    public function testValidatesRequiredFields(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        new Note([
            'content' => 'Test content'
            // Missing required title
        ]);
    }
}
```

#### Integration Tests
```php
<?php

namespace Tests\Integration;

use App\Controllers\NotesController;
use PHPUnit\Framework\TestCase;

class NotesControllerTest extends TestCase
{
    public function testCanCreateNote(): void
    {
        $request = $this->createJsonRequest('POST', '/api/notes', [
            'title' => 'Integration Test Note',
            'content' => 'Test content'
        ]);

        $response = $this->app->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Integration Test Note', $data['data']['title']);
    }
}
```

## 🔄 Git Workflow

### Branch Naming
- **Feature branches**: `feature/user-authentication`
- **Bug fixes**: `fix/login-error`
- **Documentation**: `docs/api-documentation`
- **Hotfixes**: `hotfix/critical-security-fix`

### Commit Messages
Follow [Conventional Commits](https://www.conventionalcommits.org/):

```bash
# Format
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]

# Examples
feat: add user authentication system
fix: resolve note saving issue in Safari
docs: update API documentation for notes endpoint
style: format code with prettier
refactor: simplify database connection logic
test: add unit tests for note validation
chore: update dependencies to latest versions
```

### Types
- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code formatting (no logic changes)
- **refactor**: Code refactoring
- **test**: Adding or updating tests
- **chore**: Maintenance tasks

## 🚦 CI/CD Pipeline

### GitHub Actions
The project uses GitHub Actions for continuous integration:

```yaml
# .github/workflows/ci.yml
name: CI

on: [push, pull_request]

jobs:
  frontend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
        with:
          node-version: '18'
      - run: npm ci
      - run: npm run lint
      - run: npm test
      - run: npm run build

  backend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
      - run: composer install
      - run: composer phpcs
      - run: composer phpstan
      - run: composer test
```

### Quality Checks
All pull requests must pass:
- ✅ Frontend linting (ESLint)
- ✅ Frontend tests (Jest)
- ✅ Backend code standards (PHP_CodeSniffer)
- ✅ Backend static analysis (PHPStan)
- ✅ Backend tests (PHPUnit)
- ✅ Build process completes successfully

## 📚 Resources

### Learning Resources
- [React Documentation](https://reactjs.org/docs)
- [PHP The Right Way](https://phptherightway.com/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Jest Testing Framework](https://jestjs.io/docs)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)

### Code Style Guides
- [Airbnb React/JSX Style Guide](https://github.com/airbnb/javascript/tree/master/react)
- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)

### Tools
- [ESLint](https://eslint.org/) - JavaScript linting
- [Prettier](https://prettier.io/) - Code formatting
- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) - PHP code standards
- [PHPStan](https://phpstan.org/) - PHP static analysis

## 🤝 Community

### Communication
- **GitHub Discussions** - For questions and feature discussions
- **GitHub Issues** - For bug reports and feature requests
- **Discord** - For real-time community chat

### Code of Conduct
We are committed to providing a welcoming and inspiring community for all. Please read our [Code of Conduct](CODE_OF_CONDUCT.md).

### Recognition
Contributors will be recognized in:
- README.md contributors section
- Release notes
- Annual contributor appreciation posts

## 📄 License

By contributing to StudyNotes, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to StudyNotes! 🚀
