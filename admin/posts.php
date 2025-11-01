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
                        $posts = [
                            [
                                'title' => 'Animating Delightful Micro-Interactions',
                                'status' => 'Published',
                                'category' => 'Motion Design',
                                'updated_at' => 'Nov 1, 2025 · 09:20',
                                'views' => '8.4K',
                                'comments' => 32,
                                'trend' => '+12%'
                            ],
                            [
                                'title' => 'Nordic UI: Crafting Calm Interfaces',
                                'status' => 'Scheduled',
                                'category' => 'Interface',
                                'updated_at' => 'Nov 2, 2025 · 07:00',
                                'views' => '—',
                                'comments' => 0,
                                'trend' => 'Queuing'
                            ],
                            [
                                'title' => 'Field Notes from Creative Retreat Oslo',
                                'status' => 'In Review',
                                'category' => 'Process',
                                'updated_at' => 'Oct 31, 2025 · 20:45',
                                'views' => '2.1K',
                                'comments' => 14,
                                'trend' => '+4%'
                            ],
                            [
                                'title' => 'Designing Accessible Motion Systems',
                                'status' => 'Draft',
                                'category' => 'Accessibility',
                                'updated_at' => 'Oct 29, 2025 · 16:10',
                                'views' => '980',
                                'comments' => 5,
                                'trend' => 'Needs polish'
                            ],
                            [
                                'title' => 'Creative Pulse Playlist · November Edition',
                                'status' => 'Published',
                                'category' => 'Culture',
                                'updated_at' => 'Oct 27, 2025 · 11:35',
                                'views' => '5.7K',
                                'comments' => 9,
                                'trend' => '+6%'
                            ],
                        ];

                        $categoryBreakdown = [
                            ['name' => 'Motion Design', 'share' => '27%', 'color' => 'primary'],
                            ['name' => 'Interface', 'share' => '19%', 'color' => 'secondary'],
                            ['name' => 'Process', 'share' => '14%', 'color' => 'neutral'],
                            ['name' => 'Culture', 'share' => '11%', 'color' => 'accent'],
                        ];
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
                                    <strong>18</strong>
                                    <span class="trend up"><ion-icon name="arrow-up-outline"></ion-icon> +3 vs Oct</span>
                                </div>
                                <div class="stat-pill">
                                    <span class="label">Drafts</span>
                                    <strong>9</strong>
                                    <span class="trend neutral"><ion-icon name="pause-outline"></ion-icon> Holding</span>
                                </div>
                                <div class="stat-pill">
                                    <span class="label">Avg. cadence</span>
                                    <strong>3.4 days</strong>
                                    <span class="trend up"><ion-icon name="sparkles-outline"></ion-icon> Consistent</span>
                                </div>
                            </div>
                            <div class="category-mini">
                                <span class="mini-title">Category share</span>
                                <ul>
                                    <?php foreach ($categoryBreakdown as $category) : ?>
                                        <li>
                                            <span class="dot dot-<?php echo $category['color']; ?>"></span>
                                            <span><?php echo $category['name']; ?></span>
                                            <strong><?php echo $category['share']; ?></strong>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
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
                                            <th scope="col" class="text-end">Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($posts as $post) : ?>
                                            <tr>
                                                <td>
                                                    <div class="title-stack">
                                                        <strong><?php echo $post['title']; ?></strong>
                                                        <span class="meta">ID <?php echo substr(md5($post['title']), 0, 6); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="status-chip status-<?php echo strtolower(str_replace(' ', '-', $post['status'])); ?>">
                                                        <?php echo $post['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="category-tag"><?php echo $post['category']; ?></span>
                                                </td>
                                                <td><?php echo $post['updated_at']; ?></td>
                                                <td><?php echo $post['views']; ?></td>
                                                <td><?php echo $post['comments']; ?></td>
                                                <td class="text-end">
                                                    <span class="trend-badge"><?php echo $post['trend']; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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