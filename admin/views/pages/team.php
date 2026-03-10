<?php
/**
 * Team Management - Professional Overhaul
 */

use Core\Database;
use Core\Uploader;

$db = Database::getInstance();
$message = '';

// 1. Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM team WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        echo "<script>window.location.href='?page=team';</script>";
        exit;
    }
}

// 2. Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $facebook_url = $_POST['facebook_url'];
    $twitter_url = $_POST['twitter_url'];
    $instagram_url = $_POST['instagram_url'];
    $sort_order = (int)$_POST['sort_order'];
    $id = $_POST['id'] ?? null;
    $existing_image = $_POST['existing_image'] ?? 'img/team-1.jpg';

    // Image Upload Logic
    $image_path = $existing_image;
    if (isset($_FILES['member_image']) && $_FILES['member_image']['error'] === UPLOAD_ERR_OK) {
        $result = Uploader::upload($_FILES['member_image'], UPLOAD_DIR);
        if (isset($result['success'])) {
            // $result['path'] is 'uploads/filename.jpg' (relative to admin folder)
            $image_path = $result['path'];
        } else {
            $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="fas fa-exclamation-circle me-2"></i>' . $result['error'] . '</div>';
        }
    }

    if (empty($message)) {
        if ($id) {
            // Update
            $sql = "UPDATE team SET name = ?, designation = ?, facebook_url = ?, twitter_url = ?, instagram_url = ?, image = ?, sort_order = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([$name, $designation, $facebook_url, $twitter_url, $instagram_url, $image_path, $sort_order, $id]);
        } else {
            // Insert
            $sql = "INSERT INTO team (name, designation, facebook_url, twitter_url, instagram_url, image, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([$name, $designation, $facebook_url, $twitter_url, $instagram_url, $image_path, $sort_order]);
        }

        if ($success) {
            $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Team member saved successfully!</div>';
            echo "<script>setTimeout(() => { window.location.href='?page=team'; }, 1500);</script>";
        }
    }
}

// 3. Fetch for Editing
$edit_item = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM team WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_item = $stmt->fetch();
}

// 4. Fetch All
$team = $db->query("SELECT * FROM team ORDER BY sort_order ASC, id DESC")->fetchAll();
?>

