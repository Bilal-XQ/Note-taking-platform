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

// Get all notes to count notes per module
$allNotes = $notesController->getAllNotes();

// Count notes per module
$notesCountByModule = [];
foreach ($allNotes as $note) {
    $moduleId = $note['module_id'];
    if (!isset($notesCountByModule[$moduleId])) {
        $notesCountByModule[$moduleId] = 0;
    }
    $notesCountByModule[$moduleId]++;
}

// Handle messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$messageType = isset($_GET['type']) ? $_GET['type'] : 'success';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyNotes - Modules</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/main/public/css/modern-dashboard.css">
    <link rel="stylesheet" href="/main/public/css/sidebar-toggle.css">
    <style>
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .module-card {
            position: relative;
            padding: 1.5rem;
            background-color: var(--bg-color);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-md);
        }

        .module-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .module-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        .module-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .module-stats {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: var(--bg-light);
            border-radius: var(--border-radius-lg);
        }

        .module-stat {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .module-stat-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 0.875rem;
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .module-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: auto;
        }

        .module-action {
            flex: 1;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            text-align: center;
            text-decoration: none;
            font-weight: 500;
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

        .edit-action {
            background-color: var(--secondary-light);
            color: var(--secondary-color);
        }

        .edit-action:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .delete-action {
            background-color: var(--danger-light);
            color: var(--danger-color);
        }

        .delete-action:hover {
            background-color: var(--danger-color);
            color: white;
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
                <a href="/main/src/views/student/modules.php" class="sidebar-nav-item active">
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
                    <h1>Modules</h1>
                    <p>Manage your study modules and organize your notes</p>
                </div>
                <div class="welcome-actions">
                    <button id="addModuleBtn2" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add Module</span>
                    </button>
                </div>
            </section>

            <?php if (!empty($message)): ?>
            <div class="message message-<?php echo $messageType; ?> fade-in">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <!-- Modules Section -->
            <?php if (count($modules) > 0): ?>
                <div class="modules-grid fade-in">
                    <?php foreach ($modules as $module): ?>
                        <?php 
                        $moduleId = $module['id'];
                        $noteCount = isset($notesCountByModule[$moduleId]) ? $notesCountByModule[$moduleId] : 0;
                        $moduleColor = sprintf('#%06X', crc32($module['name']) & 0xFFFFFF);
                        $moduleColorLight = sprintf('rgba(%d, %d, %d, 0.1)', 
                            hexdec(substr($moduleColor, 1, 2)), 
                            hexdec(substr($moduleColor, 3, 2)), 
                            hexdec(substr($moduleColor, 5, 2))
                        );
                        ?>
                        <div class="module-card">
                            <div class="module-header">
                                <div class="module-icon" style="background-color: <?php echo $moduleColorLight; ?>; color: <?php echo $moduleColor; ?>;">
                                    <i class="fas fa-book"></i>
                                </div>
                                <h3 class="module-title"><?php echo htmlspecialchars($module['name']); ?></h3>
                            </div>

                            <div class="module-stats">
                                <div class="module-stat">
                                    <div class="module-stat-icon">
                                        <i class="fas fa-sticky-note"></i>
                                    </div>
                                    <span><?php echo $noteCount; ?> note<?php echo $noteCount !== 1 ? 's' : ''; ?></span>
                                </div>
                                <div class="module-stat">
                                    <div class="module-stat-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <span>Last updated: 
                                        <?php 
                                        $lastUpdated = null;
                                        foreach ($allNotes as $note) {
                                            if ($note['module_id'] == $moduleId) {
                                                if ($lastUpdated === null || strtotime($note['updated_at']) > strtotime($lastUpdated)) {
                                                    $lastUpdated = $note['updated_at'];
                                                }
                                            }
                                        }
                                        echo $lastUpdated ? date('M d, Y', strtotime($lastUpdated)) : 'Never';
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <div class="module-actions">
                                <a href="/main/student/home.php?module_id=<?php echo $moduleId; ?>" class="module-action view-action">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <button class="module-action edit-action edit-module-btn" data-id="<?php echo $moduleId; ?>" data-name="<?php echo htmlspecialchars($module['name']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="module-action delete-action delete-module-btn" data-id="<?php echo $moduleId; ?>" data-name="<?php echo htmlspecialchars($module['name']); ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state fade-in">
                    <div class="empty-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3 class="empty-title">No modules yet</h3>
                    <p class="empty-text">
                        You haven't created any modules yet. Start by adding your first module.
                    </p>
                    <button class="create-first-module-btn btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Create Your First Module</span>
                    </button>
                </div>
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

<!-- Edit Module Modal -->
<div id="editModuleModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" id="closeEditModuleModal">&times;</span>
        <h2 class="modal-title">Edit Module</h2>
        <form id="editModuleForm" action="/main/src/views/student/module_actions.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editModuleId">
            <div class="form-group">
                <label for="editModuleName">Module Name</label>
                <input type="text" id="editModuleName" name="name" required>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" id="cancelEditModuleBtn">Cancel</button>
                <button type="submit" class="btn-primary">Update Module</button>
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
        const moduleBtn2 = document.getElementById('addModuleBtn2');
        const firstModuleBtn = document.querySelector('.create-first-module-btn');

        if (moduleBtn) {
            moduleBtn.onclick = openModuleModal;
        }

        if (moduleBtn2) {
            moduleBtn2.onclick = openModuleModal;
        }

        if (firstModuleBtn) {
            firstModuleBtn.onclick = openModuleModal;
        }

        // Edit module functionality
        const editModuleBtns = document.querySelectorAll('.edit-module-btn');
        const editModuleModal = document.getElementById('editModuleModal');
        const closeEditModuleModal = document.getElementById('closeEditModuleModal');
        const cancelEditModuleBtn = document.getElementById('cancelEditModuleBtn');
        const editModuleId = document.getElementById('editModuleId');
        const editModuleName = document.getElementById('editModuleName');

        editModuleBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const moduleId = this.getAttribute('data-id');
                const moduleName = this.getAttribute('data-name');

                editModuleId.value = moduleId;
                editModuleName.value = moduleName;

                editModuleModal.style.display = 'block';
                setTimeout(function() {
                    editModuleName.focus();
                }, 300);
            });
        });

        if (closeEditModuleModal) {
            closeEditModuleModal.addEventListener('click', function() {
                editModuleModal.style.display = 'none';
            });
        }

        if (cancelEditModuleBtn) {
            cancelEditModuleBtn.addEventListener('click', function() {
                editModuleModal.style.display = 'none';
            });
        }

        // Close edit module modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editModuleModal) {
                editModuleModal.style.display = 'none';
            }
        });

        // Delete module confirmation
        const deleteModuleBtns = document.querySelectorAll('.delete-module-btn');

        deleteModuleBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const moduleId = this.getAttribute('data-id');
                const moduleName = this.getAttribute('data-name');

                if (confirm(`Are you sure you want to delete the module "${moduleName}"? All associated notes will also be deleted. This action cannot be undone.`)) {
                    window.location.href = `/main/src/views/student/module_actions.php?action=delete&id=${moduleId}`;
                }
            });
        });

        // Mobile menu toggle functionality is now handled by sidebar-toggle.js
    });
</script>
</body>
</html>
