<?php include __DIR__ . '/../partials/header.php'; ?>
<main>
    <section class="edit-note">
        <h2>Edit Note</h2>
        <form method="post" action="?page=edit_note&id=<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php // echo existing title ?>" required>
            <label for="content">Content:</label>
            <textarea id="content" name="content" required><?php // echo existing content ?></textarea>
            <button type="submit">Update Note</button>
        </form>
    </section>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?> 