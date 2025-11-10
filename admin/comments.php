<?php
require_once '../config.php';
requireAuth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $commentId = intval($_POST['comment_id']);
        $newStatus = $_POST['status'];
        
        if ($commentId > 0 && in_array($newStatus, ['pending', 'approved', 'spam', 'trash'])) {
            try {
                $updateStmt = $conn->prepare("UPDATE comments SET status = ?, updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$newStatus, $commentId]);
                
                $commentStmt = $conn->prepare("SELECT post_id, status FROM comments WHERE id = ?");
                $commentStmt->execute([$commentId]);
                $oldComment = $commentStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($oldComment) {
                    if ($newStatus === 'approved' && $oldComment['status'] !== 'approved') {
                        $updateCount = $conn->prepare("UPDATE posts SET comments_count = comments_count + 1 WHERE id = ?");
                        $updateCount->execute([$oldComment['post_id']]);
                    }
                    elseif ($oldComment['status'] === 'approved' && $newStatus !== 'approved') {
                        $updateCount = $conn->prepare("UPDATE posts SET comments_count = GREATEST(0, comments_count - 1) WHERE id = ?");
                        $updateCount->execute([$oldComment['post_id']]);
                    }
                }
                
                $success = 'Comment status updated successfully.';
                header('Location: /admin/comments.php?success=' . urlencode($success));
                exit;
            } catch (PDOException $e) {
                error_log("Error updating comment: " . $e->getMessage());
                $error = 'Failed to update comment. Please try again.';
            }
        }
    } elseif (isset($_POST['delete_comment'])) {
        $commentId = intval($_POST['comment_id']);
        
        if ($commentId > 0) {
            try {
                $commentStmt = $conn->prepare("SELECT post_id, status FROM comments WHERE id = ?");
                $commentStmt->execute([$commentId]);
                $comment = $commentStmt->fetch(PDO::FETCH_ASSOC);
                
                $deleteStmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
                $deleteStmt->execute([$commentId]);
                
                if ($comment && $comment['status'] === 'approved') {
                    $updateCount = $conn->prepare("UPDATE posts SET comments_count = GREATEST(0, comments_count - 1) WHERE id = ?");
                    $updateCount->execute([$comment['post_id']]);
                }
                
                $success = 'Comment deleted successfully.';
                header('Location: /admin/comments.php?success=' . urlencode($success));
                exit;
            } catch (PDOException $e) {
                error_log("Error deleting comment: " . $e->getMessage());
                $error = 'Failed to delete comment. Please try again.';
            }
        }
    }
}

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($error) ? $error : '';

$commentsPerPage = 20;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $commentsPerPage;

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$validStatuses = ['pending', 'approved', 'spam', 'trash'];
if (!in_array($statusFilter, $validStatuses) && $statusFilter !== '') {
    $statusFilter = '';
}

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$whereConditions = [];
$params = [];

if ($statusFilter) {
    $whereConditions[] = "c.status = :status";
    $params[':status'] = $statusFilter;
}

if ($searchQuery) {
    $whereConditions[] = "(c.author_name LIKE :search OR c.body LIKE :search OR p.title LIKE :search)";
    $params[':search'] = '%' . $searchQuery . '%';
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

try {
    $countQuery = "
        SELECT COUNT(*) as total
        FROM comments c
        LEFT JOIN posts p ON c.post_id = p.id
        $whereClause
    ";
    $countStmt = $conn->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalComments = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalComments / $commentsPerPage);
    
    if ($currentPage > $totalPages && $totalPages > 0) {
        $currentPage = $totalPages;
        $offset = ($currentPage - 1) * $commentsPerPage;
    }
} catch (PDOException $e) {
    $totalComments = 0;
    $totalPages = 0;
    error_log("Error counting comments: " . $e->getMessage());
}

