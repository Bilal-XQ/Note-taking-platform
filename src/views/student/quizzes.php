<?php
require_once __DIR__ . '/../../controllers/QuizController.php';
require_once __DIR__ . '/../../controllers/NotesController.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

// Check if user is logged in
$authController = new AuthController();
if (!$authController->isLoggedIn() || $authController->isAdmin()) {
    header('Location: /main/src/views/student/login.php');
    exit;
}

$quizController = new QuizController();
$notesController = new NotesController();
$studentName = $authController->getCurrentStudentName();
$modules = $notesController->getModules();

// Get all quizzes
$allQuizzes = $quizController->getAllQuizzes();

// Group quizzes by module
$quizzesByModule = [];
foreach ($allQuizzes as $quiz) {
    $moduleId = $quiz['module_id'];
    if (!isset($quizzesByModule[$moduleId])) {
        $quizzesByModule[$moduleId] = [
            'name' => $quiz['module_name'],
            'quizzes' => []
        ];
    }
    $quizzesByModule[$moduleId]['quizzes'][] = $quiz;
}

// Handle search and filter
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterModule = isset($_GET['module']) ? (int)$_GET['module'] : 0;

// Filter quizzes based on search term and module filter
if (!empty($searchTerm) || $filterModule > 0) {
    foreach ($quizzesByModule as $moduleId => &$moduleData) {
        $filteredQuizzes = [];
        foreach ($moduleData['quizzes'] as $quiz) {
            $matchesSearch = empty($searchTerm) || 
                            stripos($quiz['title'], $searchTerm) !== false || 
                            stripos($quiz['note_title'], $searchTerm) !== false;
            $matchesModule = $filterModule === 0 || $moduleId == $filterModule;

            if ($matchesSearch && $matchesModule) {
                $filteredQuizzes[] = $quiz;
            }
        }
        $moduleData['quizzes'] = $filteredQuizzes;
    }
    // Remove empty modules
    $quizzesByModule = array_filter($quizzesByModule, function($moduleData) {
        return !empty($moduleData['quizzes']);
    });
}

