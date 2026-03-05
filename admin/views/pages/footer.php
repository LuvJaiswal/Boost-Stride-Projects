<?php
/**
 * Footer Content Manager
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
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>Footer settings updated successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="fas fa-exclamation-circle me-2"></i>Failed to update footer settings.</div>';
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
    "newsletter_text" => "Dolor amet sit justo amet elitr clita ipsum elitr est.",
    "newsletter_placeholder" => "Your email",
    "newsletter_button" => "SignUp",
    "copyright_text" => "Boost Stride",
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
        ["label" => "Terms & Condition", "url" => "terms.php"],
        ["label" => "Support", "url" => "support.php"]
    ]
];

$current = array_merge($defaults, $current);
?>

<div class="row">
    <div class="col-12">
        <div class="card p-4 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-shoe-prints"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Footer Content Management</h4>
                    <p class="text-muted small mb-0">Customize all textual and link content displayed at the bottom of your website.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <?php echo $message; ?>
        <form method="POST" class="row">
            <!-- General Content -->
            <div class="col-lg-6 mb-4">
                <div class="card p-4 h-100 border-0 shadow-sm">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-heading me-2"></i>Column Titles</h5>
                    <div class="row row-cols-1 row-cols-sm-2 g-3">
                        <div class="col mb-2">
                            <label class="form-label fw-bold extra-small text-muted">ADDRESS TITLE</label>
                            <input type="text" name="title_address" class="form-control" value="<?php echo htmlspecialchars($current['titles']['address']); ?>" required>
                        </div>
                        <div class="col mb-2">
                            <label class="form-label fw-bold extra-small text-muted">SERVICES TITLE</label>
                            <input type="text" name="title_services" class="form-control" value="<?php echo htmlspecialchars($current['titles']['services']); ?>" required>
                        </div>
                        <div class="col mb-2">
                            <label class="form-label fw-bold extra-small text-muted">QUICK LINKS TITLE</label>
                            <input type="text" name="title_links" class="form-control" value="<?php echo htmlspecialchars($current['titles']['links']); ?>" required>
                        </div>
                        <div class="col mb-2">
                            <label class="form-label fw-bold extra-small text-muted">NEWSLETTER TITLE</label>
                            <input type="text" name="title_newsletter" class="form-control" value="<?php echo htmlspecialchars($current['titles']['newsletter']); ?>" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4 mt-5 text-primary"><i class="fas fa-paper-plane me-2"></i>Newsletter Details</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold extra-small text-muted">DESCRIPTION</label>
                        <textarea name="newsletter_text" class="form-control" rows="3" required><?php echo htmlspecialchars($current['newsletter_text']); ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6 mb-2">
                            <label class="form-label fw-bold extra-small text-muted">PLACEHOLDER</label>
                            <input type="text" name="newsletter_placeholder" class="form-control" value="<?php echo htmlspecialchars($current['newsletter_placeholder']); ?>" required>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label fw-bold extra-small text-muted">BUTTON TEXT</label>
                            <input type="text" name="newsletter_button" class="form-control" value="<?php echo htmlspecialchars($current['newsletter_button']); ?>" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4 mt-5 text-primary"><i class="fas fa-align-left me-2"></i>Misc Content</h5>
                    <div class="mb-0">
                        <label class="form-label fw-bold extra-small text-muted">COPYRIGHT BRAND NAME</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="fas fa-copyright"></i></span>
                            <input type="text" name="copyright_text" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($current['copyright_text']); ?>" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4 mt-5 text-primary"><i class="fas fa-link me-2"></i>Quick Links Management</h5>
                    <p class="text-muted small mb-4">Define up to 5 custom links for your footer.</p>
                    <?php for($i=1; $i<=5; $i++): 
                        $link = $current['quick_links'][$i-1] ?? ['label' => '', 'url' => ''];
                    ?>
                    <div class="bg-light p-3 rounded-4 mb-3 border border-white">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-sm-5">
                                <label class="form-label fw-bold extra-small text-muted">LINK <?php echo $i; ?> LABEL</label>
                                <input type="text" name="ql_label_<?php echo $i; ?>" class="form-control" value="<?php echo htmlspecialchars($link['label']); ?>" placeholder="e.g. About Us">
                            </div>
                            <div class="col-12 col-sm-7">
                                <label class="form-label fw-bold extra-small text-muted">LINK <?php echo $i; ?> URL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted px-3"><i class="fas fa-link"></i></span>
                                    <input type="text" name="ql_url_<?php echo $i; ?>" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($link['url']); ?>" placeholder="e.g. about.php">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="col-lg-6 mb-4">
                <div class="card p-4 h-100 border-0 shadow-sm">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-share-alt me-2"></i>Social Media Presence</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">FACEBOOK URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-primary"><i class="fab fa-facebook-f"></i></span>
                            <input type="text" name="facebook" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($current['social']['facebook']); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">TWITTER URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-info"><i class="fab fa-twitter"></i></span>
                            <input type="text" name="twitter" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($current['social']['twitter']); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">YOUTUBE URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-danger"><i class="fab fa-youtube"></i></span>
                            <input type="text" name="youtube" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($current['social']['youtube']); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">LINKEDIN URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-primary"><i class="fab fa-linkedin-in"></i></span>
                            <input type="text" name="linkedin" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($current['social']['linkedin']); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">INSTAGRAM URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-warning"><i class="fab fa-instagram"></i></span>
                            <input type="text" name="instagram" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($current['social']['instagram']); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-3 shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Footer Configurations
                </button>
            </div>
        </form>
    </div>
</div>