try {
    $commentsQuery = "
        SELECT c.id, c.post_id, c.parent_id, c.author_name, c.author_email, c.author_url,
               c.body, c.status, c.created_at, c.updated_at, c.user_id,
               p.title as post_title, p.slug as post_slug,
               u.display_name as user_display_name, u.first_name, u.last_name
        FROM comments c
        LEFT JOIN posts p ON c.post_id = p.id
        LEFT JOIN users u ON c.user_id = u.id
        $whereClause
        ORDER BY c.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $commentsStmt = $conn->prepare($commentsQuery);
    foreach ($params as $key => $value) {
        $commentsStmt->bindValue($key, $value);
    }
    $commentsStmt->bindValue(':limit', $commentsPerPage, PDO::PARAM_INT);
    $commentsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $commentsStmt->execute();
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $comments = [];
    error_log("Error fetching comments: " . $e->getMessage());
}

try {
    $statsQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM comments
        GROUP BY status
    ";
    $statsStmt = $conn->prepare($statsQuery);
    $statsStmt->execute();
    $statsData = $statsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stats = [
        'pending' => 0,
        'approved' => 0,
        'spam' => 0,
        'trash' => 0,
        'total' => 0
    ];
    
    foreach ($statsData as $stat) {
        $stats[$stat['status']] = $stat['count'];
        $stats['total'] += $stat['count'];
    }
} catch (PDOException $e) {
    $stats = ['pending' => 0, 'approved' => 0, 'spam' => 0, 'trash' => 0, 'total' => 0];
    error_log("Error fetching comment stats: " . $e->getMessage());
}

