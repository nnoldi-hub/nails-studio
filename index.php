<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Acasă';
$page_description = 'Nail Studio Andreea - Salonul tău de încredere pentru manichiură, pedichiură și cursuri profesionale de unghii în București.';

$featured_services = get_all_services(true);
$featured_gallery = get_gallery_items(true);

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold">Bine ai venit la Nail Studio Andreea</h1>
                <p class="lead">Transformăm unghiile tale în opere de artă. Servicii profesionale de manichiură, pedichiură și cursuri de specializare.</p>
                <div class="mt-4">
                    <a href="appointment.php" class="btn btn-light btn-lg me-3">Programează-te</a>
                    <a href="services.php" class="btn btn-outline-light btn-lg">Vezi Serviciile</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/hero-image.jpg" alt="Nail Studio Andreea" class="img-fluid rounded shadow" style="max-height: 400px; width: 100%; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- Services Preview -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Serviciile Noastre</h2>
            <p class="text-muted">Oferim o gamă completă de servicii pentru îngrijirea unghiilor</p>
        </div>
        <div class="row">
            <?php foreach (array_slice($featured_services, 0, 3) as $service): ?>
            <div class="col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="assets/images/<?php echo $service['image'] ?: 'default-service.jpg'; ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($service['name']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                        <p class="card-text flex-grow-1"><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0"><?php echo number_format($service['price'], 0); ?> RON</span>
                            <span class="text-muted"><?php echo $service['duration']; ?> min</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-primary btn-lg">Vezi Toate Serviciile</a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">De Ce Să Ne Alegi?</h2>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-star fa-2x"></i>
                    </div>
                    <h5>Experiență</h5>
                    <p class="text-muted">Peste 5 ani de experiență în domeniul nail art</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-palette fa-2x"></i>
                    </div>
                    <h5>Creativitate</h5>
                    <p class="text-muted">Designuri unice și personalizate pentru fiecare client</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-heart fa-2x"></i>
                    </div>
                    <h5>Pasiune</h5>
                    <p class="text-muted">Fiecare lucrare este făcută cu dragoste și atenție la detalii</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-graduation-cap fa-2x"></i>
                    </div>
                    <h5>Educație</h5>
                    <p class="text-muted">Cursuri profesionale pentru cei care vor să învețe</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Preview -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Galeria Noastră</h2>
            <p class="text-muted">Descoperă câteva dintre lucrările noastre</p>
        </div>
        <div class="row">
            <?php foreach ($featured_gallery as $item): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <img src="assets/images/<?php echo $item['image']; ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>"
                         style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($item['description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="gallery.php" class="btn btn-outline-primary btn-lg">Vezi Toată Galeria</a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold">Gata să îți transformi unghiile?</h3>
                <p class="mb-0">Programează-te acum pentru o experiență de neuitat!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="appointment.php" class="btn btn-light btn-lg">Programează-te Acum</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
