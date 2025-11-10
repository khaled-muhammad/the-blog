<?php
if (!function_exists('isAuthenticated')) {
    require_once __DIR__ . '/../config.php';
}

$isAuthenticated = isAuthenticated();
$currentUser = getCurrentUser();
?>
    <header>
        <nav
            class="navbar navbar-expand-sm navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo $isAuthenticated ? '/admin' : '/'; ?>">
                    <div class="avatar-circle">
                        <?php 
                        if ($isAuthenticated && $currentUser) {
                            $initials = '';
                            if ($currentUser['display_name']) {
                                $names = explode(' ', $currentUser['display_name']);
                                $initials = strtoupper(substr($names[0], 0, 1));
                                if (isset($names[1])) {
                                    $initials .= strtoupper(substr($names[1], 0, 1));
                                }
                            } else {
                                $initials = strtoupper(substr($currentUser['username'] ?? 'K', 0, 1));
                            }
                            echo htmlspecialchars($initials);
                        } else {
                            echo 'K';
                        }
                        ?>
                    </div>
                </a>
                <button
                    class="navbar-toggler d-lg-none"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapsibleNavId"
                    aria-controls="collapsibleNavId"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavId">
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/posts.php">Posts</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/bookmarks.php">Bookmarks</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" href="/about.php">About Me</a>
                        </li>
                        <?php if ($isAuthenticated): ?>
                        <li class="nav-item logout-link">
                            <a class="nav-link" href="/logout.php">Logout</a>
                            <div class="corner-accent top-left"></div>
                            <div class="corner-accent bottom-right"></div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

    </header>