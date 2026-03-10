<?php
/**
 * Professional Services Catalog Manager - Listing First Approach
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
            $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="fas fa-exclamation-circle me-2"></i>' . $result['error'] . '</div>';
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
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Catalog entry updated successfully!</div>';
    } elseif ($_GET['status'] === 'deleted') {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Service entry archived.</div>';
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
    <!-- Breadcrumb & Title Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-white p-4 rounded-4 shadow-sm border-0">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1">
                                <li class="breadcrumb-item"><a href="?page=dashboard" class="text-muted text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Service Catalog</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">
                            <?php 
                                if($action === 'add') echo 'New Service Creation';
                                elseif($action === 'edit') echo 'Service Configuration';
                                else echo 'Catalog Listing';
                            ?>
                        </h3>
                    </div>
                    <div>
                        <?php if ($action === 'list'): ?>
                            <a href="?page=services&action=add" class="btn btn-primary btn-gradient rounded-pill px-4 fw-bold shadow">
                                <i class="fas fa-plus me-2"></i>Create New Entry
                            </a>
                        <?php else: ?>
                            <a href="?page=services" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border">
                                <i class="fas fa-arrow-left me-2"></i>Back to Catalog
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <?php if ($action === 'list'): ?>
        <!-- ================= LISTING VIEW ================= -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Total Active Services <span class="badge bg-primary rounded-pill ms-2 fw-normal fs-6"><?php echo count($services); ?></span></h5>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-light bg-light" placeholder="Search catalog..." id="catalogSearch">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 small text-uppercase fw-bold text-muted">Display & Title</th>
                            <th class="py-3 border-0 small text-uppercase fw-bold text-muted text-center">In-Menu Rank</th>
                            <th class="py-3 border-0 small text-uppercase fw-bold text-muted">Quick Summary</th>
                            <th class="pe-4 py-3 border-0 small text-uppercase fw-bold text-muted text-end">Management</th>
                        </tr>
                    </thead>
                    <tbody id="catalogTable">
                        <?php foreach ($services as $s): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <?php 
                                            $sImg = $s['image'] ?: 'img/service-1.jpg';
                                            $sDisplayUrl = (strpos($sImg, 'uploads/') === 0) ? $sImg : '../' . $sImg;
                                        ?>
                                        <img src="<?php echo $sDisplayUrl; ?>" class="rounded-3 shadow-sm" style="width: 65px; height: 60px; object-fit: cover; border: 2px solid white;">
                                        <span class="position-absolute top-100 start-100 translate-middle badge rounded-pill bg-white text-dark border extra-small shadow-sm">ID:<?php echo $s['id']; ?></span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($s['title']); ?></div>
                                        <div class="text-primary extra-small fw-bold"><i class="fas fa-link me-1"></i>/service-details.php?id=<?php echo $s['id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="bg-light p-2 rounded-3 border d-inline-block fw-bold text-muted" style="min-width: 50px;">
                                    <?php echo $s['sort_order']; ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small text-truncate" style="max-width: 350px;">
                                    <?php echo htmlspecialchars($s['description']); ?>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="../service-details.php?id=<?php echo $s['id']; ?>" target="_blank" class="btn btn-sm btn-white border px-3 rounded-pill shadow-sm" title="Preview Live">
                                        <i class="fas fa-eye text-info"></i>
                                    </a>
                                    <a href="?page=services&action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-white border px-3 rounded-pill shadow-sm" title="Edit Properties">
                                        <i class="fas fa-cog text-primary"></i> Manage
                                    </a>
                                    <a href="?page=services&action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-white border px-3 rounded-pill shadow-sm" title="Archive" onclick="return confirm('Archive this service forever?')">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="p-5">
                                    <i class="fas fa-layer-group fa-4x text-light mb-3"></i>
                                    <h4 class="text-muted fw-bold">No Service Metadata Found</h4>
                                    <p class="text-muted">Start building your showroom by clicking 'Create New Entry'.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-primary border-0 rounded-4 shadow-sm p-4 text-center mb-5">
            <p class="mb-0 fs-6 fw-bold"><i class="fas fa-star text-warning me-2"></i>Marketing Pro Tip: Keep your most specialized services at Rank 1 to drive high-value conversions!</p>
        </div>

    <?php else: ?>
        <!-- ================= EDITOR VIEW (ADD/EDIT) ================= -->
        <div class="row g-4">
            <!-- Sidebar (Visuals & Settings) -->
            <div class="col-lg-4 order-lg-2">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0 uppercase-tracking"><i class="fas fa-id-card-alt text-primary me-2"></i>Quick Settings</h6>
                    </div>
                    <div class="card-body p-4 bg-light bg-opacity-50">
                        <form method="POST" enctype="multipart/form-data" id="editorForm">
                            <?php if ($edit_service): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_service['id']; ?>">
                                <input type="hidden" name="existing_image" value="<?php echo $edit_service['image']; ?>">
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Priority (Sort Order)</label>
                                <input type="number" name="sort_order" class="form-control form-control-lg border-2 shadow-sm rounded-3" value="<?php echo $edit_service['sort_order'] ?? '0'; ?>" placeholder="0">
                                <div class="form-text mt-1 extra-small">Lower numbers (e.g., 1) appear first.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Service Visual (Image)</label>
                                <div id="preview-container" class="mb-3 text-center p-3 rounded-4 bg-white border border-dashed border-2 text-muted" style="min-height: 200px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <?php if ($edit_service && $edit_service['image']): ?>
                                        <?php 
                                            $eImg = $edit_service['image'];
                                            $eDisplayUrl = (strpos($eImg, 'uploads/') === 0) ? $eImg : '../' . $eImg;
                                        ?>
                                        <img src="<?php echo $eDisplayUrl; ?>" class="img-fluid rounded-3 shadow mb-3" id="preview-img">
                                        <p class="small mb-0 opacity-50 fst-italic">Currently Uploaded</p>
                                    <?php else: ?>
                                        <i class="fas fa-image fa-3x mb-3 opacity-25"></i>
                                        <p class="small mb-0 opacity-50">Recommended: 800x600px</p>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="service_image" id="file-input" class="form-control rounded-3 border-light shadow-sm" accept="image/*">
                            </div>

                            <hr class="my-4 opacity-50">

                            <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-4 fw-bold shadow">
                                <i class="fas fa-save me-2"></i> <?php echo $edit_service ? 'Confirm Update' : 'Finalize Service'; ?>
                            </button>
                            <a href="?page=services" class="btn btn-light w-100 py-2 mt-2 rounded-4 text-muted small">Discard Changes</a>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 text-white bg-dark p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-compass me-2"></i>Site Location</h6>
                    <p class="small opacity-75 mb-0">Changes saved here will be immediately reflected in your website's main service section and the footer links.</p>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-8 order-lg-1">
                <div class="card border-0 shadow-sm rounded-4 p-0 overflow-hidden mb-5">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold mb-0 text-primary">Service Core Specifications</h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- We link the fields to the form ID in the sidebar -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Official Service Title</label>
                            <input type="text" name="title" form="editorForm" class="form-control form-control-lg border-2 shadow-sm rounded-3 fw-bold" placeholder="How should clients see this service's name?" value="<?php echo $edit_service['title'] ?? ''; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Summary (Short Snippet)</label>
                            <textarea name="description" form="editorForm" class="form-control border-2 shadow-sm rounded-3" rows="3" placeholder="A 2-3 sentence overview for the cards..." required><?php echo $edit_service['description'] ?? ''; ?></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted text-uppercase">In-Depth Professional Content</label>
                            <textarea name="content" form="editorForm" class="form-control summernote"><?php echo $edit_service['content'] ?? ''; ?></textarea>
                            <div class="alert alert-light border shadow-sm p-3 mt-4 rounded-4 small">
                                <i class="fas fa-magic text-warning me-2"></i> This is where you can write tables, add lists, and use full formatting. This creates the "WordPress-style" full page for your clients!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Search Filtering
    document.getElementById('catalogSearch')?.addEventListener('keyup', function(e) {
        let value = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('#catalogTable tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // File Preview
    document.getElementById('file-input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-container').innerHTML = `
                    <img src="${e.target.result}" class="img-fluid rounded-3 shadow mb-3" id="preview-img">
                    <p class="small mb-0 text-success fw-bold"><i class="fas fa-check-circle me-1"></i> New Image Selected</p>
                `;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem; }
    .animate-fade-in { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .extra-small { font-size: 0.7rem; }
    .btn-white { background: white; color: #1e293b; transition: all 0.2s; }
    .btn-white:hover { border-color: #cbd5e1 !important; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #e2e8f0 !important; }
    .btn-gradient { background: var(--primary-gradient); border: none; }
</style>
