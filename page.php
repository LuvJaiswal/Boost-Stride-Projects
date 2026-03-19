<?php 
require_once 'admin/core/Config.php';
require_once 'admin/Core/Database.php';
require_once 'admin/Core/Renderer.php';

use Core\Database;
use Core\Renderer;

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
<?php 
    $headerImg = $pageData['header_image'] ?: 'img/carousel-1.jpg';
    $headerBg = (strpos($headerImg, 'uploads/') === 0) ? $headerImg : $headerImg;
?>
<div class="container-fluid page-header py-5 mb-5" style="background: linear-gradient(rgba(0, 0, 0, .5), rgba(0, 0, 0, .5)), url('<?php echo $headerBg; ?>') center center no-repeat; background-size: cover;">
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
        <div class="row g-5">
            <div class="col-lg-<?php echo (!empty($pageData['featured_image']) || !empty($pageData['video_url'])) ? '7' : '12'; ?> wow fadeInUp" data-wow-delay="0.1s">
                <div class="content-area summernote-content">
                    <?php echo Renderer::content($pageData['content']); ?>
                </div>
                
                <?php if (!empty($pageData['external_link'])): ?>
                    <div class="mt-5">
                        <a href="<?php echo htmlspecialchars($pageData['external_link']); ?>" class="btn btn-primary py-3 px-5 rounded-pill shadow">Take Action Now <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($pageData['featured_image']) || !empty($pageData['video_url'])): ?>
            <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.3s">
                <?php if (!empty($pageData['featured_image'])): ?>
                    <div class="mb-4 rounded-4 overflow-hidden shadow">
                        <img src="<?php echo $pageData['featured_image']; ?>" class="img-fluid w-100" alt="<?php echo htmlspecialchars($pageTitle); ?>">
                    </div>
                <?php endif; ?>

                <?php if (!empty($pageData['video_url'])): ?>
                    <?php 
                        $video_url = $pageData['video_url'];
                        $embed_url = "";
                        if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match);
                            if (isset($match[1])) $embed_url = "https://www.youtube.com/embed/" . $match[1];
                        } elseif (strpos($video_url, 'vimeo.com') !== false) {
                            $vimeo_id = (int) substr(parse_url($video_url, PHP_URL_PATH), 1);
                            if ($vimeo_id) $embed_url = "https://player.vimeo.com/video/" . $vimeo_id;
                        }
                    ?>
                    <?php if ($embed_url): ?>
                        <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow mt-4">
                            <iframe src="<?php echo $embed_url; ?>" allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Custom Content End -->

<?php include 'includes/footer.php'; ?>
