<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: /main/index.php');
    exit;
}

// Placeholder data - in real app, fetch from database
$studentName = "Student"; // Replace with actual student name from DB
$modules = [
    ['id' => 1, 'name' => 'Web Development', 'note_count' => 5],
    ['id' => 2, 'name' => 'Math', 'note_count' => 3],
    ['id' => 3, 'name' => 'OOP', 'note_count' => 7]
];

$pageTitle = 'Student Home';
include_once __DIR__ . '/../partials/header.php';
?>

<main class="main student-home">
    <div class="container">
        <section class="welcome-section">
            <h1 class="welcome-section__title">Welcome, <?php echo htmlspecialchars($studentName); ?></h1>
            <p class="welcome-section__subtitle">Manage your modules and notes</p>
        </section>
        
        <section class="modules-section">
            <h2 class="modules-section__title">Your Modules</h2>
            
            <div class="module-grid">
                <?php foreach ($modules as $module): ?>
                <div class="module-card">
                    <div class="module-card__content">
                        <h3 class="module-card__title"><?php echo htmlspecialchars($module['name']); ?></h3>
                        <p class="module-card__info"><?php echo $module['note_count']; ?> Notes</p>
                    </div>
                    <div class="module-card__actions">
                        <a href="/main/student/notes.php?module_id=<?php echo $module['id']; ?>" class="btn btn--secondary">View Notes</a>
                        <a href="#" class="btn btn--primary" data-open-modal="addNoteModal" data-module-id="<?php echo $module['id']; ?>">Add Note</a>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($modules)): ?>
                <div class="module-grid__empty">
                    <p>You haven't added any modules yet. Click the "+" button to get started.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
    
    <!-- Floating Action Button for adding a module -->
    <button class="fab" id="addModuleBtn" aria-label="Add Module">+</button>
</main>

<?php
// Add Module Modal
$modalId = 'addModuleModal';
$modalTitle = 'Add New Module';
$modalContent = <<<HTML
<form class="form" action="/main/student/add_module.php" method="post">
    <div class="form__group">
        <label class="form__label" for="moduleName">Module Name</label>
        <input class="form__input" type="text" id="moduleName" name="name" required>
    </div>
    <div class="form__actions">
        <button type="button" class="btn btn--secondary" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn--primary">Save Module</button>
    </div>
</form>
HTML;
include_once __DIR__ . '/../partials/modal.php';

// Add Note Modal
$modalId = 'addNoteModal';
$modalTitle = 'Add New Note';
$modalContent = <<<HTML
<form class="form" action="/main/student/add_note.php" method="post">
    <input type="hidden" id="noteModuleId" name="module_id">
    <div class="form__group">
        <label class="form__label" for="noteTitle">Title</label>
        <input class="form__input" type="text" id="noteTitle" name="title" required>
    </div>
    <div class="form__group">
        <label class="form__label" for="noteContent">Content</label>
        <textarea class="form__input form__input--textarea" id="noteContent" name="content" rows="4" required></textarea>
    </div>
    <div class="form__actions">
        <button type="button" class="btn btn--secondary" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn--primary">Save Note</button>
    </div>
</form>
HTML;
include_once __DIR__ . '/../partials/modal.php';

include_once __DIR__ . '/../partials/footer.php';
?> 