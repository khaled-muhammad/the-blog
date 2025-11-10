<?php
require_once 'config.php';

if (!hasAdminUsers()) {
    header('Location: /register.php');
    exit;
}

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

try {
    $featuredQuery = "
        SELECT p.id, p.title, p.slug, p.excerpt, p.body, p.publish_date, p.published_at, p.updated_at,
               p.views_count, p.hero_media_id,
               c.name as category_name, c.slug as category_slug,
               u.display_name as author_name, u.first_name, u.last_name,
               m.file_url as hero_image_url
        FROM posts p
        LEFT JOIN categories c ON p.primary_category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        LEFT JOIN media m ON p.hero_media_id = m.id
        WHERE p.status = 'published' 
          AND (p.publish_date IS NULL OR p.publish_date <= NOW())
        ORDER BY p.featured DESC, p.published_at DESC, p.publish_date DESC, p.created_at DESC
        LIMIT 1
    ";
    $featuredStmt = $conn->prepare($featuredQuery);
    $featuredStmt->execute();
    $featuredPost = $featuredStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featuredPost = null;
    error_log("Error fetching featured post: " . $e->getMessage());
}

$featuredPostId = $featuredPost ? (int)$featuredPost['id'] : 0;
try {
    if ($featuredPostId > 0) {
        $latestQuery = "
            SELECT p.id, p.title, p.slug, p.excerpt, p.body, p.publish_date, p.published_at, p.updated_at,
                   p.views_count, p.hero_media_id,
                   c.name as category_name, c.slug as category_slug,
                   u.display_name as author_name, u.first_name, u.last_name,
                   m.file_url as hero_image_url
            FROM posts p
            LEFT JOIN categories c ON p.primary_category_id = c.id
            LEFT JOIN users u ON p.author_id = u.id
            LEFT JOIN media m ON p.hero_media_id = m.id
            WHERE p.status = 'published' 
              AND (p.publish_date IS NULL OR p.publish_date <= NOW())
              AND p.id != :featured_id
            ORDER BY p.published_at DESC, p.publish_date DESC, p.created_at DESC
            LIMIT 6
        ";
        $latestStmt = $conn->prepare($latestQuery);
        $latestStmt->bindValue(':featured_id', $featuredPostId, PDO::PARAM_INT);
    } else {
        $latestQuery = "
            SELECT p.id, p.title, p.slug, p.excerpt, p.body, p.publish_date, p.published_at, p.updated_at,
                   p.views_count, p.hero_media_id,
                   c.name as category_name, c.slug as category_slug,
                   u.display_name as author_name, u.first_name, u.last_name,
                   m.file_url as hero_image_url
            FROM posts p
            LEFT JOIN categories c ON p.primary_category_id = c.id
            LEFT JOIN users u ON p.author_id = u.id
            LEFT JOIN media m ON p.hero_media_id = m.id
            WHERE p.status = 'published' 
              AND (p.publish_date IS NULL OR p.publish_date <= NOW())
            ORDER BY p.published_at DESC, p.publish_date DESC, p.created_at DESC
            LIMIT 6
        ";
        $latestStmt = $conn->prepare($latestQuery);
    }
    $latestStmt->execute();
    $latestPosts = $latestStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latestPosts = [];
    error_log("Error fetching latest posts: " . $e->getMessage());
}
?>
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
                
                <?php if ($featuredPost): ?>
                <div class="col-12">
                    <div class="most-popular-post mt-5">
                        <div class="card mb-3 bg-transparent border-0">
                            <div class="row g-0">
                                <div class="col-md-5">
                                    <img
                                        src="<?php echo htmlspecialchars($featuredPost['hero_image_url'] ?: 'https://4kwallpapers.com/images/wallpapers/blue-aesthetic-3840x2400-12656.jpg'); ?>"
                                        class="img-fluid rounded-3 h-100 object-fit-cover"
                                        alt="<?php echo htmlspecialchars($featuredPost['title']); ?>" />
                                </div>
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <?php 
                                                $dateToShow = $featuredPost['updated_at'] ?: $featuredPost['published_at'] ?: $featuredPost['publish_date'];
                                                echo timeAgo($dateToShow);
                                                ?>
                                                <?php if ($featuredPost['category_name']): ?>
                                                    · <?php echo htmlspecialchars($featuredPost['category_name']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </p>
                                        <h5 class="card-title display-3 fw-bold"><?php echo htmlspecialchars($featuredPost['title']); ?></h5>
                                        <p class="card-text">
                                            <?php 
                                            $excerpt = $featuredPost['excerpt'] ?: substr(strip_tags($featuredPost['body'] ?? ''), 0, 150);
                                            echo htmlspecialchars($excerpt);
                                            if (strlen($excerpt) >= 150) echo '...';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="overlay"></div>
                        <a class="overlay-btn" href="/post.php?slug=<?php echo urlencode($featuredPost['slug']); ?>">View Post</a>
                    </div>
                </div>
                <?php endif; ?>

                <section id="latest-posts" class="col-12 mt-5">
                    <h2 class="mb-4 magic-subtitle">Latest Posts</h2>
                    <?php if (empty($latestPosts)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <ion-icon name="document-text-outline"></ion-icon>
                            </div>
                            <h3 class="empty-state-title">No posts yet</h3>
                            <p class="empty-state-text">Check back soon for new content!</p>
                        </div>
                    <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        <?php foreach ($latestPosts as $post): ?>
                            <div class="col col-xz">
                                <a href="/post.php?slug=<?php echo urlencode($post['slug']); ?>" style="text-decoration: none;">
                                <div class="card card-x-row bg-transparent border-0 h-100">
                                    <img
                                        src="<?php echo htmlspecialchars($post['hero_image_url'] ?: 'https://wallpapers.com/images/featured/pastel-aesthetic-background-pw1aey935bvyso6f.jpg'); ?>"
                                        class="card-img-top rounded-3 object-fit-cover"
                                        alt="<?php echo htmlspecialchars($post['title']); ?>" />
                                    <div class="card-body">
                                        <p class="card-text date">
                                            <small class="text-muted">
                                                <?php 
                                                $dateToShow = $post['updated_at'] ?: $post['published_at'] ?: $post['publish_date'];
                                                echo timeAgo($dateToShow);
                                                ?>
                                                <?php if ($post['category_name']): ?>
                                                    · <?php echo htmlspecialchars($post['category_name']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </p>
                                        <h5 class="card-title h3">
                                            <a href="/post.php?slug=<?php echo urlencode($post['slug']); ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </a>
                                        </h5>
                                        <p class="card-text">
                                            <?php 
                                            $excerpt = $post['excerpt'] ?: substr(strip_tags($post['body'] ?? ''), 0, 120);
                                            echo htmlspecialchars($excerpt);
                                            if (strlen($excerpt) >= 120) echo '...';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
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