<!doctype html>
<html lang="en">

<head>
    <title>The Blog | Create Post</title>
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
</head>

<body>
    <?php include '../incs/header.php'; ?>
    <main>
        <div class="container-fluid mt-4">
            <div class="d-flex gap-3 admin-layout align-items-stretch">
                <?php include 'incs/sidebar.php'; ?>
                <div class="admin-content flex-grow-1">
                    <?php
                        $statuses = ['Draft', 'In Review', 'Scheduled', 'Published'];
                        $categories = ['Motion Design', 'Interface', 'Process', 'Accessibility', 'Culture'];
                        $tags = ['micro-interactions', 'nordic-ui', 'motion-system', 'journey-map', 'spotlight', 'sound design'];
                        $coAuthors = ['Ava Larsen', 'Jonas Holm', 'Maya Richter'];
                    ?>

                    <header class="page-header create-header">
                        <div>
                            <span class="page-kicker">Editorial Studio</span>
                            <h1 class="page-title">Compose a new story</h1>
                            <p class="page-lede">Shape the narrative, align the visuals, and schedule the drop — all from one focused workspace.</p>
                        </div>
                        <div class="header-actions">
                            <button type="button" class="ghost-btn">
                                <ion-icon name="eye-outline"></ion-icon>
                                <span>Preview</span>
                            </button>
                            <button type="button" class="ghost-btn">
                                <ion-icon name="save-outline"></ion-icon>
                                <span>Save draft</span>
                            </button>
                            <button type="submit" form="create-post-form" class="btn btn-dashboard">
                                <ion-icon name="rocket-outline"></ion-icon>
                                <span>Publish</span>
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
                                <span class="badge badge-soft">Autosave enabled</span>
                            </header>
                            <div class="form-stack">
                                <div class="form-group">
                                    <label for="post-title">Title</label>
                                    <input type="text" id="post-title" name="title" placeholder="Animating Delightful Micro-Interactions" required>
                                </div>
                                <div class="dual-group">
                                    <div class="form-group">
                                        <label for="post-slug">Slug</label>
                                        <div class="input-prefix">
                                            <span>/posts/</span>
                                            <input type="text" id="post-slug" name="slug" placeholder="delightful-micro-interactions" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="post-status">Status</label>
                                        <select id="post-status" name="status">
                                            <?php foreach ($statuses as $status) : ?>
                                                <option value="<?php echo strtolower(str_replace(' ', '-', $status)); ?>"><?php echo $status; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="dual-group">
                                    <div class="form-group">
                                        <label for="post-category">Primary category</label>
                                        <select id="post-category" name="category" required>
                                            <option value="" disabled selected>Select category</option>
                                            <?php foreach ($categories as $category) : ?>
                                                <option value="<?php echo strtolower(str_replace(' ', '-', $category)); ?>"><?php echo $category; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="post-coauthor">Co-author</label>
                                        <select id="post-coauthor" name="coauthor">
                                            <option value="" selected>Solo publish</option>
                                            <?php foreach ($coAuthors as $author) : ?>
                                                <option value="<?php echo strtolower(str_replace(' ', '-', $author)); ?>"><?php echo $author; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="post-tags">Tags</label>
                                    <div class="chips-input" role="listbox" aria-label="Selected tags">
                                        <?php foreach ($tags as $tag) : ?>
                                            <button type="button" class="chip" aria-selected="false">
                                                <ion-icon name="pricetag-outline"></ion-icon>
                                                <span><?php echo $tag; ?></span>
                                            </button>
                                        <?php endforeach; ?>
                                        <input type="text" id="post-tags" name="tags" placeholder="Add tag" aria-label="Add tag">
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
                                    <textarea id="post-excerpt" name="excerpt" rows="3" placeholder="Summarize the hook in one vivid paragraph..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="post-body">Body</label>
                                    <textarea id="post-body" name="body" rows="14" placeholder="Compose your narrative, add callouts, and weave supporting media..."></textarea>
                                </div>
                            </div>
                            <div class="form-footer">
                                <div class="autosave">
                                    <span class="dot"></span>
                                    <span>Last autosave · 2 minutes ago</span>
                                </div>
                                <div class="footer-actions">
                                    <button type="reset" class="ghost-btn">
                                        <ion-icon name="refresh-outline"></ion-icon>
                                        <span>Reset draft</span>
                                    </button>
                                    <button type="submit" class="btn btn-dashboard">
                                        <ion-icon name="arrow-forward-circle-outline"></ion-icon>
                                        <span>Save changes</span>
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
                                        <input type="date" id="publish-date" name="publish_date" value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="publish-time">Publish time</label>
                                        <input type="time" id="publish-time" name="publish_time" value="09:30">
                                    </div>
                                    <div class="form-group">
                                        <label for="timezone">Timezone</label>
                                        <select id="timezone" name="timezone">
                                            <option value="Europe/Oslo">Europe/Oslo</option>
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">America/New_York</option>
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
                                        <input type="text" id="meta-title" name="meta_title" placeholder="Nordic motion systems that feel human">
                                    </div>
                                    <div class="form-group">
                                        <label for="meta-description">Meta description</label>
                                        <textarea id="meta-description" name="meta_description" rows="3" placeholder="Summarize the story in 150 characters for search and social previews..."></textarea>
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
                                    <ul class="seo-insights">
                                        <li>
                                            <ion-icon name="checkmark-circle-outline"></ion-icon>
                                            <span>Headline length is in the sweet spot.</span>
                                        </li>
                                        <li>
                                            <ion-icon name="alert-circle-outline"></ion-icon>
                                            <span>Add keywords: motion accessibility, kinetic empathy.</span>
                                        </li>
                                        <li>
                                            <ion-icon name="sparkles-outline"></ion-icon>
                                            <span>Consider a short loop hero video for richer social shares.</span>
                                        </li>
                                    </ul>
                                </div>
                            </article>

                            <article class="panel-card summary-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Checklist</span>
                                        <h2 class="panel-title">Launch readiness</h2>
                                    </div>
                                </header>
                                <ul class="summary-list">
                                    <li>
                                        <ion-icon name="checkbox-outline"></ion-icon>
                                        <div>
                                            <strong>Editorial review</strong>
                                            <p>Assign to Ava for tone-of-voice polish.</p>
                                        </div>
                                        <span class="badge badge-soft">Pending</span>
                                    </li>
                                    <li>
                                        <ion-icon name="checkbox-outline"></ion-icon>
                                        <div>
                                            <strong>Accessibility pass</strong>
                                            <p>Ensure motion cues respect reduced-motion preferences.</p>
                                        </div>
                                        <span class="badge badge-soft">In progress</span>
                                    </li>
                                    <li>
                                        <ion-icon name="checkbox-outline"></ion-icon>
                                        <div>
                                            <strong>CTA alignment</strong>
                                            <p>Link to Motion Systems toolkit v2.1.</p>
                                        </div>
                                        <span class="badge badge-soft">Ready</span>
                                    </li>
                                </ul>
                                <button type="button" class="category-action full-width">
                                    <ion-icon name="add-outline"></ion-icon>
                                    <span>Add checklist item</span>
                                </button>
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
