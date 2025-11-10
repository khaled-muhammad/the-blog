<?php
require_once '../config.php';
requireAuth();

function formatNumber($num) {
    if ($num >= 1000) {
        return number_format($num / 1000, 1) . 'K';
    }
    return $num;
}

try {
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
    
    $publishedDiff = $publishedThisMonth - $publishedLastMonth;
    
    $draftsCount = $conn->query("
        SELECT COUNT(*) as count 
        FROM posts 
        WHERE status = 'draft'
    ")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    $inReviewCount = $conn->query("
        SELECT COUNT(*) as count 
        FROM posts 
        WHERE status = 'in-review'
    ")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    $commentsPending = $conn->query("
        SELECT COUNT(*) as count 
        FROM comments 
        WHERE status = 'pending'
    ")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    $commentsNeedingReplies = $conn->query("
        SELECT COUNT(DISTINCT c.post_id) as count 
        FROM comments c
        INNER JOIN posts p ON c.post_id = p.id
        WHERE c.status = 'approved' AND p.status = 'published'
    ")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    $totalViews = $conn->query("
        SELECT SUM(views_count) as total 
        FROM posts 
        WHERE status = 'published'
    ")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $avgReadingTime = $conn->query("
        SELECT AVG(reading_time_minutes) as avg 
        FROM posts 
        WHERE status = 'published' AND reading_time_minutes IS NOT NULL
    ")->fetch(PDO::FETCH_ASSOC)['avg'] ?? 0;
    
    $topCategory = $conn->query("
        SELECT c.name, 
               SUM(p.views_count) as total_views,
               COUNT(p.id) as post_count
        FROM categories c
        LEFT JOIN posts p ON c.id = p.primary_category_id AND p.status = 'published'
        GROUP BY c.id, c.name
        HAVING total_views > 0
        ORDER BY total_views DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);
    
    $recentPosts = $conn->query("
        SELECT p.title, p.status, p.updated_at, c.name as category_name
        FROM posts p
        LEFT JOIN categories c ON p.primary_category_id = c.id
        ORDER BY p.updated_at DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $publishedThisMonth = 0;
    $publishedDiff = 0;
    $draftsCount = 0;
    $inReviewCount = 0;
    $commentsPending = 0;
    $commentsNeedingReplies = 0;
    $totalViews = 0;
    $avgReadingTime = 0;
    $topCategory = null;
    $recentPosts = [];
}

$currentUser = getCurrentUser();
$userDisplayName = $currentUser['display_name'] ?? ($currentUser['first_name'] ?? 'Creator');
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
</head>

<body>
    <?php include '../incs/header.php'; ?>
    <main>
        <div class="container-fluid mt-4">
            <div class="d-flex gap-3 admin-layout align-items-stretch">
                <?php include 'incs/sidebar.php'; ?>
                <div class="admin-content flex-grow-1">
                    <section class="dashboard-hero mb-4">
                        <div class="hero-text">
                            <span class="hero-kicker">Welcome back, <?php echo htmlspecialchars($userDisplayName); ?></span>
                            <h1 class="hero-title">Your creative pulse at a glance</h1>
                            <p class="hero-subtitle">Track momentum, spot opportunities, and keep the community engaged.</p>
                            <div class="hero-actions">
                                <a href="/admin/posts-create.php" class="btn btn-dashboard">
                                    <ion-icon name="add-circle-outline"></ion-icon>
                                    <span>New post</span>
                                </a>
                                <div class="hero-meta" aria-label="Current date">
                                    <ion-icon name="calendar-clear-outline"></ion-icon>
                                    <span><?php echo date('l, F j'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="hero-highlight">
                            <div class="highlight-stat">
                                <span class="label">Total views</span>
                                <strong><?php echo formatNumber($totalViews); ?></strong>
                                <span class="trend up">
                                    <ion-icon name="eye-outline"></ion-icon>
                                    All time
                                </span>
                            </div>
                            <div class="highlight-divider"></div>
                            <div class="highlight-stat">
                                <span class="label">Avg. read time</span>
                                <strong><?php echo $avgReadingTime > 0 ? round($avgReadingTime) . 'm' : '—'; ?></strong>
                                <span class="trend steady">
                                    <ion-icon name="time-outline"></ion-icon>
                                    <?php echo $avgReadingTime > 0 ? 'average' : 'no data'; ?>
                                </span>
                            </div>
                        </div>
                    </section>

                    <section class="dashboard-metrics mb-4" aria-labelledby="dashboard-metrics-heading">
                        <h2 id="dashboard-metrics-heading" class="visually-hidden">Key metrics</h2>
                        <div class="metrics-grid">
                            <article class="metric-card">
                                <div class="metric-icon gradient-primary">
                                    <ion-icon name="sparkles-outline"></ion-icon>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label">Posts published</span>
                                    <strong class="metric-value"><?php echo $publishedThisMonth; ?></strong>
                                    <span class="metric-delta <?php echo $publishedDiff >= 0 ? 'up' : 'neutral'; ?>">
                                        <ion-icon name="<?php echo $publishedDiff >= 0 ? 'trending-up-outline' : 'remove-outline'; ?>"></ion-icon>
                                        <?php 
                                        if ($publishedDiff > 0) {
                                            echo '+' . $publishedDiff . ' vs last month';
                                        } elseif ($publishedDiff < 0) {
                                            echo $publishedDiff . ' vs last month';
                                        } else {
                                            echo 'Same as last month';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </article>
                            <article class="metric-card">
                                <div class="metric-icon gradient-secondary">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label">Drafts in progress</span>
                                    <strong class="metric-value"><?php echo $draftsCount + $inReviewCount; ?></strong>
                                    <span class="metric-delta neutral">
                                        <ion-icon name="pause-outline"></ion-icon>
                                        <?php echo $inReviewCount > 0 ? $inReviewCount . ' in review' : 'awaiting review'; ?>
                                    </span>
                                </div>
                            </article>
                            <article class="metric-card">
                                <div class="metric-icon gradient-primary">
                                    <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label">Comments pending</span>
                                    <strong class="metric-value"><?php echo $commentsPending; ?></strong>
                                    <span class="metric-delta up">
                                        <ion-icon name="time-outline"></ion-icon>
                                        <?php echo $commentsNeedingReplies > 0 ? $commentsNeedingReplies . ' need replies' : 'all clear'; ?>
                                    </span>
                                </div>
                            </article>
                            <article class="metric-card">
                                <div class="metric-icon gradient-secondary">
                                    <ion-icon name="albums-outline"></ion-icon>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label">Categories</span>
                                    <strong class="metric-value"><?php 
                                        $categoryCount = $conn->query("SELECT COUNT(*) as count FROM categories WHERE status != 'archived'")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                                        echo $categoryCount;
                                    ?></strong>
                                    <span class="metric-delta up">
                                        <ion-icon name="sparkles-outline"></ion-icon>
                                        Active
                                    </span>
                                </div>
                            </article>
                        </div>
                    </section>

                    <section class="dashboard-panels row g-4">
                        <div class="col-12 col-xl-6">
                            <article class="panel-card insights-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Insights</span>
                                        <h2 class="panel-title">Content health summary</h2>
                                    </div>
                                    <button type="button" class="panel-action" aria-label="Download insights report">
                                        <ion-icon name="download-outline"></ion-icon>
                                    </button>
                                </header>
                                <ul class="insights-list">
                                    <?php if ($topCategory): ?>
                                    <li>
                                        <div>
                                            <strong><?php echo htmlspecialchars($topCategory['name']); ?></strong>
                                            <p>Top-performing category with <?php echo formatNumber($topCategory['total_views']); ?> total views.</p>
                                        </div>
                                        <span class="badge badge-soft"><?php echo $topCategory['post_count']; ?> posts</span>
                                    </li>
                                    <?php endif; ?>
                                    <li>
                                        <div>
                                            <strong>Content status</strong>
                                            <p><?php 
                                                $totalPosts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                                                $publishedPosts = $conn->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published'")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                                                $publishRate = $totalPosts > 0 ? round(($publishedPosts / $totalPosts) * 100) : 0;
                                                echo $publishRate . '% of posts are published.';
                                            ?></p>
                                        </div>
                                        <span class="badge badge-soft"><?php echo $publishedPosts; ?> published</span>
                                    </li>
                                    <?php if ($totalViews > 0): ?>
                                    <li>
                                        <div>
                                            <strong>Engagement</strong>
                                            <p>Total views across all published content.</p>
                                        </div>
                                        <span class="badge badge-soft"><?php echo formatNumber($totalViews); ?> views</span>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </article>
                        </div>
                        <div class="col-12 col-xl-6">
                            <article class="panel-card activity-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Recent activity</span>
                                        <h2 class="panel-title">Latest workflow notes</h2>
                                    </div>
                                </header>
                                <ol class="activity-timeline">
                                    <?php if (empty($recentPosts)): ?>
                                        <li>
                                            <span class="timeline-point gradient-primary"></span>
                                            <div class="timeline-content">
                                                <strong>No recent activity</strong>
                                                <p>Start creating posts to see activity here.</p>
                                            </div>
                                        </li>
                                    <?php else: ?>
                                        <?php foreach ($recentPosts as $index => $post): ?>
                                            <li>
                                                <span class="timeline-point gradient-<?php echo ($index % 2 == 0) ? 'primary' : 'secondary'; ?>"></span>
                                                <div class="timeline-content">
                                                    <strong>Post updated: <?php echo htmlspecialchars($post['title']); ?></strong>
                                                    <p>Status: <?php echo ucfirst(str_replace('-', ' ', $post['status'])); ?><?php echo $post['category_name'] ? ' · ' . htmlspecialchars($post['category_name']) : ''; ?></p>
                                                    <time datetime="<?php echo date('Y-m-d\TH:i', strtotime($post['updated_at'])); ?>">
                                                        <?php echo date('H:i · M j', strtotime($post['updated_at'])); ?>
                                                    </time>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ol>
                            </article>
                        </div>
                        <div class="col-12 col-xl-5">
                            <article class="panel-card tasks-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Action items</span>
                                        <h2 class="panel-title">Today's focus</h2>
                                    </div>
                                </header>
                                <ul class="tasks-list">
                                    <li>
                                        <ion-icon name="checkbox-outline"></ion-icon>
                                        <span>Reply to featured artist interview comments</span>
                                        <span class="task-meta">Due 11:30</span>
                                    </li>
                                    <li>
                                        <ion-icon name="square-outline"></ion-icon>
                                        <span>Outline storyboard for upcoming motion tutorial</span>
                                        <span class="task-meta">Draft</span>
                                    </li>
                                    <li>
                                        <ion-icon name="square-outline"></ion-icon>
                                        <span>Review guest post pitches</span>
                                        <span class="task-meta">5 submissions</span>
                                    </li>
                                </ul>
                            </article>
                        </div>
                        <div class="col-12 col-xl-7">
                            <article class="panel-card audience-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Audience flow</span>
                                        <h2 class="panel-title">Traffic spotlight</h2>
                                    </div>
                                    <span class="badge badge-soft">Last 7 days</span>
                                </header>
                                <div class="audience-heatmap">
                                    <div class="heatmap-grid" role="img" aria-label="Traffic heatmap by weekday">
                                        <?php
                                            $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                                            $levels = ['low','low','med','high','high','med','low'];
                                            foreach ($days as $index => $day) {
                                                $level = $levels[$index];
                                                echo '<span class="heatmap-cell level-' . $level . '">' . $day . '</span>';
                                            }
                                        ?>
                                    </div>
                                    <p class="heatmap-note">Peak engagement arrives Thursday afternoons. Consider scheduling long-form drops for Thursdays 15:00.</p>
                                </div>
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