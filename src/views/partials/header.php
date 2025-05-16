<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Student Note-Taking Platform'; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/main/public/css/style.css">
</head>
<body>
    <header class="header">
        <div class="header__container">
            <div class="header__logo">NoteMaster</div>
            <?php if (isset($_SESSION['student_id']) || isset($_SESSION['admin_id'])): ?>
                <a href="/main/index.php?logout=1" class="header__btn header__btn--logout">Logout</a>
            <?php else: ?>
                <a href="#" class="header__btn header__btn--login" id="loginBtn">Login</a>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>
