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

$postsPerPage = 12;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $postsPerPage;

$categoryFilter = isset($_GET['category']) ? intval($_GET['category']) : null;

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$whereConditions = ["p.status = 'published'", "(p.publish_date IS NULL OR p.publish_date <= NOW())"];
$params = [];

if ($categoryFilter) {
    $whereConditions[] = "p.primary_category_id = :category_id";
    $params[':category_id'] = $categoryFilter;
}

if ($searchQuery) {
    $whereConditions[] = "(p.title LIKE :search OR p.excerpt LIKE :search OR p.body LIKE :search)";
    $params[':search'] = '%' . $searchQuery . '%';
}

$whereClause = implode(' AND ', $whereConditions);

try {
    $countQuery = "
        SELECT COUNT(*) as total
        FROM posts p
        WHERE $whereClause
    ";
    $countStmt = $conn->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalPosts = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalPosts / $postsPerPage);
    
    if ($currentPage > $totalPages && $totalPages > 0) {
        $currentPage = $totalPages;
        $offset = ($currentPage - 1) * $postsPerPage;
    }
} catch (PDOException $e) {
    $totalPosts = 0;
    $totalPages = 0;
    error_log("Error counting posts: " . $e->getMessage());
}

try {
    $postsQuery = "
        SELECT p.id, p.title, p.slug, p.excerpt, p.body, p.publish_date, p.published_at, p.updated_at,
               p.views_count, p.hero_media_id, p.reading_time_minutes,
               c.id as category_id, c.name as category_name, c.slug as category_slug,
               u.display_name as author_name, u.first_name, u.last_name,
               m.file_url as hero_image_url
        FROM posts p
        LEFT JOIN categories c ON p.primary_category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        LEFT JOIN media m ON p.hero_media_id = m.id
        WHERE $whereClause
        ORDER BY p.published_at DESC, p.publish_date DESC, p.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $postsStmt = $conn->prepare($postsQuery);
    foreach ($params as $key => $value) {
        $postsStmt->bindValue($key, $value);
    }
    $postsStmt->bindValue(':limit', $postsPerPage, PDO::PARAM_INT);
    $postsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $postsStmt->execute();
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $posts = [];
    error_log("Error fetching posts: " . $e->getMessage());
}

try {
    $categoriesQuery = "
        SELECT c.id, c.name, c.slug, COUNT(p.id) as post_count
        FROM categories c
        LEFT JOIN posts p ON c.id = p.primary_category_id 
            AND p.status = 'published' 
            AND (p.publish_date IS NULL OR p.publish_date <= NOW())
        GROUP BY c.id, c.name, c.slug
        HAVING post_count > 0
        ORDER BY c.name ASC
    ";
    $categoriesStmt = $conn->prepare($categoriesQuery);
    $categoriesStmt->execute();
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    error_log("Error fetching categories: " . $e->getMessage());
}

function buildPaginationUrl($page, $category = null, $search = '') {
    $params = [];
    if ($page > 1) {
        $params['page'] = $page;
    }
    if ($category) {
        $params['category'] = $category;
    }
    if ($search) {
        $params['search'] = $search;
    }
    return '/posts.php' . (!empty($params) ? '?' . http_build_query($params) : '');
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>All Posts<?php echo $categoryFilter ? ' - ' . htmlspecialchars($categories[array_search($categoryFilter, array_column($categories, 'id'))]['name'] ?? '') : ''; ?> | The Blog</title>
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
                    <h1 class="super-display D3-text">All Posts</h1>
                    <?php if ($totalPosts > 0): ?>
                        <p class="text-muted mt-2">Showing <?php echo number_format($totalPosts); ?> post<?php echo $totalPosts != 1 ? 's' : ''; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Filters and Search -->
                <div class="col-12 mt-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <form method="GET" action="/posts.php" class="d-flex gap-2">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Search posts..." 
                                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                                       style="background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(255, 255, 255, 0.6); color: #2f3a4c;">
                                <?php if ($categoryFilter): ?>
                                    <input type="hidden" name="category" value="<?php echo $categoryFilter; ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-gradient">Search</button>
                                <?php if ($searchQuery): ?>
                                    <a href="<?php echo buildPaginationUrl(1, $categoryFilter, ''); ?>" class="btn btn-outline-light">Clear</a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            <form method="GET" action="/posts.php" class="d-flex gap-2">
                                <select name="category" class="form-select" onchange="this.form.submit()" style="background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(255, 255, 255, 0.6); color: #2f3a4c;">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $categoryFilter == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?> (<?php echo $cat['post_count']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($searchQuery): ?>
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Posts Grid -->
                <section id="posts-list" class="col-12 mt-5">
                    <?php if (empty($posts)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <ion-icon name="document-text-outline"></ion-icon>
                            </div>
                            <h3 class="empty-state-title">No posts found</h3>
                            <p class="empty-state-text">
                                <?php if ($searchQuery || $categoryFilter): ?>
                                    Try adjusting your search or filter criteria.
                                <?php else: ?>
                                    Check back soon for new content!
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php foreach ($posts as $post): ?>
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
                                                        <?php if ($post['reading_time_minutes']): ?>
                                                            · <?php echo $post['reading_time_minutes']; ?> min read
                                                        <?php endif; ?>
                                                    </small>
                                                </p>
                                                <h5 class="card-title h3">
                                                    <?php echo htmlspecialchars($post['title']); ?>
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="col-12 mt-5">
                        <nav aria-label="Posts pagination">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" 
                                       href="<?php echo buildPaginationUrl($currentPage - 1, $categoryFilter, $searchQuery); ?>"
                                       aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo buildPaginationUrl(1, $categoryFilter, $searchQuery); ?>">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo buildPaginationUrl($i, $categoryFilter, $searchQuery); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Show last page if not in range -->
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo buildPaginationUrl($totalPages, $categoryFilter, $searchQuery); ?>">
                                            <?php echo $totalPages; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next Button -->
                                <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" 
                                       href="<?php echo buildPaginationUrl($currentPage + 1, $categoryFilter, $searchQuery); ?>"
                                       aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <p class="text-center text-muted mt-3">
                            Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
                        </p>
                    </div>
                <?php endif; ?>
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
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="js/main.js"></script>
</body>

</html>

