# PHP Development Guidelines

## Code Structure

### Class Organization
```php
<?php

namespace StudyNotes\Models;

class Note 
{
    // Constants first
    private const MAX_TITLE_LENGTH = 255;
    
    // Properties
    private int $id;
    private string $title;
    private string $content;
    private DateTime $createdAt;
    
    // Constructor
    public function __construct(array $data = []) 
    {
        $this->setFromArray($data);
    }
    
    // Public methods
    public function save(): bool 
    {
        return $this->id ? $this->update() : $this->create();
    }
    
    // Private methods
    private function validate(): void 
    {
        if (empty($this->title)) {
            throw new InvalidArgumentException('Title is required');
        }
    }
}
```

### Method Guidelines
- Use **type hints** for parameters and return values
- Keep methods **focused and small** (< 20 lines)
- Use **meaningful names** that describe the action
- Return **consistent types** (bool for success/failure)

### Error Handling
```php
// Use exceptions for error conditions
public function deleteNote(int $noteId): bool 
{
    try {
        $stmt = $this->db->prepare("DELETE FROM notes WHERE id = ? AND student_id = ?");
        $result = $stmt->execute([$noteId, $this->studentId]);
        
        if (!$result) {
            throw new DatabaseException("Failed to delete note");
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Note deletion failed: " . $e->getMessage());
        throw new ServiceException("Unable to delete note", 0, $e);
    }
}
```

## Database Interactions

### Prepared Statements
```php
// Always use prepared statements
public function getNotesByModule(int $moduleId): array 
{
    $stmt = $this->db->prepare("
        SELECT id, title, content, created_at 
        FROM notes 
        WHERE module_id = ? AND deleted_at IS NULL
        ORDER BY created_at DESC
    ");
    
    $stmt->execute([$moduleId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle multiple parameters safely
public function searchNotes(int $studentId, string $query): array 
{
    $stmt = $this->db->prepare("
        SELECT n.*, m.name as module_name
        FROM notes n
        INNER JOIN modules m ON n.module_id = m.id
        WHERE m.student_id = ? 
        AND (n.title LIKE ? OR n.content LIKE ?)
        ORDER BY n.updated_at DESC
    ");
    
    $searchTerm = "%{$query}%";
    $stmt->execute([$studentId, $searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

### Transaction Management
```php
public function createNoteWithTags(array $noteData, array $tags): Note 
{
    $this->db->beginTransaction();
    
    try {
        // Create the note
        $note = new Note($noteData);
        $noteId = $note->save();
        
        // Add tags
        foreach ($tags as $tag) {
            $this->addTagToNote($noteId, $tag);
        }
        
        $this->db->commit();
        return $note;
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}
```

## Security Best Practices

### Input Validation
```php
public function validateNoteInput(array $input): array 
{
    $errors = [];
    
    // Required fields
    if (empty($input['title'])) {
        $errors['title'] = 'Title is required';
    } elseif (strlen($input['title']) > 255) {
        $errors['title'] = 'Title must be less than 255 characters';
    }
    
    // Sanitize content
    if (!empty($input['content'])) {
        $input['content'] = $this->sanitizeContent($input['content']);
    }
    
    if (!empty($errors)) {
        throw new ValidationException('Validation failed', $errors);
    }
    
    return $input;
}

private function sanitizeContent(string $content): string 
{
    // Allow basic HTML tags but sanitize
    $allowedTags = '<p><br><strong><em><ul><ol><li><h1><h2><h3>';
    return strip_tags($content, $allowedTags);
}
```

### Authentication & Authorization
```php
class AuthController 
{
    public function login(string $email, string $password): array 
    {
        $user = $this->getUserByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new AuthenticationException('Invalid credentials');
        }
        
        $sessionData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'login_time' => time()
        ];
        
        $_SESSION['user'] = $sessionData;
        return $sessionData;
    }
    
    public function requireAuth(): array 
    {
        if (!isset($_SESSION['user'])) {
            throw new UnauthorizedException('Authentication required');
        }
        
        // Check session timeout (e.g., 24 hours)
        if (time() - $_SESSION['user']['login_time'] > 86400) {
            $this->logout();
            throw new UnauthorizedException('Session expired');
        }
        
        return $_SESSION['user'];
    }
}
```

## API Integration

### Gemini AI Integration
```php
class GeminiService 
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    
    public function generateSummary(string $content): string 
    {
        $prompt = "Summarize the following study notes in 3-4 bullet points:\n\n" . $content;
        
        $response = $this->makeRequest('models/gemini-pro:generateContent', [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);
        
        return $this->extractTextFromResponse($response);
    }
    
    private function makeRequest(string $endpoint, array $data): array 
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/' . $endpoint . '?key=' . $this->apiKey,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new ApiException('API request failed: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new ApiException("API returned HTTP {$httpCode}");
        }
        
        return json_decode($response, true);
    }
}
```

## Configuration Management

### Environment Configuration
```php
// config/app.php
<?php

