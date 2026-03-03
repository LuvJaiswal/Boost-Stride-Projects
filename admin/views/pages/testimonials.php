<?php
/**
 * Testimonials Manager - CRUD
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

// 1. Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Testimonial deleted successfully!</div>';
    }
}

// 2. Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $profession = $_POST['profession'];
    $text = $_POST['text'];
    $image = $_POST['image'];
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Update
        $stmt = $db->prepare("UPDATE testimonials SET name = ?, profession = ?, text = ?, image = ? WHERE id = ?");
        $success = $stmt->execute([$name, $profession, $text, $image, $id]);
    } else {
        // Insert
        $stmt = $db->prepare("INSERT INTO testimonials (name, profession, text, image) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$name, $profession, $text, $image]);
    }

    if ($success) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Testimonial saved successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="fas fa-exclamation-circle me-2"></i>Error saving testimonial.</div>';
    }
}

// 3. Fetch for Editing
$edit_item = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_item = $stmt->fetch();
}

// 4. Fetch All
$stmt = $db->query("SELECT * FROM testimonials ORDER BY id DESC");
$testimonials = $stmt->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">Customer Reviews</h3>
        <p class="text-muted small mb-0">Showcase social proof and client feedback on your hero section.</p>
    </div>
    <a href="?page=testimonials" class="btn btn-gradient shadow-sm">
        <i class="fas fa-plus me-2"></i>New Testimonial
    </a>
</div>

<?php echo $message; ?>

<div class="row g-4">
    <!-- Form Side -->
    <div class="col-12 col-xl-4">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-4">
                <i class="fas <?php echo $edit_item ? 'fa-user-edit' : 'fa-user-plus'; ?> text-success me-2"></i>
                <?php echo $edit_item ? 'Modify' : 'Register'; ?> Feedback
            </h5>
            <form method="POST">
                <?php if ($edit_item): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-600 small">Client Full Name</label>
                    <input type="text" name="name" class="form-control rounded-3" placeholder="John Doe" value="<?php echo $edit_item['name'] ?? ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small">Profession / Title</label>
                    <input type="text" name="profession" class="form-control rounded-3" value="<?php echo $edit_item['profession'] ?? ''; ?>" placeholder="e.g. CEO, Happy Customer" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small">Testimonial Content</label>
                    <textarea name="text" class="form-control rounded-3" rows="5" placeholder="What did they say about your shop?" required><?php echo $edit_item['text'] ?? ''; ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600 small">Avatar URL</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-portrait text-muted"></i></span>
                        <input type="text" name="image" class="form-control rounded-end-3" value="<?php echo $edit_item['image'] ?? 'img/testimonial-1.jpg'; ?>" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success py-2 rounded-3 fw-bold">
                        <i class="fas fa-check me-2"></i><?php echo $edit_item ? 'Update Testimonial' : 'Publish Review'; ?>
                    </button>
                    <?php if ($edit_item): ?>
                        <a href="?page=testimonials" class="btn btn-light py-2 rounded-3 text-muted">Discard Edit</a>
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
                            <th class="ps-4 py-3 border-0 small text-uppercase fw-bold text-muted">Customer</th>
                            <th class="py-3 border-0 small text-uppercase fw-bold text-muted">Message Snippet</th>
                            <th class="pe-4 py-3 border-0 small text-uppercase fw-bold text-muted text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $t): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="../<?php echo $t['image']; ?>" class="rounded-circle shadow-sm border border-2 border-white" style="width: 48px; height: 48px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($t['name']); ?></div>
                                        <div class="text-primary small fw-500"><?php echo htmlspecialchars($t['profession']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-muted small mb-0 italic" style="max-width: 350px;">
                                    "<?php echo substr(htmlspecialchars($t['text']), 0, 80); ?>..."
                                </p>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <a href="?page=testimonials&action=edit&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-white border px-3 rounded-pill me-2">
                                        <i class="fas fa-pen-nib text-primary"></i>
                                    </a>
                                    <a href="?page=testimonials&action=delete&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-white border px-3 rounded-pill" onclick="return confirm('Remove this testimonial?')">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($testimonials)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="fas fa-comments text-muted opacity-25 fa-3x mb-3"></i>
                                <h6 class="text-muted fw-bold">No customer reviews yet.</h6>
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
    .fw-500 { font-weight: 500; }
    .italic { font-style: italic; }
</style>
