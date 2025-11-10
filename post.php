<?php
require_once 'config.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    die('Post not found');
}

$isAuthenticated = isAuthenticated();
$currentUser = getCurrentUser();

try {
    $postQuery = "
        SELECT p.id, p.title, p.slug, p.excerpt, p.body, p.status, 
               p.publish_date, p.published_at, p.created_at, p.updated_at, p.views_count,
               p.reading_time_minutes, p.meta_title, p.meta_description, p.timezone,
               c.id as category_id, c.name as category_name, c.slug as category_slug,
               u.id as author_id, u.display_name as author_name, u.first_name, u.last_name,
               m.file_url as hero_image_url
        FROM posts p
        LEFT JOIN categories c ON p.primary_category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        LEFT JOIN media m ON p.hero_media_id = m.id
        WHERE p.slug = ?
    ";
    
    $postStmt = $conn->prepare($postQuery);
    $postStmt->execute([$slug]);
    $post = $postStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        header('HTTP/1.0 404 Not Found');
        die('Post not found');
    }
    
    if ($post['status'] !== 'published' && (!$isAuthenticated || $currentUser['role'] !== 'admin')) {
        header('HTTP/1.0 404 Not Found');
        die('Post not found');
    }
    
    if ($post['status'] === 'scheduled' && $post['publish_date']) {
        $publishDateTime = new DateTime($post['publish_date'], new DateTimeZone($post['timezone'] ?? 'UTC'));
        $now = new DateTime('now', new DateTimeZone($post['timezone'] ?? 'UTC'));
        if ($publishDateTime > $now && (!$isAuthenticated || $currentUser['role'] !== 'admin')) {
            header('HTTP/1.0 404 Not Found');
            die('Post not found');
        }
    }
    
    if ($post['status'] === 'published') {
        $updateViews = $conn->prepare("UPDATE posts SET views_count = views_count + 1 WHERE id = ?");
        $updateViews->execute([$post['id']]);
        $post['views_count']++;
    }
    
    $tagsQuery = "
        SELECT t.name, t.slug
        FROM tags t
        INNER JOIN post_tags pt ON t.id = pt.tag_id
        WHERE pt.post_id = ?
        ORDER BY t.name ASC
    ";
    $tagsStmt = $conn->prepare($tagsQuery);
    $tagsStmt->execute([$post['id']]);
    $tags = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $commentsQuery = "
        SELECT c.id, c.author_name, c.body, c.created_at, c.user_id,
               u.display_name as user_display_name, u.first_name, u.last_name
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ? AND c.status = 'approved'
        ORDER BY c.created_at ASC
    ";
    $commentsStmt = $conn->prepare($commentsQuery);
    $commentsStmt->execute([$post['id']]);
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $commentError = '';
    $commentSuccess = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
        $commentName = trim($_POST['name'] ?? '');
        $commentEmail = trim($_POST['email'] ?? '');
        $commentBody = trim($_POST['message'] ?? '');
        
        if (empty($commentName) || empty($commentEmail) || empty($commentBody)) {
            $commentError = 'Please fill in all fields.';
        } else {
            try {
                $insertComment = $conn->prepare("
                    INSERT INTO comments (
                        post_id, author_name, author_email, body, status, 
                        user_id, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, 'pending', ?, NOW(), NOW())
                ");
                
                $userId = $isAuthenticated ? $currentUser['id'] : null;
                $insertComment->execute([
                    $post['id'],
                    $commentName,
                    $commentEmail,
                    $commentBody,
                    $userId
                ]);
                
                $commentSuccess = 'Comment submitted! It will appear after moderation.';
                
                $commentsStmt->execute([$post['id']]);
                $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error submitting comment: " . $e->getMessage());
                $commentError = 'Failed to submit comment. Please try again.';
            }
        }
    }
    
    $publishDate = $post['published_at'] ?: $post['publish_date'] ?: $post['created_at'];
    $formattedDate = date('F j, Y', strtotime($publishDate));
    
    $authorDisplayName = $post['author_name'] ?: ($post['first_name'] . ' ' . $post['last_name']);
    $authorInitials = '';
    if ($post['author_name']) {
        $names = explode(' ', $post['author_name']);
        $authorInitials = strtoupper(substr($names[0], 0, 1));
        if (isset($names[1])) {
            $authorInitials .= strtoupper(substr($names[1], 0, 1));
        }
    } else {
        $authorInitials = strtoupper(substr($post['first_name'], 0, 1) . substr($post['last_name'], 0, 1));
    }
    
} catch (PDOException $e) {
    error_log("Error fetching post: " . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    die('Error loading post');
}
?>
<!doctype html>
<html lang="en">

<head>
    <title><?php echo htmlspecialchars($post['meta_title'] ?: $post['title']); ?> | The Blog</title>
    <meta name="description" content="<?php echo htmlspecialchars($post['meta_description'] ?: $post['excerpt'] ?: ''); ?>">
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
    <link rel="stylesheet" href="css/post.css">
</head>

<body>
    <?php include 'incs/header.php'; ?>
<main>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="mt-3">
                    <div class="card mb-3 bg-transparent border-0">
                        <div class="row g-0">
                            <img src="<?php echo htmlspecialchars($post['hero_image_url'] ?: 'https://4kwallpapers.com/images/wallpapers/blue-aesthetic-3840x2400-12656.jpg'); ?>"
                                class="img-fluid rounded-3 object-fit-cover" style="height: 200px;"
                                alt="<?php echo htmlspecialchars($post['title']); ?>" />
                        </div>
                    </div>
                </div>
            </div>

            <section id="post-content" class="col-12 col-lg-10 offset-lg-1 mt-2">
                <h2 class="magic-subtitle"><?php echo htmlspecialchars($post['title']); ?></h2>
                
                <div class="post-meta mt-5">
                    <div class="post-meta-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <?php echo $formattedDate; ?>
                    </div>
                    <?php if ($post['reading_time_minutes']): ?>
                    <div class="post-meta-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <?php echo $post['reading_time_minutes']; ?> min read
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($tags)): ?>
                    <div class="post-meta-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <?php echo htmlspecialchars(implode(', ', array_column($tags, 'name'))); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="post-content" id="post-body-content">
                    <!-- Markdown content will be rendered here -->
                </div>
                <script type="text/template" id="post-body-markdown">
