<?php
/**
 * Advanced Page Content Manager
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['form_type'];
    
    if ($type === 'about') {
        $about_data = json_encode([
            "title" => $_POST['about_title'],
            "description" => $_POST['about_desc'],
            "clients" => $_POST['about_clients'],
            "repairs" => $_POST['about_repairs'],
            "image" => $_POST['about_image'] ?? 'img/about.jpg'
        ]);
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('about_data', ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        if ($stmt->execute([$about_data])) $message = '<div class="alert alert-success border-0 shadow-sm rounded-4">About Us section updated!</div>';
    }
    
    if ($type === 'features') {
        $features = [];
        foreach ($_POST['f_titles'] as $i => $title) {
            if (!empty($title)) {
                $features[] = [
                    "title" => $title,
                    "icon" => $_POST['f_icons'][$i]
                ];
            }
        }
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('features_data', ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        if ($stmt->execute([json_encode($features)])) $message = '<div class="alert alert-success border-0 shadow-sm rounded-4">Features updated!</div>';
    }

    if ($type === 'team') {
        $team = [];
        foreach ($_POST['t_names'] as $i => $name) {
            if (!empty($name)) {
                $team[] = [
                    "name" => $name,
                    "role" => $_POST['t_roles'][$i],
                    "image" => $_POST['t_images'][$i]
                ];
            }
        }
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('team_data', ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        if ($stmt->execute([json_encode($team)])) $message = '<div class="alert alert-success border-0 shadow-sm rounded-4">Team members updated!</div>';
    }
}

// Fetch Data
$about = json_decode($db->query("SELECT setting_value FROM settings WHERE setting_key = 'about_data'")->fetchColumn() ?: '{}', true);
$features = json_decode($db->query("SELECT setting_value FROM settings WHERE setting_key = 'features_data'")->fetchColumn() ?: '[]', true);
$team = json_decode($db->query("SELECT setting_value FROM settings WHERE setting_key = 'team_data'")->fetchColumn() ?: '[]', true);

// Defaults
if (empty($features)) {
    $features = [
        ["title" => "Expert Technicians", "icon" => "fa-user-check"],
        ["title" => "Quality Service", "icon" => "fa-check"],
        ["title" => "Modern Equipment", "icon" => "fa-car"],
        ["title" => "24/7 Support", "icon" => "fa-headphones"]
    ];
}
?>

<div class="row">
    <div class="col-12">
        <div class="card p-4 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Page Content Manager</h4>
                    <p class="text-muted small mb-0">Manage static sections like About, Features, and Team across all pages.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <?php echo $message; ?>
        
        <ul class="nav nav-pills mb-4 gap-2" id="pageTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-about">About Us</button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-features">Features</button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 shadow-sm" data-bs-toggle="pill" data-bs-target="#tab-team">Our Team</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- About Tab -->
            <div class="tab-pane fade show active" id="tab-about">
                <div class="card p-4 border-0 shadow-sm">
                    <form method="POST">
                        <input type="hidden" name="form_type" value="about">
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">SECTION TITLE</label>
                                <input type="text" name="about_title" class="form-control mb-4" value="<?php echo htmlspecialchars($about['title'] ?? 'Established Auto Specialists'); ?>">
                                
                                <label class="form-label fw-bold small text-muted">DESCRIPTION CONTENT</label>
                                <textarea name="about_desc" class="form-control" rows="6"><?php echo htmlspecialchars($about['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-4 rounded-4 border border-white h-100">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-2"></i>Statistics</h6>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">HAPPY CLIENTS</label>
                                        <input type="text" name="about_clients" class="form-control" value="<?php echo htmlspecialchars($about['clients'] ?? '2500'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">REPAIRS DONE</label>
                                        <input type="text" name="about_repairs" class="form-control" value="<?php echo htmlspecialchars($about['repairs'] ?? '4305'); ?>">
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small text-muted">ABOUT IMAGE (PATH)</label>
                                        <input type="text" name="about_image" class="form-control" value="<?php echo htmlspecialchars($about['image'] ?? 'img/about.jpg'); ?>" placeholder="img/about.jpg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-4 shadow-sm fw-bold">Update About Section</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Features Tab -->
            <div class="tab-pane fade" id="tab-features">
                <div class="card p-4 border-0 shadow-sm">
                    <form method="POST">
                        <input type="hidden" name="form_type" value="features">
                        <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                            <?php foreach($features as $i => $f): ?>
                            <div class="col">
                                <div class="p-3 bg-light rounded-4 border border-white shadow-sm">
                                    <label class="form-label fw-bold extra-small text-muted">FEATURE <?php echo $i+1; ?> TITLE</label>
                                    <input type="text" name="f_titles[]" class="form-control mb-2" value="<?php echo htmlspecialchars($f['title']); ?>">
                                    <label class="form-label fw-bold extra-small text-muted">ICON CLASS (FontAwesome)</label>
                                    <input type="text" name="f_icons[]" class="form-control" value="<?php echo htmlspecialchars($f['icon']); ?>">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-4">Update All Features</button>
                    </form>
                </div>
            </div>

            <!-- Team Tab -->
            <div class="tab-pane fade" id="tab-team">
                <div class="card p-4 border-0 shadow-sm">
                    <form method="POST">
                        <input type="hidden" name="form_type" value="team">
                        <div id="team-container">
                            <?php foreach($team as $i => $tm): ?>
                            <div class="team-row bg-light p-3 rounded-4 mb-3 border border-white position-relative">
                                <button type="button" class="btn btn-sm btn-white text-danger position-absolute top-0 end-0 m-2 remove-tm"><i class="fas fa-times"></i></button>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold extra-small text-muted">NAME</label>
                                        <input type="text" name="t_names[]" class="form-control" value="<?php echo htmlspecialchars($tm['name']); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold extra-small text-muted">ROLE</label>
                                        <input type="text" name="t_roles[]" class="form-control" value="<?php echo htmlspecialchars($tm['role']); ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold extra-small text-muted">IMAGE FILENAME (e.g. team-1.jpg)</label>
                                        <input type="text" name="t_images[]" class="form-control" value="<?php echo htmlspecialchars($tm['image']); ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="add-tm" class="btn btn-outline-primary border-dashed w-100 py-3 rounded-4 mb-4 mt-2">Add Team Member</button>
                        <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-4">Save Team Roster</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link { color: #64748b; background: white; font-weight: 600; border: 1px solid #e2e8f0; }
    .nav-pills .nav-link.active { background: #6366f1 !important; color: white !important; border-color: #6366f1; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>

<script>
document.getElementById('add-tm').addEventListener('click', function() {
    const container = document.getElementById('team-container');
    const div = document.createElement('div');
    div.className = 'team-row bg-light p-3 rounded-4 mb-3 border border-white position-relative';
    div.innerHTML = `
        <button type="button" class="btn btn-sm btn-white text-danger position-absolute top-0 end-0 m-2 remove-tm"><i class="fas fa-times"></i></button>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold extra-small text-muted">NAME</label>
                <input type="text" name="t_names[]" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold extra-small text-muted">ROLE</label>
                <input type="text" name="t_roles[]" class="form-control">
            </div>
            <div class="col-md-5">
                <label class="form-label fw-bold extra-small text-muted">IMAGE FILENAME</label>
                <input type="text" name="t_images[]" class="form-control" placeholder="team-1.jpg">
            </div>
        </div>
    `;
    container.appendChild(div);
    div.querySelector('.remove-tm').onclick = () => div.remove();
});

document.querySelectorAll('.remove-tm').forEach(btn => btn.onclick = () => btn.closest('.team-row').remove());
</script>
