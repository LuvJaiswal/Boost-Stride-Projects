<?php
/**
 * Services Manager - CRUD
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

// 1. Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Service deleted successfully!</div>';
    }
}

// 2. Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_POST['image'];
    $sort_order = (int)$_POST['sort_order'];
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Update
        $stmt = $db->prepare("UPDATE services SET title = ?, description = ?, image = ?, sort_order = ? WHERE id = ?");
        $success = $stmt->execute([$title, $description, $image, $sort_order, $id]);
    } else {
        // Insert
        $stmt = $db->prepare("INSERT INTO services (title, description, image, sort_order) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$title, $description, $image, $sort_order]);
    }

    if ($success) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Service saved successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="fas fa-exclamation-circle me-2"></i>Error saving service.</div>';
    }
}

// 3. Fetch Service for Editing
$edit_service = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_service = $stmt->fetch();
}

// 4. Fetch All Services
$stmt = $db->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC");
$services = $stmt->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">Service Catalog</h3>
        <p class="text-muted small mb-0">Manage the automotive services displayed on your website.</p>
    </div>
    <a href="?page=services" class="btn btn-gradient shadow-sm">
        <i class="fas fa-plus me-2"></i>Add New Service
    </a>
</div>

<?php echo $message; ?>

<div class="row g-4">
    <!-- Form Side -->
    <div class="col-12 col-xl-4">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-4">
                <i class="fas <?php echo $edit_service ? 'fa-edit' : 'fa-plus-circle'; ?> text-primary me-2"></i>
                <?php echo $edit_service ? 'Edit' : 'Create'; ?> Service
            </h5>
            <form method="POST">
                <?php if ($edit_service): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_service['id']; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-600 small">Service Title</label>
                    <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Engine Diagnostics" value="<?php echo $edit_service['title'] ?? ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small">Description</label>
                    <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Briefly describe this service..." required><?php echo $edit_service['description'] ?? ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small">Image Path</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-image text-muted"></i></span>
                        <input type="text" name="image" class="form-control rounded-end-3" value="<?php echo $edit_service['image'] ?? 'img/service-1.jpg'; ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600 small">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control rounded-3" value="<?php echo $edit_service['sort_order'] ?? '0'; ?>">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 rounded-3 fw-bold">
                        <?php echo $edit_service ? 'Update Changes' : 'Save Service'; ?>
                    </button>
                    <?php if ($edit_service): ?>
                        <a href="?page=services" class="btn btn-light py-2 rounded-3 text-muted">Cancel Editing</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- List Side -->
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 small text-uppercase fw-bold text-muted">Service</th>
                            <th class="py-3 border-0 small text-uppercase fw-bold text-muted text-center">Order</th>
                            <th class="pe-4 py-3 border-0 small text-uppercase fw-bold text-muted text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $s): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="../<?php echo $s['image']; ?>" class="rounded-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid white;">
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($s['title']); ?></div>
                                        <div class="text-muted small text-truncate" style="max-width: 300px;">
                                            <?php echo htmlspecialchars($s['description']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border p-2 rounded-pill" style="min-width: 40px;">
                                    <?php echo $s['sort_order']; ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <a href="?page=services&action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-white border shadow-sm px-3 rounded-pill me-2">
                                        <i class="fas fa-edit text-primary"></i>
                                    </a>
                                    <a href="?page=services&action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-white border shadow-sm px-3 rounded-pill" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="text-muted opacity-50 mb-3">
                                    <i class="fas fa-folder-open fa-3x"></i>
                                </div>
                                <h6 class="fw-bold">No services found</h6>
                                <p class="small text-muted">Start by adding your first service on the left.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-white { background: white; color: #1e293b; }
    .btn-white:hover { background: #f8fafc; }
    .fw-600 { font-weight: 600; }
</style>
