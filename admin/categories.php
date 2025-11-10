<?php
require_once '../config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $categoryId = intval($_POST['category_id']);
    
    if ($categoryId > 0) {
        try {
            $checkPosts = $conn->prepare("SELECT COUNT(*) as count FROM posts WHERE primary_category_id = ?");
            $checkPosts->execute([$categoryId]);
            $postCount = $checkPosts->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($postCount > 0) {
                $updateStmt = $conn->prepare("UPDATE categories SET status = 'archived' WHERE id = ?");
                $updateStmt->execute([$categoryId]);
                $success = 'Category archived successfully (it has ' . $postCount . ' posts).';
            } else {
                $deleteStmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                $deleteStmt->execute([$categoryId]);
                $success = 'Category deleted successfully.';
            }
            
            header('Location: /admin/categories.php?success=' . urlencode($success));
            exit;
        } catch (PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            $error = 'Failed to delete category. Please try again.';
        }
    }
}

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($error) ? $error : '';
?>
<!doctype html>
<html lang="en">

<head>
    <title>The Blog | Admin Categories</title>
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
    <link rel="stylesheet" href="../css/categories.css">
</head>

<body>
    <?php include '../incs/header.php'; ?>
    <main>
        <div class="container-fluid mt-4">
            <div class="d-flex gap-3 admin-layout align-items-stretch">
                <?php include 'incs/sidebar.php'; ?>
                <div class="admin-content flex-grow-1">
                    <?php if ($error): ?>
                        <div class="alert alert-danger" style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success" style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    <?php
                        function timeAgo($datetime) {
                            if (!$datetime) return 'Never updated';
                            $timestamp = strtotime($datetime);
                            $diff = time() - $timestamp;
                            
                            if ($diff < 3600) {
                                $mins = floor($diff / 60);
                                return $mins > 0 ? 'Updated ' . $mins . 'm ago' : 'Updated just now';
                            } elseif ($diff < 86400) {
                                $hours = floor($diff / 3600);
                                return 'Updated ' . $hours . 'h ago';
                            } elseif ($diff < 604800) {
                                $days = floor($diff / 86400);
                                return 'Updated ' . $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
                            } else {
                                return 'Updated ' . date('M j', $timestamp);
                            }
                        }
                        
                        try {
                            $categoriesQuery = "
                                SELECT c.id, c.name, c.summary, c.status, c.updated_at,
                                       COUNT(p.id) as posts_count,
                                       SUM(p.views_count) as total_views
                                FROM categories c
                                LEFT JOIN posts p ON c.id = p.primary_category_id AND p.status = 'published'
                                WHERE c.status != 'archived'
                                GROUP BY c.id, c.name, c.summary, c.status, c.updated_at
                                ORDER BY c.display_order ASC, c.name ASC
                            ";
                            $categoriesStmt = $conn->prepare($categoriesQuery);
                            $categoriesStmt->execute();
                            $categoriesData = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $colors = ['primary', 'secondary', 'neutral', 'accent', 'lilac'];
                            $totalViews = array_sum(array_column($categoriesData, 'total_views'));
                            
                            $categories = [];
                            foreach ($categoriesData as $index => $cat) {
                                $percentage = $totalViews > 0 ? round(($cat['total_views'] / $totalViews) * 100) : 0;
                                $statusLabels = [
                                    'active' => 'Active',
                                    'featured' => 'Featured',
                                    'growing' => 'Growing',
                                    'steady' => 'Steady',
                                    'priority' => 'Priority',
                                    'emerging' => 'Emerging'
                                ];
                                
                                $categories[] = [
                                    'id' => $cat['id'],
                                    'name' => $cat['name'],
                                    'summary' => $cat['summary'] ?: 'No description available.',
                                    'posts' => $cat['posts_count'],
                                    'engagement' => $percentage > 0 ? $percentage . '% of views' : 'No views yet',
                                    'updated' => timeAgo($cat['updated_at']),
                                    'status' => $statusLabels[$cat['status']] ?? 'Active',
                                    'color' => $colors[$index % count($colors)]
                                ];
                            }
                            
                            $spotlightQuery = "
                                SELECT c.name, 
                                       SUM(p.views_count) as total_views,
                                       COUNT(p.id) as post_count
                                FROM categories c
                                LEFT JOIN posts p ON c.id = p.primary_category_id AND p.status = 'published'
                                WHERE c.status IN ('featured', 'active', 'growing')
                                GROUP BY c.id, c.name
                                HAVING total_views > 0
                                ORDER BY c.status = 'featured' DESC, total_views DESC
                                LIMIT 1
                            ";
                            $spotlightData = $conn->query($spotlightQuery)->fetch(PDO::FETCH_ASSOC);
                            
                            if ($spotlightData) {
                                $spotlight = [
                                    'name' => $spotlightData['name'],
                                    'momentum' => $spotlightData['post_count'] . ' posts',
                                    'audience' => 'Top performing category',
                                    'next_release' => '—',
                                    'notes' => [
                                        'This category has ' . $spotlightData['total_views'] . ' total views.',
                                        'Consider creating more content in this category.',
                                        'Engage with comments and feedback.'
                                    ]
                                ];
                            } else {
                                $spotlight = null;
                            }
                            
                        } catch (PDOException $e) {
                            error_log("Categories page error: " . $e->getMessage());
                            $categories = [];
                            $spotlight = null;
                        }
                    ?>

                    <header class="page-header">
                        <div>
                            <span class="page-kicker">Information Architecture</span>
                            <h1 class="page-title">Categories &amp; collections</h1>
                            <p class="page-lede">Maintain thematic balance, spotlight rising narratives, and keep discovery pathways fresh.</p>
                        </div>
                        <a href="/admin/category-create.php" class="btn btn-dashboard">
                            <ion-icon name="add-circle-outline"></ion-icon>
                            <span>New category</span>
                        </a>
                    </header>

                    <section class="category-toolbar">
                        <form class="toolbar-search" role="search">
                            <label for="category-search" class="visually-hidden">Search categories</label>
                            <ion-icon name="search-outline"></ion-icon>
                            <input id="category-search" type="search" placeholder="Search categories, owners, or keywords...">
                        </form>
                        <div class="toolbar-actions">
                            <button type="button" class="filter-chip active">
                                <ion-icon name="sparkles-outline"></ion-icon>
                                <span>Core pillars</span>
                            </button>
                            <button type="button" class="filter-chip">
                                <ion-icon name="trending-up-outline"></ion-icon>
                                <span>Growth</span>
                            </button>
                            <button type="button" class="filter-chip ghost">
                                <ion-icon name="options-outline"></ion-icon>
                                <span>Segments</span>
                            </button>
                        </div>
                    </section>

                    <section class="categories-grid">
                        <article class="panel-card category-list-panel">
                            <header class="panel-header">
                                <div>
                                    <span class="panel-kicker">Collections</span>
                                    <h2 class="panel-title">Active categories</h2>
                                </div>
                                <button type="button" class="panel-action" aria-label="Reorder categories">
                                    <ion-icon name="swap-vertical-outline"></ion-icon>
                                </button>
                            </header>
                            <ul class="category-list">
                                <?php if (empty($categories)): ?>
                                    <li class="category-item" style="text-align: center; padding: 2rem; color: #6b7280;">
                                        No categories yet. <a href="/admin/category-create.php">Create your first category</a>
                                    </li>
                                <?php else: ?>
                                    <?php foreach ($categories as $category) : ?>
                                        <li class="category-item">
                                            <div class="category-item-header">
                                                <div class="category-name">
                                                    <span class="category-dot category-dot-<?php echo $category['color']; ?>"></span>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                                        <p><?php echo htmlspecialchars($category['summary']); ?></p>
                                                    </div>
                                                </div>
                                                <span class="category-status status-<?php echo strtolower($category['status']); ?>"><?php echo htmlspecialchars($category['status']); ?></span>
                                            </div>
                                            <div class="category-item-metrics">
                                                <span><ion-icon name="document-text-outline"></ion-icon><?php echo $category['posts']; ?> posts</span>
                                                <span><ion-icon name="pulse-outline"></ion-icon><?php echo $category['engagement']; ?></span>
                                                <span><ion-icon name="time-outline"></ion-icon><?php echo $category['updated']; ?></span>
                                            </div>
                                            <div class="category-item-actions">
                                                <div>
                                                <a href="/admin/category-create.php?edit=<?php echo $category['id']; ?>" class="category-action">
                                                    <ion-icon name="create-outline"></ion-icon>
                                                    <span>Edit</span>
                                                </a>
                                                </div>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                    <button type="submit" name="delete_category" class="category-action ghost" style="color: #ef4444;">
                                                        <ion-icon name="trash-outline"></ion-icon>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </article>

                        <div class="category-side-stack">
                            <?php if ($spotlight): ?>
                            <article class="panel-card spotlight-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Spotlight</span>
                                        <h2 class="panel-title"><?php echo htmlspecialchars($spotlight['name']); ?> snapshot</h2>
                                    </div>
                                    <span class="badge badge-soft"><?php echo htmlspecialchars($spotlight['momentum']); ?></span>
                                </header>
                                <div class="spotlight-body">
                                    <div class="spotlight-meta">
                                        <div>
                                            <span class="label">Audience resonance</span>
                                            <strong><?php echo htmlspecialchars($spotlight['audience']); ?></strong>
                                        </div>
                                        <div>
                                            <span class="label">Status</span>
                                            <strong><?php echo htmlspecialchars($spotlight['next_release']); ?></strong>
                                        </div>
                                    </div>
                                    <ul class="spotlight-notes">
                                        <?php foreach ($spotlight['notes'] as $note) : ?>
                                            <li>
                                                <ion-icon name="checkmark-circle-outline"></ion-icon>
                                                <span><?php echo htmlspecialchars($note); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </article>
                            <?php endif; ?>

                            <article class="panel-card initiatives-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Quick Stats</span>
                                        <h2 class="panel-title">Category overview</h2>
                                    </div>
                                </header>
                                <ul class="initiatives-list">
                                    <?php
                                    $totalCategories = count($categories);
                                    $totalPosts = array_sum(array_column($categories, 'posts'));
                                    $avgPostsPerCategory = $totalCategories > 0 ? round($totalPosts / $totalCategories, 1) : 0;
                                    ?>
                                    <li>
                                        <div>
                                            <strong>Total Categories</strong>
                                            <span class="tag"><?php echo $totalCategories; ?> active</span>
                                        </div>
                                        <div class="initiative-meta">
                                            <span>All categories</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <strong>Total Posts</strong>
                                            <span class="tag"><?php echo $totalPosts; ?> posts</span>
                                        </div>
                                        <div class="initiative-meta">
                                            <span>Across all categories</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <strong>Average Posts</strong>
                                            <span class="tag"><?php echo $avgPostsPerCategory; ?> per category</span>
                                        </div>
                                        <div class="initiative-meta">
                                            <span>Distribution</span>
                                        </div>
                                    </li>
                                </ul>
                            </article>
                        </div>
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
