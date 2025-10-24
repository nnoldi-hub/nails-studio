<?php
require_once 'includes/admin_header.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();
$page_title = 'Detalii Comandă';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = null;
$products = [];
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
    $result2 = $conn->query("SELECT * FROM order_products WHERE order_id = $id");
    while ($row = $result2->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Detalii Comandă</h1>
            </div>
            <?php if ($order): ?>
            <div class="card mb-4 shadow">
                <div class="card-header">Comanda #<?= $order['id']; ?> - <?= htmlspecialchars($order['client_name']); ?></div>
                <div class="card-body">
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['client_email']); ?></p>
                    <p><strong>Telefon:</strong> <?= htmlspecialchars($order['client_phone']); ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-<?= $order['status'] == 'nou' ? 'warning' : ($order['status'] == 'confirmat' ? 'success' : 'secondary'); ?>"><?= $order['status']; ?></span></p>
                    <p><strong>Observații:</strong> <?= htmlspecialchars($order['notes']); ?></p>
                    <p><strong>Data:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])); ?></p>
                    <hr>
                    <h5>Produse comandate</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Produs</th>
                                <th>Preț</th>
                                <th>Cantitate</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $prod): ?>
                            <tr>
                                <td><?= htmlspecialchars($prod['product_name']); ?></td>
                                <td><?= number_format($prod['price'],2); ?> RON</td>
                                <td><?= $prod['quantity']; ?></td>
                                <td><?= number_format($prod['price'] * $prod['quantity'],2); ?> RON</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total comandă</th>
                                <th><?= number_format($order['total'],2); ?> RON</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-danger">Comanda nu a fost găsită.</div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
