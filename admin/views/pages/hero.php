<?php
/**
 * Hero Section Editor
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_data = json_encode([
        "subtitle" => $_POST['subtitle'],
        "title" => $_POST['title'],
        "description" => $_POST['description']
    ]);

    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('hero_data', ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    if ($stmt->execute([$hero_data])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Hero Section updated successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="fas fa-exclamation-circle me-2"></i>Failed to update Hero Section.</div>';
    }
}

// Fetch Current Data
$stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'hero_data'");
$stmt->execute();
$current = json_decode($stmt->fetchColumn() ?: '{}', true);

$subtitle = $current['subtitle'] ?? 'Welcome To Boost Stride';
$title = $current['title'] ?? 'Best Automotive & Maintenance Services';
$description = $current['description'] ?? 'Vero elitr justo clita lorem. Ipsum dolor at sed stet sit diam no.';
?>

<div class="row g-4">
    <div class="col-12 col-lg-7">
        <div class="card p-4 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-window-maximize"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Hero Experience</h4>
                    <p class="text-muted small mb-0">Control the first thing users see on your homepage.</p>
                </div>
            </div>
            
            <?php echo $message; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Subtitle / Badge</label>
                    <input type="text" name="subtitle" class="form-control rounded-3 py-2" placeholder="Short intro text" value="<?php echo htmlspecialchars($subtitle); ?>" required>
                    <div class="form-text">Appears as a small highlighted text above the main title.</div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Main Headline</label>
                    <input type="text" name="title" class="form-control rounded-3 py-2 fw-bold" placeholder="Catchy headline" value="<?php echo htmlspecialchars($title); ?>" required>
                    <div class="form-text">The largest text in the hero section.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Supporting Description</label>
                    <textarea name="description" class="form-control rounded-3 py-2" rows="5" placeholder="Elaborate on your service..." required><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn btn-gradient w-100 py-3 shadow-lg">
                        <i class="fas fa-save me-2"></i>Apply Changes to Website
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Preview Emulation -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm overflow-hidden h-100">
            <div class="bg-light p-3 border-bottom d-flex align-items-center gap-2">
                <div class="badge bg-danger p-1 rounded-circle" style="width: 10px; height: 10px;"></div>
                <div class="badge bg-warning p-1 rounded-circle" style="width: 10px; height: 10px;"></div>
                <div class="badge bg-success p-1 rounded-circle" style="width: 10px; height: 10px;"></div>
                <span class="small text-muted ms-2 fw-bold">Visitor View Preview</span>
            </div>
            <div class="p-5 d-flex flex-column justify-content-center h-100 bg-white position-relative overflow-hidden">
                <!-- Abstract Background -->
                <div class="position-absolute" style="top: -20px; right: -20px; opacity: 0.05;">
                    <i class="fas fa-car fa-10x"></i>
                </div>

                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 mb-3 align-self-start shadow-sm">
                    <?php echo htmlspecialchars($subtitle); ?>
                </span>
                <h1 class="fw-bold mb-3 display-6" style="color: #0f172a;"><?php echo htmlspecialchars($title); ?></h1>
                <p class="text-muted mb-4 lead" style="font-size: 1rem;"><?php echo htmlspecialchars($description); ?></p>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-4 btn-sm disabled">Read More</button>
                    <button class="btn btn-outline-primary rounded-pill px-4 btn-sm disabled">Our Services</button>
                </div>

                <div class="mt-5 pt-4 border-top">
                    <div class="alert alert-info py-2 px-3 border-0 small rounded-3 opacity-75">
                        <i class="fas fa-info-circle me-1"></i> This is a preview of how the content might look on the frontend.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
