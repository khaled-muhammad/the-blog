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
                            <span class="hero-kicker">Welcome back, Creator</span>
                            <h1 class="hero-title">Your creative pulse at a glance</h1>
                            <p class="hero-subtitle">Track momentum, spot opportunities, and keep the community engaged.</p>
                            <div class="hero-actions">
                                <a href="/admin/posts.php" class="btn btn-dashboard">
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
                                <span class="label">Weekly reach</span>
                                <strong>48.2K</strong>
                                <span class="trend up">
                                    <ion-icon name="arrow-up-outline"></ion-icon>
                                    +8.4%
                                </span>
                            </div>
                            <div class="highlight-divider"></div>
                            <div class="highlight-stat">
                                <span class="label">Avg. read time</span>
                                <strong>5m 12s</strong>
                                <span class="trend steady">
                                    <ion-icon name="remove-outline"></ion-icon>
                                    steady
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
                                    <strong class="metric-value">128</strong>
                                    <span class="metric-delta up">
                                        <ion-icon name="trending-up-outline"></ion-icon>
                                        +4 this week
                                    </span>
                                </div>
                            </article>
                            <article class="metric-card">
                                <div class="metric-icon gradient-secondary">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label">Drafts in progress</span>
                                    <strong class="metric-value">9</strong>
                                    <span class="metric-delta neutral">
                                        <ion-icon name="pause-outline"></ion-icon>
                                        awaiting review
                                    </span>
                                </div>
                            </article>
                            <article class="metric-card">
                                <div class="metric-icon gradient-primary">
                                    <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label">Comments pending</span>
                                    <strong class="metric-value">14</strong>
                                    <span class="metric-delta up">
                                        <ion-icon name="time-outline"></ion-icon>
                                        5 need replies
                                    </span>
                                </div>
                            </article>
                            <article class="metric-card">
                                <div class="metric-icon gradient-secondary">
                                    <ion-icon name="people-circle-outline"></ion-icon>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label">New subscribers</span>
                                    <strong class="metric-value">312</strong>
                                    <span class="metric-delta up">
                                        <ion-icon name="happy-outline"></ion-icon>
                                        +18 today
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
                                    <li>
                                        <div>
                                            <strong>Motion Design</strong>
                                            <p>Top-performing category with 27% of total views.</p>
                                        </div>
                                        <span class="badge badge-soft">+12% vs last week</span>
                                    </li>
                                    <li>
                                        <div>
                                            <strong>Call-to-action clicks</strong>
                                            <p>Readers respond best on Tuesdays between 3-5 PM.</p>
                                        </div>
                                        <span class="badge badge-soft">Optimize schedule</span>
                                    </li>
                                    <li>
                                        <div>
                                            <strong>Newsletter conversions</strong>
                                            <p>Homepage hero experiment increased signups by 9%.</p>
                                        </div>
                                        <span class="badge badge-soft">Experiment holding</span>
                                    </li>
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
                                    <li>
                                        <span class="timeline-point gradient-primary"></span>
                                        <div class="timeline-content">
                                            <strong>Storyboard Draft approved</strong>
                                            <p>“Animating micro-interactions” moved to production queue.</p>
                                            <time datetime="2025-10-31T18:20">18:20 · Oct 31</time>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="timeline-point gradient-secondary"></span>
                                        <div class="timeline-content">
                                            <strong>Comment escalated</strong>
                                            <p>Feedback from Jonas on accessibility guidelines flagged for review.</p>
                                            <time datetime="2025-10-31T15:05">15:05 · Oct 31</time>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="timeline-point gradient-primary"></span>
                                        <div class="timeline-content">
                                            <strong>New subscriber milestone</strong>
                                            <p>Creative Pulse newsletter crossed 15K engaged readers.</p>
                                            <time datetime="2025-10-30T09:45">09:45 · Oct 30</time>
                                        </div>
                                    </li>
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