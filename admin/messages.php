<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();

$page_title = 'Mesaje Contact';

$success_message = '';
$error_message = '';

// Handle mark as read/unread
if (isset($_POST['toggle_read'])) {
    $message_id = (int)$_POST['message_id'];
    $is_read = (int)$_POST['is_read'];

    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_read, $message_id);
    if ($stmt->execute()) {
        $success_message = $is_read ? 'Mesajul a fost marcat ca citit.' : 'Mesajul a fost marcat ca necitit.';
    } else {
        $error_message = 'Eroare la actualizarea statusului mesajului.';
    }
}

// Handle delete message
if (isset($_POST['delete_message'])) {
    $message_id = (int)$_POST['message_id'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    if ($stmt->execute()) {
        $success_message = 'Mesajul a fost sters cu succes.';
    } else {
        $error_message = 'Eroare la stergerea mesajului.';
    }
}

// Handle AJAX mark as read
if (isset($_POST['ajax_mark_read'])) {
    $message_id = (int)$_POST['message_id'];
    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    exit;
}

// Get all messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
$messages = $result->fetch_all(MYSQLI_ASSOC);

// Count unread
$unread_count = 0;
foreach ($messages as $m) {
    if (!$m['is_read']) $unread_count++;
}

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    Mesaje Contact 
                    <?php if ($unread_count > 0): ?>
                        <span class="badge bg-danger"><?php echo $unread_count; ?> necitite</span>
                    <?php endif; ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>Actualizeaza
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

            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="card mb-3 <?php echo !$msg['is_read'] ? 'border-primary' : ''; ?>">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">
                                    <?php if (!$msg['is_read']): ?>
                                        <i class="fas fa-circle text-primary me-2" style="font-size: 0.5rem;"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($msg['subject']); ?>
                                </h6>
                                <small class="text-muted">
                                    De la: <?php echo htmlspecialchars($msg['name']); ?> 
                                    (<?php echo htmlspecialchars($msg['email']); ?>)
                                    - <?php echo format_date($msg['created_at']); ?>
                                </small>
                            </div>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-info view-message" 
                                        data-message='<?php echo json_encode($msg); ?>'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    <input type="hidden" name="is_read" value="<?php echo $msg['is_read'] ? 0 : 1; ?>">
                                    <?php if ($msg['is_read']): ?>
                                        <button type="submit" name="toggle_read" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    <?php endif; ?>
                                </form>
                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-reply"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-message"
                                        data-message-id="<?php echo $msg['id']; ?>"
                                        data-message-subject="<?php echo htmlspecialchars($msg['subject']); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                        <h5>Nu exista mesaje</h5>
                        <p class="text-muted">Mesajele primite prin formular vor aparea aici.</p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewMessageTitle">Detalii Mesaj</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="viewMessageBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Inchide</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmare stergere</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Esti sigur ca vrei sa stergi mesajul <strong id="messageSubjectToDelete"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuleaza</button>
        <form method="POST" action="" style="display:inline;">
            <input type="hidden" id="deleteMessageId" name="message_id">
            <input type="hidden" name="delete_message" value="1">
            <button type="submit" class="btn btn-danger">Sterge</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Vizualizare mesaj
    document.querySelectorAll('.view-message').forEach(btn => {
        btn.addEventListener('click', () => {
            const msg = JSON.parse(btn.dataset.message);
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'ajax_mark_read=1&message_id=' + msg.id
            });

            document.getElementById('viewMessageTitle').textContent = msg.subject;
            document.getElementById('viewMessageBody').innerHTML = `
                <p><strong>De la:</strong> ${msg.name} &lt;${msg.email}&gt;</p>
                <p><strong>Data:</strong> ${msg.created_at}</p>
                <p><strong>Mesaj:</strong><br><div class="border rounded p-2 bg-light">${msg.message.replace(/\n/g,'<br>')}</div></p>
            `;
            new bootstrap.Modal(document.getElementById('viewMessageModal')).show();
        });
    });

    // Stergere mesaj
    document.querySelectorAll('.delete-message').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('deleteMessageId').value = btn.dataset.messageId;
            document.getElementById('messageSubjectToDelete').textContent = btn.dataset.messageSubject;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script>

<?php include 'includes/admin_footer.php'; ?>
