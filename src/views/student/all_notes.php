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
$allNotes = $notesController->getAllNotes();
$modules = $notesController->getModules();
$studentName = $authController->getCurrentStudentName();

// Group notes by module
$notesByModule = [];
foreach ($allNotes as $note) {
    $moduleId = $note['module_id'];
    if (!isset($notesByModule[$moduleId])) {
        $notesByModule[$moduleId] = [
            'name' => $note['module_name'],
            'notes' => []
        ];
    }
    $notesByModule[$moduleId]['notes'][] = $note;
}

// Handle search
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterModule = isset($_GET['module']) ? (int)$_GET['module'] : 0;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'updated_desc';

// Filter notes based on search term and module filter
if (!empty($searchTerm) || $filterModule > 0) {
    foreach ($notesByModule as $moduleId => &$moduleData) {
        $filteredNotes = [];
        foreach ($moduleData['notes'] as $note) {
            $matchesSearch = empty($searchTerm) || 
                             stripos($note['title'], $searchTerm) !== false || 
                             stripos($note['content'], $searchTerm) !== false;
            $matchesModule = $filterModule === 0 || $moduleId == $filterModule;

            if ($matchesSearch && $matchesModule) {
                $filteredNotes[] = $note;
            }
        }
        $moduleData['notes'] = $filteredNotes;
    }
    // Remove empty modules
    $notesByModule = array_filter($notesByModule, function($moduleData) {
        return !empty($moduleData['notes']);
    });
}

// Sort notes
foreach ($notesByModule as &$moduleData) {
    usort($moduleData['notes'], function($a, $b) use ($sortBy) {
        switch ($sortBy) {
            case 'title_asc':
                return strcmp($a['title'], $b['title']);
            case 'title_desc':
                return strcmp($b['title'], $a['title']);
            case 'created_asc':
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            case 'created_desc':
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            case 'updated_asc':
                return strtotime($a['updated_at']) - strtotime($b['updated_at']);
            case 'updated_desc':
            default:
                return strtotime($b['updated_at']) - strtotime($a['updated_at']);
        }
    });
}

// Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
    if (isset($_POST['note_ids']) && is_array($_POST['note_ids'])) {
        $deleteCount = 0;
        foreach ($_POST['note_ids'] as $noteId) {
            if ($notesController->deleteNote($noteId)) {
                $deleteCount++;
            }
        }
        if ($deleteCount > 0) {
            $message = "$deleteCount note(s) deleted successfully.";
            // Refresh the page to show updated notes
            header("Location: /main/src/views/student/all_notes.php?message=" . urlencode($message));
            exit;
        }
    }
}

