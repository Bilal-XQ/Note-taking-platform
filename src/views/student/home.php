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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6bff;
            --primary-hover: #3a56cc;
            --secondary-color: #ff6b6b;
            --text-color: #333333;
            --text-light: #666666;
            --bg-color: #ffffff;
            --bg-light: #f8f9fa;
            --border-color: #e9ecef;
            --border-radius: 8px;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .topbar {
            background-color: var(--bg-color);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-color);
        }

        .logo-icon {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-name {
            font-weight: 500;
        }

        .logout-link {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-link:hover {
            color: var(--primary-color);
        }

        /* Main Layout */
        .main-container {
            display: flex;
            flex: 1;
            flex-direction: column;
        }

        .content {
            display: flex;
            flex: 1;
        }

        /* Sidebar */
        .sidebar {
            width: 100%;
            max-width: 280px;
            background-color: var(--bg-color);
            border-right: 1px solid var(--border-color);
            padding: 1.5rem 0;
            display: none;
            flex-direction: column;
            height: calc(100vh - 64px);
            position: fixed;
            z-index: 5;
        }

        .sidebar-header {
            padding: 0 1.5rem 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .add-module-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .add-module-btn:hover {
            background-color: var(--primary-hover);
        }

        .modules-list {
            list-style: none;
            overflow-y: auto;
            flex: 1;
        }

        .module-item {
            margin-bottom: 0.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .module-link {
            display: block;
            padding: 0.875rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            border-radius: 0;
            flex: 1;
        }

        .module-link:hover {
            background-color: var(--bg-light);
        }

        .module-link.active {
            background-color: var(--primary-color);
            color: white;
            border-right: 4px solid var(--primary-hover);
        }

        .delete-module-btn {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem;
            opacity: 0.7;
            transition: var(--transition);
            margin-right: 0.5rem;
        }

        .delete-module-btn:hover {
            opacity: 1;
            color: var(--secondary-color);
        }

        .create-first-module-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            font-size: 0.9375rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .create-first-module-btn:hover {
            background-color: var(--primary-hover);
        }

        /* Mobile Toggle */
        .menu-toggle {
            border: none;
            background: none;
            color: var(--text-color);
            cursor: pointer;
            font-size: 1.25rem;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main Content */
        .main {
            flex: 1;
            padding: 1.5rem;
            margin-left: 0;
        }

        .page-header {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-color);
            flex: 1;
        }

        .add-note-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            font-size: 0.9375rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .add-note-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Notes Grid */
        .notes-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .note-card {
            background-color: var(--bg-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: var(--transition);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .note-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .note-header {
            margin-bottom: 1rem;
        }

        .note-date {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .note-content {
            flex: 1;
            margin-bottom: 1.5rem;
            color: var(--text-color);
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
        }

        .note-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: auto;
        }

        .note-btn {
            padding: 0.5rem 0.75rem;
            border-radius: var(--border-radius);
            border: none;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .edit-btn {
            background-color: rgba(74, 107, 255, 0.1);
            color: var(--primary-color);
        }

        .edit-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .delete-btn {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .delete-btn:hover {
            background-color: #ef4444;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-light);
        }

        .empty-icon {
            font-size: 3rem;
            color: var(--border-color);
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-text {
            margin-bottom: 1.5rem;
        }

        .empty-modules {
            padding: 1.5rem;
            text-align: center;
        }

        .empty-modules p {
            margin-bottom: 1rem;
            color: var(--text-light);
        }

        /* Backdrop */
        .backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 4;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        /* Module Dialog Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: var(--bg-color);
            margin: 15% auto;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--text-light);
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .btn-secondary {
            background-color: var(--bg-light);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 0.9375rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 0.9375rem;
            font-weight: 500;
        }

        /* Responsive */
        @media (min-width: 640px) {
            .notes-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 768px) {
            .sidebar {
                display: flex;
                position: static;
                min-height: calc(100vh - 64px);
                max-height: none;
            }

            .main {
                margin-left: 280px;
            }

            .menu-toggle {
                display: none;
            }

            .content {
                flex-direction: row;
            }
        }

        @media (min-width: 1024px) {
            .notes-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .main {
                padding: 2rem;
            }
        }

        /* Mobile Styles */
        @media (max-width: 767px) {
            .sidebar--open {
                display: flex;
            }

            .backdrop--visible {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <header class="topbar">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <a href="/main/student/home.php" class="logo">
            <i class="fas fa-book-open logo-icon"></i>
            <span class="logo-text">StudyNotes</span>
        </a>
        <div class="topbar-right">
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($studentName); ?></span>
            </div>
            <a href="/main/src/views/student/logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </header>

    <div class="main-container">
        <div class="content">
            <!-- Side Navigation -->
            <aside class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <h3 class="sidebar-title">Your Modules</h3>
                    <button id="addModuleBtn" class="add-module-btn">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <?php if (count($modules) > 0): ?>
                    <ul class="modules-list">
                        <?php foreach ($modules as $module): ?>
                            <li class="module-item">
                                <a href="?module_id=<?php echo $module['id']; ?>"
                                   class="module-link <?php echo $currentModuleId == $module['id'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($module['name']); ?>
                                </a>
                                <button class="delete-module-btn" data-id="<?php echo $module['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($module['name']); ?>" title="Delete module">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-modules">
                        <p>You haven't created any modules yet.</p>
                        <button class="create-first-module-btn">
                            <i class="fas fa-plus"></i> Create Your First Module
                        </button>
                    </div>
                <?php endif; ?>
            </aside>

            <!-- Backdrop for mobile -->
            <div class="backdrop" id="backdrop"></div>

            <!-- Main Content Area -->
            <main class="main">
                <?php if ($currentModuleId): ?>
                    <div class="page-header">
                        <h1 class="page-title"><?php echo htmlspecialchars($currentModuleName); ?> Notes</h1>
                        <a href="/main/src/views/notes/create.php?module_id=<?php echo $currentModuleId; ?>" class="add-note-btn">
                            <i class="fas fa-plus"></i>
                            <span>Add Note</span>
                        </a>
                    </div>

                    <?php if (count($notes) > 0): ?>
                        <div class="notes-grid">
                            <?php foreach ($notes as $note): ?>
                                <div class="note-card">
                                    <div class="note-header">
                                        <div class="note-date">
                                            <?php
                                            $updatedAt = new DateTime($note['updated_at']);
                                            echo 'Updated: ' . $updatedAt->format('M j, Y, g:i a');
                                            ?>
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
                                        <a href="/main/src/views/notes/delete.php?id=<?php echo $note['id']; ?>"
                                           class="note-btn delete-btn"
                                           onclick="return confirm('Are you sure you want to delete this note?')">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-sticky-note empty-icon"></i>
                            <h3 class="empty-title">No notes yet</h3>
                            <p class="empty-text">Create your first note for this module!</p>
                            <a href="/main/src/views/notes/create.php?module_id=<?php echo $currentModuleId; ?>" class="add-note-btn">
                                <i class="fas fa-plus"></i>
                                <span>Add Note</span>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open empty-icon"></i>
                        <h3 class="empty-title">Get Started with StudyNotes</h3>
                        <p class="empty-text">Create your first module to begin organizing your notes!</p>
                        <button id="createModuleBtn" class="add-note-btn">
                            <i class="fas fa-plus"></i>
                            <span>Create Module</span>
                        </button>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Module Dialog -->
    <div id="moduleDialog" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Create New Module</h2>
            <form id="createModuleForm">
                <div class="form-group">
                    <label for="moduleName">Module Name</label>
                    <input type="text" id="moduleName" name="moduleName" required placeholder="e.g., Web Development, Math, Physics">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary cancel-module">Cancel</button>
                    <button type="submit" class="btn-primary save-module">Create Module</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('backdrop');

        if (menuToggle && sidebar && backdrop) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('sidebar--open');
                backdrop.classList.toggle('backdrop--visible');
            });

            backdrop.addEventListener('click', () => {
                sidebar.classList.remove('sidebar--open');
                backdrop.classList.remove('backdrop--visible');
            });
        }

        // Module management
        const moduleDialog = document.getElementById('moduleDialog');
        const addModuleBtn = document.getElementById('addModuleBtn');
        const createModuleBtn = document.getElementById('createModuleBtn');
        const createFirstModuleBtn = document.querySelector('.create-first-module-btn');
        const closeModalBtn = document.querySelector('.close-modal');
        const cancelModuleBtn = document.querySelector('.cancel-module');
        const createModuleForm = document.getElementById('createModuleForm');
        const deleteModuleBtns = document.querySelectorAll('.delete-module-btn');

        // Show the module dialog
        function showModuleDialog() {
            moduleDialog.style.display = 'block';
            document.getElementById('moduleName').focus();
        }

        // Hide the module dialog
        function hideModuleDialog() {
            moduleDialog.style.display = 'none';
            createModuleForm.reset();
        }

        // Open modal on button clicks
        if (addModuleBtn) addModuleBtn.addEventListener('click', showModuleDialog);
        if (createModuleBtn) createModuleBtn.addEventListener('click', showModuleDialog);
        if (createFirstModuleBtn) createFirstModuleBtn.addEventListener('click', showModuleDialog);

        // Close modal
        if (closeModalBtn) closeModalBtn.addEventListener('click', hideModuleDialog);
        if (cancelModuleBtn) cancelModuleBtn.addEventListener('click', hideModuleDialog);

        // Close modal when clicking outside
        window.addEventListener('click', (event) => {
            if (event.target === moduleDialog) {
                hideModuleDialog();
            }
        });

        // Handle module creation
        if (createModuleForm) {
            createModuleForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const moduleName = document.getElementById('moduleName').value.trim();

                if (moduleName) {
                    // Send AJAX request to create module
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/main/src/views/student/module_actions.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    xhr.onload = function() {
                        console.log('Response received:', this.responseText);

                        if (this.status === 200) {
                            try {
                                const response = JSON.parse(this.responseText);
                                console.log('Parsed response:', response);

                                if (response.success) {
                                    // Reload the page to show the new module
                                    window.location.href = `/main/student/home.php?module_id=${response.module_id}`;
                                } else {
                                    alert(response.message || 'Error creating module');
                                    console.error('Error details:', response.debug || 'No debug info available');
                                }
                            } catch(e) {
                                console.error('Error parsing response:', e);
                                console.error('Raw response:', this.responseText);
                                alert('Error processing the response: ' + e.message);
                            }
                        } else {
                            console.error('HTTP Error:', this.status);
                            alert('Error: ' + this.status);
                        }
                    };

                    xhr.onerror = function() {
                        console.error('Request failed');
                        alert('Request failed. Please check your network connection.');
                    };

                    const data = `action=create&module_name=${encodeURIComponent(moduleName)}`;
                    console.log('Sending data:', data);
                    xhr.send(data);
                }
            });
        }

        // Handle module deletion
        deleteModuleBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();

                const moduleId = btn.dataset.id;
                const moduleName = btn.dataset.name;

                if (confirm(`Are you sure you want to delete the module "${moduleName}"? This will delete all notes in this module.`)) {
                    // Send AJAX request to delete module
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/main/src/views/student/module_actions.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    xhr.onload = function() {
                        if (this.status === 200) {
                            try {
                                const response = JSON.parse(this.responseText);
                                if (response.success) {
                                    // Reload the page
                                    window.location.href = '/main/student/home.php';
                                } else {
                                    alert(response.message || 'Error deleting module');
                                }
                            } catch(e) {
                                alert('Error processing the response');
                            }
                        } else {
                            alert('Error: ' + this.status);
                        }
                    };

                    xhr.send(`action=delete&module_id=${moduleId}`);
                }
            });
        });
    </script>
</body>
</html>
