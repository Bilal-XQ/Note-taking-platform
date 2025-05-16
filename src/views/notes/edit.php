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

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';

    if (empty($content)) {
        $error = 'Please enter note content';
    } else {
        $updated = $notesController->updateNote($noteId, $content);

        if ($updated) {
            $success = 'Note updated successfully';
            // Refresh note data
            $note = $notesController->getNote($noteId);
        } else {
            $error = 'Failed to update note. Please try again.';
        }
    }
}

// Get module information
$moduleId = $note['module_id'];
$modules = $notesController->getModules();
$moduleName = '';
foreach ($modules as $module) {
    if ($module['id'] == $moduleId) {
        $moduleName = $module['name'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        .module-name {
            color: #555;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            min-height: 300px;
            resize: vertical;
        }
        .error {
            color: #f44336;
            margin-bottom: 20px;
        }
        .success {
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #2196F3;
            color: white;
        }
        .btn-secondary {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        .btn-container {
            margin-top: 20px;
        }
        .note-timestamp {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Note</h1>
        <div class="module-name">Module: <?php echo htmlspecialchars($moduleName); ?></div>

        <?php if ($note): ?>
            <div class="note-timestamp">
                <?php
                $createdAt = new DateTime($note['created_at']);
                $updatedAt = new DateTime($note['updated_at']);
                echo 'Created: ' . $createdAt->format('F j, Y, g:i a') . '<br>';
                echo 'Last updated: ' . $updatedAt->format('F j, Y, g:i a');
                ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="content">Note Content</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($note['content']); ?></textarea>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">Update Note</button>
                <a href="/main/src/views/student/home.php?module_id=<?php echo $moduleId; ?>" class="btn btn-secondary">Back to Notes</a>
            </div>
        </form>
    </div>
</body>
</html>