// Get success/error messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyNotes - All Notes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/main/public/css/modern-dashboard.css">
    <link rel="stylesheet" href="/main/public/css/notes-list-fix.css">
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

        .sort-select {
            min-width: 150px;
        }

        .note-list {
            margin-bottom: 2rem;
        }

        .note-item {
            position: relative;
            padding-left: 2rem;
        }

        .note-checkbox {
            position: absolute;
            left: 0;
            top: 1.5rem;
        }

        .bulk-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            background-color: var(--bg-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow-sm);
        }

        .select-all-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        .note-count {
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
                <a href="/main/src/views/student/all_notes.php" class="sidebar-nav-item active">
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
                    <h1>All Notes</h1>
                    <p>View, search, and manage all your notes across modules</p>
                </div>
                <div class="welcome-actions">
                    <button id="addNoteBtn" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add Note</span>
                    </button>
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
                    <input type="text" name="search" placeholder="Search notes..." value="<?php echo htmlspecialchars($searchTerm); ?>" class="form-group search-input">

                    <select name="module" class="form-group filter-select">
                        <option value="0">All Modules</option>
                        <?php foreach ($modules as $module): ?>
                            <option value="<?php echo $module['id']; ?>" <?php echo $filterModule == $module['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($module['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="sort" class="form-group sort-select">
                        <option value="updated_desc" <?php echo $sortBy === 'updated_desc' ? 'selected' : ''; ?>>Latest Updated</option>
                        <option value="updated_asc" <?php echo $sortBy === 'updated_asc' ? 'selected' : ''; ?>>Oldest Updated</option>
                        <option value="created_desc" <?php echo $sortBy === 'created_desc' ? 'selected' : ''; ?>>Latest Created</option>
                        <option value="created_asc" <?php echo $sortBy === 'created_asc' ? 'selected' : ''; ?>>Oldest Created</option>
                        <option value="title_asc" <?php echo $sortBy === 'title_asc' ? 'selected' : ''; ?>>Title (A-Z)</option>
                        <option value="title_desc" <?php echo $sortBy === 'title_desc' ? 'selected' : ''; ?>>Title (Z-A)</option>
                    </select>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                </form>
            </section>

            <!-- Notes Section -->
            <form action="" method="POST">
                <input type="hidden" name="action" value="bulk_delete">

                <div class="bulk-actions fade-in">
                    <div class="select-all-container">
                        <input type="checkbox" id="selectAll" class="select-all">
                        <label for="selectAll">Select All</label>
                    </div>
                    <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                        <i class="fas fa-trash"></i>
                        <span>Delete Selected</span>
                    </button>
                </div>

                <?php if (empty($notesByModule)): ?>
                    <div class="empty-state fade-in">
                        <div class="empty-icon">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                        <h3 class="empty-title">No notes found</h3>
                        <p class="empty-text">
                            <?php if (!empty($searchTerm) || $filterModule > 0): ?>
                                No notes match your search criteria. Try adjusting your filters.
                            <?php else: ?>
                                You haven't created any notes yet. Start by adding your first note.
                            <?php endif; ?>
                        </p>
                        <button id="emptyAddNoteBtn" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Create Your First Note</span>
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($notesByModule as $moduleId => $moduleData): ?>
                        <div class="module-section fade-in">
                            <div class="module-header">
                                <span class="module-color" style="background-color: <?php echo sprintf('#%06X', crc32($moduleData['name']) & 0xFFFFFF); ?>"></span>
                                <h2 class="module-name"><?php echo htmlspecialchars($moduleData['name']); ?></h2>
                                <span class="note-count"><?php echo count($moduleData['notes']); ?> note<?php echo count($moduleData['notes']) !== 1 ? 's' : ''; ?></span>
                            </div>

                            <div class="notes-grid">
                                <?php foreach ($moduleData['notes'] as $note): ?>
                                    <div class="note-card fade-in note-item">
                                        <input type="checkbox" name="note_ids[]" value="<?php echo $note['id']; ?>" class="note-checkbox">
                                        <div class="note-header">
                                            <h3 class="note-title"><?php echo htmlspecialchars($note['title']); ?></h3>
                                            <div class="note-meta">
                                                <div class="note-module">
                                                    <i class="fas fa-book"></i>
                                                    <span><?php echo htmlspecialchars($moduleData['name']); ?></span>
                                                </div>
                                                <div class="note-date">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <span><?php echo date('M d, Y', strtotime($note['created_at'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="note-content">
                                            <?php echo nl2br(htmlspecialchars(substr($note['content'], 0, 200) . (strlen($note['content']) > 200 ? '...' : ''))); ?>
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
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </form>
        </main>
    </div>
</div>

<!-- Add Note Modal -->
<div id="addNoteModal" class="modal note-modal">
    <div class="modal-content">
        <span class="close-modal" id="closeNoteModal">&times;</span>
        <h2 class="modal-title">Add New Note</h2>
        <form id="addNoteForm" action="/main/src/views/notes/create.php" method="POST">
            <div class="form-group">
                <label for="moduleSelect">Module</label>
                <select id="moduleSelect" name="module_id" required>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>"><?php echo htmlspecialchars($module['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
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
<!-- Load the note buttons fix script -->
<script src="/main/public/js/note-buttons-fix.js"></script>
<!-- Load the sidebar toggle script -->
<script src="/main/public/js/sidebar-toggle.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const noteCheckboxes = document.querySelectorAll('.note-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                noteCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateBulkDeleteButton();
            });
        }

        noteCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkDeleteButton);
        });

        function updateBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.note-checkbox:checked').length;
            if (bulkDeleteBtn) {
                bulkDeleteBtn.disabled = checkedCount === 0;
                bulkDeleteBtn.innerHTML = `<i class="fas fa-trash"></i> Delete Selected (${checkedCount})`;
            }
        }

        // Confirm bulk delete
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function(e) {
                const checkedCount = document.querySelectorAll('.note-checkbox:checked').length;
                if (!confirm(`Are you sure you want to delete ${checkedCount} note(s)? This action cannot be undone.`)) {
                    e.preventDefault();
                }
            });
        }

        // Function to open note modal
        function openNoteModal() {
            console.log('Opening note modal');
            const addNoteModal = document.getElementById('addNoteModal');
            if (addNoteModal) {
                addNoteModal.style.display = 'block';
                setTimeout(function() {
                    const noteTitle = document.getElementById('noteTitle');
                    if (noteTitle) noteTitle.focus();
                }, 300);
            }
        }

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

        // Add direct click handlers to note buttons
        const addNoteBtn = document.getElementById('addNoteBtn');
        const emptyAddNoteBtn = document.getElementById('emptyAddNoteBtn');

        if (addNoteBtn) {
            addNoteBtn.onclick = openNoteModal;
        }

        if (emptyAddNoteBtn) {
            emptyAddNoteBtn.onclick = openNoteModal;
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

        // Mobile menu toggle functionality is now handled by sidebar-toggle.js
    });
</script>
</body>
</html>
