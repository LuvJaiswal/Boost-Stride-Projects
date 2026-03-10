<?php 
require_once 'admin/core/Config.php';
require_once 'admin/Core/Database.php';

use Core\Database;

$db = Database::getInstance();
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header("Location: index.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
$stmt->execute([$slug]);
$pageData = $stmt->fetch();

if (!$pageData) {
    include '404.php';
    exit;
}

$pageTitle = $pageData['title'];
$metaTitle = $pageData['meta_title'] ?: $pageTitle . " | Hobart Auto Shop";
$metaDescription = $pageData['meta_description'] ?: "";

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
                <li class="breadcrumb-item text-white active" aria-current="page"><?php echo htmlspecialchars($pageTitle); ?></li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Custom Content Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="wow fadeInUp" data-wow-delay="0.1s">
            <div class="content-area">
                <?php echo $pageData['content']; ?>
            </div>
        </div>
    </div>
</div>
<!-- Custom Content End -->

<style>
.content-area {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #444;
}
.content-area h1, .content-area h2, .content-area h3 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 700;
}
.content-area img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 20px 0;
}
</style>

<?php include 'includes/footer.php'; ?>
