<?php
/**
 * Navigation Architecture & Hierarchy Core
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
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Sitemap Synchronized:</b> Global navigation architecture updated.</div>';
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

<div class="animate-fade-in">
    <!-- Header Branding -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-4">
                    <div class="stat-icon bg-indigo-soft text-indigo shadow-sm" style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Navigation Architect</h4>
                        <p class="text-muted small mb-0 font-monospace extra-small">Registry: site_hierarchy_v2</p>
                    </div>
                </div>
                <div>
                   <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25 small">
                       <i class="fas fa-satellite-dish me-1"></i> Active Sitemap
                   </span>
                </div>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <form method="POST">
        <div class="row g-4">
            <!-- Global Branding Specification -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden h-100">
                    <div class="card-header bg-dark text-white p-4 d-flex align-items-center justify-content-between border-0">
                        <h6 class="fw-bold mb-0 uppercase-tracking small">Identity Parameters</h6>
                        <i class="fas fa-fingerprint text-muted"></i>
                    </div>
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">NAVBAR BRAND NAME</label>
                            <input type="text" name="brand_name" class="form-control border-0 bg-light py-3 rounded-3 fw-bold" value="<?php echo htmlspecialchars($current['brand_name']); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">PRIMARY CTA ACTION</label>
                            <input type="text" name="cta_text" class="form-control border-0 bg-light py-3 rounded-3" value="<?php echo htmlspecialchars($current['cta_text']); ?>" placeholder="Button Text">
                        </div>
                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">CTA ACTION DESTINATION</label>
                            <input type="text" name="cta_url" class="form-control border-0 bg-light py-3 rounded-3 font-monospace small" value="<?php echo htmlspecialchars($current['cta_url']); ?>" placeholder="url.php">
                        </div>

                        <div class="mt-5 pt-4 border-top">
                            <label class="extra-small fw-bold text-muted tracking-widest mb-4 d-block"><i class="fas fa-link me-2"></i>DYNAMIC PAGE REGISTRY</label>
                            <?php 
                                $custom_pages = $db->query("SELECT title, slug FROM pages WHERE status='published'")->fetchAll();
                                if(empty($custom_pages)):
                            ?>
                                <p class="text-muted small fst-italic">No dynamic pages indexed.</p>
                            <?php else: foreach($custom_pages as $cp): ?>
                                <div class="p-3 bg-light rounded-4 mb-2 border border-white shadow-sm d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-dark"><?php echo htmlspecialchars($cp['title']); ?></span>
                                    <button type="button" class="btn btn-xs btn-white border rounded shadow-none extra-small copy-slug" data-slug="page.php?slug=<?php echo $cp['slug']; ?>">Copy URL</button>
                                </div>
                            <?php endforeach; endif; ?>
                            <div class="form-text extra-small mt-3 text-muted">Use these absolute paths for custom content links.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hierarchy Builder -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 uppercase-tracking small text-muted">Sitemap Architecture</h6>
                        <button type="button" id="add-main-item" class="btn btn-primary bg-primary bg-opacity-10 text-primary border-0 rounded-pill px-4 py-2 small fw-bold shadow-none">
                            <i class="fas fa-plus-circle me-1"></i> Append Root Node
                        </button>
                    </div>
                    <div class="card-body p-5">
                        <div id="menu-builder">
                            <?php foreach($current['main_menu'] as $i => $item): ?>
                            <div class="parent-item-box bg-white p-4 rounded-4 mb-4 border border-light transition-all hover-translate-y shadow-sm" data-index="<?php echo $i; ?>">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">NODE LABEL</label>
                                        <input type="text" name="labels[]" class="form-control border-0 bg-light py-2 rounded-3 fw-bold" value="<?php echo htmlspecialchars($item['label']); ?>" placeholder="Menu Title">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">NODE TYPE</label>
                                        <select name="types[]" class="form-select border-0 bg-light py-2 rounded-3 type-selector fw-bold">
                                            <option value="link" <?php echo $item['type'] === 'link' ? 'selected' : ''; ?>>Absolute Link</option>
                                            <option value="dropdown" <?php echo $item['type'] === 'dropdown' ? 'selected' : ''; ?>>Dropdown List</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">DESTINATION</label>
                                        <input type="text" name="urls[]" class="form-control border-0 bg-light py-2 rounded-3 url-input font-monospace small" value="<?php echo htmlspecialchars($item['url']); ?>" placeholder="index.php" <?php echo $item['type'] === 'dropdown' ? 'readonly' : ''; ?>>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger border-0 w-100 py-2 remove-parent"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>

                                <!-- Child Area -->
                                <div class="submenu-container mt-4 pt-4 border-top <?php echo $item['type'] !== 'dropdown' ? 'd-none' : ''; ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="extra-small fw-bold text-indigo tracking-widest mb-0"><i class="fas fa-level-down-alt me-2"></i>SUB-LEVEL NODES</h6>
                                        <button type="button" class="btn btn-indigo-soft btn-xs rounded-pill px-3 py-1 add-sub-item fw-bold border-0">Add Sub-node</button>
                                    </div>
                                    <div class="sub-items-list">
                                        <?php if(isset($item['children'])): foreach($item['children'] as $si => $child): ?>
                                        <div class="sub-item-row bg-light p-3 rounded-4 row g-3 mb-2 align-items-center border border-white shadow-inner mx-0">
                                            <div class="col-5">
                                                <input type="text" name="sub_labels[<?php echo $i; ?>][]" class="form-control border-0 bg-white py-2 rounded-3 small fw-bold shadow-sm" value="<?php echo htmlspecialchars($child['label']); ?>" placeholder="Sub-title">
                                            </div>
                                            <div class="col-5">
                                                <input type="text" name="sub_urls[<?php echo $i; ?>][]" class="form-control border-0 bg-white py-2 rounded-3 font-monospace small shadow-sm" value="<?php echo htmlspecialchars($child['url']); ?>" placeholder="sub-page.php">
                                            </div>
                                            <div class="col-2 text-end">
                                                <button type="button" class="btn btn-sm text-danger border-0 remove-sub shadow-none"><i class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 p-4">
                         <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-pill shadow-lg fw-bold border-0">
                            <i class="fas fa-save me-2"></i>Synchronize Navigation Strategy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 2.5px; }
    .extra-small { font-size: 0.65rem; }
    .btn-xs { font-size: 0.7rem; }
    .hover-translate-y:hover { transform: translateY(-3px); border-color: #4f46e5 !important; box-shadow: 0 15px 30px -10px rgba(0,0,0,0.08) !important; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
    .btn-indigo-soft { background: #eef2ff; color: #4f46e5; }
    .btn-white { background: white; color: #1e293b; }
</style>

<script>
let parentCount = <?php echo count($current['main_menu']); ?>;

document.getElementById('add-main-item').addEventListener('click', function() {
    const builder = document.getElementById('menu-builder');
    const index = parentCount++;
    const div = document.createElement('div');
    div.className = 'parent-item-box bg-white p-4 rounded-4 mb-4 border border-light transition-all hover-translate-y shadow-sm';
    div.dataset.index = index;
    div.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">NODE LABEL</label>
                <input type="text" name="labels[]" class="form-control border-0 bg-light py-2 rounded-3 fw-bold" placeholder="New Page">
            </div>
            <div class="col-md-3">
                <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">NODE TYPE</label>
                <select name="types[]" class="form-select border-0 bg-light py-2 rounded-3 type-selector fw-bold">
                    <option value="link">Absolute Link</option>
                    <option value="dropdown">Dropdown List</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">DESTINATION</label>
                <input type="text" name="urls[]" class="form-control border-0 bg-light py-2 rounded-3 url-input font-monospace small" placeholder="index.php">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger border-0 w-100 py-2 remove-parent"><i class="fas fa-trash-alt"></i></button>
            </div>
        </div>
        <div class="submenu-container mt-4 pt-4 border-top d-none">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="extra-small fw-bold text-indigo tracking-widest mb-0"><i class="fas fa-level-down-alt me-2"></i>SUB-LEVEL NODES</h6>
                <button type="button" class="btn btn-indigo-soft btn-xs rounded-pill px-3 py-1 add-sub-item fw-bold border-0">Add Sub-node</button>
            </div>
            <div class="sub-items-list"></div>
        </div>
    `;
    builder.appendChild(div);
    attachEvents(div);
});

function attachEvents(parent) {
    const typeSelect = parent.querySelector('.type-selector');
    const urlInput = parent.querySelector('.url-input');
    const submenu = parent.querySelector('.submenu-container');
    const addSubBtn = parent.querySelector('.add-sub-item');
    const subList = parent.querySelector('.sub-items-list');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'dropdown') {
            urlInput.value = '#';
            urlInput.readOnly = true;
            submenu.classList.remove('d-none');
        } else {
            if (urlInput.value === '#') urlInput.value = '';
            urlInput.readOnly = false;
            submenu.classList.add('d-none');
        }
    });

    addSubBtn.addEventListener('click', function() {
        const pIndex = parent.dataset.index;
        const row = document.createElement('div');
        row.className = 'sub-item-row bg-light p-3 rounded-4 row g-3 mb-2 align-items-center border border-white shadow-inner mx-0';
        row.innerHTML = `
            <div class="col-5">
                <input type="text" name="sub_labels[${pIndex}][]" class="form-control border-0 bg-white py-2 rounded-3 small fw-bold shadow-sm" placeholder="Sub-title">
            </div>
            <div class="col-5">
                <input type="text" name="sub_urls[${pIndex}][]" class="form-control border-0 bg-white py-2 rounded-3 font-monospace small shadow-sm" placeholder="sub-page.php">
            </div>
            <div class="col-2 text-end">
                <button type="button" class="btn btn-sm text-danger border-0 remove-sub shadow-none"><i class="fas fa-times"></i></button>
            </div>
        `;
        subList.appendChild(row);
        row.querySelector('.remove-sub').onclick = () => row.remove();
    });

    parent.querySelector('.remove-parent').onclick = () => parent.remove();
    parent.querySelectorAll('.remove-sub').forEach(btn => btn.onclick = () => btn.closest('.sub-item-row').remove());
}

document.querySelectorAll('.copy-slug').forEach(btn => {
    btn.onclick = () => {
        navigator.clipboard.writeText(btn.dataset.slug);
        const originalText = btn.innerText;
        btn.innerText = 'Copied!';
        btn.classList.add('bg-success', 'text-white');
        setTimeout(() => {
            btn.innerText = originalText;
            btn.classList.remove('bg-success', 'text-white');
        }, 1500);
    };
});

document.querySelectorAll('.parent-item-box').forEach(attachEvents);
</script>

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
                <input type="text" name="urls[]" class="form-control url-input" placeholder="page.php">
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-white text-danger border shadow-sm rounded-3 remove-parent w-100 py-2">Delete</button>
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
    const urlInput = parent.querySelector('.url-input');
    const submenu = parent.querySelector('.submenu-container');
    const addSubBtn = parent.querySelector('.add-sub-item');
    const subList = parent.querySelector('.sub-items-list');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'dropdown') {
            urlInput.value = '#';
            urlInput.readOnly = true;
            urlInput.style.background = '#e9ecef';
            submenu.classList.remove('d-none');
        } else {
            if (urlInput.value === '#') urlInput.value = '';
            urlInput.readOnly = false;
            urlInput.style.background = '';
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
