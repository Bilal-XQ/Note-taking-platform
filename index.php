<?php
require_once __DIR__ . '/src/controllers/AuthController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Note-Taking Platform</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header class="header">
        <div class="header__container">
            <div class="header__logo">NoteMaster</div>
            <a href="#" class="header__login-btn">Login</a>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="hero__content">
                <h1 class="hero__title">Organize Your Notes, Boost Your Learning</h1>
                <p class="hero__subtitle">A simple, powerful platform to help students take, organize, and review notes by module.</p>
                <a href="#" class="hero__cta-btn">Get Started</a>
            </div>
        </section>
        <?php $login_error = $login_error ?? ''; include __DIR__ . '/src/views/partials/login_form.php'; ?>
    </main>
    <script src="public/js/script.js"></script>
</body>
</html> 