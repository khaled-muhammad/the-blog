<?php
require_once 'config.php';

if (!hasAdminUsers()) {
    header('Location: /register.php');
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>About Me | The Blog</title>
    <meta name="description" content="Learn more about Khaled Muhammad - Full Stack & Mobile Developer, Senior 3 Student at We School, Alexandria, Egypt">
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/about.css">
</head>

<body>
    <?php include 'incs/header.php'; ?>
    <main>
        <div class="container mt-4">
            <div class="row">
                <!-- Hero Section -->
                <div class="col-12">
                    <div class="about-hero">
                        <div class="row align-items-center g-4">
                            <div class="col-12 col-md-4 text-center text-md-start">
                                <div class="about-image-wrapper">
                                    <img src="https://ca.slack-edge.com/T0266FRGM-U08QKDF6F9B-edcd82c42589-512" 
                                         alt="Khaled Muhammad" 
                                         class="about-image">
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="about-hero-content">
                                    <div class="about-badge">
                                        <span class="badge-text">Open To Work</span>
                                    </div>
                                    <h1 class="about-name">Hi, I am <span class="gradient-text">Khaled Muhammad</span></h1>
                                    <div class="about-subtitle">
                                        <p class="about-role">Senior 3 Student, We School</p>
                                        <p class="about-location">
                                            <ion-icon name="location-outline"></ion-icon>
                                            Alexandria, Egypt 🇪🇬
                                        </p>
                                    </div>
                                    <p class="about-title">Full Stack & Mobile Developer</p>
                                    <div class="about-portfolio-link mt-4">
                                        <a href="https://iamkhaled.xyz" target="_blank" rel="noopener noreferrer" class="portfolio-btn">
                                            <ion-icon name="globe-outline"></ion-icon>
                                            Visit My Portfolio
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Focus Areas -->
                <div class="col-12 mt-5">
                    <h2 class="section-title">Focus Areas</h2>
                    <div class="row g-4 mt-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="focus-card">
                                <div class="focus-icon">
                                    <ion-icon name="phone-portrait-outline"></ion-icon>
                                </div>
                                <h3 class="focus-title">Mobile Apps</h3>
                                <p class="focus-description">Building native and cross-platform mobile applications</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="focus-card">
                                <div class="focus-icon">
                                    <ion-icon name="sparkles-outline"></ion-icon>
                                </div>
                                <h3 class="focus-title">AI-backed Tools</h3>
                                <p class="focus-description">Creating intelligent solutions powered by AI</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="focus-card">
                                <div class="focus-icon">
                                    <ion-icon name="color-palette-outline"></ion-icon>
                                </div>
                                <h3 class="focus-title">UI/UX</h3>
                                <p class="focus-description">Designing beautiful and intuitive user experiences</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="focus-card">
                                <div class="focus-icon">
                                    <ion-icon name="bulb-outline"></ion-icon>
                                </div>
                                <h3 class="focus-title">Problem-solving</h3>
                                <p class="focus-description">Tackling complex challenges with creative solutions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Values Section -->
                <div class="col-12 mt-5">
                    <h2 class="section-title">Values</h2>
                    <div class="values-container">
                        <div class="value-item">
                            <div class="value-icon">
                                <ion-icon name="search-outline"></ion-icon>
                            </div>
                            <h3 class="value-title">Curiosity</h3>
                        </div>
                        <div class="value-separator">·</div>
                        <div class="value-item">
                            <div class="value-icon">
                                <ion-icon name="trending-up-outline"></ion-icon>
                            </div>
                            <h3 class="value-title">Growth</h3>
                        </div>
                        <div class="value-separator">·</div>
                        <div class="value-item">
                            <div class="value-icon">
                                <ion-icon name="rocket-outline"></ion-icon>
                            </div>
                            <h3 class="value-title">Impact</h3>
                        </div>
                    </div>
                </div>

                <!-- About Me Section -->
                <div class="col-12 col-lg-10 offset-lg-1 mt-5">
                    <div class="about-content">
                        <div class="about-content-header">
                            <h2 class="about-content-title">About Me</h2>
                            <p class="about-content-tagline">Engineering Ideas. Hacking Problems. Designing with Purpose.</p>
                        </div>
                        <div class="about-content-body">
                            <p>
                                Hi, I am <strong>Khaled Muhammad</strong>, A high school student with a lot of passion towards technology since childhood. I started programming when I was 8 years old. Mum & Dad helped me alot at that age to start. And by time, I learnt many things and created a lot of projects, Joined many competitions in C.P field, Scientific field, and Entrepreneurship too, also I was selected to join more than one Scholarship like <strong>(DECI)</strong> and <strong>(Ebhar Misr)</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Read More Section -->
                <div class="col-12 mt-5 mb-5">
                    <div class="read-more-section">
                        <h3 class="read-more-title">Read More About Me</h3>
                        <p class="read-more-text">Want to know more? Feel free to reach out!</p>
                        <div class="read-more-actions">
                            <a href="https://iamkhaled.xyz" target="_blank" rel="noopener noreferrer" class="btn-gradient">
                                <ion-icon name="globe-outline"></ion-icon>
                                Visit My Portfolio
                            </a>
                            <a href="/posts.php" class="btn-outline">View My Posts</a>
                            <a href="/bookmarks.php" class="btn-outline">Check Bookmarks</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'incs/footer.php'; ?>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="js/main.js"></script>
</body>

</html>

