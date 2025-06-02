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
                                        <a href="/main/src/views/notes/generate_quiz.php?id=<?php echo $note['id']; ?>" class="note-btn quiz-btn" title="Generate AI Quiz for this note">
                                            <i class="fas fa-question-circle"></i>
                                            <span>Generate Quiz</span>
                                        </a>
                                        <a href="/main/src/views/notes/delete.php?id=<?php echo $note['id']; ?>" class="note-btn delete-btn">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state fade-in">
                            <div class="empty-icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <h3 class="empty-title">No notes yet</h3>
                            <p class="empty-text">
                                You haven't created any notes for this module yet. Start by adding your first note.
                            </p>
                            <button id="emptyAddNoteBtn" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                <span>Create Your First Note</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Modules Overview Section -->
                <section class="modules-section fade-in">
                    <div class="section-header">
                        <h2 class="section-title">All Modules</h2>
                    </div>

                    <div class="modules-overview">
                        <?php foreach ($modules as $module): ?>
                            <div class="module-card fade-in">
                                <div class="module-card-header">
                                    <div class="module-card-icon" style="background-color: <?php echo sprintf('rgba(%d, %d, %d, 0.1)', crc32($module['name']) % 200 + 55, crc32($module['name'] . 'color') % 200 + 55, crc32($module['name'] . 'shade') % 200 + 55); ?>; color: <?php echo sprintf('rgb(%d, %d, %d)', crc32($module['name']) % 200 + 55, crc32($module['name'] . 'color') % 200 + 55, crc32($module['name'] . 'shade') % 200 + 55); ?>;">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <h3 class="module-card-title"><?php echo htmlspecialchars($module['name']); ?></h3>
                                </div>

                                <?php 
                                // Count notes for this module
                                $moduleNotes = $module['id'] == $currentModuleId ? $notes : $notesController->getNotesByModule($module['id']);
                                $noteCount = count($moduleNotes);
                                ?>

                                <div class="module-card-stats">
                                    <div class="module-card-stat">
                                        <div class="module-card-stat-value"><?php echo $noteCount; ?></div>
                                        <div class="module-card-stat-label"><?php echo $noteCount == 1 ? 'Note' : 'Notes'; ?></div>
                                    </div>
                                </div>

                                <a href="?module_id=<?php echo $module['id']; ?>" class="module-card-action">
                                    View Notes
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php else: ?>
                <div class="empty-state fade-in">
                    <div class="empty-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <h3 class="empty-title">No module selected</h3>
                    <p class="empty-text">
                        Please select a module from the sidebar or create a new one to get started.
                    </p>
                    <button id="createModuleBtn" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Module
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

<!-- Add Note Modal -->
<div id="addNoteModal" class="modal note-modal">
    <div class="modal-content">
        <span class="close-modal" id="closeNoteModal">&times;</span>
        <h2 class="modal-title">Add New Note</h2>
        <form id="addNoteForm" action="/main/src/views/notes/create.php" method="POST">
            <input type="hidden" name="module_id" value="<?php echo $currentModuleId; ?>">
            <div class="form-group">
                <label for="noteTitle">Title</label>
                <input type="text" id="noteTitle" name="title" required placeholder="Note title">
            </div>
            <div class="form-group note-form-group">
                <label for="noteContent">Content</label>
                <textarea id="noteContent" name="content" required placeholder="Write your note here..."></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" id="cancelNoteBtn">Cancel</button>
                <button type="submit" class="btn-primary">Add Note</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Load the modal fix script -->
<script src="/main/public/js/modal-fix.js"></script>
<!-- Load the note buttons fix script -->
<script src="/main/public/js/note-buttons-fix.js"></script>
<!-- Load the sidebar toggle script -->
<script src="/main/public/js/sidebar-toggle.js"></script>