// Get success/error messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyNotes - Quizzes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/main/public/css/modern-dashboard.css">
    <link rel="stylesheet" href="/main/public/css/sidebar-toggle.css">
    <style>
        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
        }

        .filter-select {
            min-width: 150px;
        }

        .module-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            background-color: var(--bg-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow-sm);
        }

        .module-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 0.75rem;
        }

        .module-name {
            font-weight: 600;
            font-size: 1.125rem;
        }

        .quiz-count {
            margin-left: auto;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius-lg);
            background-color: var(--success-light);
            color: var(--success-color);
        }

        .quiz-card {
            background-color: var(--bg-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .quiz-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }

        .quiz-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .quiz-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .quiz-meta-item {
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .quiz-body {
            padding: 1.25rem;
        }

        .quiz-note {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .quiz-note-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .quiz-score {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .score-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            color: white;
        }

        .score-high {
            background-color: #4CAF50;
        }

        .score-medium {
            background-color: #FFC107;
        }

        .score-low {
            background-color: #F44336;
        }

        .score-none {
            background-color: #9E9E9E;
        }

        .score-text {
            font-size: 0.875rem;
            color: var(--text-color);
        }

        .quiz-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .quiz-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-take {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-take:hover {
            background-color: var(--primary-dark);
        }

        .btn-results {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-results:hover {
            background-color: var(--secondary-dark);
        }

        .btn-regenerate {
            background-color: var(--bg-light);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .btn-regenerate:hover {
            background-color: var(--bg-hover);
        }
    </style>
</head>
<body>
<!-- Top Navigation Bar -->
<header class="topbar">
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>
    <a href="/main/student/home.php" class="logo">
        <i class="fas fa-book-open logo-icon"></i>
        <span class="logo-text">StudyNotes</span>
    </a>
    <div class="topbar-right">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo substr(htmlspecialchars($studentName), 0, 1); ?>
            </div>
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($studentName); ?></span>
                <?php 
                $lastLogin = $authController->getLastLoginTime();
                if ($lastLogin) {
                    echo '<span class="user-last-login">Last login: ' . date('M d, g:i a', strtotime($lastLogin)) . '</span>';
                }
                ?>
            </div>
        </div>
        <a href="/main/src/views/student/logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</header>

<div class="dashboard-container">
    <div class="dashboard-content">
        <!-- Side Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3 class="sidebar-title">StudyNotes</h3>
            </div>

            <div class="sidebar-nav">
                <h4 class="sidebar-nav-title">Main Menu</h4>
                <a href="/main/student/home.php" class="sidebar-nav-item">
                    <i class="fas fa-home sidebar-nav-icon"></i>
                    <span>Home</span>
                </a>
                <a href="/main/src/views/student/all_notes.php" class="sidebar-nav-item">
                    <i class="fas fa-sticky-note sidebar-nav-icon"></i>
                    <span>All Notes</span>
                </a>
                <a href="/main/src/views/student/modules.php" class="sidebar-nav-item">
                    <i class="fas fa-book sidebar-nav-icon"></i>
                    <span>Modules</span>
                </a>
                <a href="/main/src/views/student/quizzes.php" class="sidebar-nav-item active">
                    <i class="fas fa-question-circle sidebar-nav-icon"></i>
                    <span>Quizzes</span>
                </a>
                <a href="/main/src/views/student/ai_summaries.php" class="sidebar-nav-item">
                    <i class="fas fa-brain sidebar-nav-icon"></i>
                    <span>AI Summaries</span>
                </a>
                <a href="/main/src/views/student/settings.php" class="sidebar-nav-item">
                    <i class="fas fa-cog sidebar-nav-icon"></i>
                    <span>Settings</span>
                </a>
                <a href="/main/src/views/student/logout.php" class="sidebar-nav-item">
                    <i class="fas fa-sign-out-alt sidebar-nav-icon"></i>
                    <span>Logout</span>
                </a>
            </div>

            <div class="sidebar-modules">
                <h4 class="sidebar-nav-title">Your Modules</h4>
                <?php if (count($modules) > 0): ?>
                    <?php foreach ($modules as $module): ?>
                        <div class="module-item">
                            <a href="/main/student/home.php?module_id=<?php echo $module['id']; ?>"
                               class="module-link">
                                <span class="module-color" style="background-color: <?php echo sprintf('#%06X', crc32($module['name']) & 0xFFFFFF); ?>"></span>
                                <?php echo htmlspecialchars($module['name']); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <button id="addModuleBtn" class="add-module-btn" title="Add new module">
                        <i class="fas fa-plus"></i> Add Module
                    </button>
                <?php else: ?>
                    <div class="empty-modules">
                        <p>You haven't created any modules yet.</p>
                        <button class="create-first-module-btn btn btn-primary">
                            <i class="fas fa-plus"></i> Create Your First Module
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Backdrop for mobile -->
        <div class="backdrop" id="backdrop"></div>

        <!-- Main Content Area -->
        <main class="main">
            <!-- Welcome Section -->
            <section class="welcome-section fade-in">
                <div class="welcome-text">
                    <h1>Quizzes</h1>
                    <p>View, take, and manage your generated quizzes</p>
                </div>
            </section>

            <?php if (!empty($message)): ?>
            <div class="message fade-in">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <!-- Search and Filter Section -->
            <section class="search-section fade-in">
                <form action="" method="GET" class="search-bar">
                    <input type="text" name="search" placeholder="Search quizzes..." value="<?php echo htmlspecialchars($searchTerm); ?>" class="form-group search-input">

                    <select name="module" class="form-group filter-select">
                        <option value="0">All Modules</option>
                        <?php foreach ($modules as $module): ?>
                            <option value="<?php echo $module['id']; ?>" <?php echo $filterModule == $module['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($module['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                </form>
            </section>

            <!-- Quizzes Section -->
            <?php if (empty($quizzesByModule)): ?>
                <div class="empty-state fade-in">
                    <div class="empty-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3 class="empty-title">No quizzes found</h3>
                    <p class="empty-text">
                        <?php if (!empty($searchTerm) || $filterModule > 0): ?>
                            No quizzes match your search criteria. Try adjusting your filters.
                        <?php else: ?>
                            You haven't generated any quizzes yet. Go to a note and click "Generate Quiz" to create your first quiz.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($quizzesByModule as $moduleId => $moduleData): ?>
                    <div class="module-section fade-in">
                        <div class="module-header">
                            <span class="module-color" style="background-color: <?php echo sprintf('#%06X', crc32($moduleData['name']) & 0xFFFFFF); ?>"></span>
                            <h2 class="module-name"><?php echo htmlspecialchars($moduleData['name']); ?></h2>
                            <span class="quiz-count"><?php echo count($moduleData['quizzes']); ?> quiz<?php echo count($moduleData['quizzes']) !== 1 ? 'zes' : ''; ?></span>
                        </div>

                        <div class="quizzes-grid">
                            <?php foreach ($moduleData['quizzes'] as $quiz): ?>
                                <div class="quiz-card fade-in">
                                    <div class="quiz-header">
                                        <h3 class="quiz-title"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                                        <div class="quiz-meta">
                                            <div class="quiz-meta-item">
                                                <i class="fas fa-sticky-note"></i>
                                                <span>Note: <?php echo htmlspecialchars($quiz['note_title']); ?></span>
                                            </div>
                                            <div class="quiz-meta-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>Generated: <?php echo date('M d, Y', strtotime($quiz['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="quiz-body">
                                        <div class="quiz-score">
                                            <?php 
                                            $hasAttempt = !empty($quiz['latest_attempt']);
                                            $scoreClass = 'score-none';
                                            $scoreText = 'Not attempted yet';
                                            
                                            if ($hasAttempt) {
                                                $score = $quiz['latest_attempt']['score'];
                                                if ($score >= 80) {
                                                    $scoreClass = 'score-high';
                                                } elseif ($score >= 50) {
                                                    $scoreClass = 'score-medium';
                                                } else {
                                                    $scoreClass = 'score-low';
                                                }
                                                $scoreText = 'Score: ' . round($score) . '%';
                                            }
                                            ?>
                                            <div class="score-circle <?php echo $scoreClass; ?>">
                                                <?php echo $hasAttempt ? round($score) : '-'; ?>
                                            </div>
                                            <div class="score-text">
                                                <?php echo $scoreText; ?>
                                                <?php if ($hasAttempt): ?>
                                                    <div>Attempted: <?php echo date('M d, Y', strtotime($quiz['latest_attempt']['completed_at'])); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="quiz-actions">
                                            <a href="/main/src/views/notes/generate_quiz.php?id=<?php echo $quiz['note_id']; ?>" class="quiz-btn btn-take">
                                                <i class="fas fa-play-circle"></i>
                                                <span><?php echo $hasAttempt ? 'Retake Quiz' : 'Take Quiz'; ?></span>
                                            </a>
                                            <?php if ($hasAttempt): ?>
                                                <a href="/main/src/views/notes/take_quiz.php?view_results=1&quiz_id=<?php echo $quiz['id']; ?>" class="quiz-btn btn-results">
                                                    <i class="fas fa-chart-bar"></i>
                                                    <span>View Results</span>
                                                </a>
                                            <?php endif; ?>
                                            <form method="POST" action="/main/src/views/notes/generate_quiz.php">
                                                <input type="hidden" name="action" value="regenerate">
                                                <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                                <input type="hidden" name="note_id" value="<?php echo $quiz['note_id']; ?>">
                                                <button type="submit" class="quiz-btn btn-regenerate">
                                                    <i class="fas fa-sync-alt"></i>
                                                    <span>Regenerate</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Add Module Modal -->
<div id="addModuleModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" id="closeModuleModal">&times;</span>
        <h2 class="modal-title">Add New Module</h2>
        <form id="addModuleForm" action="/main/src/views/student/module_actions.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="moduleName">Module Name</label>
                <input type="text" id="moduleName" name="name" required placeholder="e.g. Mathematics, Physics">
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" id="cancelModuleBtn">Cancel</button>
                <button type="submit" class="btn-primary">Add Module</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Load the modal fix script -->
<script src="/main/public/js/modal-fix.js"></script>
<!-- Load the sidebar toggle script -->
<script src="/main/public/js/sidebar-toggle.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to open module modal
        function openModuleModal() {
            console.log('Opening module modal');
            const addModuleModal = document.getElementById('addModuleModal');
            if (addModuleModal) {
                addModuleModal.style.display = 'block';
                setTimeout(function() {
                    const moduleName = document.getElementById('moduleName');
                    if (moduleName) moduleName.focus();
                }, 300);
            }
        }

        // Add direct click handlers to module buttons
        const moduleBtn = document.getElementById('addModuleBtn');
        const firstModuleBtn = document.querySelector('.create-first-module-btn');

        if (moduleBtn) {
            moduleBtn.onclick = openModuleModal;
        }

        if (firstModuleBtn) {
            firstModuleBtn.onclick = openModuleModal;
        }
    });
</script>
</body>
</html>