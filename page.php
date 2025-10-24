<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : '';
if (!$slug) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ? LIMIT 1");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();
$page = $result->fetch_assoc();
$stmt->close();

if (!$page) {
    // Pagina nu există
    include 'includes/header.php';
    echo '<div class="container py-5"><div class="alert alert-danger">Pagina nu a fost găsită!</div></div>';
    include 'includes/footer.php';
    exit;
}

$page_title = $page['title'];
$page_description = $page['meta_description'];
include 'includes/header.php';
?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card mb-4 shadow">
                <div class="card-body">
                    <h1 class="fw-bold mb-4"><?php echo htmlspecialchars($page['title']); ?></h1>
                    <div class="mb-4">
                        <?php if (!empty($page['image'])): ?>
                            <img src="<?php echo htmlspecialchars($page['image']); ?>" alt="<?php echo htmlspecialchars($page['title']); ?>" class="img-fluid rounded mb-3" style="max-height:300px;">
                        <?php endif; ?>
                        <div><?php echo $page['content']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
