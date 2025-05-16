<?php
session_start();
$login_error = '';
$login_username = '';
$login_remember = false;

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /main/index.php');
    exit;
}

// Load DB config
$db_config = require __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $login_username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $login_remember = isset($_POST['remember']);

    if ($login_username === '' || $password === '') {
        $login_error = 'Please enter both username and password.';
    } else {
        // DB connection
        $db = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['dbname']);
        if ($db->connect_error) {
            $login_error = 'Database connection failed.';
        } else {
            $sql = "SELECT id, 'admin' as role FROM admins WHERE username = ? AND password = ? UNION ALL SELECT id, 'student' as role FROM students WHERE username = ? AND password = ? LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ssss', $login_username, $password, $login_username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                session_regenerate_id(true);
                if ($row['role'] === 'admin') {
                    $_SESSION['admin_id'] = $row['id'];
                    if ($login_remember) setcookie('remembered_user', $login_username, time() + 60*60*24*30, "/");
                    else setcookie('remembered_user', '', time() - 3600, "/");
                    header('Location: /main/admin/dashboard.php');
                    exit;
                } else {
                    $_SESSION['student_id'] = $row['id'];
                    if ($login_remember) setcookie('remembered_user', $login_username, time() + 60*60*24*30, "/");
                    else setcookie('remembered_user', '', time() - 3600, "/");
                    header('Location: /main/student/home.php');
                    exit;
                }
            } else {
                $login_error = 'Invalid username or password.';
            }
            $stmt->close();
            $db->close();
        }
    }
} else {
    // Prefill from cookie if exists
    if (isset($_COOKIE['remembered_user'])) {
        $login_username = $_COOKIE['remembered_user'];
        $login_remember = true;
    }
} 