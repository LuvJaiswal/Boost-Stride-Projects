<?php
/**
 * Customer Leads Management - Boost Stride
 */

use Core\Database;

$db = Database::getInstance();
$message = '';

// Handle Status Update
if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $stmt = $db->prepare("UPDATE leads SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4 animate-fade-in">Lead status updated successfully!</div>';
    }
}

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM leads WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = '<div class="alert alert-success border-0 shadow-sm rounded-4 animate-fade-in">Lead record removed.</div>';
    }
}

// Fetch Leads
$leads = [];
try {
    $table_check = $db->query("SHOW TABLES LIKE 'leads'")->fetch();
    if ($table_check) {
        $leads = $db->query("SELECT * FROM leads ORDER BY created_at DESC")->fetchAll();
    } else {
        $message = '<div class="alert alert-warning border-0 shadow-sm rounded-4"><i class="fas fa-exclamation-triangle me-2"></i><strong>Database Sync Required:</strong> The "leads" table was not found. Please import the latest database_setup.sql file to use this feature.</div>';
    }
} catch (Exception $e) {
    $message = '<div class="alert alert-danger border-0 shadow-sm rounded-4">Critical Error: ' . $e->getMessage() . '</div>';
}
?>

<style>
    .lead-card {
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    .lead-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -5px rgba(0,0,0,0.05);
    }
    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-new { background: #fee2e2; color: #ef4444; }
    .status-read { background: #dcfce7; color: #10b981; }
    .status-responded { background: #e0e7ff; color: #4f46e5; }
    
    .avatar-init {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: white;
        text-transform: uppercase;
    }
</style>

<div class="animate-fade-in">
    <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-4 rounded-4 shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-danger-soft" style="background: #fff1f2; color: #e11d48; width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fas fa-inbox"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">Customer Inquiries</h4>
                <p class="text-muted small mb-0">Manage incoming business leads and client responses.</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-white border rounded-pill px-3 shadow-sm" onclick="window.location.reload()"><i class="fas fa-sync-alt me-1"></i> Refresh</button>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row g-4">
        <?php if(empty($leads)): ?>
            <div class="col-12 text-center py-5">
                <div class="bg-light p-5 rounded-5">
                    <i class="fas fa-envelope-open fa-3x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">No leads received yet.</h5>
                    <p class="small text-muted">Entries from your website contact form will appear here.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($leads as $lead): 
                $colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#8b5cf6'];
                $char = strtoupper(substr($lead['name'], 0, 1));
                $bg = $colors[ord($char) % count($colors)];
            ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card lead-card h-100 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-init shadow-sm" style="background: <?php echo $bg; ?>;"><?php echo $char; ?></div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($lead['name']); ?></h6>
                                        <small class="text-muted"><?php echo date('M d, h:i A', strtotime($lead['created_at'])); ?></small>
                                    </div>
                                </div>
                                <span class="status-badge status-<?php echo $lead['status']; ?>">
                                    <?php echo $lead['status']; ?>
                                </span>
                            </div>
                            
                            <div class="mb-4">
                                <div class="small fw-bold text-muted text-uppercase letter-spacing-1 mb-2">Subject: <?php echo htmlspecialchars($lead['subject']); ?></div>
                                <div class="text-secondary small bg-light p-3 rounded-3" style="min-height: 80px; letter-spacing: -0.2px;">
                                    "<?php echo htmlspecialchars($lead['message']); ?>"
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fas fa-envelope text-muted small"></i>
                                <a href="mailto:<?php echo $lead['email']; ?>" class="small text-primary text-decoration-none fw-bold"><?php echo htmlspecialchars($lead['email']); ?></a>
                            </div>

                            <hr class="opacity-5 my-3">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-white border px-3 dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                                        <li><a class="dropdown-item" href="?page=leads&action=update_status&status=read&id=<?php echo $lead['id']; ?>"><i class="fas fa-check text-success me-2"></i> Mark as Read</a></li>
                                        <li><a class="dropdown-item" href="?page=leads&action=update_status&status=responded&id=<?php echo $lead['id']; ?>"><i class="fas fa-reply text-primary me-2"></i> Mark Responded</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="?page=leads&action=delete&id=<?php echo $lead['id']; ?>" onclick="return confirm('Delete this lead forever?')"><i class="fas fa-trash me-2"></i> Delete Record</a></li>
                                    </ul>
                                </div>
                                <a href="mailto:<?php echo $lead['email']; ?>?subject=RE: <?php echo urlencode($lead['subject']); ?>" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm fw-bold">
                                    <i class="fas fa-paper-plane me-1"></i> Reply
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
