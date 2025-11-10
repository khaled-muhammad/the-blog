                <button
                    class="admin-mobile-toggle d-lg-none"
                    type="button"
                    aria-controls="adminSidebar"
                    aria-expanded="false">
                    <ion-icon name="menu-outline"></ion-icon>
                    <span>Menu</span>
                </button>
                <div class="admin-sidebar-wrapper" id="adminSidebarWrapper">
                    <div class="admin-sidebar-backdrop" aria-hidden="true"></div>
                    <aside class="admin-sidebar" id="adminSidebar" aria-hidden="false">
                        <button class="sidebar-toggle" type="button" aria-label="Collapse sidebar" aria-expanded="true">
                            <ion-icon name="chevron-back-outline"></ion-icon>
                        </button>
                        <nav class="admin-side-nav">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link active" href="/admin">
                                        <ion-icon name="speedometer-outline"></ion-icon>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/posts.php">
                                        <ion-icon name="newspaper-outline"></ion-icon>
                                        <span>Posts</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/categories.php">
                                        <ion-icon name="albums-outline"></ion-icon>
                                        <span>Categories</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/comments.php">
                                        <ion-icon name="chatbubbles-outline"></ion-icon>
                                        <span>Comments</span>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a class="nav-link" href="/admin/users.php">
                                        <ion-icon name="people-outline"></ion-icon>
                                        <span>Users</span>
                                    </a>
                                </li> -->
                                <li class="nav-item">
                                    <a class="nav-link" href="/" target="_blank" rel="noopener">
                                        <ion-icon name="globe-outline"></ion-icon>
                                        <span>View Site</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link logout-link" href="/logout.php">
                                        <ion-icon name="log-out-outline"></ion-icon>
                                        <span>Logout</span>
                                    </a>
                                    <div class="corner-accent top-left"></div>
                                    <div class="corner-accent bottom-right"></div>
                                </li>
                                <li class="nav-item nav-item-close d-lg-none">
                                    <button class="sidebar-close-btn" type="button">
                                        <span>Close Menu</span>
                                        <ion-icon name="close-circle-outline"></ion-icon>
                                    </button>
                                </li>
                            </ul>
                            <div class="mbg"></div>
                            <div class="lbg"></div>
                            <div class="rbg"></div>
                        </nav>
                    </aside>
                </div>