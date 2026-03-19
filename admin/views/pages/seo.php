<?php
/**
 * Search Engine Visibility Architect
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
        $message = '<div class="alert alert-success border-0 shadow-lg rounded-4 animate-fade-in"><i class="fas fa-check-circle me-2"></i><b>Search Strategy Deployed:</b> Global Meta-data updated.</div>';
    }
}

// Fetch Current Data
$stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'seo_data'");
$stmt->execute();
$current = json_decode($stmt->fetchColumn() ?: '{}', true);

$title = $current['title'] ?? 'Boost Stride - Professional Auto Shop & Car Maintenance';
$description = $current['description'] ?? 'Expert automotive repair services in Hobart. High-quality maintenance, professional mechanics, and affordable pricing for all vehicle types.';
$keywords = $current['keywords'] ?? 'car repair, auto shop, Hobart mechanics, vehicle maintenance';
?>

<div class="animate-fade-in">
    <!-- Header -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 p-4 bg-white">
        <div class="d-flex align-items-center gap-4">
            <div class="stat-icon bg-info bg-opacity-10 text-info shadow-sm" style="width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-search-dollar"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">Search Engine Architect</h4>
                <p class="text-muted small mb-0 font-monospace extra-small">Module: global_seo_v1</p>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row g-5">
        <!-- Editor Canvas -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden">
                <div class="card-header bg-dark text-white p-4 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 uppercase-tracking small">Global Meta-data Specifications</h6>
                    <i class="fas fa-code text-muted"></i>
                </div>
                <div class="card-body p-5">
                    <form method="POST">
                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-end mb-3">
                                <label class="extra-small fw-bold text-primary tracking-widest d-block">META TITLE (SERP HEADLINE)</label>
                                <span class="extra-small text-muted font-monospace"><span id="title-count">0</span> / 60</span>
                            </div>
                            <input type="text" name="title" id="seo-title" class="form-control border-0 bg-light py-3 rounded-3 fw-bold shadow-none" value="<?php echo htmlspecialchars($title); ?>" maxlength="70">
                            <div class="form-text mt-2 extra-small text-muted"><i class="fas fa-info-circle me-1"></i> This title appears in the browser tab and search results.</div>
                        </div>

                        <div class="mb-5">
                            <label class="extra-small fw-bold text-primary tracking-widest mb-3 d-block">STRATEGIC KEYWORDS</label>
                            <input type="text" name="keywords" class="form-control border-0 bg-light py-3 rounded-3 shadow-none" value="<?php echo htmlspecialchars($keywords); ?>" placeholder="e.g. car repair, auto shop, Hobart">
                            <div class="form-text mt-2 extra-small text-muted"><i class="fas fa-tags me-1"></i> Comma-separated industry terms.</div>
                        </div>

                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-end mb-3">
                                <label class="extra-small fw-bold text-primary tracking-widest d-block">META DESCRIPTION (SUMMARY)</label>
                                <span class="extra-small text-muted font-monospace"><span id="desc-count">0</span> / 160</span>
                            </div>
                            <textarea name="description" id="seo-desc" class="form-control border-0 bg-light p-4 rounded-3 shadow-none" rows="6" style="line-height: 1.6;"><?php echo htmlspecialchars($description); ?></textarea>
                            <div class="form-text mt-2 extra-small text-muted"><i class="fas fa-eye me-1"></i> The snippet that convinces users to click in search results.</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-gradient w-100 py-3 rounded-pill shadow-lg fw-bold border-0">
                            <i class="fas fa-rocket me-2"></i>Apply Search Optimization
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Google Preview Simulator -->
        <div class="col-lg-5">
            <div class="sticky-box">
                <div class="card border-0 shadow-lg rounded-5 bg-white overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0 uppercase-tracking small text-muted text-center">Google Search Preview</h6>
                    </div>
                    <div class="card-body p-5 bg-light bg-opacity-50">
                        <!-- Google Simulator -->
                        <div class="google-result p-4 bg-white rounded-4 border shadow-sm">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div class="bg-light rounded-circle" style="width: 24px; height: 24px;"></div>
                                <div class="extra-small text-muted mb-0">https://booststride.com <i class="fas fa-caret-down"></i></div>
                            </div>
                            <h5 id="preview-title" class="mb-1 fw-normal" style="color: #1a0dab; font-family: arial, sans-serif; cursor: pointer; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($title); ?>
                            </h5>
                            <p id="preview-desc" class="mb-0 small" style="color: #4d5156; font-family: arial, sans-serif; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo htmlspecialchars($description); ?>
                            </p>
                        </div>
                        
                        <div class="mt-5 pt-4 border-top">
                            <div class="alert alert-warning border-0 small rounded-4 p-4 mb-0 opacity-75">
                                <h6 class="fw-bold extra-small uppercase-tracking mb-3"><i class="fas fa-lightbulb me-2"></i>SEO PRO-TIP</h6>
                                <p class="mb-0">Keep titles under 60 characters and descriptions under 160 to avoid truncation (the "...") in search results.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 text-primary p-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-chart-line fa-2x"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Organic Reach</h6>
                            <p class="small mb-0 opacity-75">Proper meta-data can improve Click-Through-Rate (CTR) by up to 20%.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const titleInput = document.getElementById('seo-title');
    const descInput = document.getElementById('seo-desc');
    const previewTitle = document.getElementById('preview-title');
    const previewDesc = document.getElementById('preview-desc');
    const titleCount = document.getElementById('title-count');
    const descCount = document.getElementById('desc-count');

    function updatePreview() {
        previewTitle.innerText = titleInput.value || 'Your Page Title Meta';
        previewDesc.innerText = descInput.value || 'Add a meta description to see how your site will appear in Google searches.';
        
        titleCount.innerText = titleInput.value.length;
        descCount.innerText = descInput.value.length;

        titleCount.className = titleInput.value.length > 60 ? 'text-danger fw-bold' : 'text-muted';
        descCount.className = descInput.value.length > 160 ? 'text-danger fw-bold' : 'text-muted';
    }

    titleInput.addEventListener('input', updatePreview);
    descInput.addEventListener('input', updatePreview);
    updatePreview();
</script>

<style>
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 2.5px; }
    .extra-small { font-size: 0.65rem; }
    .sticky-box { position: sticky; top: 1.5rem; }
</style>
