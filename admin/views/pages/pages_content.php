<?php
/**
 * Global Brand & Content Architect
 */

use Core\Database;
use Core\Uploader;

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['form_type'];
    
    if ($type === 'about') {
        $header_image = $_POST['old_about_image'] ?? 'img/about.jpg';
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            $result = Uploader::upload($_FILES['about_image'], UPLOAD_DIR);
            if (isset($result['success'])) $header_image = $result['path'];
        }

        $about_data = json_encode([
            "title" => $_POST['about_title'],
            "description" => $_POST['about_desc'],
            "clients" => $_POST['about_clients'],
            "repairs" => $_POST['about_repairs'],
            "why_choose" => $_POST['why_choose'] ?? [], // New component
            "image" => $header_image
        ]);
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('about_data', ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        if ($stmt->execute([$about_data])) $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 text-center py-3 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Branding Synchronized:</b> The About Us architecture has been updated.</div>';
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
        if ($stmt->execute([json_encode($features)])) $message = '<div class="alert alert-success border-0 shadow-lg rounded-4">Site highlights successfully redeployed.</div>';
    }

    if ($type === 'team') {
        $team = [];
        foreach ($_POST['t_names'] as $i => $name) {
            if (!empty($name)) {
                $img_path = $_POST['t_old_images'][$i] ?? 'img/team-1.jpg';
                
                if (isset($_FILES['t_images']['name'][$i]) && $_FILES['t_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['t_images']['name'][$i],
                        'type' => $_FILES['t_images']['type'][$i],
                        'tmp_name' => $_FILES['t_images']['tmp_name'][$i],
                        'error' => $_FILES['t_images']['error'][$i],
                        'size' => $_FILES['t_images']['size'][$i]
                    ];
                    $result = Uploader::upload($file, UPLOAD_DIR);
                    if (isset($result['success'])) $img_path = $result['path'];
                }

                $team[] = [
                    "name" => $name,
                    "role" => $_POST['t_roles'][$i],
                    "image" => $img_path
                ];
            }
        }
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('team_data', ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        if ($stmt->execute([json_encode($team)])) $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 text-center py-3"><i class="fas fa-users-cog me-2"></i>Staff Directory successfully updated.</div>';
    }

    if ($type === 'theme') {
        $theme_data = json_encode([
            "primary_color" => $_POST['primary_color'],
            "secondary_color" => $_POST['secondary_color'],
            "font_family" => $_POST['font_family'],
            "border_radius" => $_POST['border_radius'] ?? '10px'
        ]);
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('theme_config', ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        if ($stmt->execute([$theme_data])) $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 text-center py-3"><i class="fas fa-magic me-2"></i>Global Visual Identity synchronized.</div>';
    }
}

// Fetch Data
$about = json_decode($db->query("SELECT setting_value FROM settings WHERE setting_key = 'about_data'")->fetchColumn() ?: '{}', true);
$features = json_decode($db->query("SELECT setting_value FROM settings WHERE setting_key = 'features_data'")->fetchColumn() ?: '[]', true);
$team = json_decode($db->query("SELECT setting_value FROM settings WHERE setting_key = 'team_data'")->fetchColumn() ?: '[]', true);
$theme = json_decode($db->query("SELECT setting_value FROM settings WHERE setting_key = 'theme_config'")->fetchColumn() ?: '{"primary_color":"#D81324","secondary_color":"#343a40","font_family":"Outfit","border_radius":"10px"}', true);

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

<div class="animate-fade-in">
    <!-- Sophisticated Header -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 p-4 bg-white overflow-hidden">
        <div class="position-absolute" style="top: -20px; right: -20px; opacity: 0.05;">
            <i class="fas fa-swatchbook fa-8x"></i>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="stat-icon bg-indigo-soft text-indigo shadow-sm" style="width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-sliders-h-square"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">Identity & Core Sections</h4>
                <p class="text-muted small mb-0 font-monospace extra-small">Registry: global_config_v2</p>
            </div>
        </div>
    </div>

    <?php echo $message; ?>
    
    <!-- Premium Navigation -->
    <ul class="nav nav-pills mb-4 gap-3 bg-white p-2 rounded-pill shadow-sm d-inline-flex" id="pageTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active rounded-pill px-4 fw-bold small" data-bs-toggle="pill" data-bs-target="#tab-about">About Us</button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4 fw-bold small" data-bs-toggle="pill" data-bs-target="#tab-features">Highlights</button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4 fw-bold small" data-bs-toggle="pill" data-bs-target="#tab-team">Team Roster</button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4 fw-bold small bg-primary bg-opacity-10 text-primary" data-bs-toggle="pill" data-bs-target="#tab-theme"><i class="fas fa-paint-brush me-2"></i>Appearance</button>
        </li>
    </ul>

    <div class="tab-content mt-2">
        <!-- About Tab -->
        <div class="tab-pane fade show active" id="tab-about">
            <div class="card border-0 shadow-lg rounded-5 bg-white p-0 overflow-hidden">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="form_type" value="about">
                    <input type="hidden" name="old_about_image" value="<?php echo $about['image'] ?? 'img/about.jpg'; ?>">
                    <div class="row g-0">
                        <div class="col-lg-8 border-end">
                            <div class="p-5">
                                <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">SECTION NARRATIVE</label>
                                <input type="text" name="about_title" class="form-control mb-4 fw-bold py-3 bg-light border-0 rounded-3" value="<?php echo htmlspecialchars($about['title'] ?? 'Established Auto Specialists'); ?>" placeholder="Section Headline">
                                <textarea name="about_desc" class="form-control bg-light border-0 p-4 rounded-3" rows="10" placeholder="The heart of your story..."><?php echo htmlspecialchars($about['description'] ?? ''); ?></textarea>
                                
                                <div class="mt-5 pt-4 border-top">
                                    <h6 class="fw-bold mb-4 small d-flex align-items-center"><i class="fas fa-list-check text-primary me-2"></i> Why Choose Us Highlights</h6>
                                    <div id="why-choose-container">
                                        <?php 
                                        $why_list = $about['why_choose'] ?? ['Expert Technicians', 'Best Prices', 'Professional Work'];
                                        foreach($why_list as $point): ?>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text bg-light border-0"><i class="fas fa-circle-check text-success small"></i></span>
                                                <input type="text" name="why_choose[]" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($point); ?>">
                                                <button type="button" class="btn btn-light bg-light border-0 text-danger px-3 shadow-none" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-link text-primary p-0 small fw-bold mt-2" onclick="addWhyPoint()"><i class="fas fa-plus-circle me-1"></i> Add Advantage Point</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 bg-light bg-opacity-50">
                            <div class="p-5">
                                <label class="extra-small fw-bold text-muted tracking-widest mb-4 d-block">PERFORMANCE METRICS</label>
                                <div class="stat-card mb-4 bg-white p-4 rounded-4 shadow-sm border">
                                    <label class="extra-small fw-bold text-muted mb-2 d-block">HAPPY CLIENTS</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-users text-primary"></i></span>
                                        <input type="text" name="about_clients" class="form-control border-0 fw-bold fs-4 p-0 shadow-none" value="<?php echo htmlspecialchars($about['clients'] ?? '2500'); ?>">
                                    </div>
                                </div>
                                <div class="stat-card mb-4 bg-white p-4 rounded-4 shadow-sm border">
                                    <label class="extra-small fw-bold text-muted mb-2 d-block">REPAIRS COMPLETED</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-wrench text-success"></i></span>
                                        <input type="text" name="about_repairs" class="form-control border-0 fw-bold fs-4 p-0 shadow-none" value="<?php echo htmlspecialchars($about['repairs'] ?? '4305'); ?>">
                                    </div>
                                </div>

                                <label class="extra-small fw-bold text-muted tracking-widest mb-3 d-block mt-5">BRAND VISUAL</label>
                                <div class="bg-white rounded-4 shadow-sm overflow-hidden border border-white border-4 mb-3" style="aspect-ratio: 4/3;">
                                    <img src="../<?php echo $about['image'] ?? 'img/about.jpg'; ?>" class="w-100 h-100 object-fit-cover shadow-inner">
                                </div>
                                <input type="file" name="about_image" class="form-control form-control-sm border-0 bg-white rounded-pill px-3 py-2 shadow-sm">
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-dark text-white d-flex align-items-center justify-content-between">
                        <span class="small opacity-50 font-monospace"><i class="fas fa-database me-2"></i>about_section_metadata</span>
                        <button type="submit" class="btn btn-primary btn-gradient px-5 py-2 rounded-pill fw-bold border-0 shadow-lg">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Highlights Tab -->
        <div class="tab-pane fade" id="tab-features">
            <div class="card border-0 shadow-lg rounded-5 bg-white p-5">
                <form method="POST">
                    <input type="hidden" name="form_type" value="features">
                    <div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
                        <?php foreach($features as $i => $f): ?>
                        <div class="col">
                            <div class="p-4 bg-light rounded-4 border-2 border-dashed border-white transition-all hover-glow shadow-sm">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 shadow-sm">
                                        <i class="fas <?php echo $f['icon']; ?> fa-lg"></i>
                                    </div>
                                    <span class="badge bg-white text-muted px-2 py-1 border small">Card #<?php echo $i+1; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">FEATURE TITLE</label>
                                    <input type="text" name="f_titles[]" class="form-control border-0 bg-white rounded-3 shadow-none py-2 fw-bold" value="<?php echo htmlspecialchars($f['title']); ?>">
                                </div>
                                <div class="mb-0">
                                    <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">ICON REFERENCE</label>
                                    <input type="text" name="f_icons[]" class="form-control border-0 bg-white rounded-3 shadow-none py-2 font-monospace small" value="<?php echo htmlspecialchars($f['icon']); ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center pt-4 border-top">
                        <button type="submit" class="btn btn-primary btn-gradient px-5 py-3 rounded-pill fw-bold border-0 shadow-lg">Deploy Feature Matrix</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Team Tab -->
        <div class="tab-pane fade" id="tab-team">
            <div class="card border-0 shadow-lg rounded-5 bg-white p-5">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="form_type" value="team">
                    <div id="team-container" class="mb-4">
                        <?php foreach($team as $i => $tm): ?>
                        <div class="team-row bg-white p-4 rounded-4 mb-3 border hover-glow transition-all position-relative shadow-sm">
                            <button type="button" class="btn btn-sm btn-white border rounded-circle text-danger position-absolute top-0 end-0 m-3 remove-tm shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-times"></i></button>
                            <input type="hidden" name="t_old_images[]" value="<?php echo $tm['image']; ?>">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-2 text-center text-md-start">
                                    <div class="avatar-preview bg-light rounded-4 shadow-inner mx-auto overflow-hidden border border-2 border-white" style="width: 100px; height: 100px;">
                                        <img src="../<?php echo $tm['image']; ?>" class="w-100 h-100 object-fit-cover shadow">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">FULL PROFESSIONAL NAME</label>
                                    <input type="text" name="t_names[]" class="form-control border-0 rounded-3 bg-light py-2 fw-bold" value="<?php echo htmlspecialchars($tm['name']); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">INDUSTRY ROLE</label>
                                    <input type="text" name="t_roles[]" class="form-control border-0 rounded-3 bg-light py-2" value="<?php echo htmlspecialchars($tm['role']); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">HI-RES PORTRAIT</label>
                                    <input type="file" name="t_images[]" class="form-control form-control-sm border-0 rounded-pill bg-light px-3 py-2">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <button type="button" id="add-tm" class="btn btn-outline-primary border-dashed w-100 py-3 rounded-pill fw-bold">
                            <i class="fas fa-plus-circle me-2"></i> Onboard New Member
                        </button>
                        <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-pill shadow-lg fw-bold border-0 mt-2">Deploy Staff Catalog</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Appearance Tab -->
        <div class="tab-pane fade" id="tab-theme">
            <div class="card border-0 shadow-lg rounded-5 bg-white p-5">
                <form method="POST">
                    <input type="hidden" name="form_type" value="theme">
                    <div class="row g-5">
                        <div class="col-md-6 border-end px-5">
                            <h6 class="fw-bold mb-5 tracking-widest small text-primary uppercase-tracking"><i class="fas fa-palette me-2"></i> Brand Architecture</h6>
                            <div class="mb-5">
                                <label class="extra-small fw-bold text-muted mb-3 d-block">PRIMARY BRAND COLOR</label>
                                <div class="d-flex gap-3 align-items-center bg-light p-3 rounded-4 shadow-inner">
                                    <input type="color" name="primary_color" class="form-control form-control-color border-0 bg-transparent rounded-circle" value="<?php echo $theme['primary_color'] ?? '#D81324'; ?>" style="width: 60px; height: 60px;">
                                    <input type="text" class="form-control border-0 bg-white rounded-3 small p-2 text-center font-monospace" value="<?php echo $theme['primary_color'] ?? '#D81324'; ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="extra-small fw-bold text-muted mb-3 d-block">SECONDARY ACCENT COLOR</label>
                                <div class="d-flex gap-3 align-items-center bg-light p-3 rounded-4 shadow-inner">
                                    <input type="color" name="secondary_color" class="form-control form-control-color border-0 bg-transparent rounded-circle" value="<?php echo $theme['secondary_color'] ?? '#343a40'; ?>" style="width: 60px; height: 60px;">
                                    <input type="text" class="form-control border-0 bg-white rounded-3 small p-2 text-center font-monospace" value="<?php echo $theme['secondary_color'] ?? '#343a40'; ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="extra-small fw-bold text-muted mb-3 d-block">GLOBAL BORDER RADIUS</label>
                                <select name="border_radius" class="form-select border-0 bg-light p-3 rounded-4 shadow-none fw-bold">
                                    <option value="0px" <?php echo ($theme['border_radius'] ?? '') === '0px' ? 'selected' : ''; ?>>Sharp (0px)</option>
                                    <option value="8px" <?php echo ($theme['border_radius'] ?? '') === '8px' ? 'selected' : ''; ?>>Subtle (8px)</option>
                                    <option value="15px" <?php echo ($theme['border_radius'] ?? '') === '15px' ? 'selected' : ''; ?>>Modern (15px)</option>
                                    <option value="30px" <?php echo ($theme['border_radius'] ?? '') === '30px' ? 'selected' : ''; ?>>Rounded (30px)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 px-5">
                            <h6 class="fw-bold mb-5 tracking-widest small text-primary uppercase-tracking"><i class="fas fa-font me-2"></i> Typography Suite</h6>
                            <div class="mb-5">
                                <label class="extra-small fw-bold text-muted mb-3 d-block">GOOGLE FONT FAMILY</label>
                                <select name="font_family" class="form-select border-0 bg-light p-3 rounded-4 shadow-none fw-bold mb-4">
                                    <option value="Outfit" <?php echo ($theme['font_family'] ?? '') === 'Outfit' ? 'selected' : ''; ?>>Outfit (Recommended)</option>
                                    <option value="Inter" <?php echo ($theme['font_family'] ?? '') === 'Inter' ? 'selected' : ''; ?>>Inter (Tech Look)</option>
                                    <option value="Roboto" <?php echo ($theme['font_family'] ?? '') === 'Roboto' ? 'selected' : ''; ?>>Roboto (Corporate)</option>
                                    <option value="Montserrat" <?php echo ($theme['font_family'] ?? '') === 'Montserrat' ? 'selected' : ''; ?>>Montserrat (Bold Flair)</option>
                                </select>
                                
                                <div class="p-5 bg-light rounded-5 text-center shadow-inner mt-4 border border-white">
                                    <div class="small fw-bold text-muted mb-2 text-uppercase letter-spacing-2">Live Preview</div>
                                    <div class="h2 fw-bold text-dark mb-0" style="font-family: '<?php echo $theme['font_family'] ?? 'Outfit'; ?>', sans-serif;">
                                        The Future<br>of Automotive
                                    </div>
                                    <p class="text-muted small mt-3 px-4">Expert repair services with a focus on quality and customer satisfaction.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-top text-center">
                        <button type="submit" class="btn btn-primary btn-gradient px-5 py-3 rounded-pill shadow-lg fw-bold border-0">Deploy Visual Identity</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link { color: #64748b; background: transparent; font-weight: 700; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .nav-pills .nav-link.active { background: var(--primary-gradient) !important; color: white !important; transform: scale(1.05); box-shadow: 0 10px 20px -5px rgba(216, 19, 36, 0.3); }
    .nav-link:not(.active):hover { background: #f1f5f9; color: #1e293b; }
    .hover-glow:hover { box-shadow: 0 15px 30px -5px rgba(0,0,0,0.08) !important; border-color: rgba(216, 19, 36, 0.1) !important; transform: translateY(-3px); }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 2px; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
    .stat-card { transition: 0.3s; }
    .stat-card:focus-within { border-color: #d81324 !important; box-shadow: 0 0 0 4px rgba(216, 19, 36, 0.05) !important; }
</style>

<script>
function addWhyPoint() {
    const container = document.getElementById('why-choose-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <span class="input-group-text bg-light border-0"><i class="fas fa-circle-check text-success small"></i></span>
        <input type="text" name="why_choose[]" class="form-control bg-light border-0 py-2" placeholder="Dynamic Advantage Point...">
        <button type="button" class="btn btn-light bg-light border-0 text-danger px-3 shadow-none" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    `;
    container.appendChild(div);
}

document.getElementById('add-tm')?.addEventListener('click', function() {
    const container = document.getElementById('team-container');
    const div = document.createElement('div');
    div.className = 'team-row bg-white p-4 rounded-4 mb-3 border hover-glow transition-all position-relative shadow-sm';
    div.innerHTML = `
        <button type="button" class="btn btn-sm btn-white border rounded-circle text-danger position-absolute top-0 end-0 m-3 remove-tm shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-times"></i></button>
        <div class="row g-4 align-items-center">
            <div class="col-md-2 text-center text-md-start">
                <div class="avatar-preview bg-light rounded-4 shadow-inner mx-auto overflow-hidden border border-2 border-white" style="width: 100px; height: 100px;">
                    <div class="h-100 d-flex align-items-center justify-content-center opacity-25"><i class="fas fa-user-plus fa-2x"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">NAME</label>
                <input type="text" name="t_names[]" class="form-control border-0 rounded-3 bg-light py-2 fw-bold" placeholder="New Staff Member">
            </div>
            <div class="col-md-3">
                <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">ROLE</label>
                <input type="text" name="t_roles[]" class="form-control border-0 rounded-3 bg-light py-2" placeholder="Position Profile">
            </div>
            <div class="col-md-3">
                <label class="extra-small fw-bold text-muted tracking-widest mb-2 d-block">PHOTO</label>
                <input type="file" name="t_images[]" class="form-control form-control-sm border-0 rounded-pill bg-light px-3 py-2">
            </div>
        </div>
    `;
    container.appendChild(div);
    div.querySelector('.remove-tm').onclick = () => div.remove();
});

document.querySelectorAll('.remove-tm').forEach(btn => btn.onclick = () => btn.closest('.team-row').remove());
</script>

<style>
    .nav-pills .nav-link { color: #64748b; background: white; font-weight: 600; border: 1px solid #f1f5f9; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .nav-pills .nav-link.active { background: var(--primary-gradient) !important; color: white !important; border-color: transparent; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3); }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; }
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.7rem; font-weight: 800; color: #94a3b8; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
    .object-fit-cover { object-fit: cover; }
</style>

<script>
document.getElementById('add-tm')?.addEventListener('click', function() {
    const container = document.getElementById('team-container');
    const div = document.createElement('div');
    div.className = 'team-row bg-light p-4 rounded-4 mb-3 border border-white position-relative shadow-inner';
    div.innerHTML = `
        <button type="button" class="btn btn-sm btn-white border rounded-circle text-danger position-absolute top-0 end-0 m-3 remove-tm shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-times"></i></button>
        <div class="row g-4 align-items-center">
            <div class="col-md-2 text-center text-md-start">
                <div class="avatar-preview bg-white rounded-circle shadow-sm mx-auto overflow-hidden border border-3 border-white" style="width: 80px; height: 80px;">
                    <i class="fas fa-user fa-3x mt-3 opacity-10"></i>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold extra-small text-muted uppercase-tracking">Full Name</label>
                <input type="text" name="t_names[]" class="form-control border-0 rounded-3 shadow-none bg-white" placeholder="e.g. John Doe">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold extra-small text-muted uppercase-tracking">Position</label>
                <input type="text" name="t_roles[]" class="form-control border-0 rounded-3 shadow-none bg-white" placeholder="e.g. Master Mechanic">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold extra-small text-muted uppercase-tracking">Upload Portrait</label>
                <input type="file" name="t_images[]" class="form-control form-control-sm border-0 rounded-pill bg-white px-3 py-2">
            </div>
        </div>
    `;
    container.appendChild(div);
    div.querySelector('.remove-tm').onclick = () => div.remove();
});

document.querySelectorAll('.remove-tm').forEach(btn => btn.onclick = () => btn.closest('.team-row').remove());
</script>
