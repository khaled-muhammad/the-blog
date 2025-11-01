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
                    <?php
                        $categories = [
                            [
                                'name' => 'Motion Design',
                                'summary' => 'Micro-interactions, transitions, prototype demos and animation principles.',
                                'posts' => 42,
                                'engagement' => '27% of reads',
                                'updated' => 'Updated 3h ago',
                                'status' => 'Featured',
                                'color' => 'primary'
                            ],
                            [
                                'name' => 'Interface',
                                'summary' => 'Nordic interface patterns, components, and modular UI systems.',
                                'posts' => 31,
                                'engagement' => '19% of reads',
                                'updated' => 'Updated yesterday',
                                'status' => 'Growing',
                                'color' => 'secondary'
                            ],
                            [
                                'name' => 'Process',
                                'summary' => 'Behind-the-scenes rituals, workflows, and creative strategy retrospectives.',
                                'posts' => 24,
                                'engagement' => '14% of reads',
                                'updated' => 'Updated 2 days ago',
                                'status' => 'Steady',
                                'color' => 'neutral'
                            ],
                            [
                                'name' => 'Accessibility',
                                'summary' => 'Inclusive design systems, motion safety, and sensory-aware storytelling.',
                                'posts' => 18,
                                'engagement' => '11% of reads',
                                'updated' => 'Updated 5 days ago',
                                'status' => 'Priority',
                                'color' => 'accent'
                            ],
                            [
                                'name' => 'Culture',
                                'summary' => 'Community spotlights, playlists, and creative rituals from the field.',
                                'posts' => 15,
                                'engagement' => '9% of reads',
                                'updated' => 'Updated 1 week ago',
                                'status' => 'Emerging',
                                'color' => 'lilac'
                            ],
                        ];

                        $spotlight = [
                            'name' => 'Motion Design',
                            'momentum' => '+18% MoM',
                            'audience' => 'Creative technologists & product teams',
                            'next_release' => 'Nov 05 · Motion Systems 201',
                            'notes' => [
                                'Keep hero video length under 45s for best completion.',
                                'Add accessibility callouts to every tutorial section.',
                                'Highlight new After Effects templates in CTA block.'
                            ]
                        ];

                        $initiatives = [
                            [
                                'title' => 'Launch "Nordic Patterns" mini-series',
                                'lead' => 'Ava',
                                'status' => 'Scripting',
                                'due' => 'Nov 08'
                            ],
                            [
                                'title' => 'Refresh Accessibility resource hub',
                                'lead' => 'Jonas',
                                'status' => 'Content audit',
                                'due' => 'Nov 12'
                            ],
                            [
                                'title' => 'Curate Q4 guest author roster',
                                'lead' => 'Maya',
                                'status' => 'Outreach',
                                'due' => 'Nov 20'
                            ],
                        ];

                        $taxonomyTasks = [
                            [
                                'label' => 'Tag October backlog posts with updated taxonomy',
                                'priority' => 'Today'
                            ],
                            [
                                'label' => 'Archive deprecated "Inspiration" category assets',
                                'priority' => 'This week'
                            ],
                            [
                                'label' => 'Review synonyms for "storyboarding" keyword map',
                                'priority' => 'Next week'
                            ],
                        ];
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
                                <?php foreach ($categories as $category) : ?>
                                    <li class="category-item">
                                        <div class="category-item-header">
                                            <div class="category-name">
                                                <span class="category-dot category-dot-<?php echo $category['color']; ?>"></span>
                                                <div>
                                                    <strong><?php echo $category['name']; ?></strong>
                                                    <p><?php echo $category['summary']; ?></p>
                                                </div>
                                            </div>
                                            <span class="category-status status-<?php echo strtolower($category['status']); ?>"><?php echo $category['status']; ?></span>
                                        </div>
                                        <div class="category-item-metrics">
                                            <span><ion-icon name="document-text-outline"></ion-icon><?php echo $category['posts']; ?> posts</span>
                                            <span><ion-icon name="pulse-outline"></ion-icon><?php echo $category['engagement']; ?></span>
                                            <span><ion-icon name="time-outline"></ion-icon><?php echo $category['updated']; ?></span>
                                        </div>
                                        <div class="category-item-actions">
                                            <button type="button" class="category-action">
                                                <ion-icon name="open-outline"></ion-icon>
                                                <span>Open board</span>
                                            </button>
                                            <button type="button" class="category-action ghost">
                                                <ion-icon name="analytics-outline"></ion-icon>
                                                <span>Insights</span>
                                            </button>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </article>

                        <div class="category-side-stack">
                            <article class="panel-card spotlight-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Spotlight</span>
                                        <h2 class="panel-title"><?php echo $spotlight['name']; ?> snapshot</h2>
                                    </div>
                                    <span class="badge badge-soft">Momentum <?php echo $spotlight['momentum']; ?></span>
                                </header>
                                <div class="spotlight-body">
                                    <div class="spotlight-meta">
                                        <div>
                                            <span class="label">Audience resonance</span>
                                            <strong><?php echo $spotlight['audience']; ?></strong>
                                        </div>
                                        <div>
                                            <span class="label">Next release</span>
                                            <strong><?php echo $spotlight['next_release']; ?></strong>
                                        </div>
                                    </div>
                                    <ul class="spotlight-notes">
                                        <?php foreach ($spotlight['notes'] as $note) : ?>
                                            <li>
                                                <ion-icon name="checkmark-circle-outline"></ion-icon>
                                                <span><?php echo $note; ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </article>

                            <article class="panel-card initiatives-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Initiatives</span>
                                        <h2 class="panel-title">In-flight programs</h2>
                                    </div>
                                </header>
                                <ul class="initiatives-list">
                                    <?php foreach ($initiatives as $initiative) : ?>
                                        <li>
                                            <div>
                                                <strong><?php echo $initiative['title']; ?></strong>
                                                <span class="tag">Lead · <?php echo $initiative['lead']; ?></span>
                                            </div>
                                            <div class="initiative-meta">
                                                <span><?php echo $initiative['status']; ?></span>
                                                <span class="badge badge-soft">Due <?php echo $initiative['due']; ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </article>

                            <article class="panel-card taxonomy-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Governance</span>
                                        <h2 class="panel-title">Housekeeping</h2>
                                    </div>
                                </header>
                                <ul class="taxonomy-task-list">
                                    <?php foreach ($taxonomyTasks as $task) : ?>
                                        <li>
                                            <ion-icon name="checkbox-outline"></ion-icon>
                                            <span><?php echo $task['label']; ?></span>
                                            <span class="task-priority"><?php echo $task['priority']; ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="category-action full-width">
                                    <ion-icon name="add-outline"></ion-icon>
                                    <span>Add task</span>
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
