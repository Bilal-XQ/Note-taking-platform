<?php
require_once __DIR__ . '/../../controllers/AuthController.php';

$authController = new AuthController();
$authController->logout();

// Redirect to login page
header('Location: /main/src/views/student/login.php');
exit;
?>
