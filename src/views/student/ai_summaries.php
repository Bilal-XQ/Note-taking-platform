<?php
require_once __DIR__ . '/../../controllers/NotesController.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

// Check if user is logged in
$authController = new AuthController();
if (!$authController->isLoggedIn() || $authController->isAdmin()) {
    header('Location: /main/src/views/student/login.php');
    exit;
}

$notesController = new NotesController();
$modules = $notesController->getModules();
$studentName = $authController->getCurrentStudentName();
$notesWithSummaries = $notesController->getNotesWithSummaries();

// Group summaries by module
$summariesByModule = [];
foreach ($notesWithSummaries as $note) {
    $moduleId = $note['module_id'];
    $moduleName = $note['module_name'];

    if (!isset($summariesByModule[$moduleId])) {
        $summariesByModule[$moduleId] = [
            'name' => $moduleName,
            'notes' => []
        ];
    }

    $summariesByModule[$moduleId]['notes'][] = $note;
}

// Handle generate summaries request
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_summaries') {
    if (isset($_POST['module_id']) && !empty($_POST['module_id'])) {
        $moduleId = (int)$_POST['module_id'];
        $result = $notesController->generateModuleSummaries($moduleId);

        if ($result && isset($result['total']) && isset($result['success'])) {
            if ($result['total'] === 0) {
                $message = "No notes found in this module to generate summaries.";
                $messageType = "error";
            } else if ($result['success'] === 0) {
                $message = "Failed to generate summaries. Please try again later.";
                $messageType = "error";
            } else {
                $message = "Successfully generated summaries for {$result['success']} out of {$result['total']} notes.";

                // Refresh the page to show the new summaries
                header("Location: /main/src/views/student/ai_summaries.php?message=" . urlencode($message) . "&type=success");
                exit;
            }
        } else {
            $message = "An error occurred while generating summaries. Please try again later.";
            $messageType = "error";
        }
    } else {
        $message = "Please select a module to generate summaries.";
        $messageType = "error";
    }
}

// Get message from URL if redirected
if (empty($message) && isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageType = isset($_GET['type']) ? $_GET['type'] : 'success';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyNotes - AI Summaries</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/main/public/css/modern-dashboard.css">
    <link rel="stylesheet" href="/main/public/css/sidebar-toggle.css">
    <style>
        .generate-form {
            background-color: var(--bg-color);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .summary-card {
            background-color: var(--bg-color);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-md);
        }

        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .summary-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .summary-meta-item {
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .summary-content {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .summary-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .summary-action {
            padding: 0.5rem 0.75rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            text-decoration: none;
            transition: var(--transition-fast);
        }

        .view-action {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .view-action:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .regenerate-action {
            background-color: var(--secondary-light);
            color: var(--secondary-color);
        }

        .regenerate-action:hover {
            background-color: var(--secondary-color);
            color: white;
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

        .summary-count {
            margin-left: auto;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius-lg);
        }

        .message-success {
            background-color: var(--success-light);
            color: var(--success-color);
        }

        .message-error {
            background-color: var(--danger-light);
            color: var(--danger-color);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
            display: none;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--bg-color);
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            color: white;
            font-size: 1.25rem;
            margin-top: 1rem;
            text-align: center;
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
                <a href="/main/src/views/student/quizzes.php" class="sidebar-nav-item">
                    <i class="fas fa-question-circle sidebar-nav-icon"></i>
                    <span>Quizzes</span>
                </a>
                <a href="/main/src/views/student/ai_summaries.php" class="sidebar-nav-item active">
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
                    <h1>AI Summaries</h1>
                    <p>Generate and view AI-powered summaries of your notes</p>
                </div>
            </section>

            <?php if (!empty($message)): ?>
            <div class="message message-<?php echo $messageType; ?> fade-in">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <!-- Generate Summaries Section -->
            <section class="generate-section fade-in">
                <form action="" method="POST" class="generate-form" id="generateForm">
                    <input type="hidden" name="action" value="generate_summaries">
                    <h3>Generate New Summaries</h3>
                    <p>Select a module to generate AI summaries for all notes within that module.</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="moduleSelect">Module</label>
                            <select id="moduleSelect" name="module_id" required class="form-control">
                                <option value="">Select a module</option>
                                <?php foreach ($modules as $module): ?>
                                    <option value="<?php echo $module['id']; ?>"><?php echo htmlspecialchars($module['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" id="generateBtn">
                            <i class="fas fa-brain"></i>
                            <span>Generate Summaries</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Summaries History Section -->
            <section class="summaries-section fade-in">
                <h2 class="section-title">Summary History</h2>

                <?php if (empty($summariesByModule)): ?>
                    <div class="empty-state fade-in">
                        <div class="empty-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="empty-title">No summaries yet</h3>
                        <p class="empty-text">
                            You haven't generated any AI summaries yet. Select a module above to get started.
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($summariesByModule as $moduleId => $moduleData): ?>
                        <div class="module-section fade-in">
                            <div class="module-header">
                                <span class="module-color" style="background-color: <?php echo sprintf('#%06X', crc32($moduleData['name']) & 0xFFFFFF); ?>"></span>
                                <h2 class="module-name"><?php echo htmlspecialchars($moduleData['name']); ?></h2>
                                <span class="summary-count"><?php echo count($moduleData['notes']); ?> summar<?php echo count($moduleData['notes']) !== 1 ? 'ies' : 'y'; ?></span>
                            </div>

                            <?php foreach ($moduleData['notes'] as $note): ?>
                                <div class="summary-card fade-in">
                                    <div class="summary-header">
                                        <div>
                                            <h3 class="summary-title"><?php echo htmlspecialchars($note['title']); ?></h3>
                                            <div class="summary-meta">
                                                <div class="summary-meta-item">
                                                    <i class="fas fa-book"></i>
                                                    <span><?php echo htmlspecialchars($moduleData['name']); ?></span>
                                                </div>
                                                <div class="summary-meta-item">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <span>Generated: <?php echo date('M d, Y, g:i a', strtotime($note['summary_generated_at'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="summary-content">
                                        <?php echo nl2br(htmlspecialchars($note['ai_summary'])); ?>
                                    </div>

                                    <div class="summary-actions">
                                        <a href="/main/src/views/notes/edit.php?id=<?php echo $note['id']; ?>" class="summary-action view-action">
                                            <i class="fas fa-eye"></i>
                                            <span>View Full Note</span>
                                        </a>
                                        <form action="" method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="generate_summaries">
                                            <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                                            <button type="submit" class="summary-action regenerate-action">
                                                <i class="fas fa-sync-alt"></i>
                                                <span>Regenerate</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
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

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <div class="loading-text">Generating AI summaries...</div>
        <div class="loading-text">This may take a few moments.</div>
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

        // Show loading overlay when generating summaries
        const generateForm = document.getElementById('generateForm');
        const loadingOverlay = document.getElementById('loadingOverlay');

        if (generateForm) {
            generateForm.addEventListener('submit', function() {
                const moduleSelect = document.getElementById('moduleSelect');
                if (moduleSelect && moduleSelect.value) {
                    loadingOverlay.style.display = 'flex';
                }
            });
        }

        // Mobile menu toggle functionality is now handled by sidebar-toggle.js
    });
</script>
</body>
</html>
