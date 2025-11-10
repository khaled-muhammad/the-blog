<?php
require_once '../config.php';
requireAuth();
?>
<!doctype html>
<html lang="en">

<head>
    <title>The Blog | Admin</title>
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
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/posts.css">
</head>

<body>
    <?php include '../incs/header.php'; ?>
    <main>
        <div class="container-fluid mt-4">
            <div class="d-flex gap-3 admin-layout align-items-stretch">
                <?php include 'incs/sidebar.php'; ?>
                <div class="admin-content flex-grow-1">
                    <?php
                        function formatNumber($num) {
                            if ($num >= 1000) {
                                return number_format($num / 1000, 1) . 'K';
                            }
                            return $num;
                        }
                        
                        function formatDate($date) {
                            if (!$date) return '—';
                            return date('M j, Y · H:i', strtotime($date));
                        }
                        
                        try {
                            $postsQuery = "
                                SELECT p.id, p.title, p.slug, p.status, p.views_count, p.comments_count, 
                                       p.updated_at, p.publish_date, p.published_at,
                                       c.name as category_name
                                FROM posts p
                                LEFT JOIN categories c ON p.primary_category_id = c.id
                                ORDER BY p.updated_at DESC
                            ";
                            $postsStmt = $conn->prepare($postsQuery);
                            $postsStmt->execute();
                            $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $categoryBreakdownQuery = "
                                SELECT c.name, 
                                       COUNT(p.id) as post_count,
                                       SUM(p.views_count) as total_views
                                FROM categories c
                                LEFT JOIN posts p ON c.id = p.primary_category_id AND p.status = 'published'
                                WHERE c.status != 'archived'
                                GROUP BY c.id, c.name
                                HAVING post_count > 0
                                ORDER BY post_count DESC
                                LIMIT 4
                            ";
                            $categoryStmt = $conn->prepare($categoryBreakdownQuery);
                            $categoryStmt->execute();
                            $categoryBreakdown = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $totalPublishedPosts = $conn->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published'")->fetch(PDO::FETCH_ASSOC)['count'] ?? 1;
                            foreach ($categoryBreakdown as &$cat) {
                                $percentage = $totalPublishedPosts > 0 ? round(($cat['post_count'] / $totalPublishedPosts) * 100) : 0;
                                $cat['share'] = $percentage . '%';
                            }
                            
                            $publishedThisMonth = $conn->query("
                                SELECT COUNT(*) as count 
                                FROM posts 
                                WHERE status = 'published' 
                                  AND MONTH(published_at) = MONTH(CURRENT_DATE())
                                  AND YEAR(published_at) = YEAR(CURRENT_DATE())
                            ")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                            
                            $publishedLastMonth = $conn->query("
                                SELECT COUNT(*) as count 
                                FROM posts 
                                WHERE status = 'published' 
                                  AND MONTH(published_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
                                  AND YEAR(published_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
                            ")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                            
                            $draftsCount = $conn->query("SELECT COUNT(*) as count FROM posts WHERE status = 'draft'")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                            
                            $avgCadence = $conn->query("
                                SELECT AVG(DATEDIFF(p2.published_at, p1.published_at)) as avg_days
                                FROM posts p1
                                INNER JOIN posts p2 ON p1.id = p2.id - 1
                                WHERE p1.status = 'published' AND p2.status = 'published'
                                  AND p1.published_at IS NOT NULL AND p2.published_at IS NOT NULL
                            ")->fetch(PDO::FETCH_ASSOC)['avg_days'] ?? 0;
                            
                        } catch (PDOException $e) {
                            error_log("Posts page error: " . $e->getMessage());
                            $posts = [];
                            $categoryBreakdown = [];
                            $publishedThisMonth = 0;
                            $publishedLastMonth = 0;
                            $draftsCount = 0;
                            $avgCadence = 0;
                        }
                    ?>

                    <header class="page-header">
                        <div>
                            <span class="page-kicker">Publishing Studio</span>
                            <h1 class="page-title">Posts overview</h1>
                            <p class="page-lede">Monitor story progress, schedule the next drop, and ensure each release lands with purpose.</p>
                        </div>
                        <a href="/admin/posts-create.php" class="btn btn-dashboard">
                            <ion-icon name="add-circle-outline"></ion-icon>
                            <span>Create post</span>
                        </a>
                    </header>

                    <section class="content-toolbar">
                        <form class="toolbar-search" role="search">
                            <label for="post-search" class="visually-hidden">Search posts</label>
                            <ion-icon name="search-outline"></ion-icon>
                            <input id="post-search" type="search" placeholder="Search titles, tags, collaborators...">
                        </form>
                        <div class="toolbar-filters">
                            <button type="button" class="filter-chip active">
                                <ion-icon name="funnel-outline"></ion-icon>
                                <span>All statuses</span>
                            </button>
                            <button type="button" class="filter-chip">
                                <ion-icon name="lock-open-outline"></ion-icon>
                                <span>Needs review</span>
                            </button>
                            <button type="button" class="filter-chip">
                                <ion-icon name="calendar-outline"></ion-icon>
                                <span>Scheduled</span>
                            </button>
                            <button type="button" class="filter-chip ghost">
                                <ion-icon name="options-outline"></ion-icon>
                                <span>More</span>
                            </button>
                        </div>
                    </section>

                    <section class="posts-dashboard-grid">
                        <article class="panel-card posts-stats">
                            <header class="panel-header">
                                <div>
                                    <span class="panel-kicker">This month</span>
                                    <h2 class="panel-title">Publishing momentum</h2>
                                </div>
                            </header>
                            <div class="posts-stats-grid">
                                <div class="stat-pill">
                                    <span class="label">Published</span>
                                    <strong><?php echo $publishedThisMonth; ?></strong>
                                    <span class="trend <?php echo ($publishedThisMonth - $publishedLastMonth) >= 0 ? 'up' : 'neutral'; ?>">
                                        <ion-icon name="<?php echo ($publishedThisMonth - $publishedLastMonth) >= 0 ? 'arrow-up-outline' : 'arrow-down-outline'; ?>"></ion-icon> 
                                        <?php 
                                        $diff = $publishedThisMonth - $publishedLastMonth;
                                        if ($diff > 0) {
                                            echo '+' . $diff . ' vs last month';
                                        } elseif ($diff < 0) {
                                            echo $diff . ' vs last month';
                                        } else {
                                            echo 'Same as last month';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="stat-pill">
                                    <span class="label">Drafts</span>
                                    <strong><?php echo $draftsCount; ?></strong>
                                    <span class="trend neutral"><ion-icon name="pause-outline"></ion-icon> Holding</span>
                                </div>
                                <div class="stat-pill">
                                    <span class="label">Avg. cadence</span>
                                    <strong><?php echo $avgCadence > 0 ? number_format($avgCadence, 1) . ' days' : '—'; ?></strong>
                                    <span class="trend up"><ion-icon name="sparkles-outline"></ion-icon> <?php echo $avgCadence > 0 ? 'Consistent' : 'No data'; ?></span>
                                </div>
                            </div>
                            <?php if (!empty($categoryBreakdown)): ?>
                            <div class="category-mini">
                                <span class="mini-title">Category share</span>
                                <ul>
                                    <?php 
                                    $colors = ['primary', 'secondary', 'neutral', 'accent'];
                                    foreach ($categoryBreakdown as $index => $category) : 
                                        $color = $colors[$index % count($colors)];
                                    ?>
                                        <li>
                                            <span class="dot dot-<?php echo $color; ?>"></span>
                                            <span><?php echo htmlspecialchars($category['name']); ?></span>
                                            <strong><?php echo $category['share']; ?></strong>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </article>

                        <article class="panel-card posts-table-card">
                            <header class="panel-header">
                                <div>
                                    <span class="panel-kicker">Queue</span>
                                    <h2 class="panel-title">Active posts timeline</h2>
                                </div>
                                <div class="table-actions">
                                    <button type="button" class="table-action">
                                        <ion-icon name="cloud-upload-outline"></ion-icon>
                                        <span>Bulk publish</span>
                                    </button>
                                    <button type="button" class="table-action ghost">
                                        <ion-icon name="download-outline"></ion-icon>
                                        <span>Export</span>
                                    </button>
                                </div>
                            </header>
                            <div class="posts-table-wrapper" role="region" aria-live="polite">
                                <table class="posts-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Title</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Updated</th>
                                            <th scope="col">Views</th>
                                            <th scope="col">Comments</th>
                                            <th scope="col" class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($posts)): ?>
                                            <tr>
                                                <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">
                                                    No posts yet. <a href="/admin/posts-create.php">Create your first post</a>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($posts as $post) : ?>
                                                <tr>
                                                    <td>
                                                        <div class="title-stack">
                                                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                                            <span class="meta">ID <?php echo $post['id']; ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="status-chip status-<?php echo strtolower(str_replace(' ', '-', $post['status'])); ?>">
                                                            <?php echo ucfirst(str_replace('-', ' ', $post['status'])); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="category-tag"><?php echo htmlspecialchars($post['category_name'] ?: 'Uncategorized'); ?></span>
                                                    </td>
                                                    <td><?php echo formatDate($post['updated_at']); ?></td>
                                                    <td><?php echo $post['status'] === 'published' ? formatNumber($post['views_count']) : '—'; ?></td>
                                                    <td><?php echo $post['comments_count'] ?? 0; ?></td>
                                                    <td class="text-end">
                                                        <div class="table-actions">
                                                            <a href="/admin/posts-create.php?edit=<?php echo $post['id']; ?>" class="table-action">
                                                                <ion-icon name="create-outline"></ion-icon>
                                                                <span>Edit</span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <?php include '../incs/footer.php'; ?>
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
    <script src="../js/main.js"></script>
    <script src="../js/sidebar.js"></script>
</body>

</html>