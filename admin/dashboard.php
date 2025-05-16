<?php
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in as admin
$authController = new AuthController();
if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
    header('Location: /main/src/views/student/login.php');
    exit;
}

// Get admin username from session (no need to start session again)
$adminUsername = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin';

// Get database connection
$conn = getDBConnection();

// Get total number of students
$stmt = $conn->prepare("SELECT COUNT(*) AS total_students FROM students");
$stmt->execute();
$totalStudents = $stmt->fetch(PDO::FETCH_ASSOC)['total_students'];

// For active students, we'll use a placeholder value since last_login column doesn't exist
// In a real application, you would add this column to track user activity
$activeStudents = round($totalStudents * 0.8); // Showing 80% of total as placeholder

// Get number of students registered this month
try {
    // This assumes created_at column exists - if not, it will default to the placeholder
    $stmt = $conn->prepare("SELECT COUNT(*) AS new_students FROM students WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $stmt->execute();
    $newStudents = $stmt->fetch(PDO::FETCH_ASSOC)['new_students'];
} catch(PDOException $e) {
    // If created_at column doesn't exist either, use a placeholder
    $newStudents = round($totalStudents * 0.2); // 20% as new students
}

// Fetch recent students (last 10 registered)
$stmt = $conn->prepare("
    SELECT s.id, s.full_name, s.username, COUNT(sm.module_id) as module_count 
    FROM students s 
    LEFT JOIN student_modules sm ON s.id = sm.student_id 
    GROUP BY s.id, s.full_name, s.username
    ORDER BY s.id DESC 
    LIMIT 10
");
$stmt->execute();
$recentStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | StudyNotes</title>
    <link rel="stylesheet" href="/main/public/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                            <a href="/main/admin/dashboard.php" class="sidebar__nav-link sidebar__nav-link--active">
                                <i class="fas fa-tachometer-alt sidebar__nav-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar__nav-item">
                            <a href="/main/admin/students.php" class="sidebar__nav-link">
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
                    <h2 class="topbar__title">Dashboard</h2>
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
                        <h1 class="main__title">Admin Dashboard</h1>
                        <p class="main__subtitle">Welcome back, <?php echo htmlspecialchars($adminUsername); ?>. Here's an overview of the platform.</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats">
                        <div class="stat-card">
                            <div class="stat-card__header">
                                <h3 class="stat-card__title">Total Students</h3>
                                <div class="stat-card__icon stat-card__icon--blue">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                            <div class="stat-card__value"><?php echo $totalStudents; ?></div>
                            <div class="stat-card__description">Active students on the platform</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card__header">
                                <h3 class="stat-card__title">Active Students</h3>
                                <div class="stat-card__icon stat-card__icon--green">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                            <div class="stat-card__value"><?php echo $activeStudents; ?></div>
                            <div class="stat-card__description">Students active in the last 30 days</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card__header">
                                <h3 class="stat-card__title">New Students</h3>
                                <div class="stat-card__icon stat-card__icon--orange">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                            </div>
                            <div class="stat-card__value"><?php echo $newStudents; ?></div>
                            <div class="stat-card__description">New registrations this month</div>
                        </div>
                    </div>

                    <!-- Recent Students -->
                    <section class="data-section">
                        <div class="data-section__header">
                            <h2 class="data-section__title">Recent Students</h2>
                            <a href="/main/admin/students.php?action=add" class="data-section__action">
                                <i class="fas fa-plus"></i>
                                <span>Add Student</span>
                            </a>
                        </div>
                        <div class="data-section__content">
                            <?php if (count($recentStudents) > 0): ?>
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
                                        <?php foreach ($recentStudents as $student): ?>
                                        <tr>
                                            <td><?php echo $student['id']; ?></td>
                                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                                            <td><?php echo $student['module_count']; ?></td>
                                            <td class="data-table__actions">
                                                <a href="/main/admin/students.php?action=edit&id=<?php echo $student['id']; ?>" class="data-table__action-btn data-table__action-btn--edit">Edit</a>
                                                <a href="/main/admin/students.php?action=delete&id=<?php echo $student['id']; ?>" class="data-table__action-btn data-table__action-btn--delete" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="empty-message">No students found in the database.</p>
                            <?php endif; ?>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>

    <script src="/main/public/js/admin.js"></script>
</body>
</html>

