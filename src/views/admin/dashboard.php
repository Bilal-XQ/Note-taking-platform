<?php
$pageTitle = 'Admin Dashboard';
include_once __DIR__ . '/../partials/header.php';
?>

<main class="main admin-dashboard">
    <div class="container">
        <div class="toolbar">
            <h1 class="toolbar__title">Manage Students</h1>
            <button class="btn btn--primary" data-open-modal="addStudentModal">
                <span class="btn__icon">+</span> Add Student
            </button>
        </div>
        
        <div class="card">
            <div class="table-container">
                <table class="table">
                    <thead class="table__head">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th class="table__actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table__body">
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $index => $student): ?>
                                <tr class="<?php echo $index % 2 === 0 ? 'table__row--even' : 'table__row--odd'; ?>">
                                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['username']); ?></td>
                                    <td class="table__actions">
                                        <button class="icon-btn icon-btn--edit" 
                                                data-open-modal="editStudentModal"
                                                data-student-id="<?php echo $student['id']; ?>"
                                                data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>"
                                                data-student-username="<?php echo htmlspecialchars($student['username']); ?>"
                                                title="Edit">
                                            <span class="icon">✎</span>
                                        </button>
                                        <a href="?action=delete&id=<?php echo $student['id']; ?>" 
                                           class="icon-btn icon-btn--delete"
                                           onclick="return confirm('Are you sure you want to delete this student?');"
                                           title="Delete">
                                            <span class="icon">🗑</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="table__empty">No students found. Add your first student!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
// Add Student Modal
$modalId = 'addStudentModal';
$modalTitle = 'Add New Student';
$modalContent = <<<HTML
<form class="form" action="/main/admin/dashboard.php?action=add" method="post">
    <div class="form__group">
        <label class="form__label" for="fullName">Full Name</label>
        <input class="form__input" type="text" id="fullName" name="full_name" required>
    </div>
    <div class="form__group">
        <label class="form__label" for="username">Username</label>
        <input class="form__input" type="text" id="username" name="username" required>
    </div>
    <div class="form__group">
        <label class="form__label" for="password">Password</label>
        <input class="form__input" type="password" id="password" name="password" required>
    </div>
    <div class="form__actions">
        <button type="button" class="btn btn--secondary" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn--primary">Save Student</button>
    </div>
</form>
HTML;
include_once __DIR__ . '/../partials/modal.php';

// Edit Student Modal
$modalId = 'editStudentModal';
$modalTitle = 'Edit Student';
$modalContent = <<<HTML
<form class="form" action="/main/admin/dashboard.php" method="post">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" id="editStudentId" name="id">
    <div class="form__group">
        <label class="form__label" for="editFullName">Full Name</label>
        <input class="form__input" type="text" id="editFullName" name="full_name" required>
    </div>
    <div class="form__group">
        <label class="form__label" for="editUsername">Username</label>
        <input class="form__input" type="text" id="editUsername" name="username" required>
    </div>
    <div class="form__group">
        <label class="form__label" for="editPassword">Password</label>
        <input class="form__input" type="password" id="editPassword" name="password" placeholder="Leave blank to keep current password">
    </div>
    <div class="form__actions">
        <button type="button" class="btn btn--secondary" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn--primary">Update Student</button>
    </div>
</form>
HTML;
include_once __DIR__ . '/../partials/modal.php';

include_once __DIR__ . '/../partials/footer.php';
?> 