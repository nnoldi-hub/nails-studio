<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin_login();


$page_title = 'Gestionare Programări';
$success_message = '';
$error_message = '';

// Handle delete appointment
if (isset($_POST['delete_appointment']) && isset($_POST['appointment_id'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        $success_message = 'Programarea a fost ștearsă cu succes.';
    } else {
        $error_message = 'A apărut o eroare la ștergerea programării.';
    }
}

// Handle status updates
if (isset($_POST['update_status']) && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $status = sanitize_input($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $appointment_id);
    
    if ($stmt->execute()) {
        $success_message = 'Statusul programării a fost actualizat cu succes.';
    } else {
        $error_message = 'A apărut o eroare la actualizarea statusului.';
    }
}

// Get all appointments
$appointments = get_all_appointments();

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestionare Programări</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Actualizează
                        </button>
                    </div>
                </div>
            </div>

            <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Appointments Table -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Lista Programărilor</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($appointments)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Email</th>
                                    <th>Telefon</th>
                                    <th>Serviciu</th>
                                    <th>Data</th>
                                    <th>Ora</th>
                                    <th>Status</th>
                                    <th>Acțiuni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?php echo $appointment['id']; ?></td>
                                    <td><?php echo htmlspecialchars($appointment['client_name']); ?></td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($appointment['client_email']); ?>">
                                            <?php echo htmlspecialchars($appointment['client_email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo htmlspecialchars($appointment['client_phone']); ?>">
                                            <?php echo htmlspecialchars($appointment['client_phone']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($appointment['service_name']); ?>
                                        <br><small class="text-muted"><?php echo number_format($appointment['price'], 0); ?> RON</small>
                                    </td>
                                    <td><?php echo format_date($appointment['appointment_date']); ?></td>
                                    <td><?php echo format_time($appointment['appointment_time']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $appointment['status']; ?>">
                                            <?php 
                                            switch($appointment['status']) {
                                                case 'pending': echo 'În așteptare'; break;
                                                case 'confirmed': echo 'Confirmată'; break;
                                                case 'completed': echo 'Finalizată'; break;
                                                case 'cancelled': echo 'Anulată'; break;
                                                default: echo ucfirst($appointment['status']);
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#statusModal" 
                                                    data-appointment-id="<?php echo $appointment['id']; ?>"
                                                    data-current-status="<?php echo $appointment['status']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailsModal"
                                                    data-appointment='<?php echo json_encode($appointment); ?>'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-appointment" 
                                                    data-appointment-id="<?php echo $appointment['id']; ?>"
                                                    data-appointment-name="<?php echo htmlspecialchars($appointment['client_name']); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                        <h5>Nu există programări</h5>
                        <p class="text-muted">Programările vor apărea aici când clienții se vor înscrie.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Delete Modal (unic, la finalul paginii) -->
<div class="modal fade" id="deleteAppointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmă Ștergerea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Ești sigur că vrei să ștergi programarea pentru <strong id="appointmentNameToDelete"></strong>?</p>
                <p class="text-muted">Această acțiune nu poate fi anulată.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                <form method="POST" action="" style="display: inline;">
                    <input type="hidden" id="deleteAppointmentId" name="appointment_id">
                    <input type="hidden" name="delete_appointment" value="1">
                    <button type="submit" class="btn btn-danger">Șterge</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizează Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" id="appointment_id" name="appointment_id">
                    <input type="hidden" name="update_status" value="1">
                    <div class="mb-3">
                        <label for="status" class="form-label">Selectează noul status:</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">În așteptare</option>
                            <option value="confirmed">Confirmată</option>
                            <option value="completed">Finalizată</option>
                            <option value="cancelled">Anulată</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Actualizează</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalii Programare</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentDetails">
                <!-- Details will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închide</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete appointment
    document.querySelectorAll('.delete-appointment').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            const appointmentName = this.getAttribute('data-appointment-name');
            document.getElementById('appointmentNameToDelete').textContent = appointmentName;
            document.getElementById('deleteAppointmentId').value = appointmentId;
            new bootstrap.Modal(document.getElementById('deleteAppointmentModal')).show();
        });
    });
    // Status modal
    const statusModal = document.getElementById('statusModal');
    statusModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const appointmentId = button.getAttribute('data-appointment-id');
        const currentStatus = button.getAttribute('data-current-status');
        
        document.getElementById('appointment_id').value = appointmentId;
        document.getElementById('status').value = currentStatus;
    });
    
    // Details modal
    const detailsModal = document.getElementById('detailsModal');
    detailsModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const appointment = JSON.parse(button.getAttribute('data-appointment'));
        
        const detailsContent = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informații Client</h6>
                    <p><strong>Nume:</strong> ${appointment.client_name}</p>
                    <p><strong>Email:</strong> ${appointment.client_email}</p>
                    <p><strong>Telefon:</strong> ${appointment.client_phone}</p>
                </div>
                <div class="col-md-6">
                    <h6>Detalii Programare</h6>
                    <p><strong>Serviciu:</strong> ${appointment.service_name}</p>
                    <p><strong>Preț:</strong> ${appointment.price} RON</p>
                    <p><strong>Durată:</strong> ${appointment.duration} minute</p>
                    <p><strong>Data:</strong> ${appointment.appointment_date}</p>
                    <p><strong>Ora:</strong> ${appointment.appointment_time}</p>
                    <p><strong>Status:</strong> <span class="badge badge-${appointment.status}">${appointment.status}</span></p>
                </div>
            </div>
            ${appointment.notes ? `<div class="row mt-3"><div class="col-12"><h6>Observații</h6><p>${appointment.notes}</p></div></div>` : ''}
        `;
        
        document.getElementById('appointmentDetails').innerHTML = detailsContent;
    });
});
</script>

<?php include 'includes/admin_footer.php'; ?>
