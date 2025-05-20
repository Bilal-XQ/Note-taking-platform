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

$quizController = new QuizController();
$notesController = new NotesController();

// Check if we're viewing results or submitting a quiz
$viewingResults = isset($_GET['view_results']) && isset($_GET['quiz_id']);
$submittingQuiz = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id']) && isset($_POST['answers']);

if (!$viewingResults && !$submittingQuiz) {
    header('Location: /main/src/views/student/home.php');
    exit;
}

if ($viewingResults) {
    $quizId = (int)$_GET['quiz_id'];

    // Get the quiz
    $quiz = $quizController->getQuiz($quizId);
    if (!$quiz) {
        header('Location: /main/src/views/student/quizzes.php');
        exit;
    }

    // Get the latest attempt
    $latestAttempt = $quizController->getLatestAttempt($quizId);
    if (!$latestAttempt) {
        // No attempt found, redirect to take the quiz
        header('Location: /main/src/views/notes/generate_quiz.php?id=' . $quiz['note_id']);
        exit;
    }

    // Reconstruct results from the latest attempt
    // This is a simplified version - in a real implementation, you would store and retrieve the full results
    $results = [
        'success' => true,
        'score' => $latestAttempt['score'],
        'total_questions' => $latestAttempt['total_questions'],
        'percentage' => $latestAttempt['score'],
        'results' => [] // We don't have detailed results stored, so this will be empty
    ];
} else {
    // Submitting a quiz
    $quizId = (int)$_POST['quiz_id'];
    $answers = $_POST['answers'];

    // Submit the quiz and get results
    $results = $quizController->submitQuizAttempt($quizId, $answers);
}

if (!$results['success']) {
    header('Location: /main/src/views/student/home.php');
    exit;
}

// Get the quiz details
$quiz = $quizController->getQuiz($quizId);
if (!$quiz) {
    header('Location: /main/src/views/student/home.php');
    exit;
}

