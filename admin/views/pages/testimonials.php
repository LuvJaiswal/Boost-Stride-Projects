<?php
/**
 * Testimonials (Customer Reviews) Manager - Professional Overhaul
 */

use Core\Database;
use Core\Uploader;

$db = Database::getInstance();
$message = '';

// 1. Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        echo "<script>window.location.href='?page=testimonials';</script>";
        exit;
    }
}

// 2. Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $profession = $_POST['profession'];
    $text = $_POST['text'];
    $id = $_POST['id'] ?? null;
    $existing_image = $_POST['existing_image'] ?? 'img/testimonial-1.jpg';

    // Image Upload Logic
    $image_path = $existing_image;
    if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] === UPLOAD_ERR_OK) {
        $result = Uploader::upload($_FILES['client_image'], UPLOAD_DIR);
        if (isset($result['success'])) {
            $image_path = $result['path'];
        } else {
            $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="fas fa-exclamation-circle me-2"></i>' . $result['error'] . '</div>';
        }
    }

    if (empty($message)) {
        if ($id) {
            // Update
            $stmt = $db->prepare("UPDATE testimonials SET name = ?, profession = ?, text = ?, image = ? WHERE id = ?");
            $success = $stmt->execute([$name, $profession, $text, $image_path, $id]);
        } else {
            // Insert
            $stmt = $db->prepare("INSERT INTO testimonials (name, profession, text, image) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([$name, $profession, $text, $image_path]);
        }

        if ($success) {
            $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Review published successfully!</div>';
            echo "<script>setTimeout(() => { window.location.href='?page=testimonials'; }, 1500);</script>";
        }
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
$testimonials = $db->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
?>

<div class="animate-fade-in">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12 d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border-0">
            <div>
                <h3 class="fw-bold mb-1">Customer Reviews</h3>
                <p class="text-muted small mb-0">Manage and exhibit the praise from your happy automotive clients.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="?page=testimonials" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border">
                    <i class="fas fa-plus me-2"></i>New Review
                </a>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row g-4">
        <!-- Editor Side -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-0 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas <?php echo $edit_item ? 'fa-user-edit' : 'fa-feather-alt'; ?> text-success me-2"></i>
                        <?php echo $edit_item ? 'Edit Client Review' : 'Add New Review'; ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data" id="testimonial-form">
                        <?php if ($edit_item): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                            <input type="hidden" name="existing_image" value="<?php echo $edit_item['image']; ?>">
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php 
                                    $imgUrl = $edit_item['image'] ?? 'img/testimonial-1.jpg';
                                    $displayUrl = (strpos($imgUrl, 'uploads/') === 0) ? $imgUrl : '../' . $imgUrl;
                                ?>
                                <img src="<?php echo $displayUrl; ?>" 
                                     id="preview-avatar" 
                                     class="rounded-circle shadow-sm border border-4 border-white" 
                                     style="width: 120px; height: 120px; object-fit: cover;">
                                <label for="client_image" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" name="client_image" id="client_image" class="d-none" accept="image/*">
                            </div>
                            <p class="small text-muted mt-2">Client Avatar (Optional)</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Client Name</label>
                            <input type="text" name="name" class="form-control rounded-3 border-light bg-light bg-opacity-50" 
                                   placeholder="e.g. Michael Smith" value="<?php echo $edit_item['name'] ?? ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Profession / Location</label>
                            <input type="text" name="profession" class="form-control rounded-3 border-light bg-light bg-opacity-50" 
                                   placeholder="e.g. Local Business Owner" value="<?php echo $edit_item['profession'] ?? ''; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Review Quote</label>
                            <textarea name="text" class="form-control rounded-3 border-light bg-light bg-opacity-50" rows="5" 
                                      placeholder="Paste the customer feedback here..." required><?php echo $edit_item['text'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success py-3 rounded-pill fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> <?php echo $edit_item ? 'Update Review' : 'Publish to Site'; ?>
                            </button>
                            <?php if ($edit_item): ?>
                                <a href="?page=testimonials" class="btn btn-light rounded-pill py-2 text-muted">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Feedback Wall -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-quote-left text-primary me-2"></i>Feedback Wall</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 700px; overflow-y: auto;">
                        <?php if (empty($testimonials)): ?>
                            <div class="p-5 text-center text-muted">
                                <i class="fas fa-comment-slash fa-3x mb-3 opacity-25"></i>
                                <p>No reviews have been published yet.</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach ($testimonials as $t): ?>
                        <div class="list-group-item p-4 border-light hover-bg-light transition-all shadow-hover-sm">
                            <div class="d-flex gap-4">
                                <?php 
                                    $listImg = $t['image'] ?: 'img/testimonial-1.jpg';
                                    $listDisplayUrl = (strpos($listImg, 'uploads/') === 0) ? $listImg : '../' . $listImg;
                                ?>
                                <img src="<?php echo $listDisplayUrl; ?>" class="rounded-circle shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($t['name']); ?></h6>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 extra-small mt-1">
                                                <?php echo htmlspecialchars($t['profession']); ?>
                                            </span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="?page=testimonials&action=edit&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-white border rounded-circle p-2 shadow-sm" title="Edit">
                                                <i class="fas fa-pen text-primary fa-fw"></i>
                                            </a>
                                            <a href="?page=testimonials&action=delete&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-white border rounded-circle p-2 shadow-sm" title="Delete" onclick="return confirm('Remove this client review permanently?')">
                                                <i class="fas fa-trash text-danger fa-fw"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-muted small fst-italic position-relative">
                                        <i class="fas fa-quote-left opacity-25 me-1"></i>
                                        <?php echo htmlspecialchars($t['text']); ?>
                                        <i class="fas fa-quote-right opacity-25 ms-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 alert alert-light border-0 shadow-sm rounded-4 p-4 text-center">
                <h6 class="fw-bold"><i class="fas fa-info-circle text-info me-2"></i>Marketing Tip</h6>
                <p class="small text-muted mb-0">Positive reviews increase conversion rates by up to 270%. Make sure to highlight your best customer feedback!</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Avatar Preview Logic
    document.getElementById('client_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-avatar').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Fade-in animation helper
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('.animate-fade-in').style.opacity = '1';
    });
</script>

<style>
    .animate-fade-in { opacity: 0; transition: opacity 0.5s ease-in-out; }
    .extra-small { font-size: 0.65rem; font-weight: 700; text-uppercase; }
    .transition-all { transition: all 0.2s ease-in-out; }
    .hover-bg-light:hover { background: #f8fafc; cursor: default; }
    .shadow-hover-sm:hover { position: relative; z-index: 1; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .btn-white { background: white; color: #1e293b; }
</style>
