<?php
/**
 * Unified Pages Directory - Advanced UI Version
 */

use Core\Database;
use Core\Uploader;

$db = Database::getInstance();
$message = '';

// Ensure dynamic pages table exists with modern schema
$db->exec("CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    header_image VARCHAR(255),
    featured_image VARCHAR(255),
    video_url VARCHAR(255),
    external_link VARCHAR(255),
    status ENUM('draft', 'published') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$action = $_GET['action'] ?? 'list';
$edit_id = $_GET['id'] ?? null;

// Handle Delete with feedback
if ($action === 'delete' && $edit_id) {
    if ($edit_id == 1) { // Example protection for a home page if applicable
        $message = '<div class="alert alert-warning border-0 shadow-sm rounded-4 animate-fade-in">System protected pages cannot be removed.</div>';
    } else {
        $stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
        if ($stmt->execute([$edit_id])) {
            $message = '<div class="alert alert-success border-0 shadow-sm rounded-4 animate-fade-in"><b>Success!</b> The page has been permanently removed from the directory.</div>';
            $action = 'list';
        }
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $content = $_POST['content'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $video_url = $_POST['video_url'];
    $external_link = $_POST['external_link'];
    $status = $_POST['status'];

    $header_image = $_POST['old_header_image'] ?? '';
    $featured_image = $_POST['old_featured_image'] ?? '';

    // Handle Header Image Upload
    if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
        $result = Uploader::upload($_FILES['header_image'], UPLOAD_DIR);
        if (isset($result['success'])) $header_image = $result['path'];
    }

    // Handle Featured Image Upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $result = Uploader::upload($_FILES['featured_image'], UPLOAD_DIR);
        if (isset($result['success'])) $featured_image = $result['path'];
    }

    if ($edit_id) {
        $stmt = $db->prepare("UPDATE pages SET title=?, slug=?, content=?, meta_title=?, meta_description=?, header_image=?, featured_image=?, video_url=?, external_link=?, status=? WHERE id=?");
        $success = $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $header_image, $featured_image, $video_url, $external_link, $status, $edit_id]);
    } else {
        $stmt = $db->prepare("INSERT INTO pages (title, slug, content, meta_title, meta_description, header_image, featured_image, video_url, external_link, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $header_image, $featured_image, $video_url, $external_link, $status]);
    }

    if ($success) {
        echo "<script>window.location.href='?page=custom_pages&status=saved';</script>";
        exit;
    } else {
        $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4 text-center py-3"><b>Slug Collision:</b> A page with this URL slug already exists. Please choose a unique name.</div>';
    }
}