<?php echo htmlspecialchars($post['body']); ?>
                </script>
            </section>

            <?php
            $formattedComments = [];
            foreach ($comments as $comment) {
                $displayName = $comment['user_display_name'];
                if (!$displayName && ($comment['first_name'] || $comment['last_name'])) {
                    $displayName = trim(($comment['first_name'] ?? '') . ' ' . ($comment['last_name'] ?? ''));
                }
                if (!$displayName) {
                    $displayName = $comment['author_name'];
                }
                
                $names = explode(' ', trim($displayName));
                $initials = strtoupper(substr($names[0], 0, 1));
                if (isset($names[1]) && !empty($names[1])) {
                    $initials .= strtoupper(substr($names[1], 0, 1));
                } else {
                    $initials = strtoupper(substr($displayName, 0, min(2, strlen($displayName))));
                }
                
                $formattedComments[] = [
                    'initials' => $initials,
                    'author' => $displayName,
                    'date' => date('F j, Y · g:i A', strtotime($comment['created_at'])),
                    'body' => $comment['body']
                ];
            }
            ?>

            <section id="comments" class="col-12 col-lg-10 offset-lg-1 mt-5">
                <div class="comments-section p-4 p-md-5">
                    <div class="row g-5">
                        <div class="col-12 col-lg-7">
                            <h3 class="magic-subtitle mb-4">Join the conversation</h3>
                            <p class="text-muted">Thoughts, feedback, and stories from fellow creatives.</p>

                            <div class="comments-list mt-4">
                                <?php if (empty($formattedComments)): ?>
                                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                                <?php else: ?>
                                    <?php foreach ($formattedComments as $comment) : ?>
                                        <article class="comment-card mb-4">
                                            <div class="comment-header d-flex align-items-start gap-3">
                                                <div class="comment-avatar"><?= htmlspecialchars($comment['initials']) ?></div>
                                                <div>
                                                    <h4 class="comment-author mb-1"><?= htmlspecialchars($comment['author']) ?></h4>
                                                    <span class="comment-meta"><?= htmlspecialchars($comment['date']) ?></span>
                                                </div>
                                            </div>
                                            <p class="comment-text mb-0 mt-3"><?= nl2br(htmlspecialchars($comment['body'])); ?></p>
                                        </article>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="comment-form card border-0 h-100">
                                <div class="card-body">
                                    <h4 class="comment-form-title">Leave a comment</h4>
                                    <p class="text-muted small">Share your thoughts and join the conversation.</p>
                                    
                                    <?php if ($commentError): ?>
                                        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($commentError); ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if ($commentSuccess): ?>
                                        <div class="alert alert-success mt-3"><?php echo htmlspecialchars($commentSuccess); ?></div>
                                    <?php endif; ?>
                                    
                                    <form action="" method="post" class="mt-4">
                                        <input type="hidden" name="submit_comment" value="1">
                                        <div class="mb-3">
                                            <label for="comment-name" class="form-label">Name</label>
                                            <input type="text" class="form-control form-control-lg" id="comment-name" name="name" 
                                                   placeholder="Your name" 
                                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                                   required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="comment-email" class="form-label">Email</label>
                                            <input type="email" class="form-control form-control-lg" id="comment-email" name="email" 
                                                   placeholder="you@example.com" 
                                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                                   required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="comment-message" class="form-label">Comment</label>
                                            <textarea class="form-control" id="comment-message" name="message" rows="5" 
                                                      placeholder="Share your thoughts" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-gradient w-100">Post comment</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
<!-- Marked.js for Markdown rendering -->
<script src="https://cdn.jsdelivr.net/npm/marked@11.1.1/marked.min.js"></script>
<script>
    marked.setOptions({
        breaks: true,
        gfm: true,
        headerIds: true,
        mangle: false
    });

    const markdownTemplate = document.getElementById('post-body-markdown');
    const postContentDiv = document.getElementById('post-body-content');
    
    if (markdownTemplate && postContentDiv) {
        const markdownText = markdownTemplate.textContent.trim();
        const htmlContent = marked.parse(markdownText);
        postContentDiv.innerHTML = htmlContent;
    }
</script>
<script src="js/main.js"></script>
</body>

</html>
