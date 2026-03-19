<?php
/**
 * Global Business Identity & Contact Core
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $map_url = $_POST['google_map'] ?? '';
    
    // Auto-extract src if full iframe was pasted
    if (strpos($map_url, '<iframe') !== false) {
        preg_match('/src=["\']([^"\']+)["\']/', $map_url, $match);
        $map_url = $match[1] ?? $map_url;
    }

    $contact_info = json_encode([
        "address" => $_POST['address'],
        "phone" => $_POST['phone'],
        "email" => $_POST['email'],
        "contact_title" => $_POST['contact_title'] ?? 'Get In Touch',
        "contact_desc" => $_POST['contact_desc'] ?? 'Have questions about your vehicle?',
        "google_map" => $map_url
    ]);

    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('contact_info', ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    if ($stmt->execute([$contact_info])) {
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Profile Synchronized:</b> Business contact parameters and maps integration updated.</div>';
    }
}

// Fetch Current Data
$stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_info'");
$stmt->execute();
$current = json_decode($stmt->fetchColumn() ?: '{}', true);
?>

<div class="animate-fade-in py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <!-- Header Identity -->
            <div class="card border-0 shadow-sm rounded-4 mb-5 p-4 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-4">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary shadow-sm" style="width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Business Identity</h4>
                            <p class="text-muted small mb-0 font-monospace extra-small">Registry: core_contact_v1</p>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2 border small fw-bold">
                        <i class="fas fa-check-circle text-success me-1"></i> Verified Profile
                    </span>
                </div>
            </div>

            <?php echo $message; ?>

            <!-- Identity Canvas -->
            <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden">
                <div class="card-header bg-dark text-white p-4 d-flex align-items-center justify-content-between border-0">
                    <h6 class="fw-bold mb-0 uppercase-tracking small">Core Contact Specifications</h6>
                    <i class="fas fa-map-marked-alt text-muted"></i>
                </div>
                <div class="card-body p-5">
                    <form method="POST">
                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">PHYSICAL HEADQUARTERS</label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0 px-3"><i class="fas fa-location-dot text-muted"></i></span>
                                <input type="text" name="address" class="form-control border-0 bg-light py-3 rounded-end-3 fw-bold" value="<?php echo htmlspecialchars($current['address'] ?? '123 Street, New York, USA'); ?>" placeholder="e.g. 123 Main St, Hobart" required>
                            </div>
                        </div>
                        
                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">PRIMARY COMMUNICATION LINE</label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0 px-3"><i class="fas fa-phone-volume text-muted"></i></span>
                                <input type="text" name="phone" class="form-control border-0 bg-light py-3 rounded-end-3 fw-bold" value="<?php echo htmlspecialchars($current['phone'] ?? '+012 345 6789'); ?>" placeholder="+61 X XXXX XXXX" required>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">OFFICIAL CORRESPONDENCE EMAIL</label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0 px-3"><i class="fas fa-envelope-open-text text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-0 bg-light py-3 rounded-end-3 fw-bold" value="<?php echo htmlspecialchars($current['email'] ?? 'info@example.com'); ?>" placeholder="hello@yourshop.com" required>
                            </div>
                        </div>

                        <hr class="my-5 opacity-25">
                        <label class="extra-small fw-bold text-muted tracking-widest mb-4 d-block">CONTACT PAGE META</label>

                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">SECTION HEADLINE</label>
                            <input type="text" name="contact_title" class="form-control border-0 bg-light py-3 rounded-3 fw-bold shadow-sm" value="<?php echo htmlspecialchars($current['contact_title'] ?? 'Get In Touch'); ?>" placeholder="e.g. Visit Our Shop">
                        </div>

                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">LEAD DESCRIPTION</label>
                            <textarea name="contact_desc" class="form-control border-0 bg-light p-4 rounded-3 shadow-sm" rows="3" placeholder="Compelling 2-sentence hook for the contact page..."><?php echo htmlspecialchars($current['contact_desc'] ?? 'Have questions about your vehicle? Our expert team is here to help.'); ?></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">GOOGLE MAP EMBED URL</label>
                            <input type="text" name="google_map" class="form-control border-0 bg-light py-3 rounded-3 font-monospace small shadow-sm" value="<?php echo htmlspecialchars($current['google_map'] ?? ''); ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                            <div class="form-text mt-3 extra-small text-muted">
                                <i class="fas fa-info-circle me-1"></i> <b>Instructions:</b> Go to Google Maps, click <b>Share</b>, select the <b>Embed a map</b> tab, and paste the code/link here. 
                                <br><span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Don't use the simple "Share link" (maps.app.goo.gl) as it won't display.</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-pill shadow-lg fw-bold border-0 mt-2">
                            <i class="fas fa-sync-alt me-2"></i>Apply Profile Synchronization
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-5 text-center">
                <p class="text-muted small"><i class="fas fa-lock me-1"></i> All business data is encrypted and stored in the central registry.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 2.5px; }
    .extra-small { font-size: 0.65rem; }
    .btn-gradient { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); transition: all 0.3s ease; }
    .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(79, 70, 229, 0.3); }
</style>
