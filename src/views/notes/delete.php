<?php
require_once __DIR__ . '/../../controllers/NotesController.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

// Check if user is logged in
$authController = new AuthController();
if (!$authController->isLoggedIn()) {
    header('Location: /main/src/views/student/login.php');
    exit;
}

// Get note ID from URL parameter
$noteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$noteId) {
    header('Location: /main/src/views/student/home.php');
    exit;
}

$notesController = new NotesController();
$note = $notesController->getNote($noteId);

// Check if note exists and belongs to the user
if (!$note) {
    header('Location: /main/src/views/student/home.php');
    exit;
}

// Get module ID to redirect back to the correct module page
$moduleId = $note['module_id'];

// Delete the note
$deleted = $notesController->deleteNote($noteId);

// Redirect back to the home page with the correct module selected
header('Location: /main/src/views/student/home.php?module_id=' . $moduleId);
exit;
?>
