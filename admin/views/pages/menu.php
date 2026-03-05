<?php
/**
 * Professional Menu & Submenu Manager
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_items = [];
    
    if (isset($_POST['labels'])) {
        foreach ($_POST['labels'] as $index => $label) {
            if (!empty($label)) {
                $item = [
                    "label" => $label,
                    "url" => $_POST['urls'][$index],
                    "type" => $_POST['types'][$index],
                    "children" => []
                ];

                // Check for sub-items if this is a dropdown
                if ($item['type'] === 'dropdown' && isset($_POST['sub_labels'][$index])) {
                    foreach ($_POST['sub_labels'][$index] as $sub_index => $sub_label) {
                        if (!empty($sub_label)) {
                            $item['children'][] = [
                                "label" => $sub_label,
                                "url" => $_POST['sub_urls'][$index][$sub_index]
                            ];
                        }
                    }
                }
                $menu_items[] = $item;
            }
        }
    }

    $menu_data = json_encode([
        "main_menu" => $menu_items,
        "brand_name" => $_POST['brand_name'] ?? 'Boost Stride',
        "cta_text" => $_POST['cta_text'] ?? 'Get A Quote',
        "cta_url" => $_POST['cta_url'] ?? 'quote.php'
    ]);

    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('menu_data', ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    if ($stmt->execute([$menu_data])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Navigation Menu & Dropdowns published successfully!</div>';
    }
}

// Fetch Current Data
$stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_data'");
$stmt->execute();
$current = json_decode($stmt->fetchColumn() ?: '{}', true);

// Default Menu
$defaults = [
    "brand_name" => "Boost Stride",
    "cta_text" => "Get A Quote",
    "cta_url" => "quote.php",
    "main_menu" => [
        ["label" => "Home", "url" => "index.php", "type" => "link"],
        ["label" => "About", "url" => "about.php", "type" => "link"],
        ["label" => "Service", "url" => "service.php", "type" => "link"],
        ["label" => "Pages", "url" => "#", "type" => "dropdown", "children" => [
            ["label" => "Feature", "url" => "feature.php"],
            ["label" => "Free Quote", "url" => "quote.php"],
            ["label" => "Our Team", "url" => "team.php"],
            ["label" => "Testimonial", "url" => "testimonial.php"]
        ]],
        ["label" => "Contact", "url" => "contact.php", "type" => "link"]
    ]
];

$current = array_merge($defaults, $current);
?>

<div class="row">
    <div class="col-12">
        <div class="card p-4 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-sitemap"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Advanced Menu Manager</h4>
                    <p class="text-muted small mb-0">Create professional multi-level navigation with dropdown sub-menus.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <?php echo $message; ?>
        <form method="POST">
            <div class="row">
                <!-- Branding Card -->
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 h-100 border-0 shadow-sm">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-id-card me-2"></i>Global Navigation</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">NAVBAR BRAND</label>
                            <input type="text" name="brand_name" class="form-control rounded-3" value="<?php echo htmlspecialchars($current['brand_name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">CTA BUTTON TEXT</label>
                            <input type="text" name="cta_text" class="form-control rounded-3" value="<?php echo htmlspecialchars($current['cta_text']); ?>">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted">CTA BUTTON URL</label>
                            <input type="text" name="cta_url" class="form-control rounded-3" value="<?php echo htmlspecialchars($current['cta_url']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Menu Construction -->
                <div class="col-lg-8 mb-4">
                    <div class="card p-4 h-100 border-0 shadow-sm">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-layer-group me-2"></i>Menu Architecture</h5>
                                <p class="text-muted small mb-0">Build your site's hierarchy.</p>
                            </div>
                            <button type="button" id="add-main-item" class="btn btn-sm btn-primary rounded-pill px-4 py-2 shadow-sm">
                                <i class="fas fa-plus me-1"></i> Add Parent
                            </button>
                        </div>

                        <div id="menu-builder">
                            <?php foreach($current['main_menu'] as $i => $item): ?>
                            <div class="parent-item-box bg-light p-3 p-sm-4 rounded-4 mb-4 border border-white shadow-sm" data-index="<?php echo $i; ?>">
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-bold extra-small text-muted mb-1">PARENT LABEL</label>
                                        <input type="text" name="labels[]" class="form-control" value="<?php echo htmlspecialchars($item['label']); ?>" placeholder="Home">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label fw-bold extra-small text-muted mb-1">TYPE</label>
                                        <select name="types[]" class="form-select type-selector">
                                            <option value="link" <?php echo $item['type'] === 'link' ? 'selected' : ''; ?>>Link</option>
                                            <option value="dropdown" <?php echo $item['type'] === 'dropdown' ? 'selected' : ''; ?>>Dropdown</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3 url-container <?php echo $item['type'] === 'dropdown' ? 'd-none' : ''; ?>">
                                        <label class="form-label fw-bold extra-small text-muted mb-1">DESTINATION URL</label>
                                        <input type="text" name="urls[]" class="form-control" value="<?php echo htmlspecialchars($item['url']); ?>" placeholder="index.php">
                                    </div>
                                    <div class="col-12 col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-white text-danger border shadow-sm rounded-3 remove-parent w-100 py-2"><i class="fas fa-trash me-2 d-md-none"></i>Delete</button>
                                        <input type="hidden" name="urls[]" class="dropdown-hidden-url <?php echo $item['type'] !== 'dropdown' ? 'd-none' : ''; ?>" value="#">
                                    </div>
                                </div>

                                <!-- Submenu Area -->
                                <div class="submenu-container mt-4 pt-3 border-top <?php echo $item['type'] !== 'dropdown' ? 'd-none' : ''; ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold extra-small text-uppercase mb-0 text-info letter-spacing-1"><i class="fas fa-level-down-alt me-2"></i>Dropdown Links</h6>
                                        <button type="button" class="btn btn-xs btn-info text-white rounded-pill px-3 py-1 extra-small add-sub-item shadow-sm">Add Sub-link</button>
                                    </div>
                                    <div class="sub-items-list px-2">
                                        <?php if(isset($item['children'])): foreach($item['children'] as $si => $child): ?>
                                        <div class="sub-item-row bg-white p-2 rounded-3 row g-2 mb-2 align-items-center shadow-sm border border-light">
                                            <div class="col-5">
                                                <input type="text" name="sub_labels[<?php echo $i; ?>][]" class="form-control form-control-sm border-0" value="<?php echo htmlspecialchars($child['label']); ?>" placeholder="Label">
                                            </div>
                                            <div class="col-5">
                                                <input type="text" name="sub_urls[<?php echo $i; ?>][]" class="form-control form-control-sm border-0" value="<?php echo htmlspecialchars($child['url']); ?>" placeholder="url.php">
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 w-100 remove-sub"><i class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-4 shadow-sm fw-bold">
                        <i class="fas fa-rocket me-2"></i>Save & Deploy Professional Menu
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .extra-small { font-size: 10px; }
    .btn-xs { font-size: 11px; padding: 2px 8px; }
    .parent-item-box { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .parent-item-box:hover { border-color: #6366f1 !important; transform: scale(1.005); }
    .btn-white { background: white; color: #64748b; }
</style>

<script>
let parentCount = <?php echo count($current['main_menu']); ?>;

document.getElementById('add-main-item').addEventListener('click', function() {
    const builder = document.getElementById('menu-builder');
    const index = parentCount++;
    const div = document.createElement('div');
    div.className = 'parent-item-box bg-light p-3 p-sm-4 rounded-4 mb-4 border border-white shadow-sm';
    div.dataset.index = index;
    div.innerHTML = `
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold extra-small text-muted mb-1">PARENT LABEL</label>
                <input type="text" name="labels[]" class="form-control" placeholder="New Navigation">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold extra-small text-muted mb-1">TYPE</label>
                <select name="types[]" class="form-select type-selector">
                    <option value="link">Link</option>
                    <option value="dropdown">Dropdown</option>
                </select>
            </div>
            <div class="col-6 col-md-3 url-container">
                <label class="form-label fw-bold extra-small text-muted mb-1">DESTINATION URL</label>
                <input type="text" name="urls[]" class="form-control" placeholder="page.php">
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-white text-danger border shadow-sm rounded-3 remove-parent w-100 py-2"><i class="fas fa-trash me-2 d-md-none"></i>Delete</button>
                <input type="hidden" name="urls[]" class="dropdown-hidden-url d-none" value="#">
            </div>
        </div>
        <div class="submenu-container mt-4 pt-3 border-top d-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold extra-small text-uppercase mb-0 text-info letter-spacing-1"><i class="fas fa-level-down-alt me-2"></i>Dropdown Links</h6>
                <button type="button" class="btn btn-xs btn-info text-white rounded-pill px-3 py-1 extra-small add-sub-item shadow-sm">Add Sub-link</button>
            </div>
            <div class="sub-items-list px-2"></div>
        </div>
    `;
    builder.appendChild(div);
    attachEvents(div);
});

function attachEvents(parent) {
    const typeSelect = parent.querySelector('.type-selector');
    const urlContainer = parent.querySelector('.url-container');
    const hiddenUrl = parent.querySelector('.dropdown-hidden-url');
    const submenu = parent.querySelector('.submenu-container');
    const addSubBtn = parent.querySelector('.add-sub-item');
    const subList = parent.querySelector('.sub-items-list');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'dropdown') {
            urlContainer.classList.add('d-none');
            hiddenUrl.classList.remove('d-none');
            submenu.classList.remove('d-none');
        } else {
            urlContainer.classList.remove('d-none');
            hiddenUrl.classList.add('d-none');
            submenu.classList.add('d-none');
        }
    });

    addSubBtn.addEventListener('click', function() {
        const pIndex = parent.dataset.index;
        const row = document.createElement('div');
        row.className = 'sub-item-row bg-white p-2 rounded-3 row g-2 mb-2 align-items-center shadow-sm border border-light';
        row.innerHTML = `
            <div class="col-5">
                <input type="text" name="sub_labels[${pIndex}][]" class="form-control form-control-sm border-0" placeholder="Label">
            </div>
            <div class="col-5">
                <input type="text" name="sub_urls[${pIndex}][]" class="form-control form-control-sm border-0" placeholder="url.php">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 w-100 remove-sub"><i class="fas fa-times"></i></button>
            </div>
        `;
        subList.appendChild(row);
        row.querySelector('.remove-sub').onclick = () => row.remove();
    });

    parent.querySelector('.remove-parent').onclick = () => parent.remove();
    parent.querySelectorAll('.remove-sub').forEach(btn => btn.onclick = () => btn.closest('.sub-item-row').remove());
}

document.querySelectorAll('.parent-item-box').forEach(attachEvents);
</script>
