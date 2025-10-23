<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Servicii';
$page_description = 'Servicii profesionale de manichiură, pedichiură și nail art la Nail Studio Andreea - Prețuri competitive și calitate superioară.';

$services = get_all_services(true);

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Serviciile Noastre</h1>
        <p class="text-muted">Oferim servicii complete pentru îngrijirea și înfrumusețarea unghiilor</p>
    </div>

    <div class="row">
        <?php foreach ($services as $service): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <?php 
                $img_path = 'assets/images/services/' . ($service['image'] ?: '');
                if (!$service['image'] || !file_exists($img_path)) {
                    $img_path = 'assets/images/default-service.jpg';
                }
                ?>
                <img src="<?php echo $img_path; ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($service['name']); ?>"
                     style="height: 250px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars($service['description']); ?></p>
                    <div class="service-details mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-clock text-primary"></i> <?php echo $service['duration']; ?> minute</span>
                            <span class="h5 text-primary mb-0"><?php echo number_format($service['price'], 0); ?> RON</span>
                        </div>
                    </div>
                    <a href="appointment.php?service=<?php echo $service['id']; ?>" class="btn btn-primary">Programează-te</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Additional Info -->
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4>Informații Importante</h4>
                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-shield-alt text-primary fa-2x mb-2"></i>
                            <h6>Produse Profesionale</h6>
                            <p class="text-muted small">Folosim doar produse de cea mai bună calitate</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-sparkles text-primary fa-2x mb-2"></i>
                            <h6>Igienă Impecabilă</h6>
                            <p class="text-muted small">Sterilizăm toate instrumentele conform normelor</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-clock text-primary fa-2x mb-2"></i>
                            <h6>Punctualitate</h6>
                            <p class="text-muted small">Respectăm programul stabilit pentru fiecare client</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
