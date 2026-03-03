<?php
/**
 * SEO & Meta Manager
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seo_data = json_encode([
        "title" => $_POST['title'],
        "keywords" => $_POST['keywords'],
        "description" => $_POST['description']
    ]);

    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('seo_data', ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    if ($stmt->execute([$seo_data])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fas fa-check-circle me-2"></i>SEO Settings updated successfully!</div>';
    }
}

// Fetch Current Data
$stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'seo_data'");
$stmt->execute();
$current = json_decode($stmt->fetchColumn() ?: '{}', true);
?>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card p-4 shadow-sm border-0 h-100">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-search-dollar"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Search Engine Optimization</h4>
                    <p class="text-muted small mb-0">Optimize your visibility on Google, Bing, and Social Media.</p>
                </div>
            </div>
            
            <?php echo $message; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">META TITLE</label>
                    <input type="text" name="title" class="form-control rounded-3 py-2" value="<?php echo htmlspecialchars($current['title'] ?? 'Boost Stride - Professional Auto Shop'); ?>" placeholder="The title shown in search results">
                    <div class="form-text small">Recommended: 50-60 characters.</div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">SEO KEYWORDS</label>
                    <input type="text" name="keywords" class="form-control rounded-3 py-2" value="<?php echo htmlspecialchars($current['keywords'] ?? ''); ?>" placeholder="car repair, auto shop, maintenance, Hobart mechanics">
                    <div class="form-text small">Separate keywords with commas.</div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold small text-muted">META DESCRIPTION</label>
                    <textarea name="description" class="form-control rounded-3 py-2" rows="5" placeholder="A brief summary of your shop for search engines..."><?php echo htmlspecialchars($current['description'] ?? ''); ?></textarea>
                    <div class="form-text small">Recommended: 150-160 characters.</div>
                </div>

                <button type="submit" class="btn btn-info text-white w-100 py-3 rounded-3 fw-bold shadow-sm">
                    <i class="fas fa-rocket me-2"></i>Apply SEO Optimization
                </button>
            </form>
        </div>
    </div>

    <!-- SEO Tips -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm bg-light p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>SEO Best Practices</h6>
            <ul class="list-group list-group-flush bg-transparent">
                <li class="list-group-item bg-transparent px-0 py-3 border-0">
                    <div class="fw-bold small mb-1">Unique Titles</div>
                    <p class="small text-muted mb-0">Ensure every page has a unique title that describes the content accurately.</p>
                </li>
                <li class="list-group-item bg-transparent px-0 py-3 border-0">
                    <div class="fw-bold small mb-1">Keywords Balance</div>
                    <p class="small text-muted mb-0">Don't overstuff keywords. Use natural phrases that customers actually search for.</p>
                </li>
                <li class="list-group-item bg-transparent px-0 py-3 border-0">
                    <div class="fw-bold small mb-1">Engaging Descriptions</div>
                    <p class="small text-muted mb-0">The meta description is your ad in search results. Make it compelling to Click!</p>
                </li>
            </ul>
            
            <div class="mt-auto pt-4">
                <div class="p-3 rounded-4 bg-white border border-primary border-opacity-10 text-center">
                    <i class="fas fa-chart-line text-primary mb-2 fa-2x"></i>
                    <p class="small mb-0 fw-bold">Boost your Rank</p>
                    <p class="text-muted" style="font-size: 0.75rem;">Quality content and proper SEO tags lead to more organic customers.</p>
                </div>
            </div>
        </div>
    </div>
</div>
