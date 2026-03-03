<?php
/**
 * Admin Dashboard - Stats Overview
 */

use Core\Database;

$db = Database::getInstance();

// Stats Queries
$service_count = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
$testimonial_count = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
$recent_logs = $db->query("SELECT attempts, ip_address, last_attempt FROM login_attempts ORDER BY last_attempt DESC LIMIT 5")->fetchAll();

// System Info
$php_version = phpversion();
$mysql_version = $db->query("SELECT VERSION()")->fetchColumn();
?>

<div class="row g-4 mb-5">
    <!-- Stat Card 1 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small text-uppercase mb-1 fw-bold">Live Services</p>
                    <h2 class="fw-bold mb-0"><?php echo $service_count; ?></h2>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-tools fa-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-success small fw-bold"><i class="fas fa-arrow-up"></i> Active</span>
                <span class="text-muted small ms-1">ready for customers</span>
            </div>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small text-uppercase mb-1 fw-bold">Customer Feed</p>
                    <h2 class="fw-bold mb-0"><?php echo $testimonial_count; ?></h2>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-quote-right fa-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-success small fw-bold"><i class="fas fa-check-circle"></i> Social Proof</span>
                <span class="text-muted small ms-1">publicly visible</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-4 h-100 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small text-uppercase mb-1 fw-bold">System Status</p>
                    <h2 class="fw-bold mb-0 text-warning">Stable</h2>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-shield-alt fa-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-warning small fw-bold">PHP <?php echo $php_version; ?></span>
            </div>
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-4 h-100 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small text-uppercase mb-1 fw-bold">DB Version</p>
                    <h2 class="fw-bold mb-0 text-info">MySQL</h2>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-database fa-lg"></i>
                </div>
            </div>
            <div class="mt-3 text-truncate">
                <span class="text-info small fw-bold"><?php echo substr($mysql_version, 0, 15); ?>...</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Actions -->
    <div class="col-12 col-lg-8">
        <div class="card p-4 h-100 shadow-lg border-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Management Portal</h5>
                <span class="badge bg-light text-primary py-2 px-3 rounded-pill border border-primary border-opacity-25">Quick Access</span>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-4 hover-bg-light transition-all">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-tv"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Hero Experience</h6>
                        </div>
                        <p class="text-muted small">Update your landing page's first impression, titles and descriptions.</p>
                        <a href="?page=hero" class="btn btn-sm btn-outline-primary w-100 rounded-pill">Manage Hero</a>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-4 hover-bg-light transition-all">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Service Catalog</h6>
                        </div>
                        <p class="text-muted small">Add, remove or edit the automotive services you offer to clients.</p>
                        <a href="?page=services" class="btn btn-sm btn-outline-success w-100 rounded-pill">Manage Services</a>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-4 hover-bg-light transition-all">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                <i class="fas fa-globe"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">SEO Optimizer</h6>
                        </div>
                        <p class="text-muted small">Configure meta tags and descriptions for better search rankings.</p>
                        <a href="?page=seo" class="btn btn-sm btn-outline-info w-100 rounded-pill">Manage SEO</a>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded-4 hover-bg-light transition-all">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Core Settings</h6>
                        </div>
                        <p class="text-muted small">Adjust site name, contact info, and other fundamental settings.</p>
                        <a href="?page=settings" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">System Config</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Monitor -->
    <div class="col-12 col-lg-4">
        <div class="card p-4 h-100 shadow-lg border-0 bg-dark text-white overflow-hidden position-relative">
            <!-- Decorative circle -->
            <div class="position-absolute translate-middle" style="width: 200px; height: 200px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; top: 0; right: -50px;"></div>
            
            <h5 class="fw-bold mb-4 position-relative z-1">Security Sentinel</h5>
            
            <div class="mb-4 position-relative z-1">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-light text-opacity-75">Rate Limiting Status</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="progress" style="height: 6px; background: rgba(255,255,255,0.1);">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>

            <div class="position-relative z-1">
                <p class="small text-light text-opacity-50 mb-3 text-uppercase fw-bold">Recent Login Failures</p>
                <div class="list-group list-group-flush bg-transparent">
                    <?php if (empty($recent_logs)): ?>
                        <div class="text-light text-opacity-50 small py-5 text-center">
                            <i class="fas fa-check-circle fa-3x d-block mb-3 opacity-25"></i>
                            All systems secure.
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_logs as $log): ?>
                            <div class="list-group-item bg-transparent border-light border-opacity-10 px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold small text-white"><?php echo $log['ip_address']; ?></div>
                                        <div class="text-light text-opacity-50" style="font-size: 0.7rem;">
                                            <?php echo date('M d, H:i', $log['last_attempt']); ?>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger rounded-pill"><?php echo $log['attempts']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-auto pt-4 position-relative z-1">
                <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-10">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-lock text-primary"></i>
                        <span class="small fw-bold">Encryption Active</span>
                    </div>
                    <p class="mb-0" style="font-size: 0.75rem; color: rgba(255,255,255,0.6);">All admin sessions are protected with JWT & Secure HttpOnly cookies.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-all { transition: all 0.3s ease; }
    .hover-bg-light:hover { background-color: #f8fafc; border-color: #6366f1 !important; transform: scale(1.02); }
</style>
