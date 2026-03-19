<?php
/**
 * Professional Hero Experience Architect
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
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Site Narrative Synchronized:</b> The hero section has been deployed live.</div>';
    } else {
        $message = '<div class="alert alert-danger border-0 shadow-lg rounded-4"><i class="fas fa-exclamation-circle me-2"></i>Failed to deploy Hero updates.</div>';
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

<div class="animate-fade-in">
    <!-- Header Branding -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-4">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary shadow-sm" style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Hero Architect</h4>
                        <p class="text-muted small mb-0 font-monospace extra-small">Module: homepage_hero_v1</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="../index.php" target="_blank" class="btn btn-light rounded-pill px-3 py-2 small border text-muted fw-bold shadow-sm">
                        <i class="fas fa-external-link-alt me-1"></i>View Live
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row g-4 overflow-visible">
        <!-- Input Canvas -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 bg-white">
                <div class="card-header bg-dark text-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 small uppercase-tracking">Core Specifications</h6>
                    <i class="fas fa-sliders-h text-muted"></i>
                </div>
                <div class="card-body p-5">
                    <form method="POST">
                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">PROLOGUE / BADGE</label>
                            <input type="text" name="subtitle" class="form-control form-control-lg border-0 bg-light py-3 rounded-3 fw-bold" placeholder="Short intro phrase..." value="<?php echo htmlspecialchars($subtitle); ?>" required>
                            <div class="form-text mt-2 extra-small text-muted"><i class="fas fa-info-circle me-1"></i> Appears as the attention-grabber above the main headline.</div>
                        </div>
                        
                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">MAIN HEADLINE (H1)</label>
                            <input type="text" name="title" class="form-control form-control-lg border-0 bg-light py-3 rounded-3 fw-bold display-6" placeholder="Your brand's core mission..." value="<?php echo htmlspecialchars($title); ?>" required style="font-size: 1.8rem;">
                            <div class="form-text mt-2 extra-small text-muted"><i class="fas fa-info-circle me-1"></i> This is the most important text for search engines and visitors.</div>
                        </div>

                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">SUPPORTING NARRATIVE</label>
                            <textarea name="description" class="form-control border-0 bg-light p-4 rounded-3" rows="6" placeholder="Details about your excellence..." required style="line-height: 1.7;"><?php echo htmlspecialchars($description); ?></textarea>
                            <div class="form-text mt-2 extra-small text-muted"><i class="fas fa-info-circle me-1"></i> Keep this under 300 characters for maximum impact.</div>
                        </div>

                        <div class="pt-2 border-top">
                            <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-pill shadow-lg fw-bold border-0 mt-4">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Deploy Content Infrastructure
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Professional Preview -->
        <div class="col-12 col-lg-5">
            <div class="sticky-box">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0 small text-muted uppercase-tracking">Real-time Visualization</h6>
                    </div>
                    <div class="card-body p-0 position-relative" style="min-height: 500px; background: #f8fafc;">
                        <!-- Mock Web Browser -->
                        <div class="p-3 bg-white border-bottom d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-danger opacity-25" style="width: 8px; height: 8px;"></span>
                            <span class="rounded-circle bg-warning opacity-25" style="width: 8px; height: 8px;"></span>
                            <span class="rounded-circle bg-success opacity-25" style="width: 8px; height: 8px;"></span>
                            <div class="bg-light rounded-pill px-3 py-1 flex-grow-1 mx-3" style="font-size: 0.6rem; color: #94a3b8;">https://booststride.com/auto-shop</div>
                        </div>

                        <div class="p-5 d-flex flex-column justify-content-center h-100 position-relative overflow-hidden" style="margin-top: 50px;">
                            <!-- Abstract Background -->
                            <div class="position-absolute" style="top: -20px; right: -20px; opacity: 0.03;">
                                <i class="fas fa-car fa-10x"></i>
                            </div>

                            <div class="mb-5">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 mb-3 align-self-start shadow-sm" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    <?php echo htmlspecialchars($subtitle); ?>
                                </span>
                                <h1 class="fw-bold mb-3 display-4" style="color: #0f172a; line-height: 1.1; letter-spacing: -1px;"><?php echo htmlspecialchars($title); ?></h1>
                                <p class="text-muted mb-4 lead" style="font-size: 0.95rem; line-height: 1.6;"><?php echo htmlspecialchars($description); ?></p>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary rounded-pill px-4 btn-sm shadow-sm" style="font-size: 0.75rem; padding: 0.6rem 1.5rem;">Explore Services</button>
                                    <button class="btn btn-outline-dark rounded-pill px-4 btn-sm" style="font-size: 0.75rem; padding: 0.6rem 1.5rem;">Contact Us</button>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-top">
                                <div class="bg-indigo-soft text-indigo p-3 border-0 small rounded-3 opacity-75 d-flex align-items-start gap-2" style="background: #eef2ff; color: #4f46e5;">
                                    <i class="fas fa-magic mt-1"></i>
                                    <div>This render represents the live layout. Fonts and spacing may vary slightly based on global theme settings.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; color: #94a3b8; }
    .extra-small { font-size: 0.65rem; }
    .display-6 { font-weight: 800; letter-spacing: -1px; }
    .shadow-lg { box-shadow: 0 20px 40px -15px rgba(0,0,0,0.06) !important; }
    .btn-gradient { background: var(--primary-gradient); border: none; }
    .sticky-box { position: sticky; top: 1.5rem; }
</style>
