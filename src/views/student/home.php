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

// Determine which module's notes to display
$currentModuleId = isset($_GET['module_id']) ? (int)$_GET['module_id'] : (isset($modules[0]) ? $modules[0]['id'] : null);
$notes = $currentModuleId ? $notesController->getNotesByModule($currentModuleId) : [];

// Get the current module name
$currentModuleName = '';
foreach ($modules as $module) {
    if ($module['id'] == $currentModuleId) {
        $currentModuleName = $module['name'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyNotes - Your Notes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/main/public/css/modern-dashboard.css">
    <link rel="stylesheet" href="/main/public/css/sidebar-toggle.css">
    <link rel="stylesheet" href="/main/public/css/notes-list-fix.css">
    <style>
        .note-card {
            position: relative;
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
                <a href="/main/student/home.php" class="sidebar-nav-item active">
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
                            <a href="?module_id=<?php echo $module['id']; ?>"
                               class="module-link <?php echo $currentModuleId == $module['id'] ? 'active' : ''; ?>">
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
                    <h1>Welcome back, <?php echo htmlspecialchars($studentName); ?>!</h1>
                    <p>Here's an overview of your study progress</p>
                </div>
                <div class="welcome-actions">
                    <button id="addNoteBtn" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add Note</span>
                    </button>
                </div>
            </section>

            <!-- Summary Section -->
            <section class="summary-section fade-in">
                <div class="summary-card">
                    <div class="summary-icon notes">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <div class="summary-content">
                        <h3><?php echo count($notes); ?></h3>
                        <p><?php echo count($notes) == 1 ? 'Note' : 'Notes'; ?> in current module</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon modules">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="summary-content">
                        <h3><?php echo count($modules); ?></h3>
                        <p><?php echo count($modules) == 1 ? 'Module' : 'Modules'; ?> total</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon ai">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="summary-content">
                        <h3>AI Ready</h3>
                        <p>Generate summaries from your notes</p>
                    </div>
                </div>
            </section>

            <?php if ($currentModuleId): ?>
                <!-- Current Module Section -->
                <section class="module-section fade-in">
                    <div class="section-header">
                        <h2 class="section-title"><?php echo htmlspecialchars($currentModuleName); ?> Notes</h2>
                        <div class="section-actions">
                            <button id="addNoteBtn2" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                <span>Add Note</span>
                            </button>
                        </div>
                    </div>

                    <?php if (count($notes) > 0): ?>

                        <div class="notes-grid">
                            <?php foreach ($notes as $note): ?>
                                <div class="note-card fade-in">
                                    <div class="note-header">
                                        <h3 class="note-title"><?php echo htmlspecialchars($note['title']); ?></h3>
                                        <div class="note-meta">
                                            <div class="note-module">
                                                <i class="fas fa-book"></i>
                                                <span><?php echo htmlspecialchars($currentModuleName); ?></span>
                                            </div>
                                            <div class="note-date">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span><?php echo date('M d, Y', strtotime($note['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="note-content">
                                        <?php echo nl2br(htmlspecialchars($note['content'])); ?>
                                    </div>
                                    <div class="note-footer">
                                        <a href="/main/src/views/notes/edit.php?id=<?php echo $note['id']; ?>" class="note-btn edit-btn">
                                            <i class="fas fa-edit"></i>
                                            <span>Edit</span>
                                        </a>
                                        <a href="/main/src/views/notes/generate_summary.php?id=<?php echo $note['id']; ?>" class="note-btn summary-btn" title="Generate AI Summary for this note">
                                            <i class="fas fa-brain"></i>
                                            <span>Generate Summary</span>
                                        </a>
                                        <button class="note-btn quiz-btn" onclick="window.location.href='/main/src/views/notes/quiz.php?note_id=<?php echo $note['id']; ?>'" title="Generate Quiz">
                                            <i class="fas fa-question-circle"></i>
                                            <span>Generate Quiz</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No notes found for this module.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </main>
    </div>
</div>
<script src="/main/public/js/sidebar-toggle.js"></script>
<script src="/main/public/js/notes-actions.js"></script>
</body>
</html>
