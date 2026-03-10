<?php 
require_once 'admin/core/Config.php';
require_once 'admin/core/Database.php';

use Core\Database;

$db = Database::getInstance();
$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: service.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM services WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$serviceData = $stmt->fetch();

if (!$serviceData) {
    include '404.php';
    exit;
}

$pageTitle = $serviceData['title'];
$metaTitle = $pageTitle . " | Boost Stride Auto Shop";

include 'includes/header.php'; 
?>
<?php include 'includes/navbar.php'; ?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5 mb-5">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a class="text-white" href="service.php">Services</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page"><?php echo htmlspecialchars($pageTitle); ?></li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Detail Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                <div class="detail-content">
                    <?php if ($serviceData['image']): ?>
                        <img class="img-fluid w-100 rounded-4 mb-5 shadow-sm" src="<?php echo $serviceData['image']; ?>" alt="<?php echo htmlspecialchars($pageTitle); ?>">
                    <?php endif; ?>
                    
                    <h2 class="display-6 mb-4"><?php echo htmlspecialchars($pageTitle); ?></h2>
                    
                    <div class="service-body-content">
                        <?php 
                            if (!empty($serviceData['content'])) {
                                echo $serviceData['content']; 
                            } else {
                                echo '<p class="lead">' . htmlspecialchars($serviceData['description']) . '</p>';
                                echo '<div class="alert alert-light border-0 shadow-sm p-4 mt-5">
                                        <h5 class="fw-bold mb-3">Service Overview</h5>
                                        <p>Contact us today for more details about our ' . htmlspecialchars($pageTitle) . ' services and how we can help you keep your vehicle in top condition.</p>
                                      </div>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="service-sidebar">
                    <!-- Contact Box -->
                    <div class="sidebar-box p-4 rounded-4 bg-primary text-white mb-4 wow fadeIn" data-wow-delay="0.3s">
                        <h4 class="text-white mb-4">Request A Quote</h4>
                        <p>Need immediate service? Fill out our form or call us directly.</p>
                        <a href="quote.php" class="btn btn-light w-100 py-3 rounded-pill fw-bold mt-2">Get Started</a>
                    </div>
                    
                    <!-- Quick Info -->
                    <div class="sidebar-box p-4 rounded-4 bg-light mb-4 wow fadeIn" data-wow-delay="0.5s">
                        <h4 class="mb-4">Quick Info</h4>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fa fa-clock text-primary me-3"></i>
                                <span>Mon - Fri: 9:00 - 18:00</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fa fa-check text-primary me-3"></i>
                                <span>Certified Mechanics</span>
                            </li>
                            <li class="mb-0 d-flex align-items-center">
                                <i class="fa fa-shield-alt text-primary me-3"></i>
                                <span>Warranty Guaranteed</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Detail End -->

<style>
.service-body-content {
    font-size: 1.15rem;
    line-height: 1.8;
    color: #444;
}
.service-body-content h1, .service-body-content h2, .service-body-content h3 {
    margin-top: 2.5rem;
    margin-bottom: 1.2rem;
    font-weight: 700;
    color: #333;
}
.service-body-content p {
    margin-bottom: 1.5rem;
}
.service-body-content ul {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}
.sidebar-box {
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}
</style>

<?php include 'includes/footer.php'; ?>
