<?php
/**
 * System Settings Manager
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_info = json_encode([
        "address" => $_POST['address'],
        "phone" => $_POST['phone'],
        "email" => $_POST['email']
    ]);

    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('contact_info', ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    if ($stmt->execute([$contact_info])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>System settings saved!</div>';
    }
}

// Fetch Current Data
$stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_info'");
$stmt->execute();
$current = json_decode($stmt->fetchColumn() ?: '{}', true);
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="card p-4 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Business Identity</h4>
                    <p class="text-muted small mb-0">Manage how customers contact and find your shop.</p>
                </div>
            </div>
            
            <?php echo $message; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">SHOP PHYSICAL ADDRESS</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-map-marker-alt text-muted"></i></span>
                        <input type="text" name="address" class="form-control rounded-end-3 py-2" value="<?php echo htmlspecialchars($current['address'] ?? '123 Street, New York, USA'); ?>" placeholder="e.g. 123 Main St, Hobart" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">PRIMARY PHONE NUMBER</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-phone text-muted"></i></span>
                        <input type="text" name="phone" class="form-control rounded-end-3 py-2" value="<?php echo htmlspecialchars($current['phone'] ?? '+012 345 6789'); ?>" placeholder="+61 X XXXX XXXX" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold small text-muted">OFFICIAL EMAIL ADDRESS</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control rounded-end-3 py-2" value="<?php echo htmlspecialchars($current['email'] ?? 'info@example.com'); ?>" placeholder="hello@yourshop.com" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-secondary w-100 py-3 rounded-3 fw-bold">
                    <i class="fas fa-sync-alt me-2"></i>Update Business Profile
                </button>
            </form>
        </div>
    </div>
</div>
