<?php
/**
 * Database Setup Script for StudyNotes
 * Run this file to initialize the database
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = ''; // Default WAMP password is empty
$database = 'my_notes';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '$database' created successfully!<br>";
    
    // Connect to the new database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute SQL file
    $sqlFile = __DIR__ . '/SQL/my_notes.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Remove the 'use my_notes;' line since we're already connected to the database
        $sql = preg_replace('/use\s+my_notes\s*;/i', '', $sql);
        
        // Split queries and execute them
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($queries as $query) {
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
        
        echo "✅ Database tables created successfully!<br>";
    } else {
        echo "❌ SQL file not found: $sqlFile<br>";
    }
    
    // Create a test admin user
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute(['admin', $adminPassword]);
    echo "✅ Default admin user created (username: admin, password: admin123)<br>";
    
    echo "<br>🎉 <strong>Database setup completed successfully!</strong><br>";
    echo "<br>📝 <strong>Access your application:</strong><br>";
    echo "- Landing Page: <a href='http://localhost:3000' target='_blank'>http://localhost:3000</a> (React Dev Server)<br>";
    echo "- Landing Page: <a href='http://localhost/main/' target='_blank'>http://localhost/main/</a> (WAMP Static)<br>";
    echo "- Student Login: <a href='http://localhost/main/src/views/student/login.php' target='_blank'>Student Login</a><br>";
    echo "- Admin Dashboard: <a href='http://localhost/main/admin/dashboard.php' target='_blank'>Admin Dashboard</a><br>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<br>💡 <strong>Make sure WAMP is running and MySQL is accessible!</strong>";
}
?>