// Success Notification Handling
if (isset($_GET['status']) && $_GET['status'] === 'saved') {
    $message = '<div class="alert alert-success border-0 shadow-sm rounded-4 text-center py-3 animate-fade-in"><i class="fas fa-check-circle me-2"></i>Page architecture and content synchronized successfully!</div>';
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

// Core Page Mapping to Admin Routes
$core_map = [
    'index.php' => ['page' => 'hero', 'label' => 'Homepage Layout', 'icon' => 'fa-home'],
    'about.php' => ['page' => 'pages', 'tab' => 'about', 'label' => 'About Us Content', 'icon' => 'fa-info-circle'],
    'service.php' => ['page' => 'services', 'label' => 'Services Catalog', 'icon' => 'fa-tools'],
    'team.php' => ['page' => 'pages', 'tab' => 'team', 'label' => 'Team Roster', 'icon' => 'fa-users'],
    'testimonial.php' => ['page' => 'testimonials', 'label' => 'Customer Reviews', 'icon' => 'fa-comment-dots'],
    'contact.php' => ['page' => 'settings', 'label' => 'Contact & Identity', 'icon' => 'fa-id-card'],
    'feature.php' => ['page' => 'pages', 'tab' => 'features', 'label' => 'Features & Highlights', 'icon' => 'fa-star'],
    'quote.php' => ['page' => 'footer', 'label' => 'Quote & Footer Content', 'icon' => 'fa-quote-right']
];

// Fetch All Custom Dynamic Pages
$custom_pages = $db->query("SELECT * FROM pages ORDER BY created_at DESC")->fetchAll();
?>

<style>
    /* Professional UI Enhancements */
    .page-header-premium {
        background: white;
        border-radius: 24px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.02);
    }
    
    .status-pill {
        padding: 0.4rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-published { background: #ecfdf5; color: #10b981; }
    .status-draft { background: #f1f5f9; color: #64748b; }
    
    .table-modern thead th {
        background: #f8fafc;
        border: none;
        padding: 1rem 1.5rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
    }
    
    .table-modern tbody td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    
    .table-modern tr:last-child td { border-bottom: none; }
    
    .table-modern tr { transition: all 0.2s; }
    .table-modern tr:hover { background: #f9fafb; }
    
    .btn-action {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
    }
    
    .btn-action:hover {
        background: #f1f5f9;
        color: #4f46e5;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    
    .btn-action.btn-delete:hover { border-color: #ef4444; color: #ef4444; }
    
    .card-editor {
        border-radius: 28px;
        border: none;
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    
    .sidebar-widget {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(0,0,0,0.03);
    }
    
    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    
    .wp-title-input {
        border: none !important;
        background: transparent !important;
        font-size: 2.25rem !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        padding: 0 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .wp-title-input:focus { box-shadow: none !important; }
    .wp-title-input::placeholder { color: #cbd5e1; }
    
    .slug-preview {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }
    
    .sticky-box {
        position: sticky;
        top: 2rem;
    }
</style>

<div class="animate-fade-in">
    <!-- Sophisticated Header -->
    <div class="page-header-premium">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-4">
                <div class="stat-icon bg-indigo-soft text-indigo shadow-sm" style="background: #eef2ff; color: #4f46e5; width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Unified Directory</h3>
                    <p class="text-muted small mb-0">Architect and manage your website's structural integrity.</p>
                </div>
            </div>
            
            <?php if ($action === 'list'): ?>
                <a href="?page=custom_pages&action=add" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-bold">
                    <i class="fas fa-plus-circle me-2"></i>New Dynamic Page
                </a>
            <?php else: ?>
                <a href="?page=custom_pages" class="btn btn-outline-secondary px-4 py-2 rounded-pill shadow-sm fw-bold">
                    <i class="fas fa-arrow-left me-2"></i>Back to Directory
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $message; ?>

    <?php if ($action === 'list'): ?>
        
        <!-- SECTION 1: SYSTEM CORE PAGES -->
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                <h6 class="fw-bold text-muted small text-uppercase mb-0" style="letter-spacing: 1.5px;">
                    <i class="fas fa-shield-alt text-primary me-2"></i>Engineered Core Pages
                </h6>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold extra-small">Immutable Routes</span>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Identity</th>
                                <th>Source Path</th>
                                <th>Logic Component</th>
                                <th class="text-end">Management</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($core_map as $file => $route): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light p-2 rounded-3 text-primary text-center" style="width: 36px;">
                                                <i class="fas <?php echo $route['icon']; ?>"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo str_replace(['Layout', 'Content', ' catalog', 'Roster', 'Reviews', '& Identity', 'Highlights'], '', $route['label']); ?></div>
                                                <div class="extra-small text-muted">Core Module</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="small text-muted py-1">/<?php echo $file; ?></code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1 small fw-normal">
                                            <i class="fas fa-puzzle-piece text-muted me-2"></i><?php echo $route['label']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="../<?php echo $file; ?>" target="_blank" class="btn-action" title="View Public Page">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <a href="?page=<?php echo $route['page'] . (isset($route['tab']) ? '&tab=' . $route['tab'] : ''); ?>" class="btn-action" title="Configure Logic">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECTION 2: DYNAMIC CUSTOM PAGES -->
        <div>
            <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                <h6 class="fw-bold text-muted small text-uppercase mb-0" style="letter-spacing: 1.5px;">
                    <i class="fas fa-bolt text-warning me-2"></i>Dynamic Asset Matrix
                </h6>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Subject Title</th>
                                <th>Access URI</th>
                                <th>Status</th>
                                <th class="text-end">Interactive Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($custom_pages)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-cloud-moon fa-3x text-light mb-3"></i>
                                            <p class="text-muted mb-0">The dynamic matrix is currently empty.</p>
                                            <a href="?page=custom_pages&action=add" class="btn btn-link text-primary mt-2">Create your first page</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($custom_pages as $p): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($p['title']); ?></div>
                                            <div class="extra-small text-muted">Created <?php echo date('M d, Y', strtotime($p['created_at'])); ?></div>
                                        </td>
                                        <td>
                                            <div class="slug-preview">
                                                <i class="fas fa-link extra-small"></i>
                                                /page.php?slug=<?php echo htmlspecialchars($p['slug']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-pill status-<?php echo $p['status']; ?>">
                                                <i class="fas <?php echo $p['status'] === 'published' ? 'fa-check-circle' : 'fa-clock'; ?> me-1"></i>
                                                <?php echo $p['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="../page.php?slug=<?php echo htmlspecialchars($p['slug']); ?>" target="_blank" class="btn-action" title="View Live">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?page=custom_pages&action=edit&id=<?php echo $p['id']; ?>" class="btn-action" title="Edit Content">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?page=custom_pages&action=delete&id=<?php echo $p['id']; ?>" class="btn-action btn-delete" title="Purge" onclick="return confirm('Archive deletion: Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>
        
        <!-- HIGH-END CMS EDITOR INTERFACE -->
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4 overflow-visible">
                <!-- Main Context Area -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg mb-4 bg-white" style="border-radius: 30px;">
                        <div class="card-body p-5">
                            <div class="mb-5">
                                <label class="extra-small fw-bold text-primary tracking-widest mb-2 d-block">PAGE TITLE</label>
                                <input type="text" name="title" class="form-control wp-title-input border-0" value="<?php echo htmlspecialchars($edit_page['title'] ?? ''); ?>" placeholder="Enter Subject..." required autocomplete="off" style="font-size: 2.8rem !important; margin-left: -2px;">
                                
                                <div class="mt-3 d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-muted fw-bold px-2 py-1 rounded-2" style="font-size: 0.65rem;">PERMALINK</span>
                                    <div class="slug-preview-container d-flex align-items-center gap-1 font-monospace text-muted small px-3 py-2 bg-light rounded-pill border">
                                        <span>.../page.php?slug=</span>
                                        <input type="text" name="slug" id="slug-input" class="border-0 bg-transparent fw-bold text-dark p-0 outline-none" value="<?php echo htmlspecialchars($edit_page['slug'] ?? ''); ?>" placeholder="click-to-edit-slug" style="width: auto; min-width: 50px;">
                                        <i class="fas fa-pen-nib shadow-sm bg-white p-1 rounded-circle" style="font-size: 0.5rem;"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="editor-wrapper border-top pt-5" style="min-height: 500px;">
                                <label class="extra-small fw-bold text-muted tracking-widest mb-4 d-block">CONTENT ARCHITECTURE</label>
                                <textarea name="content" class="form-control summernote"><?php echo htmlspecialchars($edit_page['content'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Strategic Sidebar Controls -->
                <div class="col-lg-4">
                    <div class="sticky-box">
                        <!-- Deployment Module -->
                        <div class="card border-0 shadow-lg mb-4" style="border-radius: 24px; overflow: hidden;">
                            <div class="card-header bg-dark text-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-0 small">Publishing</h6>
                                <i class="fas fa-shield-check text-success"></i>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <label class="extra-small fw-bold text-muted text-uppercase">Visibility Status</label>
                                        <span class="badge <?php echo ($edit_page['status'] ?? 'published') === 'published' ? 'bg-success' : 'bg-warning'; ?> rounded-pill">
                                            <?php echo strtoupper($edit_page['status'] ?? 'published'); ?>
                                        </span>
                                    </div>
                                    <div class="btn-group w-100 p-1 bg-light rounded-3" role="group">
                                        <input type="radio" class="btn-check" name="status" id="status-pub" value="published" <?php echo ($edit_page['status'] ?? 'published') === 'published' ? 'checked' : ''; ?>>
                                        <label class="btn btn-white border-0 shadow-sm rounded-2 flex-grow-1 py-2 fw-bold small text-uppercase" style="font-size: 0.65rem;" for="status-pub">Public</label>
                                        
                                        <input type="radio" class="btn-check" name="status" id="status-draft" value="draft" <?php echo ($edit_page['status'] ?? '') === 'draft' ? 'checked' : ''; ?>>
                                        <label class="btn btn-outline-secondary border-0 flex-grow-1 py-2 fw-bold small text-uppercase" style="font-size: 0.65rem;" for="status-draft">Draft</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill shadow-lg fw-bold mb-3 border-0" style="background: var(--primary-gradient);">
                                    <i class="fas fa-rocket me-2"></i> <?php echo $edit_page ? 'Push Updates' : 'Launch Page'; ?>
                                </button>
                                
                                <?php if($edit_id): ?>
                                    <a href="../page.php?slug=<?php echo $edit_page['slug']; ?>" target="_blank" class="btn btn-light w-100 py-2 rounded-pill small border text-muted fw-bold">
                                        <i class="fas fa-eye me-2"></i>Review Live
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Asset Engine -->
                        <div class="card border-0 shadow-lg mb-4" style="border-radius: 24px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 small d-flex align-items-center">
                                    <i class="fas fa-images text-primary me-2"></i> Visual Identity
                                </h6>
                                
                                <div class="mb-4">
                                    <label class="extra-small fw-bold text-muted mb-2 d-block">HERO BACKGROUND</label>
                                    <div class="position-relative group">
                                        <div class="bg-light rounded-4 overflow-hidden border-2 border-dashed d-flex align-items-center justify-content-center transition-all" style="height: 140px;">
                                            <?php if($edit_page && !empty($edit_page['header_image'])): ?>
                                                <img src="../<?php echo $edit_page['header_image']; ?>" class="w-100 h-100 object-fit-cover shadow-sm">
                                            <?php else: ?>
                                                <i class="fas fa-cloud-upload-alt text-muted opacity-25 fa-3x"></i>
                                            <?php endif; ?>
                                        </div>
                                        <input type="file" name="header_image" class="form-control form-control-sm mt-2 rounded-3 border-light shadow-sm">
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="extra-small fw-bold text-muted mb-2 d-block">FEATURED THUMBNAIL</label>
                                    <div class="bg-light rounded-4 overflow-hidden border-2 border-dashed d-flex align-items-center justify-content-center" style="height: 140px;">
                                        <?php if($edit_page && !empty($edit_page['featured_image'])): ?>
                                            <img src="../<?php echo $edit_page['featured_image']; ?>" class="w-100 h-100 object-fit-cover shadow-sm">
                                        <?php else: ?>
                                            <i class="fas fa-image text-muted opacity-25 fa-3x"></i>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="featured_image" class="form-control form-control-sm mt-2 rounded-3 border-light shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Search Engine Optimization (SEO) -->
                        <div class="card border-0 shadow-lg mb-4" style="border-radius: 24px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 small d-flex align-items-center">
                                    <i class="fas fa-search-plus text-primary me-2"></i> Search Presence
                                </h6>
                                <div class="mb-3">
                                    <label class="extra-small fw-bold text-muted mb-2 d-block">META TITLE</label>
                                    <input type="text" name="meta_title" class="form-control form-control-sm bg-light border-0 py-2 rounded-3" value="<?php echo htmlspecialchars($edit_page['meta_title'] ?? ''); ?>" placeholder="Google title prefix...">
                                </div>
                                <div class="mb-0">
                                    <label class="extra-small fw-bold text-muted mb-2 d-block">META DESCRIPTION</label>
                                    <textarea name="meta_description" class="form-control form-control-sm bg-light border-0 p-3 rounded-3" rows="4" placeholder="Brief summary for indexing..."><?php echo htmlspecialchars($edit_page['meta_description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Imagery Persistence State -->
            <input type="hidden" name="old_header_image" value="<?php echo $edit_page['header_image'] ?? ''; ?>">
            <input type="hidden" name="old_featured_image" value="<?php echo $edit_page['featured_image'] ?? ''; ?>">
        </form>
        
        <script>
            // Advanced Slug Engine
            const titleInput = document.querySelector('input[name="title"]');
            const slugInput = document.getElementById('slug-input');
            
            if(titleInput) {
                titleInput.addEventListener('input', function() {
                    const slug = this.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    
                    // Only auto-update slug if user hasn't typed in slug manually
                    if(!slugInput.dataset.manuallyEdited) {
                        slugInput.value = slug;
                    }
                });

                slugInput.addEventListener('input', () => {
                    slugInput.dataset.manuallyEdited = true;
                });
            }

            // Copy System URI
            document.querySelectorAll('.copy-slug').forEach(el => {
                el.onclick = function() {
                    const slug = this.getAttribute('data-slug');
                    navigator.clipboard.writeText(slug);
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    this.style.background = '#10b981';
                    this.style.color = 'white';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.background = 'white';
                        this.style.color = '#1e293b';
                    }, 1500);
                }
            });
        </script>
    <?php endif; ?>
</div>

<style>
    /* Premium Editor Styles */
    .note-editor.note-frame { border: none !important; border-radius: 0 !important; }
    .note-toolbar { 
        background: #f8fafc !important; 
        border: none !important;
        border-bottom: 2px solid #f1f5f9 !important; 
        padding: 1rem !important; 
    }
    .note-btn { 
        background: white !important; 
        border: 1px solid #e2e8f0 !important; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
        border-radius: 8px !important;
        margin-right: 4px !important;
    }
    .note-editable { 
        padding: 4rem 1rem !important; 
        font-family: inherit !important; 
        font-size: 1.2rem !important;
        line-height: 1.8 !important;
        color: #1e293b !important;
    }
</style>
