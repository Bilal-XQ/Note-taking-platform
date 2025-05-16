<?php
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in as admin
$authController = new AuthController();
if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
    header('Location: /main/src/views/student/login.php');
    exit;
}

// Get database connection
$conn = getDBConnection();

// Get admin username from session
$adminUsername = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $studentId = (int)$_GET['id'];
    
    try {
        // Begin transaction
        $conn->beginTransaction();
        
        // First delete any references in student_modules
        $stmt = $conn->prepare("DELETE FROM student_modules WHERE student_id = :studentId");
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        
        // Then delete the student's notes
        $stmt = $conn->prepare("DELETE FROM notes WHERE student_id = :studentId");
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        
        // Finally delete the student
        $stmt = $conn->prepare("DELETE FROM students WHERE id = :studentId");
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $successMessage = "Student successfully deleted.";
    } catch(PDOException $e) {
        // Rollback in case of error
        $conn->rollBack();
        $errorMessage = "Error deleting student: " . $e->getMessage();
    }
    
    // Redirect to prevent resubmission
    header('Location: students.php' . (isset($successMessage) ? '?success=' . urlencode($successMessage) : '?error=' . urlencode($errorMessage)));
    exit;
}

// Process form submissions for adding/editing a student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $moduleIds = $_POST['modules'] ?? [];
    
    // Validate input
    if (empty($fullName) || empty($username)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            // Begin transaction
            $conn->beginTransaction();
            
            if (isset($_POST['student_id'])) {
                // Update existing student
                $studentId = (int)$_POST['student_id'];
                
                $stmt = $conn->prepare("
                    UPDATE students 
                    SET full_name = :fullName, username = :username" . 
                    (empty($password) ? "" : ", password = :password") . 
                    " WHERE id = :studentId
                ");
                $stmt->bindParam(':fullName', $fullName);
                $stmt->bindParam(':username', $username);
                if (!empty($password)) {
                    $stmt->bindParam(':password', $password);
                }
                $stmt->bindParam(':studentId', $studentId);
                $stmt->execute();
                
                // Delete existing module assignments
                $stmt = $conn->prepare("DELETE FROM student_modules WHERE student_id = :studentId");
                $stmt->bindParam(':studentId', $studentId);
                $stmt->execute();
                
                // Insert new module assignments
                if (!empty($moduleIds)) {
                    $insertModuleStmt = $conn->prepare("
                        INSERT INTO student_modules (student_id, module_id) 
                        VALUES (:studentId, :moduleId)
                    ");
                    
                    foreach ($moduleIds as $moduleId) {
                        $insertModuleStmt->bindParam(':studentId', $studentId);
                        $insertModuleStmt->bindParam(':moduleId', $moduleId);
                        $insertModuleStmt->execute();
                    }
                }
                
                $successMessage = "Student updated successfully.";
            } else {
                // Check if username already exists
                $stmt = $conn->prepare("SELECT id FROM students WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    throw new Exception("Username already exists. Please choose a different one.");
                }
                
                // Add new student
                $stmt = $conn->prepare("
                    INSERT INTO students (full_name, username, password) 
                    VALUES (:fullName, :username, :password)
                ");
                $stmt->bindParam(':fullName', $fullName);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                $stmt->execute();
                
                $studentId = $conn->lastInsertId();
                
                // Insert module assignments
                if (!empty($moduleIds)) {
                    $insertModuleStmt = $conn->prepare("
                        INSERT INTO student_modules (student_id, module_id) 
                        VALUES (:studentId, :moduleId)
                    ");
                    
                    foreach ($moduleIds as $moduleId) {
                        $insertModuleStmt->bindParam(':studentId', $studentId);
                        $insertModuleStmt->bindParam(':moduleId', $moduleId);
                        $insertModuleStmt->execute();
                    }
                }
                
                $successMessage = "Student added successfully.";
            }
            
            // Commit transaction
            $conn->commit();
            
            // Redirect to prevent resubmission
            header('Location: students.php?success=' . urlencode($successMessage));
            exit;
        } catch(Exception $e) {
            // Rollback in case of error
            $conn->rollBack();
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

// Get available modules for dropdowns
$stmt = $conn->prepare("SELECT id, name FROM modules ORDER BY name");
$stmt->execute();
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get student data for editing
$studentToEdit = null;
$selectedModules = [];
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $studentId = (int)$_GET['id'];
    
    // Get student data
    $stmt = $conn->prepare("SELECT id, full_name, username FROM students WHERE id = :studentId");
    $stmt->bindParam(':studentId', $studentId);
    $stmt->execute();
    $studentToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($studentToEdit) {
        // Get assigned modules
        $stmt = $conn->prepare("SELECT module_id FROM student_modules WHERE student_id = :studentId");
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        $selectedModuleRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($selectedModuleRows as $row) {
            $selectedModules[] = $row['module_id'];
        }
    }
}

// Get students for main display
$stmt = $conn->prepare("
    SELECT s.id, s.full_name, s.username, COUNT(sm.module_id) as module_count 
    FROM students s 
    LEFT JOIN student_modules sm ON s.id = sm.student_id 
    GROUP BY s.id, s.full_name, s.username
    ORDER BY s.id DESC
");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students | StudyNotes</title>
    <link rel="stylesheet" href="/main/public/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Additional styles specific to this page */
        .form-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-title {
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            color: var(--text-color);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 0.875rem;
        }
        
        .form-input:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 0.875rem;
            height: auto;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-secondary {
            background-color: #f5f5f5;
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background-color: #e5e5e5;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background-color: rgba(52, 211, 153, 0.1);
            color: #047857;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .search-filter {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .search-input {
            flex: 1;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="admin">
        <div class="admin__content-wrapper">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar__header">
                    <div class="sidebar__logo">
                        <i class="fas fa-book-open sidebar__logo-icon"></i>
                        <span>StudyNotes</span>
                    </div>
                    <button class="sidebar__toggle" id="sidebarToggle">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <nav class="sidebar__nav">
                    <ul class="sidebar__nav-list">
                        <li class="sidebar__nav-item">
                            <a href="/main/admin/dashboard.php" class="sidebar__nav-link">
                                <i class="fas fa-tachometer-alt sidebar__nav-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar__nav-item">
                            <a href="/main/admin/students.php" class="sidebar__nav-link sidebar__nav-link--active">
                                <i class="fas fa-user-graduate sidebar__nav-icon"></i>
                                <span>Students</span>
                            </a>
                        </li>
                        <li class="sidebar__nav-item">
                            <a href="/main/src/views/student/logout.php" class="sidebar__nav-link">
                                <i class="fas fa-sign-out-alt sidebar__nav-icon"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <div class="backdrop" id="backdrop"></div>

            <!-- Main Content -->
            <div class="main-container">
                <!-- Top Bar -->
                <header class="topbar">
                    <button class="sidebar__toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="topbar__title">Manage Students</h2>
                    <div class="topbar__actions">
                        <div class="topbar__profile">
                            <span class="topbar__profile-name"><?php echo htmlspecialchars($adminUsername); ?></span>
                            <div class="topbar__profile-img">
                                <?php echo substr(htmlspecialchars($adminUsername), 0, 1); ?>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Content Area -->
                <main class="main">
                    <div class="main__header">
                        <h1 class="main__title"><?php echo isset($_GET['action']) && $_GET['action'] === 'add' ? 'Add New Student' : (isset($studentToEdit) ? 'Edit Student' : 'Manage Students'); ?></h1>
                        <p class="main__subtitle">
                            <?php 
                            if (isset($_GET['action']) && $_GET['action'] === 'add') {
                                echo 'Create a new student account.';
                            } elseif (isset($studentToEdit)) {
                                echo 'Update student information.';
                            } else {
                                echo 'View, add, edit, or remove student accounts.';
                            }
                            ?>
                        </p>
                    </div>
                    
                    <!-- Display success/error messages -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($errorMessage)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <!-- Form for adding/editing a student -->
                    <?php if ((isset($_GET['action']) && $_GET['action'] === 'add') || isset($studentToEdit)): ?>
                        <div class="form-section">
                            <h2 class="form-title"><?php echo isset($studentToEdit) ? 'Edit Student' : 'Add New Student'; ?></h2>
                            
                            <form method="POST" action="/main/admin/students.php">
                                <?php if (isset($studentToEdit)): ?>
                                    <input type="hidden" name="student_id" value="<?php echo $studentToEdit['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="fullName" class="form-label">Full Name *</label>
                                    <input type="text" id="fullName" name="full_name" class="form-input" required 
                                        value="<?php echo isset($studentToEdit) ? htmlspecialchars($studentToEdit['full_name']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" id="username" name="username" class="form-input" required 
                                        value="<?php echo isset($studentToEdit) ? htmlspecialchars($studentToEdit['username']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="password" class="form-label"><?php echo isset($studentToEdit) ? 'Password (leave blank to keep current)' : 'Password *'; ?></label>
                                    <input type="password" id="password" name="password" class="form-input" 
                                        <?php echo isset($studentToEdit) ? '' : 'required'; ?>>
                                </div>
                                
                                <div class="form-group">
                                    <label for="modules" class="form-label">Assigned Modules</label>
                                    <select id="modules" name="modules[]" class="form-select" multiple size="5">
                                        <?php foreach ($modules as $module): ?>
                                            <option value="<?php echo $module['id']; ?>" 
                                                <?php echo in_array($module['id'], $selectedModules) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($module['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small style="color: #6b7280; display: block; margin-top: 0.5rem;">
                                        Hold Ctrl (or Cmd) to select multiple modules
                                    </small>
                                </div>
                                
                                <div class="form-actions">
                                    <a href="/main/admin/students.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo isset($studentToEdit) ? 'Update Student' : 'Add Student'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Button to add new student -->
                        <div style="margin-bottom: 1.5rem;">
                            <a href="/main/admin/students.php?action=add" class="btn btn-primary">
                                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Add New Student
                            </a>
                        </div>
                        
                        <!-- Search/filter functionality -->
                        <div class="search-filter">
                            <input type="text" id="studentSearch" class="form-input search-input" placeholder="Search students...">
                        </div>
                        
                        <!-- Student list table -->
                        <div class="data-section">
                            <div class="data-section__header">
                                <h2 class="data-section__title">All Students (<?php echo count($students); ?>)</h2>
                            </div>
                            <div class="data-section__content">
                                <?php if (count($students) > 0): ?>
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Modules</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo $student['id']; ?></td>
                                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                                <td><?php echo $student['module_count']; ?></td>
                                                <td class="data-table__actions">
                                                    <a href="/main/admin/students.php?action=edit&id=<?php echo $student['id']; ?>" class="data-table__action-btn data-table__action-btn--edit">Edit</a>
                                                    <a href="/main/admin/students.php?action=delete&id=<?php echo $student['id']; ?>" class="data-table__action-btn data-table__action-btn--delete" onclick="return confirm('Are you sure you want to delete this student? This will also delete all their notes.')">Delete</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p>No students found in the database.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </div>

    <script src="/main/public/js/admin.js"></script>
    <script>
        // Simple client-side search functionality
        document.getElementById('studentSearch')?.addEventListener('input', function(e) {
            const searchValue = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            
            tableRows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const username = row.cells[2].textContent.toLowerCase();
                
                if (name.includes(searchValue) || username.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