<script>
    console.log('Script loaded');

    // Add error handling
    window.onerror = function(message, source, lineno, colno, error) {
        console.error('JavaScript error:', message, 'at line', lineno, 'column', colno, 'in', source);
        console.error('Error object:', error);
        return false;
    };

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded');

        // Check if modal exists
        var addNoteModal = document.getElementById('addNoteModal');
        var addModuleModal = document.getElementById('addModuleModal');
        console.log('addNoteModal exists:', !!addNoteModal);
        console.log('addModuleModal exists:', !!addModuleModal);

        // Function to open note modal
        function openNoteModal() {
            console.log('Opening note modal');
            if (addNoteModal) {
                addNoteModal.style.display = 'block';
                setTimeout(function() {
                    var noteTitle = document.getElementById('noteTitle');
                    if (noteTitle) noteTitle.focus();
                }, 300);
            }
        }

        // Function to open module modal
        function openModuleModal() {
            console.log('Opening module modal');
            if (addModuleModal) {
                addModuleModal.style.display = 'block';
                setTimeout(function() {
                    var moduleName = document.getElementById('moduleName');
                    if (moduleName) moduleName.focus();
                }, 300);
            }
        }

        // Add direct click handlers to note buttons
        var addNoteBtn = document.getElementById('addNoteBtn');
        var addNoteBtn2 = document.getElementById('addNoteBtn2');
        var emptyAddNoteBtn = document.getElementById('emptyAddNoteBtn');

        console.log('addNoteBtn exists:', !!addNoteBtn);
        console.log('addNoteBtn2 exists:', !!addNoteBtn2);
        console.log('emptyAddNoteBtn exists:', !!emptyAddNoteBtn);

        if (addNoteBtn) {
            addNoteBtn.onclick = openNoteModal;
        }

        if (addNoteBtn2) {
            addNoteBtn2.onclick = openNoteModal;
        }

        if (emptyAddNoteBtn) {
            emptyAddNoteBtn.onclick = openNoteModal;
        }

        // Add direct click handlers to module buttons
        var moduleBtn = document.getElementById('addModuleBtn');
        var createModuleBtn = document.getElementById('createModuleBtn');
        var firstModuleBtn = document.querySelector('.create-first-module-btn');

        console.log('moduleBtn exists:', !!moduleBtn);
        console.log('createModuleBtn exists:', !!createModuleBtn);
        console.log('firstModuleBtn exists:', !!firstModuleBtn);

        if (moduleBtn) {
            moduleBtn.onclick = openModuleModal;
        }

        if (createModuleBtn) {
            createModuleBtn.onclick = openModuleModal;
        }

        if (firstModuleBtn) {
            firstModuleBtn.onclick = openModuleModal;
        }

        // Mobile menu toggle functionality is now handled by sidebar-toggle.js
        // This code is kept here for reference but is no longer active
        /*
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('backdrop');

        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar--open');
            backdrop.classList.toggle('backdrop--visible');
        });

        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('sidebar--open');
            backdrop.classList.remove('backdrop--visible');
        });
        */

        // Note: We're not using these variables anymore since we've refactored the code above
        // Keeping the comment for reference
        // Module modal functionality is now handled by the openModuleModal function

        // Note: Module modal opening is now handled by the openModuleModal function above
        // This code is no longer needed and can be removed

        // Note: The close button event listeners are now handled by modal-fix.js
        // to avoid issues with duplicate variable declarations

        // Note: Note modal functionality is now handled by the onclick handlers above
        // to avoid duplicate event listeners

        // Note: Closing the modal is now handled by modal-fix.js

        // Close modals when clicking outside
        // Note: This functionality is now handled by modal-fix.js

        // Delete module confirmation
        const deleteModuleBtns = document.querySelectorAll('.delete-module-btn');

        deleteModuleBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const moduleId = this.dataset.id;
                const moduleName = this.dataset.name;

                if (confirm('Are you sure you want to delete the module "' + moduleName + '"? All associated notes will also be deleted.')) {
                    window.location.href = '/main/src/views/student/module_actions.php?action=delete&id=' + moduleId;
                }
            });
        });

        // Delete note confirmation
        const deleteNoteBtns = document.querySelectorAll('.delete-btn');
        deleteNoteBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const noteTitle = this.closest('.note-card').querySelector('.note-title').textContent;
                const noteUrl = this.getAttribute('href');

                if (confirm('Are you sure you want to delete the note "' + noteTitle + '"?')) {
                    window.location.href = noteUrl;
                }
            });
        });

        // Form submissions with validation
        const addModuleForm = document.getElementById('addModuleForm');
        if (addModuleForm) {
            addModuleForm.addEventListener('submit', function(e) {
                const nameInput = document.getElementById('moduleName');
                if (!nameInput.value.trim()) {
                    e.preventDefault();
                    nameInput.focus();
                    showToast('Module name cannot be empty', 'error');
                    return false;
                }

                showToast('Creating module...', 'info');
                return true;
            });
        }

        const addNoteForm = document.getElementById('addNoteForm');
        if (addNoteForm) {
            addNoteForm.addEventListener('submit', function(e) {
                const titleInput = document.getElementById('noteTitle');
                const contentInput = document.getElementById('noteContent');

                if (!titleInput.value.trim()) {
                    e.preventDefault();
                    titleInput.focus();
                    showToast('Note title cannot be empty', 'error');
                    return false;
                }

                if (!contentInput.value.trim()) {
                    e.preventDefault();
                    contentInput.focus();
                    showToast('Note content cannot be empty', 'error');
                    return false;
                }

                showToast('Creating note...', 'info');
                return true;
            });
        }

        // Toast notification system
        function showToast(message, type = 'info', duration = 3000) {
            const toastContainer = document.getElementById('toastContainer');

            const toast = document.createElement('div');
            toast.className = 'toast ' + (type ? 'toast-' + type : '');

            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';

            toast.innerHTML = `
                <div class="toast-icon"><i class="fas fa-${icon}"></i></div>
                <div class="toast-content">
                    <div class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            `;
