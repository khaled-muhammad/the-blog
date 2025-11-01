<!doctype html>
<html lang="en">

<head>
    <title>The Blog | Create Category</title>
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
    <link rel="stylesheet" href="../css/category-create.css">
</head>

<body>
    <?php include '../incs/header.php'; ?>
    <main>
        <div class="container-fluid mt-4">
            <div class="d-flex gap-3 admin-layout align-items-stretch">
                <?php include 'incs/sidebar.php'; ?>
                <div class="admin-content flex-grow-1">
                    <?php
                        $paletteOptions = [
                            ['value' => 'sunrise', 'label' => 'Sunrise Gradient', 'colors' => '#fe5038 → #ff2b87'],
                            ['value' => 'fjord', 'label' => 'Fjord Mist', 'colors' => '#4d6f91 → #79a7c7'],
                            ['value' => 'sage', 'label' => 'Sage Meadow', 'colors' => '#66d37c → #a5e0b9'],
                            ['value' => 'ember', 'label' => 'Ember Glow', 'colors' => '#ff9f1c → #ffc14f'],
                            ['value' => 'aurora', 'label' => 'Aurora Veil', 'colors' => '#8a84ff → #c39bff'],
                        ];

                        $curators = ['Ava Larsen', 'Jonas Holm', 'Maya Richter', 'Elin Skarsgard'];
                        $modules = ['Spotlight carousel', 'Hero playlist', 'Starter toolkit', 'Case study stack'];
                        $tagSuggestions = ['motion systems', 'micro-interactions', 'rituals', 'Nordic UI', 'storyboarding'];
                    ?>

                    <header class="page-header create-header">
                        <div>
                            <span class="page-kicker">Collection Design</span>
                            <h1 class="page-title">Launch a new category</h1>
                            <p class="page-lede">Shape the discovery experience, define the visual language, and sync publishing modules with confidence.</p>
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
                            <button type="submit" form="create-category-form" class="btn btn-dashboard">
                                <ion-icon name="rocket-outline"></ion-icon>
                                <span>Publish</span>
                            </button>
                        </div>
                    </header>

                    <section class="create-category-grid">
                        <form id="create-category-form" class="panel-card category-form" action="#" method="post" enctype="multipart/form-data">
                            <header class="panel-header">
                                <div>
                                    <span class="panel-kicker">Identity</span>
                                    <h2 class="panel-title">Category essentials</h2>
                                </div>
                                <span class="badge badge-soft">Autosave enabled</span>
                            </header>
                            <div class="form-stack">
                                <div class="form-group">
                                    <label for="category-name">Category name</label>
                                    <input type="text" id="category-name" name="name" placeholder="Nordic Motion Systems" required>
                                </div>
                                <div class="dual-group">
                                    <div class="form-group">
                                        <label for="category-slug">Slug</label>
                                        <div class="input-prefix">
                                            <span>/category/</span>
                                            <input type="text" id="category-slug" name="slug" placeholder="nordic-motion-systems" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="category-curator">Lead curator</label>
                                        <select id="category-curator" name="curator">
                                            <option value="" disabled selected>Select curator</option>
                                            <?php foreach ($curators as $curator) : ?>
                                                <option value="<?php echo strtolower(str_replace(' ', '-', $curator)); ?>"><?php echo $curator; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="category-intro">Introduction</label>
                                    <textarea id="category-intro" name="intro" rows="3" placeholder="Describe the promise of this category in one vivid paragraph..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="category-description">Long description</label>
                                    <textarea id="category-description" name="description" rows="6" placeholder="How does this collection serve the audience? Outline tone, purpose, and core coverage..."></textarea>
                                </div>
                                <div class="dual-group">
                                    <div class="form-group palette-grid" role="group" aria-label="Palette options">
                                        <span class="field-label">Palette</span>
                                        <div class="palette-options">
                                            <?php foreach ($paletteOptions as $palette) : ?>
                                                <label class="palette-card">
                                                    <input type="radio" name="palette" value="<?php echo $palette['value']; ?>" <?php echo $palette['value'] === 'sunrise' ? 'checked' : ''; ?>>
                                                    <span class="swatch"></span>
                                                    <div>
                                                        <strong><?php echo $palette['label']; ?></strong>
                                                        <small><?php echo $palette['colors']; ?></small>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="category-icon">Icon keyword</label>
                                        <input type="text" id="category-icon" name="icon" placeholder="ion-flash-outline">
                                        <p class="field-hint">Uses Ionicons — keep it descriptive & accessible.</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="category-tags">Keywords</label>
                                    <div class="chips-input" role="listbox" aria-label="Selected tags">
                                        <?php foreach ($tagSuggestions as $tag) : ?>
                                            <button type="button" class="chip" aria-selected="false">
                                                <ion-icon name="pricetag-outline"></ion-icon>
                                                <span><?php echo $tag; ?></span>
                                            </button>
                                        <?php endforeach; ?>
                                        <input type="text" id="category-tags" name="tags" placeholder="Add keyword" aria-label="Add keyword">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="category-hero">Hero media</label>
                                    <div class="upload-field">
                                        <input type="file" id="category-hero" name="hero" accept="image/*,video/*">
                                        <div class="upload-prompt">
                                            <ion-icon name="cloud-upload-outline"></ion-icon>
                                            <span>Drop media or <strong>browse</strong></span>
                                            <p>Recommended: 2400 × 1260 · JPG, PNG, or MP4</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-footer">
                                <div class="autosave">
                                    <span class="dot"></span>
                                    <span>Last autosave · moments ago</span>
                                </div>
                                <div class="footer-actions">
                                    <button type="reset" class="ghost-btn">
                                        <ion-icon name="refresh-outline"></ion-icon>
                                        <span>Reset</span>
                                    </button>
                                    <button type="submit" class="btn btn-dashboard">
                                        <ion-icon name="arrow-forward-circle-outline"></ion-icon>
                                        <span>Save category</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="create-category-side">
                            <article class="panel-card preview-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Preview</span>
                                        <h2 class="panel-title">Hero snapshot</h2>
                                    </div>
                                </header>
                                <div class="preview-card">
                                    <div class="preview-hero gradient-placeholder">
                                        <ion-icon name="images-outline"></ion-icon>
                                    </div>
                                    <div class="preview-body">
                                        <span class="preview-kicker">Coming soon</span>
                                        <h3>Nordic Motion Systems</h3>
                                        <p>Creative rituals and motion frameworks that balance calm precision with kinetic empathy.</p>
                                        <div class="preview-tags">
                                            <span>#motion</span>
                                            <span>#nordic</span>
                                            <span>#storytelling</span>
                                        </div>
                                        <button type="button" class="ghost-btn light">
                                            <ion-icon name="open-outline"></ion-icon>
                                            <span>Open preview</span>
                                        </button>
                                    </div>
                                </div>
                            </article>

                            <article class="panel-card modules-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Modules</span>
                                        <h2 class="panel-title">Homepage placements</h2>
                                    </div>
                                </header>
                                <div class="modules-body">
                                    <div class="form-group">
                                        <label for="module-selection">Attach modules</label>
                                        <select id="module-selection" name="modules[]" multiple size="4">
                                            <?php foreach ($modules as $module) : ?>
                                                <option value="<?php echo strtolower(str_replace(' ', '-', $module)); ?>"><?php echo $module; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="field-hint">Hold Command (⌘) to select multiple placements.</p>
                                    </div>
                                    <div class="module-note">
                                        <ion-icon name="sparkles-outline"></ion-icon>
                                        <p>Align with upcoming campaigns to maximize visibility.</p>
                                    </div>
                                </div>
                            </article>

                            <article class="panel-card guidance-panel">
                                <header class="panel-header">
                                    <div>
                                        <span class="panel-kicker">Playbook</span>
                                        <h2 class="panel-title">Editorial guidance</h2>
                                    </div>
                                </header>
                                <ul class="guidance-list">
                                    <li>
                                        <ion-icon name="checkbox-outline"></ion-icon>
                                        <div>
                                            <strong>Voice & tone</strong>
                                            <p>Lead with clarity, give space for visuals, and celebrate iterative exploration.</p>
                                        </div>
                                    </li>
                                    <li>
                                        <ion-icon name="checkbox-outline"></ion-icon>
                                        <div>
                                            <strong>Content ratio</strong>
                                            <p>Aim for 60% tutorials, 30% case studies, 10% community highlights.</p>
                                        </div>
                                    </li>
                                    <li>
                                        <ion-icon name="checkbox-outline"></ion-icon>
                                        <div>
                                            <strong>CTA strategy</strong>
                                            <p>Link to relevant toolkits, downloadable assets, and live workshops.</p>
                                        </div>
                                    </li>
                                </ul>
                                <button type="button" class="ghost-btn light full-width">
                                    <ion-icon name="add-outline"></ion-icon>
                                    <span>Add guideline</span>
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
