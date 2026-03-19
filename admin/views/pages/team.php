<?php
/**
 * Professional Team Architecture & Roster Management
 */

use Core\Database;
use Core\Uploader;

$db = Database::getInstance();
$message = '';

// 1. Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM team WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4"><i class="fas fa-trash-alt me-2"></i>Member removed from roster.</div>';
        echo "<script>setTimeout(() => { window.location.href='?page=team'; }, 1000);</script>";
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
            $image_path = $result['path'];
        } else {
            $message = '<div class="alert alert-danger border-0 shadow-lg rounded-4"><i class="fas fa-exclamation-triangle me-2"></i>' . $result['error'] . '</div>';
        }
    }

    if (empty($message)) {
        if ($id) {
            $sql = "UPDATE team SET name = ?, designation = ?, facebook_url = ?, twitter_url = ?, instagram_url = ?, image = ?, sort_order = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([$name, $designation, $facebook_url, $twitter_url, $instagram_url, $image_path, $sort_order, $id]);
        } else {
            $sql = "INSERT INTO team (name, designation, facebook_url, twitter_url, instagram_url, image, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([$name, $designation, $facebook_url, $twitter_url, $instagram_url, $image_path, $sort_order]);
        }

        if ($success) {
            $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Roster Updated:</b> Personnel data synchronized.</div>';
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
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-4">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary shadow-sm" style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-users-gear"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Personnel Architect</h4>
                        <p class="text-muted small mb-0 font-monospace extra-small">Registry: shop_experts_v2</p>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2 border small fw-bold d-none d-md-inline-block">
                        <i class="fas fa-id-badge me-1"></i> Active Roster: <?php echo count($team); ?>
                    </span>
                    <a href="?page=team" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm transition-all">
                        <i class="fas fa-plus me-2"></i>New Specialist
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row g-5">
        <!-- Roster Entry Canvas -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden mb-5">
                <div class="card-header bg-dark text-white p-4 border-0 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 uppercase-tracking small"><?php echo $edit_item ? 'Update Specialist' : 'Define New Specialist'; ?></h6>
                    <i class="fas <?php echo $edit_item ? 'fa-user-pen' : 'fa-user-plus'; ?> text-muted"></i>
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
                                    $imgUrl = $edit_item['image'] ?? 'img/team-1.jpg';
                                    $displayUrl = (strpos($imgUrl, 'uploads/') === 0) ? $imgUrl : '../' . $imgUrl;
                                ?>
                                <div class="avatar-container shadow-lg rounded-circle border border-4 border-white overflow-hidden" style="width: 140px; height: 140px;">
                                    <img src="<?php echo $displayUrl; ?>" id="preview-avatar" class="w-100 h-100 object-fit-cover transition-all">
                                </div>
                                <label for="member_image" class="btn btn-primary rounded-circle position-absolute bottom-0 end-0 p-3 shadow-lg border-0 hover-scale" style="width: 48px; height: 48px;">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" name="member_image" id="member_image" class="d-none" accept="image/*">
                            </div>
                            <p class="extra-small fw-bold text-muted tracking-widest mt-4">IDENTIFICATION IMAGE</p>
                        </div>

                        <div class="mb-4">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">SPECIALIST IDENTITY</label>
                            <input type="text" name="name" class="form-control border-0 bg-light py-3 rounded-3 fw-bold shadow-sm" 
                                   placeholder="Full Legal Name" value="<?php echo htmlspecialchars($edit_item['name'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">CORE DESIGNATION</label>
                            <input type="text" name="designation" class="form-control border-0 bg-light py-3 rounded-3 fw-bold shadow-sm" 
                                   placeholder="e.g. Lead Technical Engineer" value="<?php echo htmlspecialchars($edit_item['designation'] ?? ''); ?>" required>
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-6">
                                <label class="extra-small fw-bold text-muted tracking-widest mb-3 d-block">SORT PRIORITY</label>
                                <input type="number" name="sort_order" class="form-control border-0 bg-light py-2 rounded-3 text-center fw-bold shadow-sm" 
                                       value="<?php echo $edit_item['sort_order'] ?? '0'; ?>">
                            </div>
                        </div>

                        <div class="mb-5 pt-4 border-top">
                            <h6 class="fw-bold uppercase-tracking small text-muted mb-4"><i class="fas fa-share-nodes me-2"></i>Digital Presence</h6>
                            <div class="input-group mb-3 shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-0 text-primary px-3"><i class="fab fa-facebook-f"></i></span>
                                <input type="url" name="facebook_url" class="form-control border-0 bg-light py-2 small" 
                                       placeholder="Facebook URL" value="<?php echo htmlspecialchars($edit_item['facebook_url'] ?? ''); ?>">
                            </div>
                            <div class="input-group mb-3 shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-0 text-info px-3"><i class="fab fa-twitter"></i></span>
                                <input type="url" name="twitter_url" class="form-control border-0 bg-light py-2 small" 
                                       placeholder="Twitter URL" value="<?php echo htmlspecialchars($edit_item['twitter_url'] ?? ''); ?>">
                            </div>
                            <div class="input-group mb-0 shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-0 text-danger px-3"><i class="fab fa-instagram"></i></span>
                                <input type="url" name="instagram_url" class="form-control border-0 bg-light py-2 small" 
                                       placeholder="Instagram URL" value="<?php echo htmlspecialchars($edit_item['instagram_url'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary btn-gradient py-3 rounded-pill fw-bold shadow-lg border-0">
                                <i class="fas fa-check-circle me-2"></i> <?php echo $edit_item ? 'Synchronize Updates' : 'Commit to Roster'; ?>
                            </button>
                            <?php if ($edit_item): ?>
                                <a href="?page=team" class="btn btn-light rounded-pill py-2 border-0 small text-muted">Cancel Operations</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Roster Workspace -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 uppercase-tracking small text-muted">Active Specialist Roster</h6>
                    <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 extra-small uppercase-tracking">Verified Assets</div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 800px; overflow-y: auto;">
                        <?php if (empty($team)): ?>
                            <div class="p-5 text-center">
                                <div class="stat-icon bg-light text-muted mx-auto mb-4" style="width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                    <i class="fas fa-user-slash opacity-25"></i>
                                </div>
                                <h6 class="fw-bold text-muted">Roster currently empty.</h6>
                                <p class="extra-small text-muted uppercase-tracking">Awaiting personnel deployment.</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php foreach ($team as $m): ?>
                        <div class="list-group-item p-4 border-light hover-bg-light transition-all">
                            <div class="d-flex gap-4 align-items-center">
                                <?php 
                                    $listImg = $m['image'] ?: 'img/team-1.jpg';
                                    $listDisplayUrl = (strpos($listImg, 'uploads/') === 0) ? $listImg : '../' . $listImg;
                                ?>
                                <div class="flex-shrink-0 shadow-sm rounded-4 overflow-hidden" style="width: 80px; height: 80px; border: 3px solid white;">
                                    <img src="<?php echo $listDisplayUrl; ?>" class="w-100 h-100 object-fit-cover shadow-inner">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($m['name']); ?></h6>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 extra-small uppercase-tracking" style="font-size: 0.6rem;">
                                                <?php echo htmlspecialchars($m['designation']); ?>
                                            </span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="?page=team&action=edit&id=<?php echo $m['id']; ?>" class="btn btn-sm bg-white shadow-sm border rounded-circle p-2 hover-scale" title="Update Profile">
                                                <i class="fas fa-pen-nib text-primary fa-fw small"></i>
                                            </a>
                                            <a href="?page=team&action=delete&id=<?php echo $m['id']; ?>" class="btn btn-sm bg-white shadow-sm border rounded-circle p-2 hover-scale" title="Remove Asset" onclick="return confirm('Initiate asset removal protocol?')">
                                                <i class="fas fa-user-xmark text-danger fa-fw small"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex align-items-center gap-3">
                                        <div class="d-flex gap-2">
                                            <?php if ($m['facebook_url']): ?><i class="fab fa-facebook text-muted extra-small"></i><?php endif; ?>
                                            <?php if ($m['twitter_url']): ?><i class="fab fa-twitter text-muted extra-small"></i><?php endif; ?>
                                            <?php if ($m['instagram_url']): ?><i class="fab fa-instagram text-muted extra-small"></i><?php endif; ?>
                                        </div>
                                        <div class="v-divider" style="width: 1px; height: 10px; background: #e5e7eb;"></div>
                                        <span class="extra-small text-muted fw-bold">PRIORITY: <?php echo $m['sort_order']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 card border-0 bg-primary bg-opacity-10 p-4 rounded-5 shadow-sm">
                <div class="d-flex align-items-center gap-4">
                    <div class="stat-icon bg-white text-primary shadow-sm" style="width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-primary">Trust Architecture</h6>
                        <p class="small mb-0 opacity-75">Visible expertise is the primary driver of customer conversion. Maintain high-quality headshots for all roster assets.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Asset Preview Logic
    document.getElementById('member_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview-avatar');
                preview.src = e.target.result;
                preview.parentElement.classList.add('animate-pulse');
                setTimeout(() => preview.parentElement.classList.remove('animate-pulse'), 1000);
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 2.5px; }
    .extra-small { font-size: 0.65rem; }
    .transition-all { transition: all 0.3s ease-in-out; }
    .hover-bg-light:hover { background: #f8fafc; }
    .hover-scale:hover { transform: scale(1.1); }
    .avatar-container { background: #f1f5f9; position: relative; }
    .avatar-container::after { content: ''; position: absolute; inset: 0; box-shadow: inset 0 2px 10px rgba(0,0,0,0.1); border-radius: 50%; }
    .btn-gradient { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
    .animate-pulse { animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .7; } }
</style>

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
