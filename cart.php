<?php
ini_set('session.cookie_path', '/');
session_name('shop_session');
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Coșul meu';
$page_description = 'Produsele selectate pentru comandă.';

// Preluare produse din coș
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = [];
$total = 0;
if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $cart[$row['id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $products[] = $row;
        $total += $row['subtotal'];
    }
}

// Procesare comandă
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $client_name = sanitize_input($_POST['client_name']);
    $client_email = sanitize_input($_POST['client_email']);
    $client_phone = sanitize_input($_POST['client_phone']);
    $notes = sanitize_input($_POST['notes']);
    if ($client_name && $client_email && $client_phone && $products) {
        $stmt = $conn->prepare("INSERT INTO orders (client_name, client_email, client_phone, total, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssds', $client_name, $client_email, $client_phone, $total, $notes);
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            $stmt->close();
            foreach ($products as $prod) {
                $stmt2 = $conn->prepare("INSERT INTO order_products (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
                $stmt2->bind_param('iisdi', $order_id, $prod['id'], $prod['name'], $prod['price'], $prod['quantity']);
                $stmt2->execute();
                $stmt2->close();
            }
            $_SESSION['cart'] = [];
            $success_message = 'Comanda a fost trimisă! Veți primi confirmarea în scurt timp.';
        } else {
            $error_message = 'Eroare la salvarea comenzii.';
        }
    } else {
        $error_message = 'Completează toate câmpurile și adaugă produse în coș.';
    }
}

include 'includes/header.php';
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Coșul meu</h1>
        <p class="text-muted">Produsele selectate pentru comandă</p>
    </div>
    <?php if ($success_message): ?>
        <div class="alert alert-success text-center"> <?= $success_message ?> </div>
    <?php elseif ($error_message): ?>
        <div class="alert alert-danger text-center"> <?= $error_message ?> </div>
    <?php endif; ?>
    <?php if ($products): ?>
    <form method="POST" class="row g-4">
        <div class="col-lg-8">
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
                        <td><?= htmlspecialchars($prod['name']) ?></td>
                        <td><?= number_format($prod['price'],2) ?> RON</td>
                        <td><?= $prod['quantity'] ?></td>
                        <td><?= number_format($prod['subtotal'],2) ?> RON</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th><?= number_format($total,2) ?> RON</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title mb-3">Date client</h5>
                    <div class="mb-3">
                        <label>Nume</label>
                        <input type="text" name="client_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="client_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Telefon</label>
                        <input type="text" name="client_phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Observații</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-success w-100">Trimite comanda</button>
                </div>
            </div>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-info text-center">Coșul este gol. <a href="products.php">Vezi produsele</a></div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
