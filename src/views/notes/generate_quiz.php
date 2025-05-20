<?php
require_once __DIR__ . '/../../controllers/QuizController.php';
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
$quizController = new QuizController();
$note = $notesController->getNote($noteId);

// Check if note exists and belongs to the user
if (!$note) {
    header('Location: /main/src/views/student/home.php');
    exit;
}

$error = '';
$success = '';
$quizId = null;
$quiz = null;

// Handle form submission for generating a quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    $quizId = $quizController->generateQuiz($noteId);
    
    if ($quizId) {
        $success = 'Quiz generated successfully!';
        $quiz = $quizController->getQuiz($quizId);
    } else {
        $error = 'Failed to generate quiz. Please try again.';
    }
}

// Handle form submission for regenerating a quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'regenerate' && isset($_POST['quiz_id'])) {
    $oldQuizId = (int)$_POST['quiz_id'];
    
    // Delete the old quiz
    $quizController->deleteQuiz($oldQuizId);
    
    // Generate a new quiz
    $quizId = $quizController->generateQuiz($noteId);
    
    if ($quizId) {
        $success = 'Quiz regenerated successfully!';
        $quiz = $quizController->getQuiz($quizId);
    } else {
        $error = 'Failed to regenerate quiz. Please try again.';
    }
}

// Get existing quizzes for this note
$quizzes = $quizController->getQuizzesByNote($noteId);

// If there are existing quizzes and no quiz is currently being viewed, show the most recent one
if (empty($quiz) && !empty($quizzes)) {
    $quiz = $quizController->getQuiz($quizzes[0]['id']);
    $quizId = $quizzes[0]['id'];
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
    <title>Generate Quiz</title>
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
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        .btn-danger:hover {
            background-color: #d32f2f;
        }
        .btn-container {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .quiz-container {
            margin-top: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .quiz-header {
            background-color: #2196F3;
            color: white;
            padding: 15px;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .quiz-body {
            padding: 20px;
        }
        .question {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .question:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .question-text {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .answers {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .answer {
            padding: 10px 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }
        .answer:hover {
            background-color: #e0e0e0;
        }
        .answer input[type="radio"] {
            margin-right: 10px;
        }
        .answer-text {
            flex: 1;
        }
        .quiz-footer {
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
        .no-quiz {
            text-align: center;
            padding: 40px 0;
            color: #555;
        }
        .no-quiz i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }
        .no-quiz-text {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Quiz</h1>
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
            <?php if (empty($quizzes)): ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="generate">
                    <button type="submit" class="btn btn-success" id="generateBtn">
                        <i class="fas fa-brain"></i> Generate Quiz
                    </button>
                </form>
            <?php else: ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="regenerate">
                    <input type="hidden" name="quiz_id" value="<?php echo $quizId; ?>">
                    <button type="submit" class="btn btn-success" id="regenerateBtn">
                        <i class="fas fa-sync-alt"></i> Regenerate Quiz
                    </button>
                </form>
            <?php endif; ?>
            <a href="/main/src/views/notes/edit.php?id=<?php echo $noteId; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Note
            </a>
        </div>

        <?php if ($quiz): ?>
            <div class="quiz-container">
                <div class="quiz-header">
                    <span><?php echo htmlspecialchars($quiz['title']); ?></span>
                    <span><?php echo count($quiz['questions']); ?> Questions</span>
                </div>
                <form id="quizForm" action="/main/src/views/notes/take_quiz.php" method="POST">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                    <div class="quiz-body">
                        <?php foreach ($quiz['questions'] as $index => $question): ?>
                            <div class="question">
                                <div class="question-text"><?php echo ($index + 1) . '. ' . htmlspecialchars($question['question_text']); ?></div>
                                <div class="answers">
                                    <?php foreach ($question['answers'] as $answer): ?>
                                        <label class="answer">
                                            <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo $answer['id']; ?>" required>
                                            <span class="answer-text"><?php echo htmlspecialchars($answer['answer_text']); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="quiz-footer">
                        <span><?php echo date('F j, Y', strtotime($quiz['created_at'])); ?></span>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Submit Answers
                        </button>
                    </div>
                </form>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && ($_POST['action'] === 'generate' || $_POST['action'] === 'regenerate')): ?>
            <div class="loading">
                <div class="loading-spinner"></div>
                <div class="loading-text">Generating quiz... This may take a moment.</div>
            </div>
            <script>
                // Refresh the page after 5 seconds to check if the quiz has been generated
                setTimeout(function() {
                    window.location.reload();
                }, 5000);
            </script>
        <?php elseif (empty($quizzes)): ?>
            <div class="no-quiz">
                <i class="fas fa-brain"></i>
                <div class="no-quiz-text">No quizzes generated yet</div>
                <p>Click the "Generate Quiz" button to create a quiz based on your note content.</p>
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