// Get the note details
$note = $notesController->getNote($quiz['note_id']);
if (!$note) {
    header('Location: /main/src/views/student/home.php');
    exit;
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
    <title>Quiz Results</title>
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
            text-decoration: none;
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
        .results-container {
            margin-top: 30px;
        }
        .results-header {
            background-color: #2196F3;
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .results-body {
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .score-summary {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .score-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .score-value {
            font-size: 48px;
            font-weight: 700;
            color: #2196F3;
            margin-bottom: 10px;
        }
        .score-percent {
            font-size: 24px;
            color: #555;
            margin-bottom: 15px;
        }
        .score-message {
            font-size: 16px;
            color: #333;
        }
        .question {
            margin-bottom: 30px;
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
            display: flex;
            align-items: center;
        }
        .question-status {
            margin-right: 10px;
            font-size: 18px;
        }
        .question-status.correct {
            color: #4CAF50;
        }
        .question-status.incorrect {
            color: #f44336;
        }
        .answers {
            margin-bottom: 15px;
        }
        .answer {
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .answer.selected {
            background-color: #e3f2fd;
            border: 1px solid #2196F3;
        }
        .answer.correct {
            background-color: #e8f5e9;
            border: 1px solid #4CAF50;
        }
        .answer.incorrect {
            background-color: #ffebee;
            border: 1px solid #f44336;
        }
        .answer-marker {
            margin-right: 10px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }
        .answer-text {
            flex: 1;
        }
        .explanation {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }
        .explanation-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quiz Results</h1>
        <div class="module-name">Module: <?php echo htmlspecialchars($moduleName); ?></div>

        <div class="note-info">
            <div class="note-title"><?php echo htmlspecialchars($note['title']); ?></div>
            <p class="note-content"><?php echo htmlspecialchars(substr($note['content'], 0, 200)) . (strlen($note['content']) > 200 ? '...' : ''); ?></p>
        </div>

        <div class="results-container">
            <div class="results-header">
                <span><?php echo htmlspecialchars($quiz['title']); ?></span>
                <span><?php echo $results['total_questions']; ?> Questions</span>
            </div>
            <div class="results-body">
                <div class="score-summary">
                    <div class="score-title">Your Score</div>
                    <div class="score-value"><?php echo $results['score']; ?>/<?php echo $results['total_questions']; ?></div>
                    <div class="score-percent"><?php echo round($results['percentage']); ?>%</div>
                    <div class="score-message">
                        <?php
                        $percentage = $results['percentage'];
                        if ($percentage >= 90) {
                            echo 'Excellent! You have a great understanding of this material.';
                        } elseif ($percentage >= 70) {
                            echo 'Good job! You have a solid understanding of this material.';
                        } elseif ($percentage >= 50) {
                            echo 'Not bad! You have a basic understanding of this material.';
                        } else {
                            echo 'You might want to review this material again.';
                        }
                        ?>
                    </div>
                </div>

                <?php if (!empty($results['results'])): ?>
                    <?php foreach ($results['results'] as $index => $result): ?>
                        <div class="question">
                            <div class="question-text">
                                <div class="question-status <?php echo $result['is_correct'] ? 'correct' : 'incorrect'; ?>">
                                    <i class="fas <?php echo $result['is_correct'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                </div>
                                <?php echo ($index + 1) . '. ' . htmlspecialchars($result['question_text']); ?>
                            </div>
                            <div class="answers">
                                <?php 
                                // Get the question from the quiz
                                $question = null;
                                foreach ($quiz['questions'] as $q) {
                                    if ($q['id'] == $result['question_id']) {
                                        $question = $q;
                                        break;
                                    }
                                }

                                if ($question): 
                                    foreach ($question['answers'] as $answer):
                                        $isSelected = $answer['id'] == $result['selected_answer_id'];
                                        $isCorrect = $answer['is_correct'];
                                        $class = '';

                                        if ($isSelected && $isCorrect) {
                                            $class = 'selected correct';
                                        } elseif ($isSelected && !$isCorrect) {
                                            $class = 'selected incorrect';
                                        } elseif (!$isSelected && $isCorrect) {
                                            $class = 'correct';
                                        }
                                ?>
                                    <div class="answer <?php echo $class; ?>">
                                        <div class="answer-marker">
                                            <?php if ($isSelected): ?>
                                                <i class="fas <?php echo $isCorrect ? 'fa-check' : 'fa-times'; ?>"></i>
                                            <?php elseif ($isCorrect): ?>
                                                <i class="fas fa-check"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="answer-text"><?php echo htmlspecialchars($answer['answer_text']); ?></div>
                                    </div>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            <?php if (!empty($result['explanation'])): ?>
                                <div class="explanation">
                                    <div class="explanation-title">Explanation:</div>
                                    <?php echo htmlspecialchars($result['explanation']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="simplified-results">
                        <p>Detailed results are not available for this attempt. Take the quiz again to see detailed feedback.</p>

                        <?php foreach ($quiz['questions'] as $index => $question): ?>
                            <div class="question">
                                <div class="question-text">
                                    <?php echo ($index + 1) . '. ' . htmlspecialchars($question['question_text']); ?>
                                </div>
                                <div class="answers">
                                    <?php foreach ($question['answers'] as $answer): ?>
                                        <div class="answer <?php echo $answer['is_correct'] ? 'correct' : ''; ?>">
                                            <div class="answer-marker">
                                                <?php if ($answer['is_correct']): ?>
                                                    <i class="fas fa-check"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="answer-text"><?php echo htmlspecialchars($answer['answer_text']); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php 
                                // Find explanation from the correct answer
                                $explanation = '';
                                foreach ($question['answers'] as $answer) {
                                    if ($answer['is_correct'] && !empty($answer['explanation'])) {
                                        $explanation = $answer['explanation'];
                                        break;
                                    }
                                }
                                if (!empty($explanation)): 
                                ?>
                                    <div class="explanation">
                                        <div class="explanation-title">Explanation:</div>
                                        <?php echo htmlspecialchars($explanation); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="btn-container">
            <a href="/main/src/views/notes/generate_quiz.php?id=<?php echo $note['id']; ?>" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i> Take Quiz Again
            </a>
            <a href="/main/src/views/notes/edit.php?id=<?php echo $note['id']; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Note
            </a>
        </div>
    </div>
</body>
</html>
