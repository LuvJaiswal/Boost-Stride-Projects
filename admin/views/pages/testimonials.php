<?php
/**
 * Testimonial & Social Proof Architect
 */

use Core\Database;
use Core\Uploader;

$db = Database::getInstance();
$message = '';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        echo "<script>window.location.href='?page=testimonials';</script>";
        exit;
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $profession = $_POST['profession'];
    $text = $_POST['text'];
    $rating = $_POST['rating'] ?? 5; // New attribute
    $id = $_POST['id'] ?? null;
    $existing_image = $_POST['existing_image'] ?? 'img/testimonial-1.jpg';

    $image_path = $existing_image;
    if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] === UPLOAD_ERR_OK) {
        $result = Uploader::upload($_FILES['client_image'], UPLOAD_DIR);
        if (isset($result['success'])) {
            $image_path = $result['path'];
        }
    }

    if ($id) {
        $stmt = $db->prepare("UPDATE testimonials SET name = ?, profession = ?, text = ?, image = ?, rating = ? WHERE id = ?");
        $success = $stmt->execute([$name, $profession, $text, $image_path, $rating, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO testimonials (name, profession, text, image, rating) VALUES (?, ?, ?, ?, ?)");
        $success = $stmt->execute([$name, $profession, $text, $image_path, $rating]);
    }

    if ($success) {
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Public Praise Synchronized:</b> Review has been deployed.</div>';
        echo "<script>setTimeout(() => { window.location.href='?page=testimonials'; }, 1500);</script>";
    }
}

// Fetch Data
$edit_item = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_item = $stmt->fetch();
}

$testimonials = $db->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
?>

<div class="animate-fade-in">
    <!-- Sophisticated Header -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 p-4 bg-white">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-4">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning shadow-sm" style="width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-stars"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Social Proof Manager</h4>
                    <p class="text-muted small mb-0 font-monospace extra-small">Registry: client_endorsements_v2</p>
                </div>
            </div>
            <a href="?page=testimonials" class="btn btn-light rounded-pill px-4 fw-bold border shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Add Original Review
            </a>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row g-5">
        <!-- Editor Canvas -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden">
                <div class="card-header bg-dark text-white p-4 border-0 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 uppercase-tracking small"><?php echo $edit_item ? 'Modify Endorsement' : 'Draft New Endorsement'; ?></h6>
                    <i class="fas fa-pen-nib text-muted"></i>
                </div>
                <div class="card-body p-5">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($edit_item): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                            <input type="hidden" name="existing_image" value="<?php echo $edit_item['image']; ?>">
                        <?php endif; ?>

                        <div class="text-center mb-5">
                            <div class="position-relative d-inline-block">
                                <?php 
                                    $imgUrl = $edit_item['image'] ?? 'img/testimonial-1.jpg';
                                    $displayUrl = (strpos($imgUrl, 'uploads/') === 0) ? $imgUrl : '../' . $imgUrl;
                                ?>
                                <div class="rounded-circle shadow-lg border border-4 border-white overflow-hidden" style="width: 140px; height: 140px;">
                                    <img src="<?php echo $displayUrl; ?>" id="preview-avatar" class="w-100 h-100 object-fit-cover shadow-inner">
                                </div>
                                <label for="client_image" class="btn btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow-lg border-white border-2">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" name="client_image" id="client_image" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">CLIENT IDENTITY</label>
                            <input type="text" name="name" class="form-control border-0 bg-light py-3 rounded-3 fw-bold" placeholder="Full Name" value="<?php echo $edit_item['name'] ?? ''; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">PROFESSIONAL CONTEXT</label>
                            <input type="text" name="profession" class="form-control border-0 bg-light py-3 rounded-3" placeholder="e.g. CEO of Prime Motors" value="<?php echo $edit_item['profession'] ?? ''; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">EXPERIENCE RATING</label>
                            <select name="rating" class="form-select border-0 bg-light py-3 rounded-3 fw-bold">
                                <?php for($i=5; $i>=1; $i--): ?>
                                    <option value="<?php echo $i; ?>" <?php echo (isset($edit_item['rating']) && $edit_item['rating'] == $i) ? 'selected' : ''; ?>>
                                        <?php echo str_repeat('★', $i) . str_repeat('☆', 5-$i); ?> (<?php echo $i; ?> Stars)
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">CORE TESTIMONY</label>
                            <textarea name="text" class="form-control border-0 bg-light p-4 rounded-3" rows="6" placeholder="Paste the customer's raw feedback..." required style="line-height: 1.7;"><?php echo $edit_item['text'] ?? ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-pill shadow-lg fw-bold border-0">
                            <i class="fas fa-cloud-upload-alt me-2"></i>Deploy Social Proof
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Feedback Wall -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 uppercase-tracking small text-muted">Active Endorsements</h6>
                    <span class="badge bg-light text-dark rounded-pill px-3 shadow-none border small fw-bold"><?php echo count($testimonials); ?> Records</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 800px; overflow-y: auto;">
                        <?php if (empty($testimonials)): ?>
                            <div class="p-5 text-center text-muted opacity-50">
                                <i class="fas fa-comment-slash fa-4x mb-4 d-block"></i>
                                <p class="fw-bold">No social proof data available.</p>
                                <p class="small">Start building trust by adding your first client review.</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach ($testimonials as $t): ?>
                        <div class="list-group-item p-5 border-light hover-bg-light transition-all">
                            <div class="d-flex gap-4">
                                <?php 
                                    $listImg = $t['image'] ?: 'img/testimonial-1.jpg';
                                    $listDisplayUrl = (strpos($listImg, 'uploads/') === 0) ? $listImg : '../' . $listImg;
                                ?>
                                <div class="flex-shrink-0" style="width: 80px; height: 80px;">
                                    <img src="<?php echo $listDisplayUrl; ?>" class="w-100 h-100 object-fit-cover rounded-4 shadow">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="text-warning small mb-1">
                                                <?php for($i=1; $i<=5; $i++) echo $i <= ($t['rating'] ?? 5) ? '★' : '☆'; ?>
                                            </div>
                                            <h5 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($t['name']); ?></h5>
                                            <p class="text-muted extra-small uppercase-tracking mt-1 mb-0"><?php echo htmlspecialchars($t['profession']); ?></p>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="?page=testimonials&action=edit&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-white border rounded-pill px-3 py-2 shadow-sm transition-all hover-translate-y">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>
                                            <a href="?page=testimonials&action=delete&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-white border rounded-pill px-3 py-2 shadow-sm transition-all hover-translate-y" onclick="return confirm('Archive this endorsement?')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mt-4 p-4 bg-light rounded-4 border-start border-4 border-primary position-relative shadow-inner">
                                        <i class="fas fa-quote-left text-primary opacity-25 position-absolute top-0 start-0 m-3" style="font-size: 1.5rem;"></i>
                                        <div class="text-muted small ps-3 fst-italic"><?php echo htmlspecialchars($t['text']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 p-4 bg-indigo-soft text-indigo rounded-4 border-0 shadow-sm d-flex align-items-center gap-4" style="background: #eef2ff; color: #4f46e5;">
                <div class="stat-icon bg-white text-indigo shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Conversion Catalyst</h6>
                    <p class="small mb-0 opacity-75">Social proof can increase booking conversion rates by up to 34%.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 2px; }
    .extra-small { font-size: 0.65rem; }
    .transition-all { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .hover-bg-light:hover { background: #f8fafc; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
    .btn-white { background: white; color: #1e293b; }
    .hover-translate-y:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -5px rgba(0,0,0,0.1) !important; }
</style>

<script>
    document.getElementById('client_image')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-avatar').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
