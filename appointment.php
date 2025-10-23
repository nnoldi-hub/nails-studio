<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Programare';
$page_description = 'Programează-te la Nail Studio Andreea pentru servicii profesionale de manichiură și pedichiură.';

$services = get_all_services(true);
$success_message = '';
$error_message = '';

// Handle form submission
if ($_POST) {
    $client_name = sanitize_input($_POST['client_name']);
    $client_email = sanitize_input($_POST['client_email']);
    $client_phone = sanitize_input($_POST['client_phone']);
    $service_id = (int)$_POST['service_id'];
    $appointment_date = sanitize_input($_POST['appointment_date']);
    $appointment_time = sanitize_input($_POST['appointment_time']);
    $notes = sanitize_input($_POST['notes']);

    // Validation
    if (empty($client_name) || empty($client_email) || empty($client_phone) || empty($service_id) || empty($appointment_date) || empty($appointment_time)) {
        $error_message = 'Toate câmpurile obligatorii trebuie completate.';
    } elseif (!filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Adresa de email nu este validă.';
    } elseif (strtotime($appointment_date) < strtotime('today')) {
        $error_message = 'Data programării nu poate fi în trecut.';
    } else {
        if (add_appointment($client_name, $client_email, $client_phone, $service_id, $appointment_date, $appointment_time, $notes)) {
            $success_message = 'Programarea a fost înregistrată cu succes! Vă vom contacta pentru confirmare.';
            // Clear form data
            $_POST = array();
        } else {
            $error_message = 'A apărut o eroare. Vă rugăm să încercați din nou.';
        }
    }
}

// Pre-select service if provided in URL
$selected_service = isset($_GET['service']) ? (int)$_GET['service'] : 0;

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="fw-bold">Programează o Întâlnire</h1>
                <p class="text-muted">Completează formularul de mai jos pentru a te programa</p>
            </div>

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

            <div class="card shadow">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">Nume Complet *</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" 
                                       value="<?php echo isset($_POST['client_name']) ? htmlspecialchars($_POST['client_name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="client_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="client_email" name="client_email" 
                                       value="<?php echo isset($_POST['client_email']) ? htmlspecialchars($_POST['client_email']) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_phone" class="form-label">Telefon *</label>
                                <input type="tel" class="form-control" id="client_phone" name="client_phone" 
                                       value="<?php echo isset($_POST['client_phone']) ? htmlspecialchars($_POST['client_phone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service_id" class="form-label">Serviciu *</label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">Selectează serviciul</option>
                                    <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['id']; ?>" 
                                            <?php echo ($selected_service == $service['id'] || (isset($_POST['service_id']) && $_POST['service_id'] == $service['id'])) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($service['name']); ?> - <?php echo number_format($service['price'], 0); ?> RON
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="appointment_date" class="form-label">Data *</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                       value="<?php echo isset($_POST['appointment_date']) ? $_POST['appointment_date'] : ''; ?>" 
                                       min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="appointment_time" class="form-label">Ora *</label>
                                <select class="form-select" id="appointment_time" name="appointment_time" required>
                                    <option value="">Selectează ora</option>
                                    <option value="09:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '09:00') ? 'selected' : ''; ?>>09:00</option>
                                    <option value="10:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '10:00') ? 'selected' : ''; ?>>10:00</option>
                                    <option value="11:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '11:00') ? 'selected' : ''; ?>>11:00</option>
                                    <option value="12:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '12:00') ? 'selected' : ''; ?>>12:00</option>
                                    <option value="13:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '13:00') ? 'selected' : ''; ?>>13:00</option>
                                    <option value="14:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '14:00') ? 'selected' : ''; ?>>14:00</option>
                                    <option value="15:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '15:00') ? 'selected' : ''; ?>>15:00</option>
                                    <option value="16:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '16:00') ? 'selected' : ''; ?>>16:00</option>
                                    <option value="17:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '17:00') ? 'selected' : ''; ?>>17:00</option>
                                    <option value="18:00" <?php echo (isset($_POST['appointment_time']) && $_POST['appointment_time'] == '18:00') ? 'selected' : ''; ?>>18:00</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observații</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Menționează orice detalii suplimentare..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-calendar-check me-2"></i>Programează-te
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle text-primary me-2"></i>Informații Importante</h5>
                    <ul class="mb-0">
                        <li>Programările se confirmă telefonic în maxim 24 de ore</li>
                        <li>Pentru anulări, vă rugăm să ne contactați cu cel puțin 4 ore înainte</li>
                        <li>Întârzierea de peste 15 minute poate duce la reprogramarea întâlnirii</li>
                        <li>Pentru întrebări urgente, ne puteți contacta la telefon: +40 123 456 789</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
