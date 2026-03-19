<?php
/**
 * Admin Dashboard - Stats Overview
 */

use Core\Database;

$db = Database::getInstance();

// Stats Queries
$service_count = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
$testimonial_count = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
$pages_count = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$recent_logs = $db->query("SELECT attempts, ip_address, last_attempt FROM login_attempts ORDER BY last_attempt DESC LIMIT 5")->fetchAll();

// System Info
$php_version = phpversion();
$mysql_version = $db->query("SELECT VERSION()")->fetchColumn();
?>

<div class="row g-4 mb-5">
    <!-- Stat Card 1 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 p-4 h-100 position-relative overflow-hidden group">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 group-hover:opacity-20 transition-all">
                <i class="fas fa-tools fa-4x text-primary"></i>
            </div>
            <p class="text-muted small text-uppercase mb-1 fw-bold letter-spacing-1">Live Services</p>
            <h2 class="fw-bold mb-2 display-6"><?php echo $service_count; ?></h2>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill extra-small px-2">
                    <i class="fas fa-arrow-up me-1"></i> Active
                </span>
                <span class="text-muted extra-small">Ready for public view</span>
            </div>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 p-4 h-100 position-relative overflow-hidden group">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 group-hover:opacity-20 transition-all">
                <i class="fas fa-file-invoice fa-4x text-purple"></i>
            </div>
            <p class="text-muted small text-uppercase mb-1 fw-bold letter-spacing-1">Total Pages</p>
            <h2 class="fw-bold mb-2 display-6"><?php echo $pages_count; ?></h2>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-purple bg-opacity-10 text-purple rounded-pill extra-small px-2">
                    <i class="fas fa-link me-1"></i> Connected
                </span>
                <span class="text-muted extra-small">Site architecture assets</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 p-4 h-100 position-relative overflow-hidden group">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 group-hover:opacity-20 transition-all">
                <i class="fas fa-star fa-4x text-warning"></i>
            </div>
            <p class="text-muted small text-uppercase mb-1 fw-bold letter-spacing-1">Social Proof</p>
            <h2 class="fw-bold mb-2 display-6"><?php echo $testimonial_count; ?></h2>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill extra-small px-2">
                    <i class="fas fa-check me-1"></i> Verified
                </span>
                <span class="text-muted extra-small">Customer testimonials</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 p-4 h-100 position-relative overflow-hidden group bg-dark text-white shadow-dark">
            <p class="text-light text-opacity-50 small text-uppercase mb-1 fw-bold letter-spacing-1">Engine Version</p>
            <h2 class="fw-bold mb-2 display-6 text-white">PHP <?php echo explode('.', $php_version)[0] . '.' . explode('.', $php_version)[1]; ?></h2>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-pill extra-small px-2">
                    <i class="fas fa-shield-check me-1"></i> Secure
                </span>
                <span class="text-light text-opacity-50 extra-small">Optimized for cPanel</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Management -->
    <div class="col-12 col-lg-8">
        <div class="card p-4 h-100 border-0 shadow-lg mb-4">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h5 class="fw-bold mb-1">Recent Customer Leads</h5>
                    <p class="text-muted small mb-0">Latest inquiries from your website contact form.</p>
                </div>
                <a href="?page=leads" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">View All Leads</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="border-0 small text-muted text-uppercase fw-bold letter-spacing-1 py-3 px-4">Contact</th>
                            <th class="border-0 small text-muted text-uppercase fw-bold letter-spacing-1 py-3">Subject</th>
                            <th class="border-0 small text-muted text-uppercase fw-bold letter-spacing-1 py-3 text-end px-4">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        try {
                            $table_check = $db->query("SHOW TABLES LIKE 'leads'")->fetch();
                            $recent_leads = $table_check ? $db->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 4")->fetchAll() : [];
                            
                            if (empty($recent_leads)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted small">
                                        <?php echo !$table_check ? 'Lead management table not found. Please sync database.' : 'No inquiries yet.'; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_leads as $lead): ?>
                                    <tr>
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                                    <?php echo strtoupper(substr($lead['name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold small"><?php echo htmlspecialchars($lead['name']); ?></div>
                                                    <div class="extra-small text-muted"><?php echo htmlspecialchars($lead['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 small text-secondary">
                                            <?php echo htmlspecialchars($lead['subject']); ?>
                                            <?php if($lead['status'] === 'new'): ?>
                                                <span class="badge bg-danger ms-2 rounded-pill" style="font-size: 0.5rem; padding: 0.2rem 0.5rem;">NEW</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 text-end px-4">
                                            <span class="extra-small text-muted"><?php echo date('M d, H:i', strtotime($lead['created_at'])); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; 
                        } catch (Exception $e) {
                            echo '<tr><td colspan="3" class="text-center py-3 text-danger small">Error loading leads.</td></tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card p-4 border-0 shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h5 class="fw-bold mb-1">Management Portal</h5>
                    <p class="text-muted small mb-0">Direct access to core website modules.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="p-4 border rounded-4 hover-shadow transition-all bg-light bg-opacity-50 border-white h-100">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="fas fa-window-restore"></i>
                        </div>
                        <h6 class="fw-bold">Hero Experience</h6>
                        <p class="text-muted extra-small mb-4">Update titles, subtitles and main call-to-action sections on your landing page.</p>
                        <a href="?page=hero" class="btn btn-white border shadow-sm w-100 rounded-pill btn-sm fw-bold">Open Editor</a>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="p-4 border rounded-4 hover-shadow transition-all bg-light bg-opacity-50 border-white h-100">
                        <div class="stat-icon bg-success bg-opacity-10 text-success mb-3">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h6 class="fw-bold">Service Pipeline</h6>
                        <p class="text-muted extra-small mb-4">Manage the full catalog of automotive services and maintenance options.</p>
                        <a href="?page=services" class="btn btn-white border shadow-sm w-100 rounded-pill btn-sm fw-bold">Manage Catalog</a>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="p-4 border rounded-4 hover-shadow transition-all bg-light bg-opacity-50 border-white h-100">
                        <div class="stat-icon bg-info bg-opacity-10 text-info mb-3">
                            <i class="fas fa-search-plus"></i>
                        </div>
                        <h6 class="fw-bold">SEO Optimizer</h6>
                        <p class="text-muted extra-small mb-4">Configure search engine meta tags and site-wide descriptions for better reach.</p>
                        <a href="?page=seo" class="btn btn-white border shadow-sm w-100 rounded-pill btn-sm fw-bold">Configure Tags</a>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="p-4 border rounded-4 hover-shadow transition-all bg-light bg-opacity-50 border-white h-100">
                        <div class="stat-icon bg-dark bg-opacity-10 text-dark mb-3">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h6 class="fw-bold">Global Navigation</h6>
                        <p class="text-muted extra-small mb-4">Adjust the multi-level menu and footer quick-links for better navigation flow.</p>
                        <a href="?page=menu" class="btn btn-white border shadow-sm w-100 rounded-pill btn-sm fw-bold">Edit Hierarchy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Monitor -->
    <div class="col-12 col-lg-4">
        <div class="card p-0 border-0 h-100 bg-dark text-white shadow-dark overflow-hidden">
            <div class="p-4 bg-primary bg-opacity-10 border-bottom border-white border-opacity-10">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-shield-check text-primary"></i> Security Sentinel
                </h5>
            </div>
            
            <div class="p-4">
                <div class="mb-5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-light text-opacity-75">JWT Session Security</span>
                        <span class="badge bg-success badge-premium">Active</span>
                    </div>
                    <div class="progress" style="height: 4px; background: rgba(255,255,255,0.05);">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>

                <p class="extra-small text-uppercase fw-bold text-light text-opacity-50 mb-3 letter-spacing-1">Recent Login Attempts</p>
                <div class="list-group list-group-flush">
                    <?php if (empty($recent_logs)): ?>
                        <div class="text-center py-5 opacity-50">
                            <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                            <p class="small mb-0">No unauthorized attempts detected.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_logs as $log): ?>
                            <div class="list-group-item bg-transparent border-white border-opacity-10 px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold small"><?php echo $log['ip_address']; ?></div>
                                        <div class="text-light text-opacity-40 extra-small">
                                            <?php echo date('M d, H:i', $log['last_attempt']); ?>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger rounded-pill px-2 py-1"><?php echo $log['attempts']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mt-5 p-3 rounded-4 border border-white border-opacity-10 bg-white bg-opacity-5">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-database text-warning"></i>
                        <div>
                            <div class="extra-small text-light text-opacity-40 fw-bold">DATABASE CONNECTED</div>
                            <div class="small fw-bold">MySQL <?php echo substr($mysql_version, 0, 7); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-purple { color: #7c3aed; }
    .bg-purple { background-color: #7c3aed; }
    .shadow-dark { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3) !important; }
    .hover-shadow:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); transform: translateY(-5px); }
    .transition-all { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .letter-spacing-1 { letter-spacing: 0.5px; }
</style>
