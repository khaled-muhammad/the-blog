<?php
require_once '../config.php';
requireAuth();

$error = '';
$success = '';
$currentUser = getCurrentUser();
$isEdit = isset($_GET['edit']) && intval($_GET['edit']) > 0;
$postId = $isEdit ? intval($_GET['edit']) : null;
$postData = null;

if ($isEdit && $postId) {
    try {
        $postQuery = "SELECT * FROM posts WHERE id = ?";
        $postStmt = $conn->prepare($postQuery);
        $postStmt->execute([$postId]);
        $postData = $postStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$postData) {
            $error = 'Post not found.';
            $isEdit = false;
            $postId = null;
        } else {
            $tagsQuery = "SELECT t.name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = ?";
            $tagsStmt = $conn->prepare($tagsQuery);
            $tagsStmt->execute([$postId]);
            $existingTags = $tagsStmt->fetchAll(PDO::FETCH_COLUMN);
            $postData['tags'] = implode(', ', $existingTags);
            
            $coAuthorQuery = "SELECT user_id FROM post_authors WHERE post_id = ? AND is_primary = FALSE LIMIT 1";
            $coAuthorStmt = $conn->prepare($coAuthorQuery);
            $coAuthorStmt->execute([$postId]);
            $coAuthor = $coAuthorStmt->fetch(PDO::FETCH_ASSOC);
            $postData['co_author_id'] = $coAuthor['user_id'] ?? null;
        }
    } catch (PDOException $e) {
        error_log("Error loading post: " . $e->getMessage());
        $error = 'Failed to load post.';
        $isEdit = false;
        $postId = null;
    }
}

try {
    $categoriesQuery = "SELECT id, name FROM categories WHERE status != 'archived' ORDER BY name ASC";
    $categoriesStmt = $conn->prepare($categoriesQuery);
    $categoriesStmt->execute();
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $usersQuery = "SELECT id, username, first_name, last_name, display_name FROM users WHERE status = 'active' ORDER BY display_name, first_name ASC";
    $usersStmt = $conn->prepare($usersQuery);
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $tagsQuery = "SELECT id, name FROM tags ORDER BY name ASC LIMIT 20";
    $tagsStmt = $conn->prepare($tagsQuery);
    $tagsStmt->execute();
    $tags = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching form data: " . $e->getMessage());
    $categories = [];
    $users = [];
    $tags = [];
}

