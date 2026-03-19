<?php
/**
 * Global Footer Architecture & Social Asset Hub
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $footer_data = json_encode([
        "titles" => [
            "address" => $_POST['title_address'],
            "services" => $_POST['title_services'],
            "links" => $_POST['title_links'],
            "newsletter" => $_POST['title_newsletter']
        ],
        "newsletter_text" => $_POST['newsletter_text'],
        "newsletter_placeholder" => $_POST['newsletter_placeholder'],
        "newsletter_button" => $_POST['newsletter_button'],
        "copyright_text" => $_POST['copyright_text'],
        "social" => [
            "facebook" => $_POST['facebook'],
            "twitter" => $_POST['twitter'],
            "youtube" => $_POST['youtube'],
            "linkedin" => $_POST['linkedin'],
            "instagram" => $_POST['instagram']
        ],
        "quick_links" => [
            ["label" => $_POST['ql_label_1'], "url" => $_POST['ql_url_1']],
            ["label" => $_POST['ql_label_2'], "url" => $_POST['ql_url_2']],
            ["label" => $_POST['ql_label_3'], "url" => $_POST['ql_url_3']],
            ["label" => $_POST['ql_label_4'], "url" => $_POST['ql_url_4']],
            ["label" => $_POST['ql_label_5'], "url" => $_POST['ql_url_5']]
        ]
    ]);

    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('footer_data', ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    if ($stmt->execute([$footer_data])) {
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Base Synchronized:</b> Global footer parameters updated.</div>';
    }
}

// Fetch Current Data
$stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'footer_data'");
$stmt->execute();
$current = json_decode($stmt->fetchColumn() ?: '{}', true);

// Default values if empty
$defaults = [
    "titles" => [
        "address" => "Address",
        "services" => "Services",
        "links" => "Quick Links",
        "newsletter" => "Newsletter"
    ],
    "newsletter_text" => "Stay updated with our latest automotive service offers and tips.",
    "newsletter_placeholder" => "Your Email Address",
    "newsletter_button" => "Subscribe",
    "copyright_text" => "Boost Stride Professional Auto",
    "social" => [
        "facebook" => "#",
        "twitter" => "#",
        "youtube" => "#",
        "linkedin" => "#",
        "instagram" => "#"
    ],
    "quick_links" => [
        ["label" => "About Us", "url" => "about.php"],
        ["label" => "Contact Us", "url" => "contact.php"],
        ["label" => "Our Services", "url" => "service.php"],
        ["label" => "Terms", "url" => "terms.php"],
        ["label" => "Privacy", "url" => "privacy.php"]
    ]
];

$current = array_merge($defaults, $current);
?>

<div class="animate-fade-in">
    <!-- Sophisticated Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-4">
                    <div class="stat-icon bg-success bg-opacity-10 text-success shadow-sm" style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Footer Architect</h4>
                        <p class="text-muted small mb-0 font-monospace extra-small">Registry: site_foundation_v1</p>
                    </div>
                </div>
                <span class="badge bg-light text-dark rounded-pill px-3 py-2 border small fw-bold">
                    <i class="fas fa-layer-group me-1"></i> Foundation Active
                </span>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <form method="POST">
        <div class="row g-5">
            <!-- Content Strategy Canvas -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden mb-5">
                    <div class="card-header bg-dark text-white p-4 border-0 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 uppercase-tracking small">Foundation Column Identities</h6>
                        <i class="fas fa-columns text-muted"></i>
                    </div>
                    <div class="card-body p-5">
                        <div class="row g-4">
                            <div class="col-md-6 mb-3">
                                <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">COLUMN 1: ADDRESS HUB</label>
                                <input type="text" name="title_address" class="form-control border-0 bg-light py-3 rounded-3 fw-bold" value="<?php echo htmlspecialchars($current['titles']['address']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">COLUMN 2: SERVICE CATALOG</label>
                                <input type="text" name="title_services" class="form-control border-0 bg-light py-3 rounded-3 fw-bold" value="<?php echo htmlspecialchars($current['titles']['services']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">COLUMN 3: NAVIGATION REPOSITORY</label>
                                <input type="text" name="title_links" class="form-control border-0 bg-light py-3 rounded-3 fw-bold" value="<?php echo htmlspecialchars($current['titles']['links']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">COLUMN 4: CAPTURE CHANNEL</label>
                                <input type="text" name="title_newsletter" class="form-control border-0 bg-light py-3 rounded-3 fw-bold" value="<?php echo htmlspecialchars($current['titles']['newsletter']); ?>">
                            </div>
                        </div>

                        <div class="mt-5 pt-5 border-top">
                            <h6 class="fw-bold uppercase-tracking small text-muted mb-4"><i class="fas fa-paper-plane me-2"></i>Newsletter Strategic Copy</h6>
                            <div class="mb-4">
                                <label class="extra-small fw-bold text-muted mb-2 d-block">ENGAGEMENT HOOK</label>
                                <textarea name="newsletter_text" class="form-control border-0 bg-light p-4 rounded-3" rows="3"><?php echo htmlspecialchars($current['newsletter_text']); ?></textarea>
                            </div>
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <label class="extra-small fw-bold text-muted mb-2 d-block">INPUT HINT</label>
                                    <input type="text" name="newsletter_placeholder" class="form-control border-0 bg-light py-2 rounded-3" value="<?php echo htmlspecialchars($current['newsletter_placeholder']); ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="extra-small fw-bold text-muted mb-2 d-block">ACTION LABEL</label>
                                    <input type="text" name="newsletter_button" class="form-control border-0 bg-light py-2 rounded-3" value="<?php echo htmlspecialchars($current['newsletter_button']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-5 border-top">
                            <h6 class="fw-bold uppercase-tracking small text-muted mb-4"><i class="fas fa-link me-2"></i>Quick Navigation Nodes</h6>
                            <?php for($i=1; $i<=5; $i++): 
                                $link = $current['quick_links'][$i-1] ?? ['label' => '', 'url' => ''];
                            ?>
                            <div class="p-3 bg-light rounded-4 mb-3 border border-white shadow-sm">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-5">
                                        <input type="text" name="ql_label_<?php echo $i; ?>" class="form-control border-0 bg-white py-2 rounded-3 fw-bold small" value="<?php echo htmlspecialchars($link['label']); ?>" placeholder="Link Title">
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-0 text-muted px-2"><i class="fas fa-share-square"></i></span>
                                            <input type="text" name="ql_url_<?php echo $i; ?>" class="form-control border-0 bg-white py-2 rounded-end-3 font-monospace small" value="<?php echo htmlspecialchars($link['url']); ?>" placeholder="url.php">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Assets & Legal Side -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden mb-5">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0 uppercase-tracking small text-muted">Social Manifest</h6>
                    </div>
                    <div class="card-body p-5 bg-light bg-opacity-50">
                        <div class="mb-4">
                            <label class="extra-small fw-bold text-muted tracking-widest mb-3 d-block"><i class="fab fa-facebook text-primary me-2"></i>FACEBOOK PORTAL</label>
                            <input type="text" name="facebook" class="form-control border-0 bg-white shadow-sm py-2 rounded-3" value="<?php echo htmlspecialchars($current['social']['facebook']); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="extra-small fw-bold text-muted tracking-widest mb-3 d-block"><i class="fab fa-twitter text-info me-2"></i>X / TWITTER NODE</label>
                            <input type="text" name="twitter" class="form-control border-0 bg-white shadow-sm py-2 rounded-3" value="<?php echo htmlspecialchars($current['social']['twitter']); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="extra-small fw-bold text-muted tracking-widest mb-3 d-block"><i class="fab fa-youtube text-danger me-2"></i>YOUTUBE PIPELINE</label>
                            <input type="text" name="youtube" class="form-control border-0 bg-white shadow-sm py-2 rounded-3" value="<?php echo htmlspecialchars($current['social']['youtube']); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="extra-small fw-bold text-muted tracking-widest mb-3 d-block"><i class="fab fa-linkedin text-primary me-2"></i>LINKEDIN ENGINE</label>
                            <input type="text" name="linkedin" class="form-control border-0 bg-white shadow-sm py-2 rounded-3" value="<?php echo htmlspecialchars($current['social']['linkedin']); ?>">
                        </div>
                        <div class="mb-0">
                            <label class="extra-small fw-bold text-muted tracking-widest mb-3 d-block"><i class="fab fa-instagram text-warning me-2"></i>INSTAGRAM FEED</label>
                            <input type="text" name="instagram" class="form-control border-0 bg-white shadow-sm py-2 rounded-3" value="<?php echo htmlspecialchars($current['social']['instagram']); ?>">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden mb-5">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0 uppercase-tracking small text-muted">Legal Foundation</h6>
                    </div>
                    <div class="card-body p-5">
                        <div class="mb-0">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">COPYRIGHT SIGNATURE</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 px-3"><i class="fas fa-copyright"></i></span>
                                <input type="text" name="copyright_text" class="form-control border-0 bg-light py-2 rounded-end-3" value="<?php echo htmlspecialchars($current['copyright_text']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 text-primary p-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-shield-alt fa-2x"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Brand Integrity</h6>
                            <p class="small mb-0 opacity-75">Consistency in footer content helps anchor your user trust across all pages.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-pill shadow-lg fw-bold border-0">
                    <i class="fas fa-anchor me-2"></i>Deploy Base Architecture
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 2.5px; }
    .extra-small { font-size: 0.65rem; }
</style>