<div class="animate-fade-in">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12 d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border-0">
            <div>
                <h3 class="fw-bold mb-1">Our Expert Team</h3>
                <p class="text-muted small mb-0">Manage the professionals who drive your business forward.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="?page=team" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border">
                    <i class="fas fa-plus me-2"></i>Add Member
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
                        <i class="fas <?php echo $edit_item ? 'fa-user-edit' : 'fa-user-plus'; ?> text-primary me-2"></i>
                        <?php echo $edit_item ? 'Edit Team Member' : 'Add New Member'; ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data" id="team-form">
                        <?php if ($edit_item): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                            <input type="hidden" name="existing_image" value="<?php echo $edit_item['image']; ?>">
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php 
                                    $imgUrl = $edit_item['image'] ?? 'img/team-1.jpg';
                                    $displayUrl = (strpos($imgUrl, 'uploads/') === 0) ? $imgUrl : '../' . $imgUrl;
                                ?>
                                <img src="<?php echo $displayUrl; ?>" 
                                     id="preview-avatar" 
                                     class="rounded-circle shadow-sm border border-4 border-white" 
                                     style="width: 120px; height: 120px; object-fit: cover;">
                                <label for="member_image" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" name="member_image" id="member_image" class="d-none" accept="image/*">
                            </div>
                            <p class="small text-muted mt-2">Member Photo</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Full Name</label>
                            <input type="text" name="name" class="form-control rounded-3 border-light bg-light bg-opacity-50" 
                                   placeholder="e.g. John Doe" value="<?php echo $edit_item['name'] ?? ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Designation / Role</label>
                            <input type="text" name="designation" class="form-control rounded-3 border-light bg-light bg-opacity-50" 
                                   placeholder="e.g. Master Mechanic" value="<?php echo $edit_item['designation'] ?? ''; ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control rounded-3 border-light bg-light bg-opacity-50" 
                                       value="<?php echo $edit_item['sort_order'] ?? '0'; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Social Links (Optional)</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light border-light text-primary"><i class="fab fa-facebook-f"></i></span>
                                <input type="url" name="facebook_url" class="form-control rounded-end-3 border-light bg-light bg-opacity-50" 
                                       placeholder="Facebook URL" value="<?php echo $edit_item['facebook_url'] ?? ''; ?>">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light border-light text-info"><i class="fab fa-twitter"></i></span>
                                <input type="url" name="twitter_url" class="form-control rounded-end-3 border-light bg-light bg-opacity-50" 
                                       placeholder="Twitter URL" value="<?php echo $edit_item['twitter_url'] ?? ''; ?>">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light border-light text-danger"><i class="fab fa-instagram"></i></span>
                                <input type="url" name="instagram_url" class="form-control rounded-end-3 border-light bg-light bg-opacity-50" 
                                       placeholder="Instagram URL" value="<?php echo $edit_item['instagram_url'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 rounded-pill fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> <?php echo $edit_item ? 'Update Member' : 'Add to Team'; ?>
                            </button>
                            <?php if ($edit_item): ?>
                                <a href="?page=team" class="btn btn-light rounded-pill py-2 text-muted">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Members List -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-users text-primary me-2"></i>Active Team</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 700px; overflow-y: auto;">
                        <?php if (empty($team)): ?>
                            <div class="p-5 text-center text-muted">
                                <i class="fas fa-users-slash fa-3x mb-3 opacity-25"></i>
                                <p>No team members added yet.</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach ($team as $m): ?>
                        <div class="list-group-item p-4 border-light hover-bg-light transition-all shadow-hover-sm">
                            <div class="d-flex gap-4 align-items-center">
                                <?php 
                                    $listImg = $m['image'] ?: 'img/team-1.jpg';
                                    $listDisplayUrl = (strpos($listImg, 'uploads/') === 0) ? $listImg : '../' . $listImg;
                                ?>
                                <img src="<?php echo $listDisplayUrl; ?>" class="rounded-circle shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($m['name']); ?></h6>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 extra-small mt-1">
                                                <?php echo htmlspecialchars($m['designation']); ?>
                                            </span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="?page=team&action=edit&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-white border rounded-circle p-2 shadow-sm" title="Edit">
                                                <i class="fas fa-pen text-primary fa-fw"></i>
                                            </a>
                                            <a href="?page=team&action=delete&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-white border rounded-circle p-2 shadow-sm" title="Delete" onclick="return confirm('Remove this team member?')">
                                                <i class="fas fa-trash text-danger fa-fw"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <?php if ($m['facebook_url']): ?><i class="fab fa-facebook text-muted me-2"></i><?php endif; ?>
                                        <?php if ($m['twitter_url']): ?><i class="fab fa-twitter text-muted me-2"></i><?php endif; ?>
                                        <?php if ($m['instagram_url']): ?><i class="fab fa-instagram text-muted me-2"></i><?php endif; ?>
                                        <small class="text-muted ms-2">Order: <?php echo $m['sort_order']; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 alert alert-light border-0 shadow-sm rounded-4 p-4 text-center">
                <h6 class="fw-bold"><i class="fas fa-heart text-danger me-2"></i>Cultural Impact</h6>
                <p class="small text-muted mb-0">Showcasing your team builds trust with potential customers. Let them see the experts who will handle their vehicles.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Photo Preview Logic
    document.getElementById('member_image').addEventListener('change', function(e) {
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

<style>
    .extra-small { font-size: 0.65rem; font-weight: 700; text-uppercase; }
    .transition-all { transition: all 0.2s ease-in-out; }
    .hover-bg-light:hover { background: #f8fafc; cursor: default; }
    .shadow-hover-sm:hover { position: relative; z-index: 1; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .btn-white { background: white; color: #1e293b; }
</style>
