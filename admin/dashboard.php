<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /main/index.php');
    exit;
}

// Load admin controller to get student data
require_once __DIR__ . '/../src/controllers/AdminController.php';

// Include the dashboard view with all the styling and components
require_once __DIR__ . '/../src/views/admin/dashboard.php'; 