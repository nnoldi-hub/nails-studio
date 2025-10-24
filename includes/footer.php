    </main>

    <!-- Footer -->
    <?php
    // Footer din baza de date (site_settings)
    $footer = [
        'brand_title' => 'Nail Studio Andreea',
        'brand_text' => 'Salonul tău de încredere pentru servicii profesionale de manichiură, pedichiură și cursuri de specializare.',
        'facebook' => '#',
        'instagram' => '#',
        'tiktok' => '#',
        'services' => [
            ['label' => 'Manichiură', 'url' => SITE_URL . '/services.php'],
            ['label' => 'Pedichiură', 'url' => SITE_URL . '/services.php'],
            ['label' => 'Extensii Unghii', 'url' => SITE_URL . '/services.php'],
            ['label' => 'Cursuri Coaching', 'url' => SITE_URL . '/coaching.php'],
        ],
        'contact_address' => 'Str. Exemplu, Nr. 123, București',
        'contact_phone' => '+40 123 456 789',
        'contact_email' => 'contact@nailstudioandreea.ro',
        'contact_hours' => 'Lun-Vin: 9:00-19:00, Sâm: 9:00-17:00',
        'copyright' => '&copy; ' . date('Y') . ' Nail Studio Andreea. Toate drepturile rezervate.',
        'admin_link' => SITE_URL . '/admin',
    ];
    // TODO: Înlocuiește cu citire din DB (site_settings)
    ?>
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><?php echo $footer['brand_title']; ?></h5>
                    <p><?php echo $footer['brand_text']; ?></p>
                    <div class="social-links">
                        <a href="<?php echo $footer['facebook']; ?>" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo $footer['instagram']; ?>" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo $footer['tiktok']; ?>" class="text-light me-3"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Servicii</h5>
                    <ul class="list-unstyled">
                        <?php foreach ($footer['services'] as $service): ?>
                        <li><a href="<?php echo $service['url']; ?>" class="text-light text-decoration-none"><?php echo $service['label']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Contact</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> <?php echo $footer['contact_address']; ?></p>
                    <p><i class="fas fa-phone me-2"></i> <?php echo $footer['contact_phone']; ?></p>
                    <p><i class="fas fa-envelope me-2"></i> <?php echo $footer['contact_email']; ?></p>
                    <p><i class="fas fa-clock me-2"></i> <?php echo $footer['contact_hours']; ?></p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p><?php echo $footer['copyright']; ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="<?php echo $footer['admin_link']; ?>" class="text-light text-decoration-none">Admin</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
