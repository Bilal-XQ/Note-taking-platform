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

// Handle form submission for generating a summary
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    $result = $notesController->generateAISummary($noteId);
    
    if ($result) {
        $success = 'Summary generated successfully!';
        // Refresh the note data to get the updated summary
        $note = $notesController->getNote($noteId);
    } else {
        $error = 'Failed to generate summary. Please try again.';
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
    <title>Generate Summary</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-top: 0;
            color: #333;
            font-size: 24px;
            font-weight: 600;
        }
        .module-name {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .note-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .note-title {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .note-content {
            color: #555;
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0;
        }
        .error {
            color: #f44336;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            color: #4CAF50;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            margin-right: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .btn i {
            margin-right: 8px;
        }
        .btn-primary {
            background-color: #2196F3;
            color: white;
        }
        .btn-primary:hover {
            background-color: #1976D2;
        }
        .btn-secondary {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        .btn-secondary:hover {
            background-color: #e0e0e0;
        }
        .btn-success {
            background-color: #4CAF50;
            color: white;
        }
        .btn-success:hover {
            background-color: #388E3C;
        }
        .btn-container {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .summary-container {
            margin-top: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .summary-header {
            background-color: #2196F3;
            color: white;
            padding: 15px;
            font-weight: 600;
            font-size: 18px;
        }
        .summary-body {
            padding: 20px;
            line-height: 1.6;
        }
        .summary-footer {
            padding: 15px 20px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e0e0e0;
        }
        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2196F3;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            color: #555;
            font-size: 16px;
        }
        .no-summary {
            text-align: center;
            padding: 40px 0;
            color: #555;
        }
        .no-summary i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }
        .no-summary-text {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Summary</h1>
        <div class="module-name">Module: <?php echo htmlspecialchars($moduleName); ?></div>

        <div class="note-info">
            <div class="note-title"><?php echo htmlspecialchars($note['title']); ?></div>
            <p class="note-content"><?php echo htmlspecialchars(substr($note['content'], 0, 200)) . (strlen($note['content']) > 200 ? '...' : ''); ?></p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="btn-container">
            <?php if (empty($note['ai_summary'])): ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="generate">
                    <button type="submit" class="btn btn-success" id="generateBtn">
                        <i class="fas fa-brain"></i> Generate Summary
                    </button>
                </form>
            <?php else: ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="generate">
                    <button type="submit" class="btn btn-success" id="regenerateBtn">
                        <i class="fas fa-sync-alt"></i> Regenerate Summary
                    </button>
                </form>
            <?php endif; ?>
            <a href="/main/src/views/notes/edit.php?id=<?php echo $noteId; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Note
            </a>
        </div>

        <?php if (!empty($note['ai_summary'])): ?>
            <div class="summary-container">
                <div class="summary-header">
                    <span>AI-Generated Summary</span>
                </div>
                <div class="summary-body">
                    <?php echo nl2br(htmlspecialchars($note['ai_summary'])); ?>
                </div>
                <div class="summary-footer">
                    <span>Generated: <?php echo isset($note['summary_generated_at']) ? date('F j, Y, g:i a', strtotime($note['summary_generated_at'])) : 'Unknown'; ?></span>
                    <a href="/main/src/views/student/ai_summaries.php" class="btn btn-primary">
                        <i class="fas fa-list"></i> View All Summaries
                    </a>
                </div>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate'): ?>
            <div class="loading">
                <div class="loading-spinner"></div>
                <div class="loading-text">Generating summary... This may take a moment.</div>
            </div>
            <script>
                // Refresh the page after 5 seconds to check if the summary has been generated
                setTimeout(function() {
                    window.location.reload();
                }, 5000);
            </script>
        <?php else: ?>
            <div class="no-summary">
                <i class="fas fa-brain"></i>
                <div class="no-summary-text">No summary generated yet</div>
                <p>Click the "Generate Summary" button to create a summary based on your note content.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const generateBtn = document.getElementById('generateBtn');
            const regenerateBtn = document.getElementById('regenerateBtn');
            
            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
                });
            }
            
            if (regenerateBtn) {
                regenerateBtn.addEventListener('click', function() {
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Regenerating...';
                });
            }
        });
    </script>
</body>
</html>