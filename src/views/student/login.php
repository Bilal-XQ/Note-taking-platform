<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$error = '';
$username = '';
$remember = false;

// Check for remember me cookie
if(isset($_COOKIE['remember_user'])) {
    $username = $_COOKIE['remember_user'];
    $remember = true;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $authController = new AuthController();
        $loginResult = $authController->login($username, $password);
        
        if ($loginResult) {
            // Set remember me cookie if checked
            if($remember) {
                setcookie('remember_user', $username, time() + (86400 * 30), "/"); // 30 days
            } else {
                // Delete cookie if exists but not checked
                if(isset($_COOKIE['remember_user'])) {
                    setcookie('remember_user', '', time() - 3600, "/");
                }
            }
            
            // Redirect based on user type
            if($loginResult === 'admin') {
                header('Location: /main/admin/dashboard.php');
            } else {
                header('Location: /main/student/home.php');
            }
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyNotes - Login</title>
    <link rel="stylesheet" href="/main/public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .login-container {
            max-width: 500px;
            margin: 80px auto;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 40px;
        }
        
        .login-title {
            text-align: center;
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-input:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .remember-group {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember-checkbox {
            margin-right: 10px;
        }
        
        .login-btn {
            width: 100%;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .login-btn:hover {
            background-color: var(--primary-dark);
        }
        
        .error-message {
            color: var(--secondary-color);
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(255, 107, 107, 0.1);
            border-radius: var(--border-radius);
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-light);
            text-decoration: none;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="header__container container">
            <div class="header__logo">
                <span class="header__logo-text">StudyNotes</span>
            </div>
            <nav class="header__nav">
                <a href="/main/index.php#features" class="header__nav-link">Features</a>
                <a href="/main/index.php#how-it-works" class="header__nav-link">How It Works</a>
                <a href="#" class="header__nav-link">About</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="login-container">
            <h1 class="login-title">Welcome Back</h1>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
                
                <div class="remember-group">
                    <input type="checkbox" id="remember" name="remember" class="remember-checkbox" <?php echo $remember ? 'checked' : ''; ?>>
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="login-btn">Log In</button>
            </form>
            
            <a href="/main/index.php" class="back-link">Back to Home</a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer__bottom">
            <p>&copy; <?php echo date('Y'); ?> StudyNotes. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
