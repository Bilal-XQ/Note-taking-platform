<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/NotesController.php';

// Check if user is logged in
$authController = new AuthController();
if (!$authController->isLoggedIn() || $authController->isAdmin()) {
    header('Location: /main/src/views/student/login.php');
    exit;
}

// Get the current student ID
$studentId = $authController->getCurrentStudentId();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (empty($_POST['title'])) {
        $_SESSION['error'] = "Note title is required";
        header('Location: /main/student/home.php');
        exit;
    }

    if (empty($_POST['content'])) {
        $_SESSION['error'] = "Note content is required";
        header('Location: /main/student/home.php');
        exit;
    }

    if (empty($_POST['module_id'])) {
        $_SESSION['error'] = "Module is required";
        header('Location: /main/student/home.php');
        exit;
    }

    // Get form data
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $moduleId = (int)$_POST['module_id'];
    $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

    // Create note
    $notesController = new NotesController();
    $result = $notesController->createNote($title, $content, $moduleId, $categoryId);

    if ($result) {
        $_SESSION['success'] = "Note created successfully!";
        header('Location: /main/student/home.php?module_id=' . $moduleId . '&action=add_note&status=success');
    } else {
        $_SESSION['error'] = "Failed to create note";
        header('Location: /main/student/home.php?module_id=' . $moduleId);
    }
    exit;
} else {
    // If not POST request, redirect to home
    header('Location: /main/student/home.php');
    exit;
}
?>