$statuses = [
    'draft' => 'Draft',
    'in-review' => 'In Review',
    'scheduled' => 'Scheduled',
    'published' => 'Published',
    'archived' => 'Archived'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
    $isEdit = $editId > 0;
    
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $categoryId = !empty($_POST['category']) ? (int)$_POST['category'] : null;
    $coAuthorId = !empty($_POST['coauthor']) ? (int)$_POST['coauthor'] : null;
    $publishDate = !empty($_POST['publish_date']) ? $_POST['publish_date'] : null;
    $publishTime = !empty($_POST['publish_time']) ? $_POST['publish_time'] : '09:00';
    $timezone = $_POST['timezone'] ?? 'UTC';
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $tagsInput = trim($_POST['tags'] ?? '');
    
    if (empty($title)) {
        $error = 'Title is required.';
    } elseif (empty($body)) {
        $error = 'Body content is required.';
    } else {
        if (empty($slug)) {
            $slug = generateSlug($title);
        } else {
            $slug = generateSlug($slug);
        }
        
        $slugCheck = $conn->prepare("SELECT id FROM posts WHERE slug = ?" . ($isEdit ? " AND id != ?" : ""));
        if ($isEdit) {
            $slugCheck->execute([$slug, $editId]);
        } else {
            $slugCheck->execute([$slug]);
        }
        if ($slugCheck->fetch()) {
            $slug .= '-' . time();
        }
        
        try {
            $conn->beginTransaction();
            
            $heroMediaId = null;
            if (!empty($_FILES['hero']['name']) && $_FILES['hero']['error'] === UPLOAD_ERR_OK) {
                $heroMediaId = uploadMedia($_FILES['hero'], $currentUser['id']);
            } elseif ($isEdit) {
                if (!$postData) {
                    $postQuery = "SELECT hero_media_id, social_image_id, publish_date, status FROM posts WHERE id = ?";
                    $postStmt = $conn->prepare($postQuery);
                    $postStmt->execute([$editId]);
                    $postData = $postStmt->fetch(PDO::FETCH_ASSOC);
                }
                $heroMediaId = $postData['hero_media_id'] ?? null;
            }
            
            $socialImageId = null;
            if (!empty($_FILES['social_image']['name']) && $_FILES['social_image']['error'] === UPLOAD_ERR_OK) {
                $socialImageId = uploadMedia($_FILES['social_image'], $currentUser['id']);
            } elseif ($isEdit) {
                $socialImageId = $postData['social_image_id'] ?? null;
            }
            
            $publishDateTime = null;
            if ($status === 'scheduled' && $publishDate) {
                $publishDateTime = $publishDate . ' ' . $publishTime . ':00';
            } elseif ($status === 'published' && (!$isEdit || ($postData && $postData['status'] !== 'published'))) {
                $publishDateTime = date('Y-m-d H:i:s');
            } elseif ($isEdit && $postData && $postData['publish_date']) {
                $publishDateTime = $postData['publish_date'];
            }
            
            $readingTime = calculateReadingTime($body);
            
            if ($isEdit) {
                $updatePost = $conn->prepare("
                    UPDATE posts SET
                        title = ?, slug = ?, excerpt = ?, body = ?, status = ?, primary_category_id = ?,
                        hero_media_id = ?, social_image_id = ?, meta_title = ?, meta_description = ?,
                        publish_date = ?, timezone = ?, reading_time_minutes = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $publishedAt = null;
                if ($status === 'published' && $postData && $postData['status'] !== 'published') {
                    $publishedAt = date('Y-m-d H:i:s');
                    $updatePost = $conn->prepare("
                        UPDATE posts SET
                            title = ?, slug = ?, excerpt = ?, body = ?, status = ?, primary_category_id = ?,
                            hero_media_id = ?, social_image_id = ?, meta_title = ?, meta_description = ?,
                            publish_date = ?, timezone = ?, reading_time_minutes = ?, published_at = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                }
                
                $params = [
                    $title,
                    $slug,
                    $excerpt ?: null,
                    $body,
                    $status,
                    $categoryId,
                    $heroMediaId,
                    $socialImageId,
                    $metaTitle ?: null,
                    $metaDescription ?: null,
                    $publishDateTime,
                    $timezone,
                    $readingTime
                ];
                
                if ($publishedAt) {
                    $params[] = $publishedAt;
                }
                
                $params[] = $editId;
                $updatePost->execute($params);
                
                $deleteCoAuthors = $conn->prepare("DELETE FROM post_authors WHERE post_id = ? AND is_primary = FALSE");
                $deleteCoAuthors->execute([$editId]);
                
                if ($coAuthorId && $coAuthorId != $currentUser['id']) {
                    $insertCoAuthor = $conn->prepare("
                        INSERT INTO post_authors (post_id, user_id, is_primary, created_at)
                        VALUES (?, ?, FALSE, NOW())
                    ");
                    $insertCoAuthor->execute([$editId, $coAuthorId]);
                }
                
                $deleteTags = $conn->prepare("DELETE FROM post_tags WHERE post_id = ?");
                $deleteTags->execute([$editId]);
                
                $decrementTags = $conn->prepare("
                    UPDATE tags t
                    INNER JOIN post_tags pt ON t.id = pt.tag_id
                    SET t.usage_count = GREATEST(0, t.usage_count - 1)
                    WHERE pt.post_id = ?
                ");
                $decrementTags->execute([$editId]);
                
                if (!empty($tagsInput)) {
                    $tagNames = array_map('trim', explode(',', $tagsInput));
                    foreach ($tagNames as $tagName) {
                        if (empty($tagName)) continue;
                        
                        $tagSlug = generateSlug($tagName);
                        
                        $tagCheck = $conn->prepare("SELECT id FROM tags WHERE slug = ?");
                        $tagCheck->execute([$tagSlug]);
                        $tag = $tagCheck->fetch();
                        
                        if (!$tag) {
                            $insertTag = $conn->prepare("INSERT INTO tags (name, slug, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
                            $insertTag->execute([$tagName, $tagSlug]);
                            $tagId = $conn->lastInsertId();
                        } else {
                            $tagId = $tag['id'];
                        }
                        
                        $linkTag = $conn->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id, created_at) VALUES (?, ?, NOW())");
                        $linkTag->execute([$editId, $tagId]);
                        
                        $updateTagCount = $conn->prepare("UPDATE tags SET usage_count = usage_count + 1 WHERE id = ?");
                        $updateTagCount->execute([$tagId]);
                    }
                }
                
                $success = 'Post updated successfully!';
            } else {
                $insertPost = $conn->prepare("
                    INSERT INTO posts (
                        title, slug, excerpt, body, status, primary_category_id,
                        hero_media_id, social_image_id, author_id, meta_title, meta_description,
                        publish_date, timezone, reading_time_minutes, published_at, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;
                
                $insertPost->execute([
                    $title,
                    $slug,
                    $excerpt ?: null,
                    $body,
                    $status,
                    $categoryId,
                    $heroMediaId,
                    $socialImageId,
                    $currentUser['id'],
                    $metaTitle ?: null,
                    $metaDescription ?: null,
                    $publishDateTime,
                    $timezone,
                    $readingTime,
                    $publishedAt
                ]);
                
                $postId = $conn->lastInsertId();
                
                if ($coAuthorId && $coAuthorId != $currentUser['id']) {
                    $insertCoAuthor = $conn->prepare("
                        INSERT INTO post_authors (post_id, user_id, is_primary, created_at)
                        VALUES (?, ?, FALSE, NOW())
                    ");
                    $insertCoAuthor->execute([$postId, $coAuthorId]);
                }
                
                if (!empty($tagsInput)) {
                    $tagNames = array_map('trim', explode(',', $tagsInput));
                    foreach ($tagNames as $tagName) {
                        if (empty($tagName)) continue;
                        
                        $tagSlug = generateSlug($tagName);
                        
                        $tagCheck = $conn->prepare("SELECT id FROM tags WHERE slug = ?");
                        $tagCheck->execute([$tagSlug]);
                        $tag = $tagCheck->fetch();
                        
                        if (!$tag) {
                            $insertTag = $conn->prepare("INSERT INTO tags (name, slug, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
                            $insertTag->execute([$tagName, $tagSlug]);
                            $tagId = $conn->lastInsertId();
                        } else {
                            $tagId = $tag['id'];
                        }
                        
                        $linkTag = $conn->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id, created_at) VALUES (?, ?, NOW())");
                        $linkTag->execute([$postId, $tagId]);
                        
                        $updateTagCount = $conn->prepare("UPDATE tags SET usage_count = usage_count + 1 WHERE id = ?");
                        $updateTagCount->execute([$tagId]);
                    }
                }
                
                if ($categoryId) {
                    $updateCategoryCount = $conn->prepare("UPDATE categories SET posts_count = posts_count + 1 WHERE id = ?");
                    $updateCategoryCount->execute([$categoryId]);
                }
                
                $success = 'Post created successfully!';
            }
            
            $conn->commit();
            
            header("Refresh: 2; url=/admin/posts.php");
            
        } catch (PDOException $e) {
            $conn->rollBack();
            error_log("Error saving post: " . $e->getMessage());
            $error = 'Failed to save post. Please try again.';
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>The Blog | <?php echo $isEdit ? 'Edit' : 'Create'; ?> Post</title>
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
    <link rel="stylesheet" href="../css/posts-create.css">
    <style>
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>

<body>
    <?php include '../incs/header.php'; ?>
    <main>
        <div class="container-fluid mt-4">
            <div class="d-flex gap-3 admin-layout align-items-stretch">
                <?php include 'incs/sidebar.php'; ?>
                <div class="admin-content flex-grow-1">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?> Redirecting...</div>
                    <?php endif; ?>

                    <header class="page-header create-header">
                        <div>
                            <span class="page-kicker">Editorial Studio</span>
                            <h1 class="page-title"><?php echo $isEdit ? 'Edit Post' : 'Compose a new story'; ?></h1>
                            <p class="page-lede"><?php echo $isEdit ? 'Update post content, metadata, and settings.' : 'Shape the narrative, align the visuals, and schedule the drop — all from one focused workspace.'; ?></p>
                        </div>
                        <div class="header-actions">
                            <button type="submit" form="create-post-form" class="btn btn-dashboard">
                                <ion-icon name="checkmark-circle-outline"></ion-icon>
                                <span><?php echo $isEdit ? 'Update Post' : 'Save Post'; ?></span>
                            </button>
                        </div>
                    </header>

                    <section class="create-post-grid">
                        <form id="create-post-form" class="panel-card create-form" action="#" method="post" enctype="multipart/form-data">
                            <header class="panel-header">
                                <div>
                                    <span class="panel-kicker">Story</span>
                                    <h2 class="panel-title">Post essentials</h2>
                                </div>
                            </header>
                            <div class="form-stack">
                                <?php if ($isEdit): ?>
                                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="post-title">Title</label>
                                    <input type="text" id="post-title" name="title" placeholder="Animating Delightful Micro-Interactions" value="<?php echo htmlspecialchars($postData['title'] ?? ''); ?>" required>
                                </div>
                                <div class="dual-group">
                                    <div class="form-group">
                                        <label for="post-slug">Slug</label>
                                        <div class="input-prefix">
                                            <span>/posts/</span>
                                            <input type="text" id="post-slug" name="slug" placeholder="delightful-micro-interactions" value="<?php echo htmlspecialchars($postData['slug'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="post-status">Status</label>
                                        <select id="post-status" name="status">
                                            <?php foreach ($statuses as $value => $label) : ?>
                                                <option value="<?php echo $value; ?>" <?php echo ($postData['status'] ?? 'draft') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="dual-group">
                                    <div class="form-group">
                                        <label for="post-category">Primary category</label>
                                        <select id="post-category" name="category">
                                            <option value="" disabled <?php echo !isset($postData['primary_category_id']) ? 'selected' : ''; ?>>Select category</option>
                                            <?php foreach ($categories as $category) : ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo ($postData['primary_category_id'] ?? null) == $category['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="post-coauthor">Co-author</label>
                                        <select id="post-coauthor" name="coauthor">
                                            <option value="" <?php echo !isset($postData['co_author_id']) ? 'selected' : ''; ?>>Solo publish</option>
                                            <?php foreach ($users as $user) : 
                                                if ($user['id'] == $currentUser['id']) continue;
                                                $displayName = $user['display_name'] ?: ($user['first_name'] . ' ' . $user['last_name']);
                                            ?>
                                                <option value="<?php echo $user['id']; ?>" <?php echo ($postData['co_author_id'] ?? null) == $user['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($displayName); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="post-tags">Tags</label>
                                    <div class="chips-input" role="listbox" aria-label="Selected tags">
                                        <?php foreach ($tags as $tag) : ?>
                                            <button type="button" class="chip" aria-selected="false" data-tag="<?php echo htmlspecialchars($tag['name']); ?>">
                                                <ion-icon name="pricetag-outline"></ion-icon>
                                                <span><?php echo htmlspecialchars($tag['name']); ?></span>
                                            </button>
                                        <?php endforeach; ?>
                                        <input type="text" id="post-tags" name="tags" placeholder="Add tags (comma-separated)" aria-label="Add tag" value="<?php echo htmlspecialchars($postData['tags'] ?? ''); ?>">
                                        <p style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">Enter tags separated by commas, or click existing tags above</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="post-hero">Hero media</label>
                                    <div class="upload-field">
                                        <input type="file" id="post-hero" name="hero" accept="image/*,video/*">
                                        <div class="upload-prompt">
                                            <ion-icon name="cloud-upload-outline"></ion-icon>
                                            <span>Drop media or <strong>browse</strong></span>
                                            <p>Recommended: 3200 × 1800 · JPG, PNG, or MP4</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="post-excerpt">Excerpt</label>
                                    <textarea id="post-excerpt" name="excerpt" rows="3" placeholder="Summarize the hook in one vivid paragraph..."><?php echo htmlspecialchars($postData['excerpt'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="post-body">Body</label>
                                    <textarea id="post-body" name="body" rows="14" placeholder="Compose your narrative, add callouts, and weave supporting media..." required><?php echo htmlspecialchars($postData['body'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="form-footer">
                                <div class="footer-actions">
                                    <button type="reset" class="ghost-btn">
                                        <ion-icon name="refresh-outline"></ion-icon>
                                        <span>Reset</span>
                                    </button>
                                    <button type="submit" class="btn btn-dashboard">
                                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                                        <span><?php echo $isEdit ? 'Update Post' : 'Save Post'; ?></span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="create-side-stack">
                            <article class="panel-card schedule-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Schedule</span>
                                        <h2 class="panel-title">Release timing</h2>
                                    </div>
                                </header>
                                <div class="schedule-body">
                                    <div class="form-group">
                                        <label for="publish-date">Publish date</label>
                                        <?php 
                                        $publishDateValue = '';
                                        if ($isEdit && $postData['publish_date']) {
                                            $publishDateValue = date('Y-m-d', strtotime($postData['publish_date']));
                                        } else {
                                            $publishDateValue = date('Y-m-d', strtotime('+2 days'));
                                        }
                                        ?>
                                        <input type="date" id="publish-date" name="publish_date" value="<?php echo $publishDateValue; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="publish-time">Publish time</label>
                                        <?php 
                                        $publishTimeValue = '09:30';
                                        if ($isEdit && $postData['publish_date']) {
                                            $publishTimeValue = date('H:i', strtotime($postData['publish_date']));
                                        }
                                        ?>
                                        <input type="time" id="publish-time" name="publish_time" value="<?php echo $publishTimeValue; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="timezone">Timezone</label>
                                        <select id="timezone" name="timezone">
                                            <option value="Europe/Oslo" <?php echo ($postData['timezone'] ?? 'UTC') === 'Europe/Oslo' ? 'selected' : ''; ?>>Europe/Oslo</option>
                                            <option value="UTC" <?php echo ($postData['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                            <option value="America/New_York" <?php echo ($postData['timezone'] ?? '') === 'America/New_York' ? 'selected' : ''; ?>>America/New_York</option>
                                        </select>
                                    </div>
                                    <div class="schedule-note">
                                        <ion-icon name="sparkles-outline"></ion-icon>
                                        <p>Best engagement hits on Thursdays between 15:00 – 17:00 CET.</p>
                                    </div>
                                </div>
                            </article>

                            <article class="panel-card seo-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Distribution</span>
                                        <h2 class="panel-title">SEO &amp; sharing</h2>
                                    </div>
                                </header>
                                <div class="seo-body">
                                    <div class="form-group">
                                        <label for="meta-title">Meta title</label>
                                        <input type="text" id="meta-title" name="meta_title" placeholder="Nordic motion systems that feel human" value="<?php echo htmlspecialchars($postData['meta_title'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="meta-description">Meta description</label>
                                        <textarea id="meta-description" name="meta_description" rows="3" placeholder="Summarize the story in 150 characters for search and social previews..."><?php echo htmlspecialchars($postData['meta_description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="social-image">Social image</label>
                                        <div class="upload-field compact">
                                            <input type="file" id="social-image" name="social_image" accept="image/*">
                                            <div class="upload-prompt">
                                                <ion-icon name="image-outline"></ion-icon>
                                                <span>Upload 1200 × 630 preview</span>
                                            </div>
                                        </div>
                                    </div>
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
    <script>
        document.getElementById('post-title').addEventListener('input', function() {
            const slugInput = document.getElementById('post-slug');
            if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
                const title = this.value;
                const slug = title.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
                slugInput.dataset.autoGenerated = 'true';
            }
        });
        
        document.getElementById('post-slug').addEventListener('input', function() {
            this.dataset.autoGenerated = 'false';
        });
        
        document.querySelectorAll('.chip[data-tag]').forEach(chip => {
            chip.addEventListener('click', function() {
                const tagInput = document.getElementById('post-tags');
                const tagName = this.dataset.tag;
                const currentTags = tagInput.value.split(',').map(t => t.trim()).filter(t => t);
                
                if (!currentTags.includes(tagName)) {
                    currentTags.push(tagName);
                    tagInput.value = currentTags.join(', ');
                }
            });
        });
    </script>
</body>

</html>
