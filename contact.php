<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Contact';
$page_description = 'Contactează Nail Studio Andreea pentru programări și informații. Suntem aici să răspundem la toate întrebările tale.';

$success_message = '';
$error_message = '';

// Handle contact form submission
if ($_POST) {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Toate câmpurile sunt obligatorii.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Adresa de email nu este validă.';
    } else {
        if (add_contact_message($name, $email, $subject, $message)) {
            $success_message = 'Mesajul a fost trimis cu succes! Vă vom răspunde în cel mai scurt timp.';
            // Clear form data
            $_POST = array();
        } else {
            $error_message = 'A apărut o eroare. Vă rugăm să încercați din nou.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Contactează-ne</h1>
        <p class="text-muted">Suntem aici să răspundem la toate întrebările tale</p>
    </div>

    <div class="row">
        <!-- Contact Form -->
        <div class="col-lg-8 mb-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">Trimite-ne un Mesaj</h4>

                    <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nume Complet *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subiect *</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Mesaj *</label>
                            <textarea class="form-control" id="message" name="message" rows="5" 
                                      placeholder="Scrie-ne mesajul tău aici..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-paper-plane me-2"></i>Trimite Mesajul
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title">Informații de Contact</h5>
                    
                    <div class="contact-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="contact-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Adresa</h6>
                                <p class="text-muted mb-0">Str. Exemplu, Nr. 123<br>București, România</p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="contact-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Telefon</h6>
                                <p class="text-muted mb-0">
                                    <a href="tel:+40123456789" class="text-decoration-none">+40 123 456 789</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="contact-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Email</h6>
                                <p class="text-muted mb-0">
                                    <a href="mailto:contact@nailstudioandreea.ro" class="text-decoration-none">contact@nailstudioandreea.ro</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="d-flex align-items-center">
                            <div class="contact-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Program</h6>
                                <p class="text-muted mb-0">
                                    Lun-Vin: 9:00-19:00<br>
                                    Sâmbătă: 9:00-17:00<br>
                                    Duminică: Închis
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="card shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Urmărește-ne</h5>
                    <div class="social-links">
                        <a href="#" class="btn btn-primary btn-sm me-2 mb-2">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="#" class="btn btn-primary btn-sm me-2 mb-2">
                            <i class="fab fa-instagram"></i> Instagram
                        </a>
                        <a href="#" class="btn btn-primary btn-sm mb-2">
                            <i class="fab fa-tiktok"></i> TikTok
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="map-container" style="height: 400px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        <div class="text-center">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                            <h5>Hartă Google Maps</h5>
                            <p class="text-muted">Harta va fi integrată aici cu locația salonului</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