function formatCommentDate($datetime) {
    if (!$datetime) return '—';
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins > 0 ? $mins . 'm ago' : 'just now';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . 'h ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . 'd ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

function buildPaginationUrl($page, $status = '', $search = '') {
    $params = [];
    if ($page > 1) {
        $params['page'] = $page;
    }
    if ($status) {
        $params['status'] = $status;
    }
    if ($search) {
        $params['search'] = $search;
    }
    return '/admin/comments.php' . (!empty($params) ? '?' . http_build_query($params) : '');
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>The Blog | Admin Comments</title>
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
    <link rel="stylesheet" href="../css/comments.css">
</head>

<body>
    <?php include '../incs/header.php'; ?>
    <main>
        <div class="container-fluid mt-4">
            <div class="d-flex gap-3 admin-layout align-items-stretch">
                <?php include 'incs/sidebar.php'; ?>
                <div class="admin-content flex-grow-1 comments-page">
                    <?php if ($error): ?>
                        <div class="alert alert-danger admin-alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success admin-alert">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <header class="page-header">
                        <div>
                            <span class="page-kicker">Community</span>
                            <h1 class="page-title">Comments &amp; moderation</h1>
                            <p class="page-lede">Review, approve, and manage community engagement across all posts.</p>
                        </div>
                    </header>

                    <section class="category-toolbar">
                        <form class="toolbar-search" role="search" method="GET" action="/admin/comments.php">
                            <label for="comment-search" class="visually-hidden">Search comments</label>
                            <ion-icon name="search-outline"></ion-icon>
                            <input id="comment-search" type="search" name="search" placeholder="Search by author, content, or post..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <?php if ($statusFilter): ?>
                                <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
                            <?php endif; ?>
                        </form>
                        <div class="toolbar-actions mt-3">
                            <a href="<?php echo buildPaginationUrl(1, '', $searchQuery); ?>" class="filter-chip <?php echo $statusFilter === '' ? 'active' : ''; ?>">
                                <ion-icon name="chatbubbles-outline"></ion-icon>
                                <span>All (<?php echo $stats['total']; ?>)</span>
                            </a>
                            <a href="<?php echo buildPaginationUrl(1, 'pending', $searchQuery); ?>" class="filter-chip <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>">
                                <ion-icon name="time-outline"></ion-icon>
                                <span>Pending (<?php echo $stats['pending']; ?>)</span>
                            </a>
                            <a href="<?php echo buildPaginationUrl(1, 'approved', $searchQuery); ?>" class="filter-chip <?php echo $statusFilter === 'approved' ? 'active' : ''; ?>">
                                <ion-icon name="checkmark-circle-outline"></ion-icon>
                                <span>Approved (<?php echo $stats['approved']; ?>)</span>
                            </a>
                            <a href="<?php echo buildPaginationUrl(1, 'spam', $searchQuery); ?>" class="filter-chip <?php echo $statusFilter === 'spam' ? 'active' : ''; ?>">
                                <ion-icon name="warning-outline"></ion-icon>
                                <span>Spam (<?php echo $stats['spam']; ?>)</span>
                            </a>
                        </div>
                    </section>

                    <section class="categories-grid">
                        <article class="panel-card category-list-panel mb-3">
                            <header class="panel-header">
                                <div>
                                    <span class="panel-kicker">Moderation</span>
                                    <h2 class="panel-title">Comments</h2>
                                </div>
                            </header>
                            <?php if (empty($comments)): ?>
                                <div class="comments-empty">
                                    <ion-icon name="chatbubbles-outline" class="comments-empty-icon"></ion-icon>
                                    <p>No comments found.</p>
                                    <?php if ($statusFilter || $searchQuery): ?>
                                        <p class="comments-empty-hint">Try adjusting your filters or search.</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="posts-table-wrapper">
                                    <table class="posts-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Author</th>
                                                <th scope="col">Comment</th>
                                                <th scope="col">Post</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Status</th>
                                                <th scope="col" class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($comments as $comment) : 
                                                $authorDisplayName = $comment['user_display_name'] ?: 
                                                    (($comment['first_name'] ?? '') . ' ' . ($comment['last_name'] ?? '')) ?: 
                                                    $comment['author_name'];
                                                
                                                $authorInitials = '';
                                                $names = explode(' ', trim($authorDisplayName));
                                                $authorInitials = strtoupper(substr($names[0], 0, 1));
                                                if (isset($names[1]) && !empty($names[1])) {
                                                    $authorInitials .= strtoupper(substr($names[1], 0, 1));
                                                } else {
                                                    $authorInitials = strtoupper(substr($authorDisplayName, 0, min(2, strlen($authorDisplayName))));
                                                }
                                            ?>
                                                <tr>
                                                    <td>
                                                        <div class="title-stack">
                                                            <div class="comment-author">
                                                                <div class="avatar-circle avatar-sm">
                                                                    <?php echo htmlspecialchars($authorInitials); ?>
                                                                </div>
                                                                <div class="comment-author-details">
                                                                    <strong><?php echo htmlspecialchars($authorDisplayName); ?></strong>
                                                                    <?php if ($comment['author_email']): ?>
                                                                        <div class="meta"><?php echo htmlspecialchars($comment['author_email']); ?></div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="comment-body">
                                                            <p>
                                                                <?php echo htmlspecialchars(mb_substr($comment['body'], 0, 150)); ?>
                                                                <?php if (mb_strlen($comment['body']) > 150) echo '...'; ?>
                                                            </p>
                                                            <?php if ($comment['parent_id']): ?>
                                                                <span class="comment-meta-badge">
                                                                    <ion-icon name="return-down-back-outline"></ion-icon> Reply
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($comment['post_title']): ?>
                                                            <a href="/post.php?slug=<?php echo urlencode($comment['post_slug']); ?>" target="_blank" class="comment-post-link">
                                                                <?php echo htmlspecialchars(mb_substr($comment['post_title'], 0, 40)); ?>
                                                                <?php if (mb_strlen($comment['post_title']) > 40) echo '...'; ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">Post deleted</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo formatCommentDate($comment['created_at']); ?></td>
                                                    <td>
                                                        <span class="status-chip status-<?php echo $comment['status']; ?>">
                                                            <?php echo ucfirst($comment['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="table-actions">
                                                            <?php if ($comment['status'] !== 'approved'): ?>
                                                                <form method="POST">
                                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                                    <input type="hidden" name="status" value="approved">
                                                                    <button type="submit" name="update_status" class="table-action approve">
                                                                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                                                                        <span>Approve</span>
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                            <?php if ($comment['status'] !== 'spam'): ?>
                                                                <form method="POST">
                                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                                    <input type="hidden" name="status" value="spam">
                                                                    <button type="submit" name="update_status" class="table-action spam">
                                                                        <ion-icon name="warning-outline"></ion-icon>
                                                                        <span>Spam</span>
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this comment? This action cannot be undone.');">
                                                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                                <button type="submit" name="delete_comment" class="table-action delete">
                                                                    <ion-icon name="trash-outline"></ion-icon>
                                                                    <span>Delete</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <div class="mt-4">
                                    <nav aria-label="Comments pagination">
                                        <ul class="pagination justify-content-center">
                                            <!-- Previous Button -->
                                            <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                                <a class="page-link" 
                                                   href="<?php echo buildPaginationUrl($currentPage - 1, $statusFilter, $searchQuery); ?>"
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
                                                    <a class="page-link" href="<?php echo buildPaginationUrl(1, $statusFilter, $searchQuery); ?>">1</a>
                                                </li>
                                                <?php if ($startPage > 2): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                                    <a class="page-link" href="<?php echo buildPaginationUrl($i, $statusFilter, $searchQuery); ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($endPage < $totalPages): ?>
                                                <?php if ($endPage < $totalPages - 1): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                <?php endif; ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="<?php echo buildPaginationUrl($totalPages, $statusFilter, $searchQuery); ?>">
                                                        <?php echo $totalPages; ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <!-- Next Button -->
                                            <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                                <a class="page-link" 
                                                   href="<?php echo buildPaginationUrl($currentPage + 1, $statusFilter, $searchQuery); ?>"
                                                   aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <p class="text-center text-muted mt-3">
                                        Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?> · <?php echo number_format($totalComments); ?> total comments
                                    </p>
                                </div>
                            <?php endif; ?>
                        </article>

                        <div class="category-side-stack">
                            <article class="panel-card spotlight-panel mb-3">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Overview</span>
                                        <h2 class="panel-title">Comment stats</h2>
                                    </div>
                                </header>
                                <div class="spotlight-body">
                                    <div class="spotlight-meta">
                                        <div>
                                            <span class="label">Total Comments</span>
                                            <strong><?php echo number_format($stats['total']); ?></strong>
                                        </div>
                                        <div>
                                            <span class="label">Approved</span>
                                            <strong><?php echo number_format($stats['approved']); ?></strong>
                                        </div>
                                    </div>
                                    <ul class="spotlight-notes">
                                        <li>
                                            <ion-icon name="time-outline"></ion-icon>
                                            <span><?php echo number_format($stats['pending']); ?> pending moderation</span>
                                        </li>
                                        <li>
                                            <ion-icon name="warning-outline"></ion-icon>
                                            <span><?php echo number_format($stats['spam']); ?> marked as spam</span>
                                        </li>
                                        <li>
                                            <ion-icon name="trash-outline"></ion-icon>
                                            <span><?php echo number_format($stats['trash']); ?> in trash</span>
                                        </li>
                                    </ul>
                                </div>
                            </article>

                            <article class="panel-card initiatives-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Quick Actions</span>
                                        <h2 class="panel-title">Moderation</h2>
                                    </div>
                                </header>
                                <ul class="initiatives-list">
                                    <li>
                                        <div>
                                            <strong>Pending Review</strong>
                                            <span class="tag"><?php echo $stats['pending']; ?> comments</span>
                                        </div>
                                        <div class="initiative-meta">
                                            <a href="<?php echo buildPaginationUrl(1, 'pending', $searchQuery); ?>" class="comments-action-link">
                                                Review now →
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <strong>Approved</strong>
                                            <span class="tag"><?php echo $stats['approved']; ?> comments</span>
                                        </div>
                                        <div class="initiative-meta">
                                            <a href="<?php echo buildPaginationUrl(1, 'approved', $searchQuery); ?>" class="comments-action-link">
                                                View all →
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <strong>Spam</strong>
                                            <span class="tag"><?php echo $stats['spam']; ?> comments</span>
                                        </div>
                                        <div class="initiative-meta">
                                            <a href="<?php echo buildPaginationUrl(1, 'spam', $searchQuery); ?>" class="comments-action-link">
                                                Review spam →
                                            </a>
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

