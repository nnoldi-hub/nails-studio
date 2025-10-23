<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Cursuri de Coaching';
$page_description = 'Cursuri profesionale de nail art și coaching la Nail Studio Andreea - Învață de la experți și dezvoltă-ți propriul business.';

$coaching_sessions = get_coaching_sessions(true);
$success_message = '';
$error_message = '';

// Handle coaching booking form submission
if ($_POST) {
    $client_name = sanitize_input($_POST['client_name']);
    $client_email = sanitize_input($_POST['client_email']);
    $client_phone = sanitize_input($_POST['client_phone']);
    $session_id = (int)$_POST['session_id'];
    $booking_date = sanitize_input($_POST['booking_date']);
    $booking_time = sanitize_input($_POST['booking_time']);
    $notes = sanitize_input($_POST['notes']);

    // Validation
    if (empty($client_name) || empty($client_email) || empty($client_phone) || empty($session_id) || empty($booking_date) || empty($booking_time)) {
        $error_message = 'Toate câmpurile obligatorii trebuie completate.';
    } elseif (!filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Adresa de email nu este validă.';
    } elseif (strtotime($booking_date) < strtotime('today')) {
        $error_message = 'Data cursului nu poate fi în trecut.';
    } else {
        if (add_coaching_booking($client_name, $client_email, $client_phone, $session_id, $booking_date, $booking_time, $notes)) {
            $success_message = 'Înscrierea la curs a fost înregistrată cu succes! Vă vom contacta pentru confirmare și detalii de plată.';
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
        <h1 class="fw-bold">Cursuri de Coaching</h1>
        <p class="text-muted">Învață de la experți și dezvoltă-ți abilitățile în domeniul nail art</p>
    </div>

    <!-- Coaching Sessions -->
    <div class="row">
        <?php foreach ($coaching_sessions as $session): ?>
        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-sm coaching-card" 
                 data-session='<?php echo json_encode($session); ?>' 
                 style="cursor:pointer;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-primary"><?php echo htmlspecialchars($session['session_name']); ?></h5>
                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars($session['description']); ?></p>
                    <div class="session-details mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-clock text-primary"></i> <?php echo $session['duration']; ?> ore</span>
                            <span><i class="fas fa-users text-primary"></i> Max <?php echo $session['max_participants']; ?> participanți</span>
                        </div>
                        <div class="text-center">
                            <span class="h4 text-primary"><?php echo number_format($session['price'], 0); ?> RON</span>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-auto" data-bs-toggle="modal" data-bs-target="#bookingModal" 
                            data-session-id="<?php echo $session['id']; ?>" 
                            data-session-name="<?php echo htmlspecialchars($session['session_name']); ?>">
                        Înscrie-te
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
<!-- Modal Detalii Curs -->
<div class="modal fade" id="courseDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseDetailsTitle">Detalii Curs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="courseDetailsBody">
                <!-- Detalii populate din JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închide</button>
            </div>
        </div>
    </div>
</div>
    </div>

    <!-- Benefits Section -->
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <div class="card bg-light">
                <div class="card-body">
                    <h4 class="text-center mb-4">De Ce Să Alegi Cursurile Noastre?</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-award text-primary fa-2x me-3 mt-1"></i>
                                <div>
                                    <h6>Instructor Certificat</h6>
                                    <p class="text-muted small">Cursuri ținute de instructori cu experiență și certificări internaționale</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-certificate text-primary fa-2x me-3 mt-1"></i>
                                <div>
                                    <h6>Certificare</h6>
                                    <p class="text-muted small">Primești certificat de absolvire recunoscut în industrie</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-hands-helping text-primary fa-2x me-3 mt-1"></i>
                                <div>
                                    <h6>Practică Intensivă</h6>
                                    <p class="text-muted small">Multe ore de practică cu feedback personalizat</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-toolbox text-primary fa-2x me-3 mt-1"></i>
                                <div>
                                    <h6>Kit Inclus</h6>
                                    <p class="text-muted small">Toate materialele necesare pentru practică sunt incluse</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Înscrie-te la Curs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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
                            <label for="session_id" class="form-label">Curs *</label>
                            <select class="form-select" id="session_id" name="session_id" required>
                                <option value="">Selectează cursul</option>
                                <?php foreach ($coaching_sessions as $session): ?>
                                <option value="<?php echo $session['id']; ?>" 
                                        <?php echo (isset($_POST['session_id']) && $_POST['session_id'] == $session['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($session['session_name']); ?> - <?php echo number_format($session['price'], 0); ?> RON
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="booking_date" class="form-label">Data Preferată *</label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                   value="<?php echo isset($_POST['booking_date']) ? $_POST['booking_date'] : ''; ?>" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="booking_time" class="form-label">Ora Preferată *</label>
                            <select class="form-select" id="booking_time" name="booking_time" required>
                                <option value="">Selectează ora</option>
                                <option value="09:00" <?php echo (isset($_POST['booking_time']) && $_POST['booking_time'] == '09:00') ? 'selected' : ''; ?>>09:00</option>
                                <option value="14:00" <?php echo (isset($_POST['booking_time']) && $_POST['booking_time'] == '14:00') ? 'selected' : ''; ?>>14:00</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Experiența Anterioară / Întrebări</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Menționează experiența ta anterioară sau orice întrebări..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-graduation-cap me-2"></i>Înscrie-te
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingModal = document.getElementById('bookingModal');
    bookingModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const sessionId = button.getAttribute('data-session-id');
        const sessionName = button.getAttribute('data-session-name');
        
        const sessionSelect = bookingModal.querySelector('#session_id');
        if (sessionId) {
            sessionSelect.value = sessionId;
        }
    });

    // Modal detalii curs
    const courseDetailsModal = document.getElementById('courseDetailsModal');
    const courseDetailsBody = document.getElementById('courseDetailsBody');
    const courseDetailsTitle = document.getElementById('courseDetailsTitle');
    document.querySelectorAll('.coaching-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Evită click pe butonul Înscrie-te
            if (e.target.closest('button')) return;
            const session = JSON.parse(card.getAttribute('data-session'));
            courseDetailsTitle.textContent = session.session_name;
            let longDesc = session.long_description || session.description;
            let html = `<div class='mb-3'><strong>Descriere detaliată:</strong><br>${longDesc}</div>`;
            html += `<div class='row mb-3'>
                        <div class='col-md-6'><i class='fas fa-clock text-primary'></i> Durată: <strong>${session.duration} ore</strong></div>
                        <div class='col-md-6'><i class='fas fa-users text-primary'></i> Max <strong>${session.max_participants}</strong> participanți</div>
                    </div>`;
            html += `<div class='mb-3'><strong>Preț:</strong> <span class='badge bg-success'>${parseFloat(session.price).toFixed(2)} RON</span></div>`;
            if (session.benefits) {
                let benefitsArr = session.benefits;
                if (typeof benefitsArr === 'string') {
                    try { benefitsArr = JSON.parse(benefitsArr); } catch(e) { benefitsArr = []; }
                }
                if (Array.isArray(benefitsArr) && benefitsArr.length) {
                    html += `<div class='mb-3'><strong>Ce vei învăța:</strong><ul>`;
                    benefitsArr.forEach(function(b) { html += `<li>${b}</li>`; });
                    html += `</ul></div>`;
                }
            }
            courseDetailsBody.innerHTML = html;
            var modal = new bootstrap.Modal(courseDetailsModal);
            modal.show();
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
