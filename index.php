<!doctype html>
<html lang="en">

<head>
    <title>The Blog</title>
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
</head>

<body>
    <?php include 'incs/header.php'; ?>
    <main>
        <div class="container mt-4">
            <div class="row">
                <div class="col-12">
                    <h1 class="super-display D3-text">The Blog</h1>
                </div>
                <div class="col-12">
                    <div class="most-popular-post mt-5">
                        <div class="card mb-3 bg-transparent border-0">
                            <div class="row g-0">
                                <div class="col-md-5">
                                    <img
                                        src="https://4kwallpapers.com/images/wallpapers/blue-aesthetic-3840x2400-12656.jpg"
                                        class="img-fluid rounded-3 h-100 object-fit-cover"
                                        alt="Card title" />
                                </div>
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <p class="card-text">
                                            <small class="text-muted">Last updated 3 mins ago</small>
                                        </p>
                                        <h5 class="card-title display-3 fw-bold">Tentang Creativity Block Pada UI Designer</h5>
                                        <p class="card-text">
                                            This is a wider card with supporting text below as a
                                            natural lead-in to additional content. This content is a
                                            little bit longer.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="overlay"></div>
                        <a class="overlay-btn" href="/post.php">View Post</a>
                    </div>
                </div>

                <section id="latest-posts" class="col-12 mt-5">
                    <h2 class="mb-4 magic-subtitle">Latest Posts</h2>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        <?php for ($i = 0; $i < 6; $i++) : ?>
                            <div class="col col-xz">
                                <div class="card card-x-row bg-transparent border-0 h-100">
                                    <img
                                        src="https://wallpapers.com/images/featured/pastel-aesthetic-background-pw1aey935bvyso6f.jpg"
                                        class="card-img-top rounded-3 object-fit-cover"
                                        alt="Card title" />
                                    <div class="card-body">
                                        <p class="card-text date">
                                            <small class="text-muted">Last updated 3 mins ago</small>
                                        </p>
                                        <h5 class="card-title h3">Card title</h5>
                                        <p class="card-text">
                                            This is a longer card with supporting text below as a
                                            natural lead-in to additional content. This content is a
                                            little bit longer.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </section>
    </main>
    <?php include 'incs/footer.php'; ?>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="js/main.js"></script>
</body>

</html>