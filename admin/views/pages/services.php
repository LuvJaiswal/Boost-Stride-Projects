<?php
/**
 * Professional Service Architecture & Catalog Management
 */

use Core\Database;
use Core\Uploader;

$db = Database::getInstance();
$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// 1. Handle Delete
if ($action === 'delete' && $id) {
    $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
    if ($stmt->execute([$id])) {
        // Redirect with Success
        echo "<script>window.location.href='?page=services&status=deleted';</script>";
        exit;
    }
}

// 2. Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $content = $_POST['content'] ?? '';
    $sort_order = (int)$_POST['sort_order'];
    $existing_image = $_POST['existing_image'] ?? 'img/service-1.jpg';

    // Image Upload Logic
    $image_path = $existing_image;
    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
        $result = Uploader::upload($_FILES['service_image'], UPLOAD_DIR);
        if (isset($result['success'])) {
            $image_path = $result['path'];
        } else {
            $message = '<div class="alert alert-danger border-0 shadow-lg rounded-4"><i class="fas fa-exclamation-triangle me-2"></i>' . $result['error'] . '</div>';
        }
    }

    if (empty($message)) {
        if ($id) {
            $stmt = $db->prepare("UPDATE services SET title = ?, description = ?, content = ?, image = ?, sort_order = ? WHERE id = ?");
            $success = $stmt->execute([$title, $description, $content, $image_path, $sort_order, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO services (title, description, content, image, sort_order) VALUES (?, ?, ?, ?, ?)");
            $success = $stmt->execute([$title, $description, $content, $image_path, $sort_order]);
        }

        if ($success) {
            echo "<script>window.location.href='?page=services&status=saved';</script>";
            exit;
        }
    }
}

// Handle Success Messages from Redirects
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'saved') {
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Catalog Synchronized:</b> Service parameters updated.</div>';
    } elseif ($_GET['status'] === 'deleted') {
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-archive me-2"></i><b>Asset Archived:</b> Service entry removed from registry.</div>';
    }
}

// 3. Fetch Service for Editing
$edit_service = null;
if (($action === 'add' || $action === 'edit') && $id) {
    $stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $edit_service = $stmt->fetch();
}

// 4. Fetch All Services for Listing
$services = $db->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC")->fetchAll();
?>

