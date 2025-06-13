<?php
/**
 * Database Connection Test for StudyNotes
 */

try {
    // Test database connection
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'my_notes';
    
    echo "<h2>🔍 Testing Database Connection...</h2>";
    
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ MySQL server connection: <strong>SUCCESS</strong><br>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Database '$database': <strong>EXISTS</strong><br>";
        
        // Connect to specific database
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Database connection: <strong>SUCCESS</strong><br>";
        
        // Check tables
        echo "<h3>📋 Database Tables:</h3>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            foreach ($tables as $table) {
                $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
                $count = $countStmt->fetchColumn();
                echo "📁 Table: <strong>$table</strong> (Records: $count)<br>";
            }
        } else {
            echo "⚠️ No tables found in database<br>";
        }
        
    } else {
        echo "❌ Database '$database': <strong>NOT FOUND</strong><br>";
        echo "<a href='setup_database.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 Setup Database</a>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong>Database Error:</strong> " . $e->getMessage() . "<br>";
    echo "<br>💡 <strong>Troubleshooting:</strong><br>";
    echo "1. Make sure WAMP is running (green icon)<br>";
    echo "2. Check MySQL service is started<br>";
    echo "3. Verify database credentials<br>";
}

echo "<br><hr><br>";
echo "<h3>🌐 Application Links:</h3>";
echo "🎯 <a href='http://localhost:3002' target='_blank'>React Landing Page</a><br>";
echo "🏠 <a href='http://localhost/main/' target='_blank'>Static Landing Page</a><br>";
echo "👤 <a href='http://localhost/main/src/views/student/login.php' target='_blank'>Student Login</a><br>";
echo "🔧 <a href='http://localhost/main/admin/dashboard.php' target='_blank'>Admin Dashboard</a><br>";
echo "🗄️ <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a><br>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h2, h3 { color: #333; }
a { color: #007cba; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
