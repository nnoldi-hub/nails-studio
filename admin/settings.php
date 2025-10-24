<?php
require_once 'includes/admin_header.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();
$page_title = 'Setări Site';
$error_message = '';
$success_message = '';

// Handle shop module activation/deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_shop'])) {
    $shop_enabled = isset($_POST['shop_enabled']) ? '1' : '0';
    $stmt = $conn->prepare("INSERT INTO settings (name, value) VALUES ('shop_enabled', ?) ON DUPLICATE KEY UPDATE value = ?");
    $stmt->bind_param('ss', $shop_enabled, $shop_enabled);
    if ($stmt->execute()) {
        $success_message = 'Setarea magazinului a fost actualizată!';
    } else {
        $error_message = 'Eroare la actualizarea setării!';
    }
    $stmt->close();
}

// Get current shop setting
$shop_enabled = get_shop_enabled();
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Setări Site</h1>
            </div>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"> <?php echo $success_message; ?> </div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
            <?php endif; ?>
            <div class="card mb-4 shadow">
                <div class="card-header">Activare/Dezactivare Module</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="shop_enabled" id="shop_enabled" value="1" <?php echo $shop_enabled ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="shop_enabled">Activează modulul Magazin</label>
                        </div>
                        <button type="submit" name="toggle_shop" class="btn btn-primary">Salvează</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