<div class="animate-fade-in">
    <!-- Sophisticated Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-4">
                    <div class="stat-icon bg-info bg-opacity-10 text-info shadow-sm" style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-cubes-stacked"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Service Architect</h4>
                        <p class="text-muted small mb-0 font-monospace extra-small">Registry: core_offerings_v3</p>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <?php if ($action === 'list'): ?>
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2 border small fw-bold d-none d-md-inline-block">
                            <i class="fas fa-list-check me-1"></i> Total: <?php echo count($services); ?>
                        </span>
                        <a href="?page=services&action=add" class="btn btn-primary btn-gradient rounded-pill px-4 fw-bold shadow-lg border-0 transition-all">
                            <i class="fas fa-plus me-2"></i>Assemble New Service
                        </a>
                    <?php else: ?>
                        <a href="?page=services" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border transition-all">
                            <i class="fas fa-chevron-left me-2"></i>Return to Registry
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <?php if ($action === 'list'): ?>
        <!-- ================= CATALOGUE LISTING ================= -->
        <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden mb-5">
            <div class="card-header bg-dark text-white p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center border-0 gap-3">
                <h6 class="fw-bold mb-0 uppercase-tracking small text-center text-md-start">Active Service Inventory</h6>
                <div class="d-flex gap-2 w-100 w-md-auto">
                    <div class="input-group input-group-sm w-100" style="max-width: 320px;">
                        <span class="input-group-text bg-white bg-opacity-10 border-0 text-muted px-3"><i class="fas fa-search px-1"></i></span>
                        <input type="text" class="form-control border-0 bg-white bg-opacity-10 text-white placeholder-muted py-2 small" placeholder="Query inventory..." id="catalogSearch">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 ps-md-4 py-3 py-md-4 border-0 extra-small uppercase-tracking text-primary">Service Identity</th>
                            <th class="py-3 py-md-4 border-0 extra-small uppercase-tracking text-primary text-center d-none d-sm-table-cell">Rank</th>
                            <th class="py-3 py-md-4 border-0 extra-small uppercase-tracking text-primary d-none d-lg-table-cell">Engagement Snippet</th>
                            <th class="pe-3 pe-md-4 py-3 py-md-4 border-0 extra-small uppercase-tracking text-primary text-end">Operations</th>
                        </tr>
                    </thead>
                    <tbody id="catalogTable">
                        <?php foreach ($services as $s): ?>
                        <tr class="transition-all hover-bg-light">
                            <td class="ps-3 ps-md-4 py-3 py-md-4">
                                <div class="d-flex align-items-center gap-2 gap-md-4">
                                    <div class="position-relative d-none d-sm-block">
                                        <?php 
                                            $sImg = $s['image'] ?: 'img/service-1.jpg';
                                            $sDisplayUrl = (strpos($sImg, 'uploads/') === 0) ? $sImg : '../' . $sImg;
                                        ?>
                                        <div class="rounded-4 overflow-hidden shadow-sm border border-2 border-white" style="width: 75px; height: 65px;">
                                            <img src="<?php echo $sDisplayUrl; ?>" class="w-100 h-100 object-fit-cover shadow-inner">
                                        </div>
                                        <span class="position-absolute top-100 start-100 translate-middle badge rounded-pill bg-dark text-white border-0 extra-small shadow fw-normal" style="font-size: 0.55rem; padding: 0.2rem 0.4rem;">ID:<?php echo $s['id']; ?></span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-1 fs-6"><?php echo htmlspecialchars($s['title']); ?></div>
                                        <div class="text-muted extra-small font-monospace opacity-75 d-flex align-items-center">
                                            <i class="fas fa-fingerprint me-1"></i>#<?php echo $s['id']; ?>
                                            <span class="d-sm-none ms-2 badge bg-light text-dark border">Rank: <?php echo $s['sort_order']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center d-none d-sm-table-cell">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-3 fw-bold small shadow-inner">
                                    <?php echo $s['sort_order']; ?>
                                </span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <div class="text-muted small text-truncate-2" style="max-width: 350px; line-height: 1.4;">
                                    <?php echo htmlspecialchars($s['description']); ?>
                                </div>
                            </td>
                            <td class="pe-3 pe-md-4 text-end">
                                <div class="d-flex justify-content-end gap-1 gap-md-2">
                                    <a href="../service-details.php?id=<?php echo $s['id']; ?>" target="_blank" class="btn btn-sm bg-white shadow-sm border rounded-circle p-2 hover-scale" title="Visual Preview">
                                        <i class="fas fa-arrow-up-right-from-square text-info small fa-fw"></i>
                                    </a>
                                    <a href="?page=services&action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm bg-white shadow-sm border rounded-circle p-2 hover-scale" title="Configure Core">
                                        <i class="fas fa-sliders text-primary small fa-fw"></i>
                                    </a>
                                    <a href="?page=services&action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm bg-white shadow-sm border rounded-circle p-2 hover-scale" title="Offload Asset" onclick="return confirm('Archive this service asset?')">
                                        <i class="fas fa-trash-can text-danger small fa-fw"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="p-5">
                                    <div class="stat-icon bg-light text-muted mx-auto mb-4" style="width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                        <i class="fas fa-layer-group opacity-25"></i>
                                    </div>
                                    <h5 class="text-muted fw-bold">Inventory Empty</h5>
                                    <p class="extra-small text-muted uppercase-tracking">No service metadata registered.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Marketing Intel Alert -->
        <div class="card border-0 bg-primary bg-opacity-10 p-4 rounded-4 rounded-md-5 shadow-sm mb-5">
            <div class="d-flex align-items-center gap-3 gap-md-4">
                <div class="stat-icon bg-white text-primary shadow-sm d-none d-sm-flex" style="width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-primary">Marketing Intelligence</h6>
                    <p class="small mb-0 opacity-75">Prioritize high-margin services at Rank 1. The top three services receive 80% of customer visual focus.</p>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ================= SERVICE CONFIGURATOR ================= -->
        <div class="row g-4 g-lg-5">
            <!-- Core Logic Container -->
            <div class="col-lg-8 order-2 order-lg-1">
                <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden mb-5">
                    <div class="card-header bg-dark text-white p-4 border-0 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 uppercase-tracking small">Identity Specifications</h6>
                        <i class="fas fa-pen-field text-muted"></i>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block text-uppercase">MASTER SERVICE TITLE</label>
                            <input type="text" name="title" form="editorForm" class="form-control border-0 bg-light py-3 rounded-3 fw-bold fs-5 shadow-sm" placeholder="Public Service Designation" value="<?php echo htmlspecialchars($edit_service['title'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block text-uppercase">CONCIERGE SUMMARY (SNIPPET)</label>
                            <textarea name="description" form="editorForm" class="form-control border-0 bg-light p-4 rounded-3 shadow-sm" rows="3" placeholder="Compelling 2-sentence hook for catalogue cards..." required><?php echo htmlspecialchars($edit_service['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-0 pt-4 border-top">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-4 d-block text-uppercase"><i class="fas fa-file-lines me-2"></i>DEEP-DIVE TECHNICAL CONTENT</label>
                            <textarea name="content" form="editorForm" class="form-control summernote"><?php echo $edit_service['content'] ?? ''; ?></textarea>
                            
                            <div class="mt-5 p-4 bg-light rounded-4 border-start border-4 border-primary shadow-sm d-none d-md-block">
                                <div class="d-flex gap-3">
                                    <i class="fas fa-wand-magic-sparkles text-primary fs-4 mt-1"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Professional Content Canvas</h6>
                                        <p class="small text-muted mb-0">Use this workspace to define detailed technical specs, use-cases, or value-add tables. This content populates the full-page service view.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Control Sidebar -->
            <div class="col-lg-4 order-1 order-lg-2">
                <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0 uppercase-tracking small text-muted text-uppercase">Asset Configuration</h6>
                    </div>
                    <div class="card-body p-4 p-md-5 bg-light bg-opacity-50">
                        <form method="POST" enctype="multipart/form-data" id="editorForm">
                            <?php if ($edit_service): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_service['id']; ?>">
                                <input type="hidden" name="existing_image" value="<?php echo $edit_service['image']; ?>">
                            <?php endif; ?>

                            <div class="mb-4 mb-md-5">
                                <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block text-uppercase">SORT PRIORITY</label>
                                <input type="number" name="sort_order" class="form-control border-0 shadow-sm py-3 rounded-3 text-center fw-bold" value="<?php echo $edit_service['sort_order'] ?? '0'; ?>" placeholder="0">
                            </div>

                            <div class="mb-4 mb-md-5">
                                <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block text-uppercase">HERO VISUAL (MASTER)</label>
                                <div id="preview-container" class="mb-4 text-center p-3 rounded-4 bg-white border border-dashed border-2 text-muted shadow-sm transition-all" style="min-height: 180px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <?php if ($edit_service && $edit_service['image']): ?>
                                        <?php 
                                            $eImg = $edit_service['image'];
                                            $eDisplayUrl = (strpos($eImg, 'uploads/') === 0) ? $eImg : '../' . $eImg;
                                        ?>
                                        <img src="<?php echo $eDisplayUrl; ?>" class="img-fluid rounded-4 shadow-sm mb-3" id="preview-img" style="max-height: 200px; object-fit: contain;">
                                        <p class="extra-small mb-0 uppercase-tracking opacity-50 fw-bold text-success">Asset Linked</p>
                                    <?php else: ?>
                                        <i class="fas fa-file-image fa-3x mb-3 opacity-25"></i>
                                        <p class="extra-small mb-0 uppercase-tracking opacity-50 fw-bold">No Image Uploaded</p>
                                    <?php endif; ?>
                                </div>
                                <div class="d-grid shadow-sm rounded-pill overflow-hidden">
                                    <input type="file" name="service_image" id="file-input" class="form-control border-0 rounded-pill py-2 small" accept="image/*">
                                </div>
                            </div>

                            <hr class="my-4 my-md-5 opacity-25">

                            <div class="d-grid gap-3">
                                <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-pill fw-bold shadow-lg border-0 transition-all">
                                    <i class="fas fa-shield-check me-2"></i> <?php echo $edit_service ? 'Authorize Update' : 'Finalize Assembly'; ?>
                                </button>
                                <a href="?page=services" class="btn btn-light w-100 py-2 rounded-pill text-muted extra-small uppercase-tracking fw-bold">Abort Changes</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4 mb-4 d-none d-lg-block">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-globe-americas fa-2x opacity-25"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Global Deployment</h6>
                            <p class="extra-small mb-0 opacity-50 uppercase-tracking">Changes push to main grid and navigation foundation instantly.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Asset Selection Hook
    document.getElementById('file-input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('preview-container');
                container.innerHTML = `
                    <img src="${e.target.result}" class="img-fluid rounded-4 shadow-sm mb-3" id="preview-img" style="max-height: 250px; object-fit: contain;">
                    <p class="extra-small mb-0 text-success fw-bold uppercase-tracking text-uppercase"><i class="fas fa-circle-check me-1"></i> New Physical Asset Selected</p>
                `;
                container.classList.add('animate-pulse');
                setTimeout(() => container.classList.remove('animate-pulse'), 1000);
            };
            reader.readAsDataURL(file);
        }
    });

    // Catalog Search
    document.getElementById('catalogSearch')?.addEventListener('keyup', function(e) {
        let val = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('#catalogTable tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    });
</script>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.7rem; }
    .extra-small { font-size: 0.65rem; }
    .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .hover-bg-light:hover { background: #f8fafc; }
    .hover-scale:hover { transform: scale(1.1); }
    .placeholder-muted::placeholder { color: rgba(255,255,255,0.3) !important; }
    .animate-pulse { animation: pulse 1s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .7; } }
    .btn-gradient { background: var(--primary-gradient); border: none; }
    .object-fit-cover { object-fit: cover; }
    .uppercase-tracking { letter-spacing: 1.5px; }
</style>
