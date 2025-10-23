<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Galerie';
$page_description = 'Galeria de lucrări Nail Studio Andreea - Inspiră-te din creațiile noastre și descoperă stilul perfect pentru tine.';

$gallery_items = get_gallery_items();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Galeria Noastră</h1>
        <p class="text-muted">Descoperă câteva dintre lucrările noastre preferate</p>
    </div>

    <div class="row">
        <?php if (!empty($gallery_items)): ?>
            <?php foreach ($gallery_items as $item): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card gallery-item">
                    <div class="position-relative overflow-hidden">
                        <?php 
                        $image_path = 'assets/images/gallery/' . $item['image'];
                        $display_path = file_exists($image_path) ? $image_path : 'assets/images/placeholder.png';
                        ?>
                        <img src="<?php echo htmlspecialchars($display_path); ?>" 
                             class="card-img-top gallery-image" alt="<?php echo htmlspecialchars($item['title']); ?>"
                             style="height: 300px; object-fit: cover; transition: transform 0.3s ease;">
                        <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                             style="background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s ease;">
                            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#imageModal" 
                                    data-image="<?php echo htmlspecialchars($display_path); ?>" 
                                    data-title="<?php echo htmlspecialchars($item['title']); ?>">
                                <i class="fas fa-expand-alt"></i> Vezi mai mare
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($item['description']); ?></p>
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i><?php echo format_date($item['created_at']); ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <h4>În curând</h4>
                <p class="text-muted">Galeria noastră va fi disponibilă în curând cu lucrări spectaculoase!</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<style>
.gallery-item:hover .gallery-image {
    transform: scale(1.05);
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const imageSrc = button.getAttribute('data-image');
            const imageTitle = button.getAttribute('data-title');
            
            const modalTitle = imageModal.querySelector('#imageModalTitle');
            const modalImage = imageModal.querySelector('#modalImage');
            
            if (modalTitle) modalTitle.textContent = imageTitle;
            if (modalImage) {
                modalImage.src = imageSrc;
                modalImage.alt = imageTitle;
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
