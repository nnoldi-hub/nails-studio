<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Articole Blog';
$page_description = 'Descoperă articole utile despre manichiură, pedichiură, tendințe, produse și sfaturi de la experți.';

// Selectează doar articolele publicate
$result = $conn->query("SELECT * FROM articles WHERE status = 'published' ORDER BY created_at DESC");

include 'includes/header.php';
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Articole Blog</h1>
        <p class="text-muted">Sfaturi, tendințe și inspirație pentru unghii</p>
    </div>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <?php
                $img_path = $row['image'];
                $img_path = ltrim(str_replace(' ', '%20', $img_path), '/');
                $local_path = __DIR__ . '/' . $img_path;
                if (!empty($img_path) && file_exists($local_path)) {
                    echo '<img src="' . htmlspecialchars($img_path) . '" class="card-img-top" alt="' . htmlspecialchars($row['title']) . '" style="height:200px;object-fit:cover;">';
                } else {
                    echo '<img src="assets/images/placeholder.png" class="card-img-top" alt="placeholder" style="height:200px;object-fit:cover;">';
                }
                ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars(mb_substr(strip_tags($row['content']),0,120)) . '...'; ?></p>
                    <a href="article.php?slug=<?php echo urlencode($row['slug']); ?>" class="btn btn-primary mt-2">Citește articolul</a>
                </div>
                <div class="card-footer text-muted small">
                    <i class="fas fa-calendar-alt me-1"></i> <?php echo date('d.m.Y', strtotime($row['created_at'])); ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php if ($result->num_rows === 0): ?>
    <div class="text-center py-5">
        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
        <h4>Niciun articol publicat momentan</h4>
        <p class="text-muted">Revin-o curând pentru noutăți și inspirație!</p>
    </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
