<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin_login();

$page_title = 'Gestionare Cursuri Coaching';

$success_message = '';
$error_message = '';
$active_tab = 'sessions';

// Handle add/edit coaching session
if ($_POST) {
    // Ștergere rezervare
    if (isset($_POST['delete_booking'])) {
        $booking_id = (int)$_POST['booking_id'];
        $stmt = $conn->prepare("DELETE FROM coaching_bookings WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            $success_message = 'Rezervarea a fost ștearsă.';
        } else {
            $error_message = 'Eroare la ștergerea rezervării.';
        }
        $active_tab = 'bookings';
    }
    if (isset($_POST['form_action']) && $_POST['form_action'] === 'add_coaching') {
    $session_name = sanitize_input($_POST['session_name']);
    $description = sanitize_input($_POST['description']);
    $long_description = sanitize_input($_POST['long_description']);
    $benefits = isset($_POST['benefits']) ? array_map('sanitize_input', $_POST['benefits']) : [];
    $benefits_json = json_encode(array_filter($benefits));
        $price = (float)$_POST['price'];
        $duration = (int)$_POST['duration'];
        $max_participants = (int)$_POST['max_participants'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
    $stmt = $conn->prepare("INSERT INTO coaching_sessions (session_name, description, long_description, benefits, price, duration, max_participants, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdiii", $session_name, $description, $long_description, $benefits_json, $price, $duration, $max_participants, $is_active);
        
        if ($stmt->execute()) {
            $success_message = 'Cursul a fost adăugat cu succes.';
        } else {
            $error_message = 'A apărut o eroare la adăugarea cursului.';
        }
    }
    
    if (isset($_POST['form_action']) && $_POST['form_action'] === 'edit_coaching') {
        $id = (int)$_POST['coaching_id'];
    $session_name = sanitize_input($_POST['session_name']);
    $description = sanitize_input($_POST['description']);
    $long_description = isset($_POST['long_description']) ? sanitize_input($_POST['long_description']) : '';
    $benefits = isset($_POST['benefits']) ? array_map('sanitize_input', $_POST['benefits']) : [];
    $benefits_json = json_encode(array_filter($benefits));
        $price = (float)$_POST['price'];
        $duration = (int)$_POST['duration'];
        $max_participants = (int)$_POST['max_participants'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
    $stmt = $conn->prepare("UPDATE coaching_sessions SET session_name = ?, description = ?, long_description = ?, benefits = ?, price = ?, duration = ?, max_participants = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("ssssdiiii", $session_name, $description, $long_description, $benefits_json, $price, $duration, $max_participants, $is_active, $id);
        
        if ($stmt->execute()) {
            $success_message = 'Cursul a fost actualizat cu succes.';
        } else {
            $error_message = 'A apărut o eroare la actualizarea cursului.';
        }
    }
    
    if (isset($_POST['delete_coaching'])) {
        $id = (int)$_POST['coaching_id'];
        
        $stmt = $conn->prepare("DELETE FROM coaching_sessions WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success_message = 'Cursul a fost șters cu succes.';
        } else {
            $error_message = 'A apărut o eroare la ștergerea cursului.';
        }
    }
    
    if (isset($_POST['update_booking_status']) && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $active_tab = 'bookings';
        // DEBUG: Afișează $_POST pentru status update
        echo '<div style="background:#ffe0e0;color:#900;padding:10px;margin:10px 0;border:2px solid #900;font-size:16px;">';
        echo '<strong>DEBUG $_POST:</strong><br><pre>';
        print_r($_POST);
        echo '</pre></div>';
        $booking_id = (int)$_POST['booking_id'];
        $status = sanitize_input($_POST['status']);
        $stmt = $conn->prepare("UPDATE coaching_bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $booking_id);
        if ($stmt->execute()) {
            $success_message = 'Statusul rezervării a fost actualizat cu succes.';
        } else {
            $error_message = 'A apărut o eroare la actualizarea statusului rezervării.';
        }
    }
}

// Get all coaching sessions
$result = $conn->query("SELECT * FROM coaching_sessions ORDER BY id DESC");
$coaching_sessions = [];
while ($row = $result->fetch_assoc()) {
    $row['benefits'] = $row['benefits'] ? $row['benefits'] : '[]';
    $coaching_sessions[] = $row;
}

// Get coaching bookings
$sql = "SELECT cb.*, cs.session_name, cs.price, cs.duration 
        FROM coaching_bookings cb 
        JOIN coaching_sessions cs ON cb.session_id = cs.id 
        ORDER BY cb.booking_date DESC, cb.booking_time DESC";
$result = $conn->query($sql);
$coaching_bookings = $result->fetch_all(MYSQLI_ASSOC);

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestionare Cursuri Coaching</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#coachingModal">
                        <i class="fas fa-plus me-1"></i>Adaugă Curs
                    </button>
                </div>
            </div>

            <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo ($active_tab=='sessions')?'active':''; ?>" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button" role="tab">
                        <i class="fas fa-graduation-cap me-1"></i>Cursuri
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo ($active_tab=='bookings')?'active':''; ?>" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button" role="tab">
                        <i class="fas fa-users me-1"></i>Rezervări
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
<!-- Modal Vizualizare Curs -->
<div class="modal fade" id="viewCoachingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCoachingTitle">Detalii Curs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewCoachingBody">
                <!-- Detalii populate din JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închide</button>
                <button type="button" class="btn btn-primary" id="updateCoachingBtn">Actualizează curs</button>
            </div>
        </div>
    </div>
</div>
                <!-- Sessions Tab -->
                <div class="tab-pane fade <?php echo ($active_tab=='sessions')?'show active':''; ?>" id="sessions" role="tabpanel">
                    <div class="card shadow mt-3">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Lista Cursurilor</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($coaching_sessions)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;text-align:center;">ID</th>
                                            <th style="width:250px;">Nume Curs</th>
                                            <th style="width:250px;">Descriere</th>
                                            <th style="width:100px;text-align:center;">Preț</th>
                                            <th style="width:70px;text-align:center;">Durată</th>
                                            <th style="width:70px;text-align:center;">Max</th>
                                            <th style="width:70px;text-align:center;">Status</th>
                                            <th style="width:350px;text-align:center;">Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($coaching_sessions as $session): ?>
                                        <tr>
                                            <td><?php echo $session['id']; ?></td>
                                            <td><?php echo htmlspecialchars($session['session_name']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($session['description'], 0, 50)) . '...'; ?></td>
                                            <td><?php echo number_format($session['price'], 0); ?></td>
                                            <td><?php echo $session['duration']; ?></td>
                                            <td><?php echo $session['max_participants']; ?></td>
                                            <td>
                                                    <span class="badge <?php echo $session['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo $session['is_active'] ? 'Activ' : 'Inactiv'; ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars(mb_strimwidth($session['long_description'] ?? '', 0, 40, '...')) ?></td>
                                                <td>
                                                    <?php 
                                                    $benefitsArr = [];
                                                    try { $benefitsArr = json_decode($session['benefits'], true); } catch(Exception $e) {}
                                                    if (is_array($benefitsArr) && count($benefitsArr)) {
                                                        echo '<span class="badge bg-info text-dark">'.htmlspecialchars($benefitsArr[0]).'</span>';
                                                        if (count($benefitsArr) > 1) echo ' <span class="badge bg-secondary">+'.(count($benefitsArr)-1).' alte</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-info view-coaching" 
                                                            data-coaching='<?php echo json_encode($session); ?>'
                                                            data-bs-toggle="modal" data-bs-target="#viewCoachingModal">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-coaching" 
                                                            data-coaching-id="<?php echo $session['id']; ?>"
                                                            data-coaching-name="<?php echo htmlspecialchars($session['session_name']); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <h5>Nu există cursuri</h5>
                                <p class="text-muted">Adaugă primul curs pentru a începe.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Bookings Tab -->
                <div class="tab-pane fade <?php echo ($active_tab=='bookings')?'show active':''; ?>" id="bookings" role="tabpanel">
                    <div class="card shadow mt-3">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Rezervări Cursuri</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($coaching_bookings)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Client</th>
                                            <th>Email</th>
                                            <th>Telefon</th>
                                            <th>Curs</th>
                                            <th>Data</th>
                                            <th>Ora</th>
                                            <th>Status</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($coaching_bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo $booking['id']; ?></td>
                                            <td><?php echo htmlspecialchars($booking['client_name']); ?></td>
                                            <td>
                                                <a href="mailto:<?php echo htmlspecialchars($booking['client_email']); ?>">
                                                    <?php echo htmlspecialchars($booking['client_email']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="tel:<?php echo htmlspecialchars($booking['client_phone']); ?>">
                                                    <?php echo htmlspecialchars($booking['client_phone']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($booking['session_name']); ?>
                                                <br><small class="text-muted"><?php echo number_format($booking['price'], 0); ?> RON</small>
                                            </td>
                                            <td><?php echo format_date($booking['booking_date']); ?></td>
                                            <td><?php echo format_time($booking['booking_time']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $booking['status']; ?>">
                                                    <?php 
                                                    switch($booking['status']) {
                                                        case 'pending': echo 'În așteptare'; break;
                                                        case 'confirmed': echo 'Confirmată'; break;
                                                        case 'completed': echo 'Finalizată'; break;
                                                        case 'cancelled': echo 'Anulată'; break;
                                                        default: echo ucfirst($booking['status']);
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#bookingStatusModal" 
                                                            data-booking-id="<?php echo $booking['id']; ?>"
                                                            data-current-status="<?php echo $booking['status']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" action="" style="display:inline;">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                        <input type="hidden" name="delete_booking" value="1">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Sigur doriți să ștergeți această rezervare?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>Nu există rezervări</h5>
                                <p class="text-muted">Rezervările vor apărea aici când clienții se vor înscrie la cursuri.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Coaching Session Modal -->
<div class="modal fade" id="coachingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="coachingModalTitle">Adaugă Curs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="coachingForm">
                <input type="hidden" id="form_action" name="form_action" value="add_coaching">
                <div class="modal-body">
                    <input type="hidden" id="coaching_id" name="coaching_id">
                    
                    <div class="mb-3">
                        <label for="session_name" class="form-label">Nume Curs *</label>
                        <input type="text" class="form-control" id="session_name" name="session_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Preț (RON) *</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="duration" class="form-label">Durată (ore) *</label>
                            <input type="number" class="form-control" id="duration" name="duration" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="max_participants" class="form-label">Max Participanți *</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants" min="1" value="1" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descriere scurtă</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="long_description" class="form-label">Descriere detaliată</label>
                        <textarea class="form-control" id="long_description" name="long_description" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ce vei învăța (beneficii, fiecare pe rând)</label>
                        <div id="benefitsList">
                            <input type="text" class="form-control mb-2" name="benefits[]" placeholder="Beneficiu 1">
                            <input type="text" class="form-control mb-2" name="benefits[]" placeholder="Beneficiu 2">
                            <input type="text" class="form-control mb-2" name="benefits[]" placeholder="Beneficiu 3">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="addBenefitBtn">Adaugă rând</button>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Curs activ
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">Adaugă Curs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Booking Status Modal -->
<div class="modal fade" id="bookingStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizează Status Rezervare</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" id="booking_id" name="booking_id" value="">
                    <input type="hidden" name="update_booking_status" value="1">
                    <div class="mb-3">
                        <label for="booking_status" class="form-label">Selectează noul status:</label>
                        <select class="form-select" id="booking_status" name="status" required>
                            <option value="pending">În așteptare</option>
                            <option value="confirmed">Confirmată</option>
                            <option value="completed">Finalizată</option>
                            <option value="cancelled">Anulată</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" name="update_booking_status" class="btn btn-primary">Actualizează</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmă Ștergerea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Ești sigur că vrei să ștergi cursul <strong id="coachingNameToDelete"></strong>?</p>
                <p class="text-muted">Această acțiune nu poate fi anulată.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                <form method="POST" action="" style="display: inline;">
                    <input type="hidden" id="deleteCoachingId" name="coaching_id">
                    <button type="submit" name="delete_coaching" class="btn btn-danger">Șterge</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
// Modal vizualizare curs
document.addEventListener('DOMContentLoaded', function() {
    // Dacă tab-ul activ e bookings, setează automat tab-ul la reload
    <?php if ($active_tab=='bookings'): ?>
    setTimeout(function() {
        var bookingsTab = document.getElementById('bookings-tab');
        if (bookingsTab) bookingsTab.click();
    }, 100);
    <?php endif; ?>
    const viewCoachingModal = document.getElementById('viewCoachingModal');
    const viewCoachingBody = document.getElementById('viewCoachingBody');
    const viewCoachingTitle = document.getElementById('viewCoachingTitle');
    let currentCoaching = null;
    document.querySelectorAll('.view-coaching').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentCoaching = JSON.parse(btn.getAttribute('data-coaching'));
            viewCoachingTitle.textContent = currentCoaching.session_name;
            let html = `<div class='mb-3'><strong>Descriere detaliată:</strong><br>${currentCoaching.long_description || currentCoaching.description}</div>`;
            html += `<div class='row mb-3'>
                        <div class='col-md-6'><i class='fas fa-clock text-primary'></i> Durată: <strong>${currentCoaching.duration} ore</strong></div>
                        <div class='col-md-6'><i class='fas fa-users text-primary'></i> Max <strong>${currentCoaching.max_participants}</strong> participanți</div>
                    </div>`;
            html += `<div class='mb-3'><strong>Preț:</strong> <span class='badge bg-success'>${parseFloat(currentCoaching.price).toFixed(2)} RON</span></div>`;
            let benefitsArr = currentCoaching.benefits;
            if (typeof benefitsArr === 'string') {
                try { benefitsArr = JSON.parse(benefitsArr); } catch(e) { benefitsArr = []; }
            }
            if (Array.isArray(benefitsArr) && benefitsArr.length) {
                html += `<div class='mb-3'><strong>Ce vei învăța:</strong><ul>`;
                benefitsArr.forEach(function(b) { html += `<li>${b}</li>`; });
                html += `</ul></div>`;
            }
            viewCoachingBody.innerHTML = html;
        });
    });
    document.getElementById('updateCoachingBtn').addEventListener('click', function() {
        // Deschide modalul de editare cu datele curente
        if (currentCoaching) {
            document.getElementById('coachingModalTitle').textContent = 'Editează Curs';
            document.getElementById('coaching_id').value = currentCoaching.id;
            document.getElementById('session_name').value = currentCoaching.session_name;
            document.getElementById('description').value = currentCoaching.description;
            document.getElementById('long_description').value = currentCoaching.long_description || '';
            // Populează beneficiile
            const benefitsList = document.getElementById('benefitsList');
            benefitsList.innerHTML = '';
            let benefitsArr = currentCoaching.benefits;
            if (typeof benefitsArr === 'string') {
                try { benefitsArr = JSON.parse(benefitsArr); } catch(e) { benefitsArr = []; }
            }
            if (Array.isArray(benefitsArr) && benefitsArr.length) {
                benefitsArr.forEach(function(b) {
                    benefitsList.innerHTML += `<input type='text' class='form-control mb-2' name='benefits[]' value='${b}' placeholder='Beneficiu'>`;
                });
            } else {
                for (let i=0; i<3; i++) benefitsList.innerHTML += `<input type='text' class='form-control mb-2' name='benefits[]' placeholder='Beneficiu ${i+1}'>`;
            }
            document.getElementById('price').value = currentCoaching.price;
            document.getElementById('duration').value = currentCoaching.duration;
            document.getElementById('max_participants').value = currentCoaching.max_participants;
            document.getElementById('is_active').checked = currentCoaching.is_active == 1;
            document.getElementById('form_action').value = 'edit_coaching';
            document.getElementById('submitBtn').textContent = 'Actualizează Curs';
            var editModal = new bootstrap.Modal(document.getElementById('coachingModal'));
            editModal.show();
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Edit coaching session
    document.querySelectorAll('.edit-coaching').forEach(button => {
        button.addEventListener('click', function() {
            const coaching = JSON.parse(this.getAttribute('data-coaching'));
            document.getElementById('coachingModalTitle').textContent = 'Editează Curs';
            document.getElementById('coaching_id').value = coaching.id;
            document.getElementById('session_name').value = coaching.session_name;
            document.getElementById('description').value = coaching.description;
            document.getElementById('long_description').value = coaching.long_description || '';
            // Populează beneficiile din DB
            const benefitsList = document.getElementById('benefitsList');
            benefitsList.innerHTML = '';
            let benefitsArr = [];
            try { benefitsArr = JSON.parse(coaching.benefits); } catch(e) {}
            if (Array.isArray(benefitsArr) && benefitsArr.length) {
                benefitsArr.forEach(function(b) {
                    benefitsList.innerHTML += `<input type='text' class='form-control mb-2' name='benefits[]' value='${b}' placeholder='Beneficiu'>`;
                });
            } else {
                for (let i=0; i<3; i++) benefitsList.innerHTML += `<input type='text' class='form-control mb-2' name='benefits[]' placeholder='Beneficiu ${i+1}'>`;
            }
            document.getElementById('price').value = coaching.price;
            document.getElementById('duration').value = coaching.duration;
            document.getElementById('max_participants').value = coaching.max_participants;
            document.getElementById('is_active').checked = coaching.is_active == 1;
            // Setează corect tipul de submit
            document.getElementById('form_action').value = 'edit_coaching';
            document.getElementById('submitBtn').textContent = 'Actualizează Curs';
        });
    });
    // Resetare formular la adăugare
    document.querySelector('[data-bs-target="#coachingModal"]').addEventListener('click', function() {
        document.getElementById('coachingModalTitle').textContent = 'Adaugă Curs';
        document.getElementById('coaching_id').value = '';
        document.getElementById('session_name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('long_description').value = '';
        document.getElementById('price').value = '';
        document.getElementById('duration').value = '';
        document.getElementById('max_participants').value = '';
        document.getElementById('is_active').checked = true;
        const benefitsList = document.getElementById('benefitsList');
        benefitsList.innerHTML = '';
        for (let i=0; i<3; i++) benefitsList.innerHTML += `<input type='text' class='form-control mb-2' name='benefits[]' placeholder='Beneficiu ${i+1}'>`;
    document.getElementById('form_action').value = 'add_coaching';
    document.getElementById('submitBtn').textContent = 'Adaugă Curs';
    });
    
    // Delete coaching session
    document.querySelectorAll('.delete-coaching').forEach(button => {
        button.addEventListener('click', function() {
            const coachingId = this.getAttribute('data-coaching-id');
            const coachingName = this.getAttribute('data-coaching-name');
            
            document.getElementById('coachingNameToDelete').textContent = coachingName;
            document.getElementById('deleteCoachingId').value = coachingId;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
    
    // Booking status modal
    const bookingStatusModal = document.getElementById('bookingStatusModal');
    bookingStatusModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const bookingId = button.getAttribute('data-booking-id');
        const currentStatus = button.getAttribute('data-current-status');
        // Setează explicit value pe input
        var inputBookingId = bookingStatusModal.querySelector('input[name="booking_id"]');
        if (inputBookingId) inputBookingId.value = bookingId;
        document.getElementById('booking_status').value = currentStatus;
    });
    
    // Reset form when modal is hidden
    document.getElementById('coachingModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('coachingForm').reset();
        document.getElementById('coachingModalTitle').textContent = 'Adaugă Curs';
        document.getElementById('coaching_id').value = '';
    document.getElementById('submitBtn').setAttribute('name', 'add_coaching');
        document.getElementById('submitBtn').textContent = 'Adaugă Curs';
        document.getElementById('is_active').checked = true;
        // Reset beneficii
        const benefitsList = document.getElementById('benefitsList');
        benefitsList.innerHTML = '';
        for (let i=0; i<3; i++) benefitsList.innerHTML += `<input type='text' class='form-control mb-2' name='benefits[]' placeholder='Beneficiu ${i+1}'>`;
        document.getElementById('long_description').value = '';
    });
    // Adaugă rând nou la beneficii
    document.getElementById('addBenefitBtn').addEventListener('click', function() {
        document.getElementById('benefitsList').innerHTML += `<input type='text' class='form-control mb-2' name='benefits[]' placeholder='Beneficiu'>`;
    });
});
</script>

<?php include 'includes/admin_footer.php'; ?>
