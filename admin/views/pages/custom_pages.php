<?php
/**
 * Unified Pages Directory
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

// Ensure dynamic pages table exists
$db->exec("CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    status ENUM('draft', 'published') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$action = $_GET['action'] ?? 'list';
$edit_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $edit_id) {
    $stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
    if ($stmt->execute([$edit_id])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4">Custom Page deleted successfully!</div>';
        $action = 'list';
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $content = $_POST['content'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $status = $_POST['status'];

    if ($edit_id) {
        $stmt = $db->prepare("UPDATE pages SET title=?, slug=?, content=?, meta_title=?, meta_description=?, status=? WHERE id=?");
        $success = $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $status, $edit_id]);
    } else {
        $stmt = $db->prepare("INSERT INTO pages (title, slug, content, meta_title, meta_description, status) VALUES (?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $status]);
    }

    if ($success) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4">Page saved successfully!</div>';
        $action = 'list';
    } else {
        $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4">Error saving page. Slug might already exist.</div>';
    }
}

// Fetch Page for Editing
$edit_page = null;
if ($action === 'edit' && $edit_id) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_page = $stmt->fetch();
}

// Fetch Current Menu to extract Core Pages
$menu_raw = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'menu_data'")->fetchColumn();
$menu_data = json_decode($menu_raw ?: '{}', true);
$menu_items = $menu_data['main_menu'] ?? [];

// Flatten menu to get all unique URLs
$all_urls = [];
foreach($menu_items as $item) {
    if($item['url'] !== '#') $all_urls[] = $item;
    if($item['type'] === 'dropdown' && isset($item['children'])) {
        foreach($item['children'] as $child) {
            $child['is_child'] = true;
            $all_urls[] = $child;
        }
    }
}

// Core Page Mapping to Admin Routes
$core_map = [
    'index.php' => ['page' => 'hero', 'label' => 'Homepage Layout'],
    'about.php' => ['page' => 'pages', 'tab' => 'about', 'label' => 'About Us Content'],
    'service.php' => ['page' => 'services', 'label' => 'Services Catalog'],
    'team.php' => ['page' => 'pages', 'tab' => 'team', 'label' => 'Team Roster'],
    'testimonial.php' => ['page' => 'testimonials', 'label' => 'Customer Reviews'],
    'contact.php' => ['page' => 'settings', 'label' => 'Contact & Identity'],
    'feature.php' => ['page' => 'pages', 'tab' => 'features', 'label' => 'Features & Highlights'],
    'quote.php' => ['page' => 'footer', 'label' => 'Quote & Footer Content'],
    'project.php' => ['page' => 'hero', 'label' => 'Work & Projects (Hero Section)'] // Mapping to Hero for now or keep generic
];

// Fetch All Custom Dynamic Pages
$custom_pages = $db->query("SELECT * FROM pages ORDER BY created_at DESC")->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <div class="card p-4 border-0 shadow-sm mb-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Website Pages Directory</h4>
                        <p class="text-muted small mb-0">Find and edit any page currently active in your menu.</p>
                    </div>
                </div>
                <?php if ($action === 'list'): ?>
                <a href="?page=custom_pages&action=add" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Create Dynamic Page
                </a>
                <?php else: ?>
                <a href="?page=custom_pages" class="btn btn-white border rounded-pill px-4 py-2 shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Directory
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12">
        <?php echo $message; ?>

        <?php if ($action === 'list'): ?>
        
        <!-- Section 1: Core Website Pages -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-muted extra-small mb-0 text-uppercase letter-spacing-1"><i class="fas fa-star me-2"></i>Header Menu Web Pages</h6>
            <div class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 extra-small fw-bold">System Core</div>
        </div>
        <div class="card border-0 shadow-sm overflow-hidden mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted extra-small fw-bold">MENU LABEL</th>
                            <th class="py-3 text-muted extra-small fw-bold">FILE / PATH</th>
                            <th class="py-3 text-muted extra-small fw-bold">MANAGEMENT SYSTEM</th>
                            <th class="pe-4 py-3 text-center text-muted extra-small fw-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach($core_map as $file => $route):
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark"><?php echo str_replace(['Layout', 'Content', ' catalog', 'Roster', 'Reviews', '& Identity', 'Highlights'], '', $route['label']); ?></span>
                            </td>
                            <td><code class="small bg-light px-2 py-1 rounded text-muted"><?php echo $file; ?></code></td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">
                                    <i class="fas fa-cog me-1"></i><?php echo $route['label']; ?>
                                </span>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="../<?php echo $file; ?>" target="_blank" class="btn btn-sm btn-light border shadow-sm rounded-pill px-3 fw-bold">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <a href="?page=<?php echo $route['page'] . (isset($route['tab']) ? '&tab=' . $route['tab'] : ''); ?>" class="btn btn-sm btn-gradient py-2 px-3 rounded-pill shadow-sm fw-bold">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Custom Dynamic Pages -->
        <h6 class="fw-bold text-muted extra-small mb-3 text-uppercase letter-spacing-1"><i class="fas fa-plus-circle me-2"></i>Dynamic Custom Pages</h6>
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted extra-small fw-bold">PAGE TITLE</th>
                            <th class="py-3 text-muted extra-small fw-bold">PUBLIC URL</th>
                            <th class="py-3 text-muted extra-small fw-bold">STATUS</th>
                            <th class="pe-4 py-3 text-center text-muted extra-small fw-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($custom_pages)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted small">No dynamic custom pages created yet.</td>
                        </tr>
                        <?php endif; ?>
                        <?php foreach($custom_pages as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($p['title']); ?></span>
                            </td>
                            <td>
                                <code class="small bg-light px-2 py-1 rounded">page.php?slug=<?php echo htmlspecialchars($p['slug']); ?></code>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $p['status'] === 'published' ? 'success' : 'secondary'; ?> bg-opacity-10 text-<?php echo $p['status'] === 'published' ? 'success' : 'secondary'; ?> rounded-pill px-3">
                                    <?php echo ucfirst($p['status']); ?>
                                </span>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="../page.php?slug=<?php echo htmlspecialchars($p['slug']); ?>" target="_blank" class="btn btn-sm btn-white border shadow-sm rounded-3"><i class="fas fa-eye text-info"></i></a>
                                    <a href="?page=custom_pages&action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-white border shadow-sm rounded-3"><i class="fas fa-edit text-primary"></i></a>
                                    <a href="?page=custom_pages&action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-white border shadow-sm rounded-3" onclick="return confirm('Delete this page forever?')"><i class="fas fa-trash text-danger"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php else: ?>
        <!-- EDITOR UI (UNCHANGED) -->
        <form method="POST">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card p-4 border-0 shadow-sm mb-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">PAGE TITLE</label>
                            <input type="text" name="title" class="form-control form-control-lg fw-bold" value="<?php echo htmlspecialchars($edit_page['title'] ?? ''); ?>" placeholder="e.g. Terms of Service" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">URL SLUG (Auto-generated if empty)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3 small">page.php?slug=</span>
                                <input type="text" name="slug" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($edit_page['slug'] ?? ''); ?>" placeholder="terms-of-service">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted">PAGE CONTENT (HTML allowed)</label>
                            <textarea name="content" class="form-control summernote" rows="15" placeholder="Write your page content here..."><?php echo htmlspecialchars($edit_page['content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card p-4 border-0 shadow-sm mb-4">
                        <h6 class="fw-bold mb-4">Publishing Info</h6>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">STATUS</label>
                            <select name="status" class="form-select">
                                <option value="published" <?php echo ($edit_page['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="draft" <?php echo ($edit_page['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-4 shadow-sm fw-bold">
                            <i class="fas fa-save me-2"></i> Save Page
                        </button>
                    </div>

                    <div class="card p-4 border-0 shadow-sm">
                        <h6 class="fw-bold mb-4">SEO Settings</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">META TITLE</label>
                            <input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($edit_page['meta_title'] ?? ''); ?>">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted">META DESCRIPTION</label>
                            <textarea name="meta_description" class="form-control" rows="4"><?php echo htmlspecialchars($edit_page['meta_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
