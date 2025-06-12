# Database Guidelines

## Schema Design

### Table Structure Standards
```sql
-- Use consistent naming conventions
-- Tables: plural nouns (students, notes, modules)
-- Columns: snake_case (created_at, student_id)
-- Indexes: descriptive names (idx_notes_student_created)

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_students_email (email),
    INDEX idx_students_created (created_at)
);

-- Modules table
CREATE TABLE modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#3B82F6', -- Hex color code
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_modules_student (student_id),
    INDEX idx_modules_created (created_at)
);

-- Notes table
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    summary TEXT, -- AI-generated summary
    is_favorite BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    INDEX idx_notes_module (module_id),
    INDEX idx_notes_created (created_at),
    INDEX idx_notes_updated (updated_at),
    INDEX idx_notes_favorite (is_favorite),
    FULLTEXT KEY ft_notes_search (title, content)
);
```

### Indexing Strategy
```sql
-- Primary indexes for foreign keys
ALTER TABLE notes ADD INDEX idx_notes_module_id (module_id);
ALTER TABLE modules ADD INDEX idx_modules_student_id (student_id);

-- Composite indexes for common queries
ALTER TABLE notes ADD INDEX idx_notes_module_created (module_id, created_at);
ALTER TABLE notes ADD INDEX idx_notes_module_updated (module_id, updated_at);

-- Full-text search indexes
ALTER TABLE notes ADD FULLTEXT(title, content);

-- Covering indexes for specific queries
ALTER TABLE notes ADD INDEX idx_notes_list_cover (module_id, deleted_at, id, title, created_at);
```

## Query Optimization

### Efficient Queries
```sql
-- Get recent notes with module information
SELECT 
    n.id,
    n.title,
    n.created_at,
    n.is_favorite,
    m.name AS module_name,
    m.color AS module_color
FROM notes n
INNER JOIN modules m ON n.module_id = m.id
WHERE m.student_id = ? 
    AND n.deleted_at IS NULL
    AND m.deleted_at IS NULL
ORDER BY n.created_at DESC
LIMIT 20;

-- Search notes with relevance scoring
SELECT 
    n.id,
    n.title,
    n.created_at,
    m.name AS module_name,
    MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance
FROM notes n
INNER JOIN modules m ON n.module_id = m.id
WHERE m.student_id = ?
    AND MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE)
    AND n.deleted_at IS NULL
ORDER BY relevance DESC, n.updated_at DESC
LIMIT 50;

-- Get note statistics per module
SELECT 
    m.id,
    m.name,
    COUNT(n.id) AS note_count,
    MAX(n.updated_at) AS last_updated
FROM modules m
LEFT JOIN notes n ON m.id = n.module_id AND n.deleted_at IS NULL
WHERE m.student_id = ? AND m.deleted_at IS NULL
GROUP BY m.id, m.name
ORDER BY last_updated DESC;
```

### Pagination Queries
```sql
-- Cursor-based pagination (recommended for large datasets)
SELECT 
    n.id,
    n.title,
    n.created_at,
    m.name AS module_name
FROM notes n
INNER JOIN modules m ON n.module_id = m.id
WHERE m.student_id = ?
    AND n.created_at < ? -- cursor value
    AND n.deleted_at IS NULL
ORDER BY n.created_at DESC
LIMIT 20;

-- Offset-based pagination (for smaller datasets)
SELECT 
    n.id,
    n.title,
    n.created_at,
    m.name AS module_name
FROM notes n
INNER JOIN modules m ON n.module_id = m.id
WHERE m.student_id = ? AND n.deleted_at IS NULL
ORDER BY n.created_at DESC
LIMIT 20 OFFSET ?;
```

## Migration Scripts

### Version Control for Schema
```sql
-- migrations/001_create_initial_tables.sql
-- Migration: Create initial tables
-- Date: 2024-01-15

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- migrations/002_add_soft_deletes.sql
-- Migration: Add soft delete support
-- Date: 2024-01-20

ALTER TABLE students ADD COLUMN deleted_at TIMESTAMP NULL;
ALTER TABLE modules ADD COLUMN deleted_at TIMESTAMP NULL;
ALTER TABLE notes ADD COLUMN deleted_at TIMESTAMP NULL;

-- migrations/003_add_note_favorites.sql
-- Migration: Add favorite notes feature
-- Date: 2024-01-25

ALTER TABLE notes ADD COLUMN is_favorite BOOLEAN DEFAULT FALSE;
ALTER TABLE notes ADD INDEX idx_notes_favorite (is_favorite);
```

