<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyNotes - Smarter Note-Taking for Students</title>
    <meta name="description" content="Organize your course notes, improve your study habits, and boost academic performance with our intelligent note-taking platform.">
    <link rel="stylesheet" href="/main/public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="header__container container">
            <div class="header__logo">
                <span class="header__logo-text">StudyNotes</span>
            </div>
            <nav class="header__nav">
                <a href="#features" class="header__nav-link">Features</a>
                <a href="#how-it-works" class="header__nav-link">How It Works</a>
                <a href="#" class="header__nav-link">About</a>
                <a href="/main/src/views/student/login.php" class="header__login-btn">Log In</a>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero__container container">
                <div class="hero__content">
                    <h1 class="hero__title">Take Better Notes.<br><span class="hero__title-highlight">Learn Smarter.</span></h1>
                    <p class="hero__subtitle">Organize your course notes, improve your study habits, and boost your academic performance with our intelligent note-taking platform.</p>
                    <div class="hero__cta">
                        <a href="/main/src/views/student/login.php" class="hero__cta-btn">Start Taking Notes</a>
                        <a href="#features" class="hero__cta-secondary">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="hero__decoration">
                    <div class="hero__decoration-circle"></div>
                    <div class="hero__decoration-square"></div>
                    <div class="hero__decoration-triangle"></div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="features">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Why Choose StudyNotes</h2>
                    <p class="section-subtitle">Powerful tools designed specifically for students</p>
                </div>
                <div class="features__grid">
                    <div class="feature-card">
                        <div class="feature-card__icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h3 class="feature-card__title">Organize by Module</h3>
                        <p class="feature-card__description">Keep your notes organized by course modules. No more scattered notes or missing information.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-card__icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h3 class="feature-card__title">Easy Note Management</h3>
                        <p class="feature-card__description">Create, edit, and delete notes with our intuitive interface. Focus on learning, not managing files.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-card__icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="feature-card__title">Smart Study Tools</h3>
                        <p class="feature-card__description">Coming soon: AI-powered summaries and auto-generated quizzes to enhance your learning experience.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works" id="how-it-works">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">How It Works</h2>
                    <p class="section-subtitle">Simple steps to better note organization</p>
                </div>
                <div class="steps">
                    <div class="step">
                        <div class="step__number">1</div>
                        <h3 class="step__title">Create an Account</h3>
                        <p class="step__description">Sign up for free and set up your student profile.</p>
                    </div>
                    <div class="step">
                        <div class="step__number">2</div>
                        <h3 class="step__title">Add Your Modules</h3>
                        <p class="step__description">Add the courses or modules you're currently studying.</p>
                    </div>
                    <div class="step">
                        <div class="step__number">3</div>
                        <h3 class="step__title">Take Notes</h3>
                        <p class="step__description">Create, organize, and edit your notes for each module.</p>
                    </div>
                    <div class="step">
                        <div class="step__number">4</div>
                        <h3 class="step__title">Study Smarter</h3>
                        <p class="step__description">Use your organized notes to improve your study efficiency.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <div class="cta__content">
                    <h2 class="cta__title">Ready to transform your note-taking?</h2>
                    <p class="cta__subtitle">Join thousands of students who are already studying smarter, not harder.</p>
                    <a href="/main/src/views/student/login.php" class="cta__button">Get Started Today</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer__container">
            <div class="footer__about">
                <div class="footer__logo">StudyNotes</div>
                <p>A modern platform designed to help students organize and maximize the effectiveness of their course notes.</p>
                <div class="footer__social">
                    <a href="#" class="footer__social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="footer__social-link"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="footer__social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer__links">
                <h4 class="footer__links-heading">Platform</h4>
                <ul class="footer__links-list">
                    <li class="footer__links-item"><a href="#features">Features</a></li>
                    <li class="footer__links-item"><a href="#how-it-works">How It Works</a></li>
                    <li class="footer__links-item"><a href="#">About</a></li>
                </ul>
            </div>
            <div class="footer__links">
                <h4 class="footer__links-heading">Resources</h4>
                <ul class="footer__links-list">
                    <li class="footer__links-item"><a href="#">Help Center</a></li>
                    <li class="footer__links-item"><a href="#">Study Tips</a></li>
                    <li class="footer__links-item"><a href="#">Blog</a></li>
                </ul>
            </div>
        </div>
        <div class="footer__bottom container">
            <p>&copy; <?php echo date('Y'); ?> StudyNotes. All rights reserved.</p>
        </div>
    </footer>

    <script src="/main/public/js/main.js"></script>
</body>
</html>