return [
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'name' => $_ENV['DB_NAME'] ?? 'studynotes',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
    ],
    
    'gemini' => [
        'api_key' => $_ENV['GEMINI_API_KEY'] ?? '',
    ],
    
    'app' => [
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    ],
];
```

### Database Connection
```php
class Database 
{
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO 
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/app.php';
            $db = $config['database'];
            
            $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4";
            
            self::$instance = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        
        return self::$instance;
    }
}
```

## Testing Guidelines

### Unit Testing
```php
<?php

use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase 
{
    public function testCreateNote(): void 
    {
        $data = [
            'title' => 'Test Note',
            'content' => 'This is a test note',
            'module_id' => 1
        ];
        
        $note = new Note($data);
        
        $this->assertEquals('Test Note', $note->getTitle());
        $this->assertEquals('This is a test note', $note->getContent());
        $this->assertEquals(1, $note->getModuleId());
    }
    
    public function testValidateRequiredFields(): void 
    {
        $this->expectException(ValidationException::class);
        
        $note = new Note([
            'content' => 'Content without title'
        ]);
        
        $note->validate();
    }
}
```

## Performance Optimization

### Query Optimization
```php
// Use indexes for frequently queried columns
public function getRecentNotes(int $studentId, int $limit = 10): array 
{
    // Ensure indexes on student_id and created_at
    $stmt = $this->db->prepare("
        SELECT n.id, n.title, n.created_at, m.name as module_name
        FROM notes n
        INNER JOIN modules m ON n.module_id = m.id
        WHERE m.student_id = ?
        ORDER BY n.created_at DESC
        LIMIT ?
    ");
    
    $stmt->execute([$studentId, $limit]);
    return $stmt->fetchAll();
}

// Use pagination for large datasets
public function getPaginatedNotes(int $studentId, int $page = 1, int $perPage = 20): array 
{
    $offset = ($page - 1) * $perPage;
    
    $stmt = $this->db->prepare("
        SELECT n.*, m.name as module_name
        FROM notes n
        INNER JOIN modules m ON n.module_id = m.id
        WHERE m.student_id = ?
        ORDER BY n.updated_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([$studentId, $perPage, $offset]);
    return $stmt->fetchAll();
}
```

### Caching Strategy
```php
class CacheService 
{
    public function remember(string $key, int $ttl, callable $callback): mixed 
    {
        $cached = $this->get($key);
        
        if ($cached !== null) {
            return $cached;
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): void 
    {
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        file_put_contents($this->getCachePath($key), serialize($data));
    }
    
    public function get(string $key): mixed 
    {
        $path = $this->getCachePath($key);
        
        if (!file_exists($path)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($path));
        
        if (time() > $data['expires']) {
            unlink($path);
            return null;
        }
        
        return $data['value'];
    }
}
