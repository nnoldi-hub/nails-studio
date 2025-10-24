<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : '';
if (!$slug) {
    header('Location: articles.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM articles WHERE slug = ? AND status = 'published' LIMIT 1");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();
$stmt->close();

if (!$article) {
    include 'includes/header.php';
    echo '<div class=\"container py-5\"><div class=\"alert alert-danger\">Articolul nu a fost găsit!</div></div>';
    include 'includes/footer.php';
    exit;
}

$page_title = $article['meta_title'] ?: $article['title'];
$page_description = $article['meta_description'];
include 'includes/header.php';
?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card mb-4 shadow">
                <div class="card-body">
                    <h1 class="fw-bold mb-4"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <?php
                    $img_path = $article['image'];
                    $img_path = ltrim(str_replace(' ', '%20', $img_path), '/');
                    $local_path = __DIR__ . '/' . $img_path;
                    if (!empty($img_path) && file_exists($local_path)) {
                        echo '<img src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($article['title']) . '" class="img-fluid rounded mb-3" style="max-height:300px;">';
                    } else {
                        echo '<img src="assets/images/placeholder.png" alt="placeholder" class="img-fluid rounded mb-3" style="max-height:300px;">';
                    }
                    ?>
                    <div><?php echo $article['content']; ?></div>
                </div>
                <div class="card-footer text-muted small">
                    <i class="fas fa-calendar-alt me-1"></i> Publicat la <?php echo date('d.m.Y', strtotime($article['created_at'])); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
