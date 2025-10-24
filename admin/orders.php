<?php
require_once 'includes/admin_header.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();
$page_title = 'Comenzi Magazin';
$error_message = '';
$success_message = '';

// Confirmare comandă
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("UPDATE orders SET status='confirmat' WHERE id=?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $success_message = 'Comanda a fost confirmată!';
    } else {
        $error_message = 'Eroare la confirmare!';
    }
    $stmt->close();
}

// Listare comenzi
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Comenzi Magazin</h1>
            </div>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"> <?php echo $success_message; ?> </div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
            <?php endif; ?>
            <div class="card mb-4 shadow">
                <div class="card-header">Comenzi primite</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Telefon</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $orders->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['client_name']); ?></td>
                                <td><?= htmlspecialchars($row['client_email']); ?></td>
                                <td><?= htmlspecialchars($row['client_phone']); ?></td>
                                <td><?= number_format($row['total'],2); ?> RON</td>
                                <td><span class="badge bg-<?= $row['status'] == 'nou' ? 'warning' : ($row['status'] == 'confirmat' ? 'success' : 'secondary'); ?>"><?= $row['status']; ?></span></td>
                                <td><?= date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display:inline-block;">
                                        <input type="hidden" name="confirm_order" value="1">
                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                        <?php if ($row['status'] == 'nou'): ?>
                                        <button type="submit" class="btn btn-sm btn-success">Confirmă</button>
                                        <?php endif; ?>
                                    </form>
                                    <a href="order_details.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary ms-1">Detalii</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