### Migration Runner
```php
class MigrationRunner 
{
    private PDO $db;
    
    public function __construct(PDO $db) 
    {
        $this->db = $db;
        $this->createMigrationsTable();
    }
    
    public function runMigrations(): void 
    {
        $migrationFiles = glob(__DIR__ . '/migrations/*.sql');
        sort($migrationFiles);
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            
            if (!$this->hasBeenRun($filename)) {
                $this->runMigration($file);
                $this->recordMigration($filename);
                echo "Ran migration: {$filename}\n";
            }
        }
    }
    
    private function runMigration(string $file): void 
    {
        $sql = file_get_contents($file);
        $statements = explode(';', $sql);
        
        $this->db->beginTransaction();
        
        try {
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $this->db->exec($statement);
                }
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Migration failed: {$file} - " . $e->getMessage());
        }
    }
}
```

## Data Integrity

### Constraints and Validation
```sql
-- Ensure data consistency with constraints
ALTER TABLE notes 
ADD CONSTRAINT chk_title_length 
CHECK (CHAR_LENGTH(title) > 0 AND CHAR_LENGTH(title) <= 255);

ALTER TABLE students 
ADD CONSTRAINT chk_email_format 
CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$');

ALTER TABLE modules 
ADD CONSTRAINT chk_color_format 
CHECK (color REGEXP '^#[0-9A-Fa-f]{6}$');

-- Ensure referential integrity
ALTER TABLE notes 
ADD CONSTRAINT fk_notes_module 
FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE;

ALTER TABLE modules 
ADD CONSTRAINT fk_modules_student 
FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE;
```

### Triggers for Audit Trail
```sql
-- Create audit table
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(64) NOT NULL,
    record_id INT NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON,
    new_values JSON,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_audit_table_record (table_name, record_id),
    INDEX idx_audit_created (created_at)
);

-- Audit trigger for notes
DELIMITER $$
CREATE TRIGGER notes_audit_update
    AFTER UPDATE ON notes
    FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, action, old_values, new_values, user_id)
    VALUES (
        'notes',
        NEW.id,
        'UPDATE',
        JSON_OBJECT('title', OLD.title, 'content', OLD.content),
        JSON_OBJECT('title', NEW.title, 'content', NEW.content),
        @current_user_id
    );
END$$
DELIMITER ;
```

## Backup and Recovery

### Backup Strategy
```sql
-- Daily backup script
mysqldump \
    --single-transaction \
    --routines \
    --triggers \
    --databases studynotes \
    --result-file=/backups/studynotes_$(date +%Y%m%d_%H%M%S).sql

-- Weekly full backup with compression
mysqldump \
    --single-transaction \
    --routines \
    --triggers \
    --databases studynotes \
    | gzip > /backups/weekly/studynotes_$(date +%Y%m%d).sql.gz
```

### Point-in-Time Recovery
```sql
-- Enable binary logging in my.cnf
[mysqld]
log-bin=mysql-bin
expire_logs_days=7
max_binlog_size=100M

-- Restore to specific point in time
mysql studynotes < /backups/studynotes_20240115_000000.sql
mysqlbinlog \
    --start-datetime="2024-01-15 00:00:00" \
    --stop-datetime="2024-01-15 14:30:00" \
    mysql-bin.000001 | mysql studynotes
```

## Performance Monitoring

### Query Analysis
```sql
-- Enable slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
SET GLOBAL log_queries_not_using_indexes = 'ON';

-- Analyze query performance
EXPLAIN FORMAT=JSON 
SELECT n.*, m.name 
FROM notes n 
JOIN modules m ON n.module_id = m.id 
WHERE m.student_id = 123;

-- Check index usage
SHOW INDEX FROM notes;
SHOW TABLE STATUS LIKE 'notes';
```

### Performance Optimization Queries
```sql
-- Find unused indexes
SELECT 
    s.table_schema,
    s.table_name,
    s.index_name,
    s.cardinality
FROM information_schema.statistics s
LEFT JOIN information_schema.index_statistics i 
    ON s.table_schema = i.table_schema 
    AND s.table_name = i.table_name 
    AND s.index_name = i.index_name
WHERE s.table_schema = 'studynotes' 
    AND i.index_name IS NULL 
    AND s.index_name != 'PRIMARY';

-- Find slow queries
SELECT 
    query_time,
    lock_time,
    rows_sent,
    rows_examined,
    sql_text
FROM mysql.slow_log 
WHERE start_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY query_time DESC
LIMIT 10;
```
